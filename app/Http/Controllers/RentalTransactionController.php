<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\RentalTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalTransactionController extends Controller
{
    public function index() {
        $rentalTransactions = RentalTransaction::all();
        return response()->json(['rentalTransactions' => $rentalTransactions], 200);
    }

    public function show($id) {
        $rentalTransaction = RentalTransaction::find($id);
        if (!$rentalTransaction) {
            return response()->json(['message' => 'Rental transaction not found'], 404);
        }
        return response()->json(['rentalTransaction' => $rentalTransaction], 200);
    }

    public function store(Request $request) {
        // Validate request data
        $this->validate($request, [
            // 'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'rental_start_date' => 'required|date',
            'rental_end_date' => 'required|date|after:rental_start_date'
        ]);

        $request['user_id'] = Auth::user()->id;
        // Create a new rental transaction
        $rental = RentalTransaction::create($request->all());

        $amount = $this->calculateRentalCost($rental->rental_start_date, $rental->rental_end_date, $rental->car->price);
        $payment = PaymentTransaction::create([
            'rental_transaction_id' => $rental->id,
            'user_id' => Auth::user()->id,
            'amount' => $amount,
            'payment_date' => now(),
        ]);

        return response()->json(['message' => 'Rental transaction created', 'rentalTransaction' => $rental], 201);
    }

    public function update(Request $request, $id) {
        // Find the rental transaction
        $rentalTransaction = RentalTransaction::find($id);
        if (!$rentalTransaction) {
            return response()->json(['message' => 'Rental transaction not found'], 404);
        }

        // Validate and update the rental transaction
        $this->validate($request, [
            'rental_start_date' => 'date',
            'rental_end_date' => 'date|after:rental_start_date'
        ]);

        $rentalTransaction->update($request->all());

        return response()->json(['message' => 'Rental transaction updated', 'rentalTransaction' => $rentalTransaction], 200);
    }

    public function destroy($id) {
        $rentalTransaction = RentalTransaction::find($id);
        if (!$rentalTransaction) {
            return response()->json(['message' => 'Rental transaction not found'], 404);
        }

        $rentalTransaction->delete();

        return response()->json(['message' => 'Rental transaction deleted'], 200);
    }
    public function returnCar(Request $request, $id) {
        // Find the rental transaction
        $rental = RentalTransaction::findOrFail($id);

        // Update the rental end date
        $rental->rental_end_date = $request->rental_end_date;
        $rental->save();

        // Calculate rental cost and create payment transaction
        $amount = $this->calculateRentalCost($rental->rental_start_date, $rental->rental_end_date, $rental->car->price);
        $payment = PaymentTransaction::create([
            'rental_transaction_id' => $rental->id,
            'user_id' => $rental->user_id,
            'amount' => $amount,
            'payment_date' => now(),
        ]);

        // Return the updated rental transaction
        return response()->json(['message' => 'Car returned successfully', 'rental' => $rental], 200);
    }

    private function calculateRentalCost($start, $end, $pricePerDay) {
        // Calculate the rental cost based on the rental period and daily price
        $start = \Carbon\Carbon::parse($start);
        $end = \Carbon\Carbon::parse($end);
        $days = $end->diffInDays($start) + 1; // Include the start day
        return $days * $pricePerDay;
    }

    public function myRentHistory(Request $request){
        $pageSize = $request->input('pageSize', 10); // Default page size is 10
        $pageNumber = $request->input('pageNumber', 1); // Default page number is 1

        $rental = RentalTransaction::with('car', 'paymentTransaction')
        ->where('user_id', Auth::user()->id)
        ->paginate($pageSize, ['*'], 'page', $pageNumber);
        return $rental;
    }
    public function RentHistory(Request $request){
        $pageSize = $request->input('pageSize', 10);
        $pageNumber = $request->input('pageNumber', 1);

        $rental = RentalTransaction::with('car', 'paymentTransaction', 'user') // Include the 'user' relationship
            ->paginate($pageSize, ['*'], 'page', $pageNumber);

        return $rental;
    }

}
