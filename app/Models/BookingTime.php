<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BookingTime extends Model
{
    protected static function boot()
    {
        parent::boot();
    }

    public function __construct() {
        parent::__construct();
    }

    public function getStartTimeAttribute($value)
    {
        return Carbon::createFromFormat('H:i:s', $value)->setTimezone('UTC')->format('H:i:s');
    }

    public function getEndTimeAttribute($value)
    {
        return Carbon::createFromFormat('H:i:s', $value)->setTimezone('UTC')->format('H:i:s');
    }

    public function getUtcStartTimeAttribute() {
        return Carbon::createFromFormat('H:i:s', $this->attributes['start_time'])->format('H:i:s');
    }

    public function getUtcEndTimeAttribute() {
        return Carbon::createFromFormat('H:i:s', $this->attributes['end_time'])->format('H:i:s');
    }

    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = Carbon::parse($value, 'UTC')->setTimezone('UTC')->format('H:i:s');
    }

    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = Carbon::parse($value, 'UTC')->setTimezone('UTC')->format('H:i:s');
    }
}
