<?php

namespace App\Models;

use App\Http\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeDetails extends Model
{
    use HasFactory, Translatable;
    protected $guarded = ['id'];

    public function badges(){
        return $this->belongsTo(Badge::class, 'badge_id');
    }
}
