<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use softDeletes;
    protected $guarded = ['id'];
    protected $dates   = ['deleted_at'];

    public function getUsernameAttribute()
    {
        return $this->name;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messages(){
        return $this->hasMany(TicketMessage::class)->latest();
    }

    public function lastReply(){
        return $this->hasOne(TicketMessage::class)->latest();
    }
    public function  getLastMessageAttribute(){
        return Str::limit($this->lastReply->message,40);
    }

}
