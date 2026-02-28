<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\OrderPlaced;
use App\Models\Models\Transaction;
use App\Models\Models\Order;
use App\Models\Models\Cart;
use App\Services\Saga\OrderSaga;
use App\Services\Saga\Steps\ReserveStockStep;
use App\Services\Saga\Steps\ProcessPaymentStep;
use App\Services\Saga\Steps\CreateShipmentStep;
use App\Services\Saga\Steps\SendNotificationStep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Order Service — Transaction & Order API
 *
 * Microservice: order-service (port 9002)
 * Database:     order_db (mysql-order:3311)
 * Patterns:     Saga, Event-Driven (Outbox), Distributed Transaction
 */
class OrderServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('user_id');

        $query = Transaction::orderBy('id', 'DESC');

        if ($userId) {
            $query->where('tr_user_id', $userId);
        }

        $orders = $query->paginate(20);

        return response()->json($orders)
            ->header('X-Service', 'order-service');
    }

    public function show(int $id): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);

        $orderItems = Order::where('od_transaction_id', $id)->get();

        return response()->json([
            'success'     => true,
            'transaction' => $transaction,
            'items'       => $orderItems,
        ])->header('X-Service', 'order-service');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'        => 'required|integer',
            'payment_method' => 'required|string|in:cod,momo,vnpay,paypal',
        ]);

        DB::beginTransaction();

        try {
            $cartItems = Cart::where('user_id', $request->user_id)->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Cart is empty',
                ], 400);
            }

            $total = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $transaction = new Transaction();
            $transaction->tr_user_id = $request->user_id;
            $transaction->tr_total   = $total;
            $transaction->tr_status  = Transaction::STATUS_DEFAULT;
            $transaction->save();

            foreach ($cartItems as $item) {
                DB::table('oders')->insert([
                    'od_transaction_id' => $transaction->id,
                    'od_product_id'     => $item->pro_id,
                    'od_qty'            => $item->quantity,
                    'od_price'          => $item->price,
                    'od_sale'           => 0,
                ]);
            }

            DB::commit();

            // Saga: orchestrate distributed steps
            try {
                $saga = new OrderSaga($transaction);
                $saga->addStep(new ReserveStockStep())
                     ->addStep(new ProcessPaymentStep())
                     ->addStep(new CreateShipmentStep())
                     ->addStep(new SendNotificationStep());

                $saga->execute();

                Log::info('[ORDER] Saga completed', [
                    'transaction_id' => $transaction->id,
                    'status'         => $saga->getStatus(),
                ]);
            } catch (\Exception $sagaError) {
                Log::error('[ORDER] Saga failed but order saved', [
                    'transaction_id' => $transaction->id,
                    'error'          => $sagaError->getMessage(),
                ]);
            }

            event(new OrderPlaced($transaction, $cartItems->toArray()));

            Cart::where('user_id', $request->user_id)->delete();

            return response()->json([
                'success'        => true,
                'transaction_id' => $transaction->id,
                'total'          => $total,
                'status'         => 'processing',
                'message'        => 'Order placed via Saga + Event-Driven pattern',
            ], 201)->header('X-Service', 'order-service');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[ORDER] Create failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2,3',
        ]);

        $transaction = Transaction::findOrFail($id);

        $validTransitions = [
            Transaction::STATUS_DEFAULT => [Transaction::STATUS_WAIT],
            Transaction::STATUS_WAIT    => [Transaction::STATUS_DONE, Transaction::STATUS_FAILUE],
        ];

        $allowed = $validTransitions[$transaction->tr_status] ?? [];

        if (!in_array($request->status, $allowed)) {
            return response()->json([
                'success' => false,
                'error'   => "Cannot transition from status {$transaction->tr_status} to {$request->status}",
            ], 422);
        }

        $transaction->tr_status = $request->status;
        $transaction->save();

        Log::info('[ORDER] Status updated', [
            'transaction_id' => $id,
            'new_status'     => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $transaction->fresh(),
            'message' => 'Order status updated',
        ])->header('X-Service', 'order-service');
    }
}
