<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'rental_transaction_id', 'amount', 'payment_date'];

    public function rentalTransaction() {
        return $this->belongsTo(RentalTransaction::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    
}
