<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected static function boot()
    {
        parent::boot();
    }

    public function employee()
    {
        return $this->belongsTo(User::class);
    }
}
