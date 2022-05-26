<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory;

    protected $table = "messages";

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    // protected $fillable = [
    //     'messages',
    //     'receiver_user_id',
    // ];

    // public function receiver(){
    //     return $this->belongsTo(User::class, 'receiver_user_id', 'id');
    // }
    
    // public function sender(){
    //     return $this->belongsTo(User::class, 'sender_user_id', 'id');
    // }
}
