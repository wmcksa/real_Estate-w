<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ManageProperty extends Model
{
    use HasFactory, softDeletes;

    protected $guarded = ['id'];
    protected $dates   = ['deleted_at', 'start_date', 'expire_date'];

    protected $casts = ['amenity_id' => 'array'];


    protected $appends = ['investmentAmount', 'limitamenity', 'allamenity', 'managetime'];

    /*
     * Price With Currency
     */

    public function getInvestmentAmountAttribute()
    {
        if ($this->fixed_amount == null) {
            return config('basic.currency_symbol') . $this->minimum_amount . ' - ' . config('basic.currency_symbol') . $this->maximum_amount;
        }
        return config('basic.currency_symbol') . $this->fixed_amount;
    }

    public function language()
    {
        return $this->hasMany(Language::class, 'language_id', 'id');
    }

    public function details()
    {
        return $this->hasOne(ManagePropertyDetails::class);
    }

    Public function getAddress(){
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    public function image()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function getLimitAmenityAttribute()
    {
        if($this->amenity_id == null){
            return [];
        }
        return Amenity::with('details')->whereIn('id', $this->amenity_id)->where('status', 1)->limit(3)->orderBy('id', 'ASC')->get();
    }

    public function getAllAmenityAttribute()
    {
        if($this->amenity_id == null){
            return [];
        }
        return Amenity::with('details')->whereIn('id', $this->amenity_id)->where('status', 1)->orderBy('id', 'ASC')->get();
    }

    public function getManagetimeAttribute()
    {
        return ManageTime::where('id', $this->how_many_days)->first();
    }

    public function getInvestment()
    {
        return $this->hasMany(Investment::class, 'property_id', 'id');
    }

    public function totalInvestUserAndAmount(){
        $totalInvestedUser = 0;
        $totalInvestedAmount = 0;
        $investment = Investment::where('property_id', $this->id)->get();
        foreach ($investment as $key => $invest){
            $totalInvestedAmount += $invest->amount;
            $totalInvestedUser = $key+1;
        }
        return [
            'totalInvestedUser' => $totalInvestedUser,
            'totalInvestedAmount' => $totalInvestedAmount,
        ];
    }

    public function totalRunningInvestUserAndAmount(){
        $totalInvestedUser = 0;
        $totalInvestedAmount = 0;
        $investment = Investment::where('property_id', $this->id)->where('invest_status', 1)->where('status', 0)->where('is_active', 1)->get();
        foreach ($investment as $key => $invest){
            $totalInvestedAmount += $invest->amount;
            $totalInvestedUser = $key+1;
        }
        return [
            'totalInvestedUser' => $totalInvestedUser,
            'totalInvestedAmount' => $totalInvestedAmount,
        ];
    }

    public function totalDueInvestUserAndAmount(){
        $totalInvestedUser = 0;
        $totalInvestedAmount = 0;
        $totalDueAmount = 0;
        $investment = Investment::with('property')->where('property_id', $this->id)->where('invest_status', 0)->where('status', 0)->where('is_active', 1)->get();
        foreach ($investment as $key => $invest){
            $totalInvestedAmount += $invest->amount;
            $totalInvestedUser = $key+1;
            $totalDueAmount += optional($invest->property)->fixed_amount - $invest->amount;
        }
        return [
            'totalInvestedUser' => $totalInvestedUser,
            'totalInvestedAmount' => $totalInvestedAmount,
            'totalDueAmount' => $totalDueAmount,
        ];
    }

    public function totalCompletedInvestUserAndAmount(){
        $totalInvestedUser = 0;
        $totalInvestedAmount = 0;
        $investment = Investment::where('property_id', $this->id)->where('invest_status', 1)->where('status', 1)->where('is_active', 1)->get();
        foreach ($investment as $key => $invest){
            $totalInvestedAmount += $invest->amount;
            $totalInvestedUser = $key+1;
        }
        return [
            'totalInvestedUser' => $totalInvestedUser,
            'totalInvestedAmount' => $totalInvestedAmount,
        ];
    }

    public function rud(){
        $todayDate = now();
        $startDate = $this->start_date;
        $expireDate = $this->expire_date;
        $datetime1 = new DateTime($todayDate);
        $datetime2 = new DateTime($startDate);

        $runningProperties = $todayDate->gt($expireDate);
        $upcomingProperties = $startDate->gt($todayDate);
        $difference = $datetime1->diff($datetime2);

        return [
          'runningProperties'  => $runningProperties,
          'upcomingProperties' => $upcomingProperties,
          'difference'         => $difference
        ];
    }

    public function isInvested()
    {
        return in_array(auth()->id(), $this->getInvestment->pluck('user_id')->toArray());
    }

    public function investableAmount()
    {
        return ($this->fixed_amount > $this->available_funding && $this->available_funding > 0) ? $this->available_funding : ($this->available_funding < $this->minimum_amount && $this->available_funding != 0 ? $this->minimum_amount : ($this->is_invest_type == 0 ? $this->fixed_amount : ''));
    }

    public function getReviews()
    {
        return $this->hasMany(InvestorReview::class, 'property_id');
    }

    public function avgRating(){
        return  $this->getReviews()->avg('rating2');
    }

    public function getFavourite()
    {
        $clientId = null;
        if (Auth::check()) {
            $clientId = Auth::user()->id;
        }
        return $this->hasOne(Favourite::class, 'property_id')->where('client_id', $clientId);

    }




}
