<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private function buildVnpayQuery(array $inputData): string
    {
        ksort($inputData);

        $pairs = [];

        foreach ($inputData as $key => $value) {
            $pairs[] = urlencode($key) . '=' . urlencode($value);
        }

        return implode('&', $pairs);
    }

    // Tạo URL thanh toán VNPay
    public function createPayment(Request $request, Order $order): View|RedirectResponse
    {
        $vnp_Url = env('VNP_URL');
        $vnp_ReturnUrl = env('VNP_RETURN_URL');
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');

        // Validate cấu hình
        if (empty($vnp_ReturnUrl) || empty($vnp_TmnCode) || empty($vnp_HashSecret)) {
            Log::error('vnpay.config_missing', [
                'vnp_ReturnUrl' => !empty($vnp_ReturnUrl) ? 'set' : 'MISSING',
                'vnp_TmnCode' => !empty($vnp_TmnCode) ? 'set' : 'MISSING',
                'vnp_HashSecret' => !empty($vnp_HashSecret) ? 'set' : 'MISSING',
            ]);
            return redirect()->route('orders.show', $order)->with('error', 'Cấu hình VNPay không hoàn chỉnh.');
        }

        // Cảnh báo nếu VNP_RETURN_URL chứa sandbox hoặc query params
        if (strpos($vnp_ReturnUrl, 'sandbox.vnpayment.vn') !== false || strpos($vnp_ReturnUrl, '?') !== false) {
            Log::warning('vnpay.return_url_invalid', ['vnp_ReturnUrl' => $vnp_ReturnUrl]);
        }

        $vnp_TxnRef = $order->order_code . '-' . Str::upper(Str::random(8));
        $vnp_OrderInfo = "Thanh toan don hang: " . $order->order_code;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = (int) ($order->total_amount * 100);
        $vnp_Locale = "vn";
        $vnp_IpAddr = $request->ip();

        $request->session()->put('vnpay_order_id', $order->id);
        $request->session()->put('vnpay_txn_ref', $vnp_TxnRef);

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_BankCode" => "NCB"
        );

        $query = $this->buildVnpayQuery($inputData);
        $hashdata = $query;

        // Calculate HMAC-SHA512
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        $paymentUrl = $vnp_Url . "?" . $query . '&vnp_SecureHash=' . $vnpSecureHash;
        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&data=' . rawurlencode($paymentUrl);

        // Log for debugging
        Log::info('vnpay.createPayment', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'query' => $query,
            'hashdata' => $hashdata,
            'vnpSecureHash' => $vnpSecureHash,
            'vnp_ReturnUrl_base' => $vnp_ReturnUrl,
        ]);

        return view('payments.vnpay-qr', [
            'order' => $order,
            'paymentUrl' => $paymentUrl,
            'qrImageUrl' => $qrImageUrl,
            'vnpTxnRef' => $vnp_TxnRef,
            'vnpAmount' => $vnp_Amount / 100,
        ]);
    }

    // Xử lý kết quả trả về từ VNPay
    public function vnpayReturn(Request $request)
    {
        $order = null;

        if ($request->filled('vnp_TxnRef')) {
            $txnRef = (string) $request->query('vnp_TxnRef');
            $orderCode = Str::before($txnRef, '-');

            if ($orderCode !== '') {
                $order = Order::where('order_code', $orderCode)->first();
            }
        }

        if (!$order) {
            $orderId = $request->query('order_id') ?? session('vnpay_order_id');
            if (!empty($orderId)) {
                $order = Order::find($orderId);
            }
        }

        if (!$order) {
            Log::warning('vnpay.vnpayReturn.missing_order', ['query' => $request->query()]);
            return redirect()->route('home')->with('error', 'Không tìm thấy mã đơn hàng.');
        }

        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;

        if (empty($vnp_SecureHash)) {
            Log::warning('vnpay.vnpayReturn.missing_hash', ['order_id' => $order->id]);
            return redirect()->route('orders.show', $order)->with('error', 'Chữ ký không được cung cấp.');
        }

        // Loại bỏ các tham số hash để tính toán lại và đối chiếu
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        $query = $this->buildVnpayQuery($inputData);
        $hashData = $query;

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Log for debugging
        Log::info('vnpay.vnpayReturn', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'received_secure_hash' => $vnp_SecureHash,
            'computed_secure_hash' => $secureHash,
            'computed_hashdata' => $hashData,
            'hash_match' => ($secureHash === $vnp_SecureHash),
            'params_count' => count($inputData),
            'vnp_ResponseCode' => $request->vnp_ResponseCode,
        ]);

        if ($secureHash === $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                // Cập nhật trạng thái thanh toán và đơn hàng
                $order->update([
                    'payment_status' => 'paid',
                    'order_status' => 'confirmed',
                ]);

                $payment = Payment::where('order_id', $order->id)->first();
                if ($payment) {
                    $payment->update([
                        'status' => 'completed',
                        'transaction_code' => $request->vnp_TransactionNo,
                        'paid_at' => now(),
                    ]);
                }

                return redirect()->route('orders.show', $order)->with('success', 'Thanh toán thành công! Mã đơn: ' . $order->order_code);
            } else {
                return redirect()->route('orders.show', $order)->with('error', 'Giao dịch thất bại (Code: ' . $request->vnp_ResponseCode . '). Vui lòng thử lại.');
            }
        } else {
            Log::error('vnpay.vnpayReturn.hash_mismatch', [
                'order_id' => $order->id,
                'received' => $vnp_SecureHash,
                'computed' => $secureHash,
            ]);
            return redirect()->route('orders.show', $order)->with('error', 'Chữ ký không hợp lệ! Thanh toán bị từ chối.');
        }
    }

    // Tạo URL thanh toán MoMo
    public function createMomoPayment(Request $request, Order $order): View|RedirectResponse
    {
        $momo_MerchantId = env('MOMO_MERCHANT_ID');
        $momo_AccessKey = env('MOMO_ACCESS_KEY');
        $momo_SecretKey = env('MOMO_SECRET_KEY');
        $momo_ApiUrl = env('MOMO_API_URL');
        $momo_ReturnUrl = env('MOMO_RETURN_URL');
        $momo_IpnUrl = env('MOMO_IPN_URL');

        // Validate cấu hình
        if (empty($momo_MerchantId) || empty($momo_AccessKey) || empty($momo_SecretKey) || empty($momo_ApiUrl)) {
            Log::error('momo.config_missing', [
                'momo_MerchantId' => !empty($momo_MerchantId) ? 'set' : 'MISSING',
                'momo_AccessKey' => !empty($momo_AccessKey) ? 'set' : 'MISSING',
                'momo_SecretKey' => !empty($momo_SecretKey) ? 'set' : 'MISSING',
                'momo_ApiUrl' => !empty($momo_ApiUrl) ? 'set' : 'MISSING',
            ]);
            return redirect()->route('orders.show', $order)->with('error', 'Cấu hình MoMo không hoàn chỉnh.');
        }

        $momo_RequestId = time() . "";
        $momo_OrderInfo = "Thanh toan don hang: " . $order->order_code;
        $momo_Amount = (int) $order->total_amount; // MoMo sử dụng VND thông thường (10,000 - 50,000,000 VND)
        $momo_OrderId = $order->order_code;
        $momo_RequestType = "payWithATM";
        $momo_ExtraData = base64_encode(json_encode([
            'order_id' => $order->id,
            'order_code' => $order->order_code,
        ]));

        $request->session()->put('momo_order_id', $order->id);
        $request->session()->put('momo_request_id', $momo_RequestId);

        // Build raw signature string theo đúng thứ tự (QUAN TRỌNG!)
        // Thứ tự: accessKey, amount, extraData, ipnUrl, orderId, orderInfo, partnerCode, redirectUrl, requestId, requestType
        $rawSignature = "accessKey=" . $momo_AccessKey .
            "&amount=" . $momo_Amount .
            "&extraData=" . $momo_ExtraData .
            "&ipnUrl=" . $momo_IpnUrl .
            "&orderId=" . $momo_OrderId .
            "&orderInfo=" . $momo_OrderInfo .
            "&partnerCode=" . $momo_MerchantId .
            "&redirectUrl=" . $momo_ReturnUrl .
            "&requestId=" . $momo_RequestId .
            "&requestType=" . $momo_RequestType;

        $signature = hash_hmac('sha256', $rawSignature, $momo_SecretKey);

        // Dữ liệu request theo spec của MoMo
        $requestData = [
            'partnerCode' => $momo_MerchantId,
            'partnerName' => 'CatBook Store',
            'storeId' => 'CatBookStore',
            'requestId' => $momo_RequestId,
            'amount' => $momo_Amount,
            'orderId' => $momo_OrderId,
            'orderInfo' => $momo_OrderInfo,
            'redirectUrl' => $momo_ReturnUrl,
            'ipnUrl' => $momo_IpnUrl,
            'lang' => 'vi',
            'extraData' => $momo_ExtraData,
            'requestType' => $momo_RequestType,
            'signature' => $signature
        ];

        // Log for debugging
        Log::info('momo.createPayment', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'momo_Amount' => $momo_Amount,
            'rawSignature' => $rawSignature,
            'signature' => $signature,
            'requestData' => $requestData,
        ]);

        // Call MoMo API
        try {
            $response = Http::timeout(10)->post($momo_ApiUrl, $requestData);
            $responseData = $response->json();

            Log::info('momo.apiResponse', [
                'order_id' => $order->id,
                'status_code' => $response->status(),
                'response' => $responseData,
            ]);

            // MoMo trả về resultCode = 0 khi thành công
            if ($responseData['resultCode'] == 0 && isset($responseData['payUrl'])) {
                $payUrl = $responseData['payUrl'];
                $deeplink = $responseData['deeplink']
                    ?? $responseData['deeplinkWebInApp']
                    ?? $responseData['qrCodeUrl']
                    ?? $payUrl;
                $fallbackUrl = $payUrl;
                $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&data=' . rawurlencode($deeplink);

                return view('payments.momo', [
                    'order' => $order,
                    'amount' => $momo_Amount,
                    'payUrl' => $payUrl,
                    'deeplink' => $deeplink,
                    'fallbackUrl' => $fallbackUrl,
                    'qrImageUrl' => $qrImageUrl,
                    'expiresAt' => now()->addMinutes(15),
                ]);
            } else {
                $errorMessage = $responseData['message'] ?? 'Lỗi từ MoMo. Vui lòng thử lại.';
                Log::error('momo.apiError', [
                    'order_id' => $order->id,
                    'resultCode' => $responseData['resultCode'] ?? null,
                    'message' => $errorMessage,
                ]);
                return redirect()->route('orders.show', $order)->with('error', 'MoMo: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('momo.requestError', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('orders.show', $order)->with('error', 'Lỗi kết nối MoMo. Vui lòng thử lại.');
        }
    }

    // Trang trung gian cho chuyển khoản ngân hàng
    public function createTransferPayment(Order $order): View|RedirectResponse
    {

        return view('payments.transfer', [
            'order' => $order,
            'amount' => (float) $order->total_amount,
            'accountName' => 'PHAM VAN DANG',
            'accountNumber' => '106875074961',
            'bankName' => 'VietinBank - CN HOAN KIEM - HOI SO',
            'qrImageUrl' => asset('images/QRCode.png'),
            'transferContent' => $order->order_code,
            'expiresAt' => now()->addMinutes(15),
        ]);
    }

    // Xử lý kết quả trả về từ MoMo
    public function momoReturn(Request $request)
    {
        $orderId = $request->query('orderId');
        $resultCode = $request->query('resultCode', '-1');
        
        Log::info('momo.momoReturn.received', [
            'orderId' => $orderId,
            'resultCode' => $resultCode,
            'all_params' => $request->query(),
        ]);

        $order = null;

        if (!empty($orderId)) {
            $order = Order::where('order_code', $orderId)->first();
        }

        if (!$order) {
            $orderId = $request->query('order_id') ?? session('momo_order_id');
            if (!empty($orderId)) {
                $order = Order::find($orderId);
            }
        }

        if (!$order) {
            Log::warning('momo.momoReturn.missing_order', ['query' => $request->query()]);
            return redirect()->route('home')->with('error', 'Không tìm thấy mã đơn hàng.');
        }

        // Log full return
        Log::info('momo.momoReturn.found_order', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'resultCode' => $resultCode,
        ]);

        // resultCode = 0: thành công, các code khác: thất bại
        if ($resultCode == 0) {
            // Payment successful
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'confirmed',
            ]);

            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'transaction_code' => $request->query('transId', ''),
                    'paid_at' => now(),
                ]);
            }

            Log::info('momo.momoReturn.success', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'transId' => $request->query('transId'),
            ]);

            return redirect()->route('orders.show', $order)->with('success', 'Thanh toán MoMo thành công! Mã đơn: ' . $order->order_code);
        } else {
            $errorMessages = [
                '1' => 'Giao dịch bị từ chối',
                '2' => 'Số tiền không hợp lệ',
                '3' => 'Timeout',
                '9999' => 'Lỗi không xác định',
            ];
            $errorMessage = $errorMessages[$resultCode] ?? 'Thanh toán MoMo thất bại (Code: ' . $resultCode . ')';
            
            Log::warning('momo.momoReturn.failed', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'resultCode' => $resultCode,
                'error' => $errorMessage,
            ]);

            return redirect()->route('orders.show', $order)->with('error', $errorMessage);
        }
    }

    // Webhook từ MoMo (IPN - Instant Payment Notification)
    public function momoWebhook(Request $request)
    {
        $orderId = $request->input('orderId');
        $resultCode = $request->input('resultCode', '-1');
        $transId = $request->input('transId', '');

        Log::info('momo.webhook.received', [
            'orderId' => $orderId,
            'resultCode' => $resultCode,
            'transId' => $transId,
            'all_data' => $request->all(),
        ]);

        $order = Order::where('order_code', $orderId)->first();

        if (!$order) {
            Log::warning('momo.webhook.order_not_found', ['orderId' => $orderId]);
            return response()->json(['status' => 404, 'message' => 'Order not found']);
        }

        // resultCode = 0: thành công
        if ($resultCode == 0) {
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'confirmed',
            ]);

            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'transaction_code' => $transId,
                    'paid_at' => now(),
                ]);
            }

            Log::info('momo.webhook.success', [
                'order_id' => $order->id,
                'orderId' => $orderId,
                'transId' => $transId,
            ]);

            return response()->json(['status' => 200, 'message' => 'Webhook processed successfully']);
        } else {
            Log::warning('momo.webhook.failed', [
                'order_id' => $order->id,
                'orderId' => $orderId,
                'resultCode' => $resultCode,
            ]);

            return response()->json(['status' => 200, 'message' => 'Payment failed with result code ' . $resultCode]);
        }
    }
}