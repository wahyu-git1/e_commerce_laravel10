<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Shop\Entities\Order;
use Modules\Shop\Entities\Payment;

class PaymentController extends Controller
{
   public function midtrans(Request $request)
   {
     // Inisialisasi Midtrans Config
      $payload= $request->getContent();
    //   error_log($payload);
      $notification=json_decode($payload);
    // dd($payload);
    //   dd($notification);
      if ((bool)env('MIDTRANS_PRODUCTION', false)) {
        $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . env('MIDTRANS_SERVER_KEY'));
        if ($notification->signature_key != $validSignatureKey) {
            return response(['code' => 403, 'message' => 'Invalid Signature Key'], 403);
             }
        }
    // $this->initPaymentGateway();

    // $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . env('MIDTRANS_SERVER_KEY'));

    // dd($validSignatureKey);
      // dd($validSignatureKey);
    //   if ($notification->signature_key !=$validSignatureKey){
    //     return response(['code' =>403, 'message' => 'Invalid Signature Key'], 403);
    //   }
      $this->initPaymentGateway(); //memastikan konfigurasi Midtrans sudah siap.
      $paymentNotitification = new \Midtrans\Notification(); // membaca notifikasi yang dikirim oleh Midtrans.
      $transaction = $paymentNotitification->transaction_status;
    //   dd($transaction);

      $order= Order::where('id', $paymentNotitification->order_id)->first();
      if (!$order){
        return response(['code' => '404','message'=> 'Order not found', ]);
      }

      if ($order->status == Order::STATUS_CONFIRMED) {
        return response(['code' => '403', 'message' => 'Order already paid'], 403);
        }


      $type = $paymentNotitification->payment_type;
      $order_id = $paymentNotitification->order_id;
      $fraud = $paymentNotitification->fraud_status;
    //   dd($order_id);
      $paymentSuccess =false;

 
      error_log($payload);
      
      error_log("Order ID $notification->order_id: "."transaction status = $transaction, fraud staus = $fraud");
      
      if ($transaction == 'capture') {
        // For credit card transaction, we need to check whether transaction is challenge by FDS or not
        if ($type == 'credit_card') {
            if ($fraud == 'challenge') {
                // TODO set payment status in merchant's database to 'Challenge by FDS'
                // TODO merchant should decide whether this transaction is authorized or not in MAP
                echo "Transaction order_id: " . $order_id ." is challenged by FDS";
                $paymentSuccess =false;

            } else {
                // TODO set payment status in merchant's database to 'Success'
                // echo "Transaction order_id: " . $order_id ." successfully captured using " . $type;
                $paymentSuccess =True;

            }
        }
        } else if ($transaction == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'
            echo "Transaction order_id: " . $order_id ." successfully transfered using " . $type;
            $paymentSuccess =True;

        } else if ($transaction == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
            echo "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type; 
            $paymentSuccess =false;

        } else if ($transaction == 'deny') {
            // TODO set payment status in merchant's database to 'Denied'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
            $paymentSuccess =false;

        } else if ($transaction == 'expire') {
            // TODO set payment status in merchant's database to 'expire'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
            $paymentSuccess =false;

        } else if ($transaction == 'cancel') {
            // TODO set payment status in merchant's database to 'Denied'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
            $paymentSuccess =false;
        }
        
        $paymentParams = [
            'code' => Payment::generateCode(),
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'status' => $transaction,
            'payment_gateway' => 'MIDTRANS',
            'payment_type' => $paymentNotitification->payment_type,
            'amount' => $paymentNotitification->gross_amount,
            'payloads' => $payload,
        ];

        // dd($paymentParams);

        $payment = Payment::create($paymentParams);
        if ($paymentSuccess && $payment) {
            DB::beginTransaction();

            try {
                $order->status = Order::STATUS_CONFIRMED;
                $order->save();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            DB::commit();
        }

        $message = 'Payment status is : ' . $transaction;

        return response(['code' => 200, 'message' => $message], 200);


   }


   private function initPaymentGateway()
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = (bool)env('MIDTRANS_PRODUCTION', false);
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
    }


}
