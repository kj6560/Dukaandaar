<?php

namespace App\Http\Controllers;

use App\Models\CronTable;
use App\Models\SubsFeature;
use App\Models\Transaction;
use App\Models\TransactionOrder;
use App\Models\TransactionPayment;
use App\Models\User;
use App\Models\UserFeaturePurchase;
use Illuminate\Broadcasting\Broadcasters\UsePusherChannelConventions;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RazorPayController extends Controller
{
    public function paymentWebhook(Request $request)
    {

        try {
            $count = 0;
            $data = $request->all();
            $cronTable = new CronTable();
            $cronTable->cron_time = now();
            $cronTable->msg = json_encode($data, true);
            $cronTable->save();
            $event = $data['event'];
            $account_id = $data['account_id'];
            $payload = $data['payload'];
            $payment = $payload['payment']['entity'];
            $order = $payload['order']['entity'];
            if (!empty($payment)) {
                $transactionPayment = new TransactionPayment();

                $transactionPayment->id = $payment['id'];
                $transactionPayment->order_id = $payment['order_id'] ?? null;
                $transactionPayment->amount = $payment['amount'];
                $transactionPayment->currency = $payment['currency'] ?? null;
                $transactionPayment->status = $payment['status'] ?? null;
                $transactionPayment->method = $payment['method'] ?? null;
                $transactionPayment->captured = $payment['captured'] ?? null;
                $transactionPayment->amount_refunded = $payment['amount_refunded'] ?? 0;
                $transactionPayment->refund_status = $payment['refund_status'] ?? null;
                $transactionPayment->description = $payment['description'] ?? null;
                $transactionPayment->vpa = $payment['vpa'] ?? ($payment['upi']['vpa'] ?? null);
                $transactionPayment->email = $payment['email'] ?? null;
                $user = User::where('email',$payment['email']);
                if(!empty($payment['contact'])){
                    $user = $user->where('number',str_replace("+91","",($payment['contact'])));
                }
                $user = $user->first();
                $transactionPayment->contact = $payment['contact'] ?? null;
                $transactionPayment->fee = $payment['fee'] ?? null;
                $transactionPayment->tax = $payment['tax'] ?? null;
                $transactionPayment->rrn = $payment['acquirer_data']['rrn'] ?? null;
                $transactionPayment->upi_transaction_id = $payment['id'];
                $transactionPayment->created_at = isset($payment['created_at']) ? \Carbon\Carbon::createFromTimestamp($payment['created_at']) : now();
                $transactionPayment->updated_at = now();

                if ($transactionPayment->save()) {
                    $count++;
                }
            }

            if (!empty($order)) {
                $transactionOrder = TransactionOrder::find($order['id']);

                if (!$transactionOrder) {
                    $transactionOrder = new TransactionOrder();
                    $transactionOrder->id = $order['id'];
                }

                $transactionOrder->entity = $order['entity'] ?? null;
                $transactionOrder->amount = $order['amount'];
                $transactionOrder->amount_paid = $order['amount_paid'] ?? null;
                $transactionOrder->amount_due = $order['amount_due'] ?? null;
                $transactionOrder->currency = $order['currency'] ?? null;
                $transactionOrder->receipt = $order['receipt'] ?? null;
                $transactionOrder->offer_id = $order['offer_id'] ?? null;
                $transactionOrder->status = $order['status'] ?? null;
                $transactionOrder->attempts = $order['attempts'] ?? 0;
                $transactionOrder->notes = json_encode($order['notes'] ?? []);
                $transactionOrder->created_at_unix = $order['created_at'] ?? null;
                $transactionOrder->created_at = isset($order['created_at']) ? \Carbon\Carbon::createFromTimestamp($order['created_at']) : now();
                $transactionOrder->updated_at = now();

                if ($transactionOrder->save()) {
                    $count++;
                }
            }

            if ($count == 2) {
                $transaction = Transaction::where('transaction_orders_id', $order['id'])->first();
                if (empty($transaction)) {
                    $transaction = new Transaction();
                }
                $transaction->event = $event;
                $transaction->account_id = $account_id;
                $transaction->transaction_orders_id = $order['id'];
                $transaction->transaction_payments_id = $payment['id'];
                $transaction->user_id = $user->id ?? 0;

                if ($transaction->save()) {
                    $count++;
                }
            }

            if ($count == 3) {
                return response()->json([
                    'message' => "Payment saved successfuly",
                    'error' => false,
                    'data' => []
                ], 200);
            } else {
                
                return response()->json([
                    'message' => "Payment saved successfuly",
                    'error' => false,
                    'data' => []
                ], 200);
            }

        } catch (\Exception $e) {
            $data = $request->all();
                $cronTable = new CronTable();
                $cronTable->cron_time = now();
                $cronTable->msg = json_encode($e->getMessage(), true);
                $cronTable->save();
            return response()->json([
                'message' => "Exception in saving payment",
                'error' => $e->getMessage(),
                'data' => []
            ], 200);
        }
    }
    public function createUserFeaturePurchaseOrder(Request $request)
    {
        $amount = $request->amount;
        $user_id = $request->user_id;
        $user = User::where('id', $user_id)->first();
        $featurePurchase_id = $request->feature_purchase_id;
        if (empty($featurePurchase_id)) {
            return response()->json([
                'message' => "Feature Purchase ID is required",
                'error' => false,
                'data' => []
            ], 400);
        }
        $featurePurchase = UserFeaturePurchase::where('id', $featurePurchase_id)->where('user_id', $user_id)->first();
        if (empty($featurePurchase->id)) {
            return response()->json([
                'message' => "Feature Purchase ID not found",
                'error' => false,
                'data' => []
            ], 400);
        }

        if (!$user_id) {
            return response()->json([
                'message' => "User ID is required",
                'error' => false,
                'data' => []
            ], 400);
        }
        if (empty($user->id)) {
            return response()->json([
                'message' => "User not found",
                'error' => false,
                'data' => []
            ], 400);
        }
        if ($amount <= 0) {
            return response()->json([
                'message' => 'Amount must be greater than zero',
                'error' => false,
                'data' => []
            ], 400);
        }
        if (!is_numeric($amount)) {
            return response()->json([
                'message' => 'Amount must be valid numeric value',
                'error' => false,
                'data' => []
            ], 400);
        }
        if ($amount > 100000) {
            return response()->json([
                'message' => 'Amount must be greater than zero',
                'error' => false,
                'data' => []
            ], 400);
        }
        if ($amount < 1) {
            return response()->json([
                'message' => 'Amount must be greater than zero',
                'error' => false,
                'data' => []
            ], 400);
        }
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $payload = [
            'receipt' => 'rcpt_' . uniqid(),
            'amount' => intval($amount), // Amount in paise
            'currency' => 'INR',
        ];
        try {
            $order = $api->order->create($payload);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => true,
                'data' => []
            ], 400);
        }
        if (!empty($order['id'])) {
            $transaction = new Transaction();
            $transaction->transaction_orders_id = $order['id'];
            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->ref_id = $featurePurchase_id;
            $transaction->transaction_type = 1;
            if ($transaction->save()) {
                $data = [
                    "amount" => $order['amount'],
                    "amount_due" => $order['amount_due'],
                    "amount_paid" => $order['amount_paid'],
                    "attempts" => $order['attempts'],
                    "created_at" => $order['created_at'],
                    "currency" => $order['currency'],
                    "entity" => $order['entity'],
                    "order_id" => $order['id'],
                    "offer_id" => $order['offer_id'],
                    "receipt" => $order['receipt'],
                    "status" => $order['status'],
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'user_id' => $user->id,
                    'contact' => $user->number,
                ];
                return response()->json([
                    'message' => 'Order created successfully',
                    'error' => false,
                    'data' => $data
                ], 200);
            }
        }
    }
    public function verifyPayment(Request $request)
    {
        $razorpayPaymentId = $request->payment_id;
        if (empty($razorpayPaymentId)) {
            return response()->json([
                'message' => 'Razorpay Payment ID are required',
                'error' => true,
                'data' => []
            ], 400);
        }
        $transactionPayment = TransactionPayment::where('id', $razorpayPaymentId)->first();
        $razorpayOrderId = $transactionPayment->order_id ?? null;
        if (!$razorpayOrderId) {
            return response()->json([
                'message' => 'Razorpay Order ID not found',
                'error' => true,
                'data' => []
            ], 400);
        }
        $razorpaySignature = $request->signature;

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        try {
            $api->utility->verifyPaymentSignature(array('razorpay_order_id' => $razorpayOrderId, 'razorpay_payment_id' => $razorpayPaymentId, 'razorpay_signature' => $razorpaySignature));
            $payment = $api->payment->fetch($razorpayPaymentId);
            if ($payment->status == 'captured') {
                $invoice = $this->generateInvoice($request->request_id);
                $transaction = Transaction::where('transaction_orders_id', $razorpayOrderId)->first();
                $transaction->transaction_payments_id = $razorpayPaymentId;
                $transaction->invoice_url = $invoice['url'];
                if ($transaction->save()) {
                    $featurePurchase = UserFeaturePurchase::where('id', $transaction->ref_id)->first();
                    if (!empty($featurePurchase->id)) {
                        $featurePurchase->is_active = 1;
                        $featurePurchase->save();
                    }
                    return response()->json([
                        'message' => 'Payment received successfully',
                        'error' => false,
                        'data' => []
                    ], 200);
                }

            } else {
                return response()->json([
                    'message' => 'Payment not captured',
                    'error' => true,
                    'data' => []
                ], 400);
            }
        } catch (\Exception $e) {
            $cron = new CronTable();
            $cron->cron_time = now();
            $cron->msg = $e->getMessage();
            $cron->save();
            return response()->json([
                'message' => $e->getMessage(),
                'error' => true,
                'data' => []
            ], 400);
        }
    }
    public function generateInvoice($request_id)
    {

        if (empty($request_id)) {
            $error[] = 'Request id cannot be empty';
            return response()->json([
                'message' => 'Request id cannot be empty',
                'error' => true,
                'data' => []
            ], 400);
        }
        $featurePurchase = UserFeaturePurchase::where('id', $request_id)->first();

        $mentorRequestPlan = SubsFeature::where('mentor_request_type', '=', getMentorRequestType($featurePurchase->request_type))
                            ->where('mentor_id', $featurePurchase->mentor_id)->first();

        if (empty($mentorRequest->id)) {
            return response()->json([
                'message' => 'Mentor Request Not Found',
                'error' => true,
                'data' => []
            ], 400);
        }
        $transaction_types = TransactionTypes::where('title', 'Mentor Request')->first();
        $transaction = Transaction::
            select([
                'transaction.created_at as invoice_date',
                DB::raw("CONCAT_WS(' ', users.first_name, users.last_name) as to_name"),
                'users.email as to_email',
                'users.number as to_number',
                'transaction.amount',
                'transaction.user_id',
                'transaction.transaction_orders_id'
            ])

            ->join('users', 'users.id', 'transaction.user_id')
            ->where('transaction.request_id', $mentorRequest->id)
            ->where('transaction.user_id', $mentorRequest->user_id)
            ->where('transaction.transaction_type', '=', $transaction_types->id)
            ->first();

        $transactionPayments = TransactionPayment::where('id', $transaction->transaction_payments_id)->first();

        $settings = $this->getSettings();
        $invoiceData = [];
        $invoiceData['invoice_id'] = $transaction->transaction_payments_id;
        $invoiceData['invoice_date'] = $transaction->invoice_date;
        $invoiceData['status'] = "Paid";
        $invoiceData['biller_email'] = $settings['site_email'];
        $invoiceData['biller_number'] = $settings['site_number'];
        $invoiceData['biller_company_address'] = $settings['site_address'];
        $invoiceData['biller_gst'] = $settings['company_gst'];
        $invoiceData['biller_pan'] = $settings['company_pan'];
        $invoiceData['biller'] = $settings['company_name'];

        $invoiceData['to_name'] = $transaction->to_name;
        $invoiceData['to_number'] = $transaction->to_number;
        $invoiceData['to_email'] = $transaction->to_email;
        $invoiceData['to_address'] = "";
        $invoiceData['qty'] = 1;
        $allItems = [];
        $items = [
            "name" => "Mentor Request Service",
            'qty' => 1,
            'unit_price' => $mentorRequestPlan->plan_price,
            'item_total' => $mentorRequestPlan->plan_price
        ];
        array_push($allItems, $items);
        $invoiceData['items'] = $allItems;
        $invoiceData['sub_total'] = $mentorRequestPlan->plan_price;
        $invoiceData['gst_percentage'] = $mentorRequestPlan->plan_gst;
        $invoiceData['gst_amount'] = ($mentorRequestPlan->plan_price * $mentorRequestPlan->plan_gst) / 100;
        $invoiceData['total'] = $transaction->amount/100;
        $pdf = Pdf::loadView('Invoice.invoice_template_1', ['invoiceData' => $invoiceData]);
        $fileName = 'invoices/invoice_' . $transaction->transaction_orders_id . '.pdf';

        Storage::disk('public')->put($fileName, $pdf->output());
        $pdfUrl = asset('storage/' . $fileName);

        $localPath = storage_path('app/public/' . $fileName);

        $uploadedFile = new UploadedFile(
            $localPath,
            basename($fileName),
            'application/pdf',
            null,
            true
        );

        $response = $this->uploadToNewMediaServer($uploadedFile, 'mentor_request_invoices', $transaction->user_id);
        if (!empty($response->data->fileName)) {
            // unlink($pdfUrl);
            return [
                'message' => 'Invoice fetch Successful',
                'error' => false,
                "url" => "https://files.univsports.in/storage/uploads/mentor_request_invoices/" . $response->data->fileName
            ];
        } else {
            return false;
        }
    }
}
