<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    public function MenuItemsP(){
        return $this->hasMany('MenuItem', 'parent_id');
    }
}
