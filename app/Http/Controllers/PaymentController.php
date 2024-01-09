<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function processPayment(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rental_transaction_id' => 'required|exists:rental_transactions,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $payment = PaymentTransaction::create($request->all());

        return response()->json(['message' => 'Payment processed successfully', 'payment' => $payment], 201);
    }
}

