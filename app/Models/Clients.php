<?php

namespace App\Models;

use App\Models\Base\BaseModel;

class Clients extends BaseModel
{
    protected $table = 'clients';

    protected $fillable = [
        'name', 'active' ,'pos'
    ];

    public function setActiveAttribute($value){
        if($value == 'false') $this->attributes['active'] = 0;
        else $this->attributes['active'] = 1;
    }

    public function getActiveAttribute($value){
        if($value) return true;
        return false;
    }

}
