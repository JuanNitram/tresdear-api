<?php

namespace App\Models;

use App\Models\Base\BaseModel;

class Projects extends BaseModel
{
    protected $table = 'projects';

    protected $fillable = [
        'name', 'description','description_quill','link',
        'highlighted', 'active', 'pos'
    ];

    public function setActiveAttribute($value){
        if($value == 'false') $this->attributes['active'] = 0;
        else $this->attributes['active'] = 1;
    }

    public function getActiveAttribute($value){
        if($value) return true;
        return false;
    }

    public function setHighlightedAttribute($value){
        if($value == 'false') $this->attributes['highlighted'] = 0;
        else $this->attributes['highlighted'] = 1;
    }

    public function getHighlightedAttribute($value){
        if($value) return true;
        return false;
    }

}
