<?php

namespace App\Jobs;

use App\Models\Models\OutboxMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishOutboxMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Batch size for processing
     */
    private int $batchSize;

    /**
     * Create a new job instance.
     */
    public function __construct(int $batchSize = 100)
    {
        $this->batchSize = $batchSize;
    }

    /**
     * Execute the job - Publish unpublished messages to Redis queue
     */
    public function handle(): void
    {
        $messages = OutboxMessage::unpublished()
            ->limit($this->batchSize)
            ->get();

        if ($messages->isEmpty()) {
            Log::info('No unpublished outbox messages found');
            return;
        }

        $redis = \Illuminate\Support\Facades\Redis::connection();
        $queueName = config('queue.connections.redis.queue', 'notifications');

        foreach ($messages as $message) {
            try {
                // Prepare event data for notification service
                $eventData = [
                    'event_type' => $message->event_type,
                    'aggregate_type' => $message->aggregate_type,
                    'aggregate_id' => $message->aggregate_id,
                    'payload' => $message->payload,
                    'occurred_at' => $message->occurred_at->toDateTimeString(),
                ];

                // Wrap in Laravel queue job format
                $job = [
                    'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                    'displayName' => 'NotificationEvent',
                    'job' => 'NotificationEvent',
                    'maxTries' => null,
                    'timeout' => null,
                    'data' => $eventData,
                ];

                // Push to Redis queue
                $redis->lpush($queueName, json_encode($job));

                Log::info('Published outbox message to Redis', [
                    'id' => $message->id,
                    'event_type' => $message->event_type,
                    'queue' => $queueName,
                ]);

                $message->markPublished();
            } catch (\Exception $e) {
                Log::error('Failed to publish outbox message', [
                    'id' => $message->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Published {count} outbox messages', [
            'count' => $messages->count(),
        ]);
    }
}
