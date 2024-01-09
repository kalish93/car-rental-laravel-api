<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalTransaction extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'car_id', 'rental_start_date', 'rental_end_date'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function car() {
        return $this->belongsTo(Car::class);
    }
    public function paymentTransaction() {
        return $this->hasOne(PaymentTransaction::class, 'rental_transaction_id', 'id');
    }
}
