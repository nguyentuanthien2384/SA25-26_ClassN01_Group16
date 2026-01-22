<?php

namespace App\Http\Controllers;

use App\Models\Models\Cart;
use App\Models\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function show(Request $request, $method, Transaction $transaction)
    {
        if ((int) $transaction->tr_user_id !== (int) get_data_user('web')) {
            abort(403);
        }

        $method = strtolower($method);
        $allowed = ['momo', 'qrcode', 'paypal', 'vnpay'];
        if (!in_array($method, $allowed, true)) {
            abort(404);
        }

        $qrData = null;
        $qrUrl = null;
        $qrBank = null;
        $qrAccount = null;
        $qrName = null;
        if ($method === 'qrcode') {
            $vietqr = config('services.vietqr');
            $qrBank = $vietqr['bank'] ?? null;
            $qrAccount = $vietqr['account'] ?? null;
            $qrName = $vietqr['name'] ?? null;
            $template = $vietqr['template'] ?? 'compact2';
            $amount = (int) $transaction->tr_total;
            $qrData = 'Thanh toan don hang #' . $transaction->id;

            if ($qrBank && $qrAccount && $qrName) {
                $qrUrl = 'https://img.vietqr.io/image/' . urlencode($qrBank) . '-' . urlencode($qrAccount) . '-' . urlencode($template)
                    . '.png?amount=' . $amount
                    . '&addInfo=' . urlencode($qrData)
                    . '&accountName=' . urlencode($qrName);
            } else {
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($qrData . ' | ' . $transaction->tr_total . ' VND');
            }
        }

        return view('payment.show', [
            'method' => $method,
            'transaction' => $transaction,
            'qrData' => $qrData,
            'qrUrl' => $qrUrl,
            'qrBank' => $qrBank,
            'qrAccount' => $qrAccount,
            'qrName' => $qrName,
        ]);
    }

    public function init(Request $request, $method, Transaction $transaction)
    {
        if ((int) $transaction->tr_user_id !== (int) get_data_user('web')) {
            abort(403);
        }

        $method = strtolower($method);
        $allowed = ['momo', 'qrcode', 'paypal', 'vnpay'];
        if (!in_array($method, $allowed, true)) {
            abort(404);
        }

        if ($method === 'qrcode') {
            return redirect()->route('payment.show', [
                'method' => $method,
                'transaction' => $transaction->id,
            ]);
        }

        if ($method === 'momo') {
            return $this->initMomo($transaction);
        }

        if ($method === 'paypal') {
            return $this->initPaypal($transaction);
        }

        if ($method === 'vnpay') {
            return $this->initVnpay($request, $transaction);
        }

        abort(404);
    }

    public function momoReturn(Request $request, Transaction $transaction)
    {
        if ((int) $transaction->tr_user_id !== (int) get_data_user('web')) {
            abort(403);
        }

        $resultCode = (int) $request->get('resultCode', 99);
        if ($resultCode === 0) {
            $this->markPaid($transaction, 'momo', $request->all());
            return redirect()->route('home')->with('success', 'Thanh toán MoMo thành công.');
        }

        $transaction->tr_status = Transaction::STATUS_FAILUE;
        $transaction->tr_payment_status = 2;
        $transaction->updated_at = Carbon::now();
        $transaction->save();

        return redirect()->route('home')->with('danger', 'Thanh toán MoMo thất bại.');
    }

    public function momoIpn(Request $request, Transaction $transaction)
    {
        $resultCode = (int) $request->get('resultCode', 99);
        if ($resultCode === 0) {
            $this->markPaid($transaction, 'momo', $request->all());
        }

        return response()->json(['received' => true]);
    }

    public function paypalReturn(Request $request, Transaction $transaction)
    {
        if ((int) $transaction->tr_user_id !== (int) get_data_user('web')) {
            abort(403);
        }

        $orderId = $request->get('token');
        if (!$orderId) {
            return redirect()->route('home')->with('danger', 'Thiếu mã đơn PayPal.');
        }

        $capture = $this->capturePaypalOrder($orderId);
        if ($capture && isset($capture['status']) && $capture['status'] === 'COMPLETED') {
            $this->markPaid($transaction, 'paypal', $capture);
            return redirect()->route('home')->with('success', 'Thanh toán PayPal thành công.');
        }

        $transaction->tr_status = Transaction::STATUS_FAILUE;
        $transaction->tr_payment_status = 2;
        $transaction->updated_at = Carbon::now();
        $transaction->save();

        return redirect()->route('home')->with('danger', 'Thanh toán PayPal thất bại.');
    }

    public function paypalCancel(Transaction $transaction)
    {
        if ((int) $transaction->tr_user_id !== (int) get_data_user('web')) {
            abort(403);
        }

        return redirect()->route('home')->with('warning', 'Bạn đã hủy thanh toán PayPal.');
    }

    public function vnpayReturn(Request $request, Transaction $transaction)
    {
        if ((int) $transaction->tr_user_id !== (int) get_data_user('web')) {
            abort(403);
        }

        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = [];
        foreach ($inputData as $key => $value) {
            $hashData[] = $key . '=' . $value;
        }
        $hashDataString = implode('&', $hashData);

        $config = config('services.vnpay');
        $secureHash = hash_hmac('sha512', $hashDataString, $config['hash_secret'] ?? '');

        if ($secureHash !== $vnp_SecureHash) {
            return redirect()->route('home')->with('danger', 'Sai chữ ký VNPay.');
        }

        if (($inputData['vnp_ResponseCode'] ?? '') === '00') {
            $this->markPaid($transaction, 'vnpay', $inputData);
            return redirect()->route('home')->with('success', 'Thanh toán VNPay thành công.');
        }

        $transaction->tr_status = Transaction::STATUS_FAILUE;
        $transaction->tr_payment_status = 2;
        $transaction->updated_at = Carbon::now();
        $transaction->save();

        return redirect()->route('home')->with('danger', 'Thanh toán VNPay thất bại.');
    }

    public function vnpayIpn(Request $request, Transaction $transaction)
    {
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = [];
        foreach ($inputData as $key => $value) {
            $hashData[] = $key . '=' . $value;
        }
        $hashDataString = implode('&', $hashData);

        $config = config('services.vnpay');
        $secureHash = hash_hmac('sha512', $hashDataString, $config['hash_secret'] ?? '');

        if ($secureHash !== $vnp_SecureHash) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        if (($inputData['vnp_ResponseCode'] ?? '') === '00') {
            $this->markPaid($transaction, 'vnpay', $inputData);
            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        }

        $transaction->tr_status = Transaction::STATUS_FAILUE;
        $transaction->tr_payment_status = 2;
        $transaction->updated_at = Carbon::now();
        $transaction->save();

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }

    public function qrcodeConfirm(Transaction $transaction)
    {
        if ((int) $transaction->tr_user_id !== (int) get_data_user('web')) {
            abort(403);
        }

        $this->markPaid($transaction, 'qrcode', []);
        return redirect()->route('home')->with('success', 'Đã ghi nhận thanh toán QR Code.');
    }

    private function initVnpay(Request $request, Transaction $transaction)
    {
        $config = config('services.vnpay');
        if (empty($config['tmn_code']) || empty($config['hash_secret'])) {
            return redirect()->route('payment.show', ['method' => 'vnpay', 'transaction' => $transaction->id])
                ->with('danger', 'Chưa cấu hình VNPay sandbox.');
        }

        $vnp_TxnRef = (string) $transaction->id;
        $vnp_OrderInfo = 'Thanh toan don hang #' . $transaction->id;
        $vnp_Amount = (int) $transaction->tr_total * 100;
        $vnp_IpAddr = $request->ip();
        $vnp_CreateDate = Carbon::now()->format('YmdHis');
        $vnp_Returnurl = $config['return_url'] ?: route('payment.vnpay.return', ['transaction' => $transaction->id]);
        $vnp_IpnUrl = $config['ipn_url'] ?: route('payment.vnpay.ipn', ['transaction' => $transaction->id]);

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $config['tmn_code'],
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $vnp_CreateDate,
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_IpnUrl' => $vnp_IpnUrl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        ksort($inputData);
        $hashData = [];
        foreach ($inputData as $key => $value) {
            $hashData[] = $key . '=' . $value;
        }
        $hashDataString = implode('&', $hashData);
        $vnp_SecureHash = hash_hmac('sha512', $hashDataString, $config['hash_secret']);

        $query = [];
        foreach ($inputData as $key => $value) {
            $query[] = urlencode($key) . '=' . urlencode($value);
        }
        $queryString = implode('&', $query) . '&vnp_SecureHash=' . $vnp_SecureHash;

        $transaction->tr_payment_method = 'vnpay';
        $transaction->tr_payment_status = 0;
        $transaction->tr_status = Transaction::STATUS_WAIT;
        $transaction->tr_payment_code = $vnp_TxnRef;
        $transaction->tr_payment_meta = json_encode($inputData);
        $transaction->updated_at = Carbon::now();
        $transaction->save();

        return redirect()->away($config['url'] . '?' . $queryString);
    }

    private function initMomo(Transaction $transaction)
    {
        $config = config('services.momo');
        if (empty($config['partner_code']) || empty($config['access_key']) || empty($config['secret_key'])) {
            return redirect()->route('payment.show', ['method' => 'momo', 'transaction' => $transaction->id])
                ->with('danger', 'Chưa cấu hình MoMo sandbox.');
        }

        $orderId = (string) $transaction->id;
        $requestId = (string) $transaction->id;
        $amount = (string) $transaction->tr_total;
        $orderInfo = 'Thanh toan don hang #' . $transaction->id;
        $extraData = '';

        $redirectUrl = $config['redirect_url'] ?: route('payment.momo.return', ['transaction' => $transaction->id]);
        $ipnUrl = $config['ipn_url'] ?: route('payment.momo.ipn', ['transaction' => $transaction->id]);

        $rawHash = "accessKey={$config['access_key']}&amount={$amount}&extraData={$extraData}&ipnUrl={$ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$config['partner_code']}&redirectUrl={$redirectUrl}&requestId={$requestId}&requestType=captureWallet";
        $signature = hash_hmac('sha256', $rawHash, $config['secret_key']);

        $payload = [
            'partnerCode' => $config['partner_code'],
            'partnerName' => 'MoMo',
            'storeId' => 'WebBanDoDienTu',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => 'captureWallet',
            'signature' => $signature,
        ];

        $response = Http::post($config['endpoint'], $payload);
        if (!$response->ok()) {
            Log::error('MoMo init failed', ['body' => $response->body()]);
            return redirect()->route('payment.show', ['method' => 'momo', 'transaction' => $transaction->id])
                ->with('danger', 'Không khởi tạo được thanh toán MoMo.');
        }

        $data = $response->json();
        if (!empty($data['payUrl'])) {
            $transaction->tr_payment_method = 'momo';
            $transaction->tr_payment_status = 0;
            $transaction->tr_status = Transaction::STATUS_WAIT;
            $transaction->tr_payment_code = $orderId;
            $transaction->tr_payment_meta = json_encode($data);
            $transaction->updated_at = Carbon::now();
            $transaction->save();

            return redirect()->away($data['payUrl']);
        }

        return redirect()->route('payment.show', ['method' => 'momo', 'transaction' => $transaction->id])
            ->with('danger', 'MoMo không trả về payUrl.');
    }

    private function initPaypal(Transaction $transaction)
    {
        $config = config('services.paypal');
        if (empty($config['client_id']) || empty($config['client_secret'])) {
            return redirect()->route('payment.show', ['method' => 'paypal', 'transaction' => $transaction->id])
                ->with('danger', 'Chưa cấu hình PayPal sandbox.');
        }

        $returnUrl = $config['return_url'] ?: route('payment.paypal.return', ['transaction' => $transaction->id]);
        $cancelUrl = $config['cancel_url'] ?: route('payment.paypal.cancel', ['transaction' => $transaction->id]);

        $tokenResponse = Http::asForm()
            ->withBasicAuth($config['client_id'], $config['client_secret'])
            ->post($config['endpoint'] . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (!$tokenResponse->ok()) {
            Log::error('PayPal token failed', ['body' => $tokenResponse->body()]);
            return redirect()->route('payment.show', ['method' => 'paypal', 'transaction' => $transaction->id])
                ->with('danger', 'Không lấy được token PayPal.');
        }

        $accessToken = $tokenResponse->json()['access_token'] ?? null;
        if (!$accessToken) {
            return redirect()->route('payment.show', ['method' => 'paypal', 'transaction' => $transaction->id])
                ->with('danger', 'Token PayPal không hợp lệ.');
        }

        $orderResponse = Http::withToken($accessToken)
            ->post($config['endpoint'] . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => 'TXN-' . $transaction->id,
                        'description' => 'Thanh toan don hang #' . $transaction->id,
                        'custom_id' => (string) $transaction->id,
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($transaction->tr_total / 24000, 2, '.', ''),
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                ],
            ]);

        if (!$orderResponse->ok()) {
            Log::error('PayPal order failed', ['body' => $orderResponse->body()]);
            return redirect()->route('payment.show', ['method' => 'paypal', 'transaction' => $transaction->id])
                ->with('danger', 'Không tạo được đơn PayPal.');
        }

        $orderData = $orderResponse->json();
        $approveUrl = null;
        if (!empty($orderData['links'])) {
            foreach ($orderData['links'] as $link) {
                if (($link['rel'] ?? '') === 'approve') {
                    $approveUrl = $link['href'];
                    break;
                }
            }
        }

        if ($approveUrl) {
            $transaction->tr_payment_method = 'paypal';
            $transaction->tr_payment_status = 0;
            $transaction->tr_status = Transaction::STATUS_WAIT;
            $transaction->tr_payment_code = $orderData['id'] ?? null;
            $transaction->tr_payment_meta = json_encode($orderData);
            $transaction->updated_at = Carbon::now();
            $transaction->save();

            return redirect()->away($approveUrl);
        }

        return redirect()->route('payment.show', ['method' => 'paypal', 'transaction' => $transaction->id])
            ->with('danger', 'PayPal không trả về link duyệt.');
    }

    private function capturePaypalOrder(string $orderId): ?array
    {
        $config = config('services.paypal');
        $tokenResponse = Http::asForm()
            ->withBasicAuth($config['client_id'], $config['client_secret'])
            ->post($config['endpoint'] . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (!$tokenResponse->ok()) {
            Log::error('PayPal token failed', ['body' => $tokenResponse->body()]);
            return null;
        }

        $accessToken = $tokenResponse->json()['access_token'] ?? null;
        if (!$accessToken) {
            return null;
        }

        $captureResponse = Http::withToken($accessToken)
            ->post($config['endpoint'] . '/v2/checkout/orders/' . $orderId . '/capture');

        if (!$captureResponse->ok()) {
            Log::error('PayPal capture failed', ['body' => $captureResponse->body()]);
            return null;
        }

        return $captureResponse->json();
    }

    private function markPaid(Transaction $transaction, string $method, array $meta = []): void
    {
        $transaction->tr_payment_method = $method;
        $transaction->tr_payment_status = 1;
        $transaction->tr_status = Transaction::STATUS_DONE;
        $transaction->tr_payment_meta = $meta ? json_encode($meta) : $transaction->tr_payment_meta;
        $transaction->updated_at = Carbon::now();
        $transaction->save();

        Cart::where('user_id', $transaction->tr_user_id)->delete();
    }
}
