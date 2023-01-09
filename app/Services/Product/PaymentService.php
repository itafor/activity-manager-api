<?php
namespace App\Services\Product;

use App\Models\Category;
use App\Models\Payment;
use App\Traits\Common;
use App\Traits\FileUpload;

/**
 *
 */
class PaymentService
{
    use Common;

    public function logPayment($request)
    {
            $payment = new Payment();
            $payment->user_id = auth()->user()->id;
            $payment->amount = $request->amount;
            $payment->provider = $request->provider;
            $payment->provider_reference_id = $request->provider_reference_id;
            $payment->status = $request->status;
            $payment->save();

            return $payment;
    }

    public function listPayments()
    {
        
           return Payment::orderBy('id', 'desc')->get();

            
    }

    public function showPayment($paymentId)
    {
            return Payment::where('id', $paymentId)->first();
    }

  
}
