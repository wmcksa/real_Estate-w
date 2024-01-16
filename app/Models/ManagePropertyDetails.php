<?php

namespace App\Models;

use App\Http\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagePropertyDetails extends Model
{
    use HasFactory, Translatable;
    protected $guarded = ['id'];

    protected $casts = ['faq' => 'object'];


    public function manageProperty(){
        return $this->belongsTo(ManageProperty::class, 'manage_property_id', 'id');
    }
}
