<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Models\OutboxMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Notification Service — Email / SMS queue API
 *
 * Microservice: notification-service (port 9004)
 * Database:     notification_db (mysql-notification:3313)
 * Patterns:     Event-Driven, Outbox, Async processing
 */
class NotificationServiceController extends Controller
{
    // ── Send notification ───────────────────────────────────────

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'type'      => 'required|string|in:email,sms,push',
            'recipient' => 'required|string',
            'subject'   => 'required|string|max:255',
            'body'      => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $notification = DB::table('notifications_log')->insertGetId([
                'type'       => $request->type,
                'recipient'  => $request->recipient,
                'subject'    => $request->subject,
                'body'       => $request->body,
                'status'     => 'queued',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            OutboxMessage::create([
                'aggregate_type' => 'notification',
                'aggregate_id'   => $notification,
                'event_type'     => 'notification.queued',
                'payload'        => json_encode([
                    'type'      => $request->type,
                    'recipient' => $request->recipient,
                    'subject'   => $request->subject,
                ]),
            ]);

            DB::commit();

            Log::info('[NOTIFICATION] Queued', [
                'id'        => $notification,
                'type'      => $request->type,
                'recipient' => $request->recipient,
            ]);

            return response()->json([
                'success'         => true,
                'notification_id' => $notification,
                'status'          => 'queued',
                'message'         => 'Notification queued (Outbox pattern)',
            ], 201)->header('X-Service', 'notification-service');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[NOTIFICATION] Send failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ── Process outbox (worker endpoint) ────────────────────────

    public function processOutbox(): JsonResponse
    {
        $pending = OutboxMessage::unpublished()
            ->take(50)
            ->get();

        $processed = 0;

        foreach ($pending as $message) {
            try {
                Log::info('[NOTIFICATION] Delivering outbox message', [
                    'event_type'   => $message->event_type,
                    'aggregate_id' => $message->aggregate_id,
                ]);

                $message->markPublished();
                $processed++;
            } catch (\Exception $e) {
                Log::error('[NOTIFICATION] Outbox delivery failed', [
                    'message_id' => $message->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success'   => true,
            'processed' => $processed,
            'remaining' => OutboxMessage::unpublished()->count(),
        ])->header('X-Service', 'notification-service');
    }

    // ── History ─────────────────────────────────────────────────

    public function history(Request $request): JsonResponse
    {
        $items = DB::table('notifications_log')
            ->orderBy('id', 'DESC')
            ->paginate(20);

        return response()->json($items)
            ->header('X-Service', 'notification-service');
    }
}
