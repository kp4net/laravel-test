<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
    }

    public function businessService(){
        return $this->belongsTo(BusinessService::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
