<?php

namespace App\Models;

use App\Observers\BusinessServiceObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BusinessService extends Model
{
    protected static function boot()
    {
        parent::boot();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function bookingItems(){
        return $this->hasMany(BookingItem::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

}
