<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prices extends Model
{
    protected $table = 'prices';

    protected $fillable = [
        'title', 'subtitle', 'price', 'details', 'iva',
        'description', 'active' ,'pos'
    ];

    public function setActiveAttribute($value)
    {
        if($value == 'false') $this->attributes['active'] = 0;
        else $this->attributes['active'] = 1;
    }

    public function getActiveAttribute($value)
    {
        if($value) return true;
        return false;
    }

    public function setIvaAttribute($value)
    {
        if($value == 'false') $this->attributes['iva'] = 0;
        else $this->attributes['iva'] = 1;
    }

    public function getIvaAttribute($value)
    {
        if($value) return true;
        return false;
    }

}
