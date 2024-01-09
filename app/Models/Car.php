<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    protected $fillable = [
        'make',
        'model',
        'year',
        'price',
        'plate_number',
        'available',
        'main_picture',
        'rear_picture_1',
        'rear_picture_2',
        'rear_picture_3'];
        public function rentalTransactions() {
            return $this->hasMany(RentalTransaction::class);
        }
}
