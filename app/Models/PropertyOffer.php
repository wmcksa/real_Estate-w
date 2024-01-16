<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyOffer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

    public function property()
    {
        return $this->belongsTo(ManageProperty::class, 'property_id');
    }

    public function getInvestment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'offered_from');
    }


    public function owner()
    {
        return $this->belongsTo(User::class, 'offered_to');
    }

    public function propertyShare()
    {
        return $this->belongsTo(PropertyShare::class, 'property_share_id')->withTrashed();
    }

    public function totalOfferList($id)
    {
        return PropertyOffer::where('property_share_id', $id)->count();
    }

    public function offerlock()
    {
//        return $this->hasOne(OfferLock::class, 'property_share_id', 'property_share_id')->where('status', 0); // offer lock see for all offered person //
        return $this->hasOne(OfferLock::class, 'property_share_id', 'property_share_id'); // offer lock see for all offered person //
    }

    public function receiveMyOffer(){
        return $this->hasOne(OfferLock::class, 'property_offer_id', 'id'); // offer lock for me.
    }

    public function lockInfo(){
//        return OfferLock::where('property_offer_id', $this->id)->where('status', 0)->first();
        return OfferLock::where('property_offer_id', $this->id)->first();
    }

}
