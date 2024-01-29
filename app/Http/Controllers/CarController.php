<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller {
    // List all cars
    public function index() {
        $cars = Car::all();
        return response()->json(['cars' => $cars], 200);
    }

    // Register a new car
    public function registerCar(Request $request) {
        // Validate request data including picture files
        $request->validate([
            'make' => 'required',
            'model' => 'required',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'price' => 'required|numeric|min:0',
            'plate_number' => 'required|unique:cars',
            'available' => 'required|boolean',
            'main_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rear_picture_1' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rear_picture_2' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rear_picture_3' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Store the picture files and get their URLs
        $mainPictureUrl = $request->file('main_picture')->store('car_pictures','local');
        $rearPicture1Url = $request->file('rear_picture_1')->store('car_pictures','local');
        $rearPicture2Url = $request->file('rear_picture_2')->store('car_pictures','local');
        $rearPicture3Url = $request->file('rear_picture_3')->store('car_pictures','local');

        // Create a new car with the picture file URLs
        $car = Car::create([
            'make' => $request->make,
            'model' => $request->model,
            'year' => $request->year,
            'price' => $request->price,
            'plate_number' => $request->plate_number,
            'available' => $request->available,
            'main_picture' => $mainPictureUrl,
            'rear_picture_1' => $rearPicture1Url,
            'rear_picture_2' => $rearPicture2Url,
            'rear_picture_3' => $rearPicture3Url,
            // Add more fields as needed
        ]);

        // Return the created car
        return response()->json(['message' => 'Car registered successfully', 'car' => $car], 201);
    }

    // View a specific car
    public function show($id) {
        $car = Car::find($id);
        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }
        return response()->json(['car' => $car], 200);
    }

    // Update a car
    public function update(Request $request, $id) {
        $car = Car::find($id);
        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        // Validate request data
        $request->validate([
            'make' => 'required',
            'model' => 'required',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'price' => 'required|numeric|min:0',
            'plate_number' => 'required|unique:cars,plate_number,' . $id,
            'available' => 'required|boolean',
            // Add more validation rules as needed
        ]);

        // Update the car
        $car->make = $request->make;
        $car->model = $request->model;
        $car->year = $request->year;
        $car->price = $request->price;
        $car->plate_number = $request->plate_number;
        $car->available = $request->available;
        // Update more fields as needed
        $car->save();

        // Return the updated car
        return response()->json(['message' => 'Car updated successfully', 'car' => $car], 200);
    }

    public function destroy($id) {
        $car = Car::find($id);
        if (!$car) {
            return $this->sendErrorResponse('Car not found', null, 404);
        }

        // Delete the car's picture files
        Storage::delete([$car->main_picture, $car->rear_picture_1, $car->rear_picture_2, $car->rear_picture_3]);

        // Delete the car
        $car->delete();

        // Return a success message
        return $this->sendSuccessResponse('Car deleted successfully', null, 200);
    }

    // Change car status
    public function changeCarStatus($id, Request $request) {
        $car = Car::find($id);

        if (!$car) {
            return $this->sendErrorResponse('Car not found', null, 404);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'available' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse('Validation failed', $validator->errors(), 422);
        }

        // Update the car status
        $car->update(['available' => $request->available]);

        // Return a success message
        return $this->sendSuccessResponse('Car status changed successfully', null, 200);
    }

    // List available cars
    public function availableCars() {
        $today = now()->toDateString(); // Get the current date

        $availableCars = Car::whereDoesntHave('rentalTransactions', function ($query) use ($today) {
            $query->where('rental_end_date', '>=', $today);
        })->where('available', '=', true)->get();

        return $this->sendSuccessResponse('Available cars fetched successfully', ['cars' => $availableCars], 200);
    }

    // Helper method for sending success responses
    private function sendSuccessResponse($message, $data = null, $status = 200) {
        return response()->json(['message' => $message, 'data' => $data], $status);
    }

    // Helper method for sending error responses
    private function sendErrorResponse($message, $errors = null, $status = 422) {
        return response()->json(['message' => $message, 'errors' => $errors], $status);
    }
}

