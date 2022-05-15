<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    // relation
    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function loves(){
        return $this->hasMany('App\Models\Love');
    }

    public function comments(){
        return $this->hasMany('App\Models\Comment');
    }

    public function getLoveCountAttribute(){
        return $this->loves()->count();
    }
}
