<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getProperty()
    {
        return $this->belongsTo(ManageProperty::class, 'manage_property_id', 'id');
    }

    public function totalCount()
    {
        return $this->hasMany(Analytics::class, 'manage_property_id', 'manage_property_id');
    }

    public function lastVisited()
    {
        return $this->hasOne(Analytics::class,'manage_property_id','manage_property_id')->latest();
    }
}
