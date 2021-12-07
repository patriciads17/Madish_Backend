<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_reservation', 'id_customer', 'name', 'phone_num', 'num_customer', 'booking_date', 'room', 'table_num'
    ];

    public function getCreatedAtAttribute()
    {
       if (!is_null($this->attributes['created_at'])) {
           return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
       }
    } 

    public function getUpdatedAtAttribute()
    {
        if (!is_null($this->attributes['updated_at'])) {
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    } 
}
