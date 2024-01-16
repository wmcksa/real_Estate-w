<?php

namespace App\Models;

use App\Http\Traits\Notify;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, Notify;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $allusers = [];

    protected $appends = ['fullname', 'mobile', 'lastSeen', 'imgPath'];

    protected $dates = ['sent_at'];

    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getMobileAttribute()
    {
        return $this->phone_code . $this->phone;
    }

    public function getImgPathAttribute()
    {
        return getFile(config('location.user.path') . $this->image);
    }

    public function funds()
    {
        return $this->hasMany(Fund::class)->latest()->where('status', '!=', 0)->where('plan_id', null);
    }


    public function transaction()
    {
        return $this->hasOne(Transaction::class)->orderBy('id', 'DESC');
    }

    public function ticket()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function payout()
    {
        return $this->hasMany(PayoutLog::class, 'user_id');
    }


    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_id');
    }

    public function siteNotificational()
    {
        return $this->morphOne(SiteNotification::class, 'siteNotificational', 'site_notificational_type', 'site_notificational_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->mail($this, 'PASSWORD_RESET', $params = [
            'message' => '<a href="' . url('password/reset', $token) . '?email=' . $this->email . '" target="_blank">Click To Reset Password</a>'
        ]);
    }


    public function getReferralLinkAttribute()
    {
        return $this->referral_link = route('register', ['ref' => $this->username]);
    }


    public function referralBonusLog()
    {
        return $this->hasMany(ReferralBonus::class, 'from_user_id', 'id');
    }


    public function invests()
    {
        return $this->hasMany(Investment::class)->latest();
    }

    public function isInvested()
    {
        return in_array(auth()->id(), $this->invests->pluck('user_id')->toArray());
    }

    public function getBadges($id){
        $allBadges = json_decode($this->all_badges);

        if ($allBadges != null){
            return in_array($id, $allBadges);
        }else{
            return 'false';
        }
    }

    public function ranking($id, $type=null){
        if ($type != null){
            $allBadges = json_decode($this->all_badges);
        }else{
            $allBadges = $this->all_badges;
        }

        if ($allBadges != null){
            return in_array($id, $allBadges);
        }else{
            return 'false';
        }
    }

    public function userBadge(){
        return $this->belongsTo(Badge::class, 'last_level', 'id');
    }

    public function countTotalInvestment()
    {
        return $this->invests()->count();
    }

    public function scopeLevel()
    {
        $count = 0;
        $user_id = $this->id;
        while ($user_id != null) {
            $user = User::where('referral_id', $user_id)->first();
            if (!$user) {
                break;
            } else {
                $user_id = $user->id;
                $count++;
            }
        }
        return $count;
    }

    public function referralUsers($id, $currentLevel = 1)
    {
        $users = $this->getUsers($id);
        if ($users['status']) {
            $this->allusers[$currentLevel] = $users['user'];
            $currentLevel++;
            $this->referralUsers($users['ids'], $currentLevel);
        }
        return $this->allusers;
    }

    public function uplineRefer($id)
    {
        return User::where('id', $id)->first();
    }

    public function getUsers($id)
    {
        if (isset($id)) {
            $data['user'] = User::whereIn('referral_id', $id)->get(['id', 'firstname', 'lastname', 'username', 'email', 'phone_code', 'phone', 'referral_id', 'created_at']);
            if (count($data['user']) > 0) {
                $data['status'] = true;
                $data['ids'] = $data['user']->pluck('id');
                return $data;
            }
        }
        $data['status'] = false;
        return $data;
    }

    public function  get_social_links_user(){
        return $this->hasMany(UserSocial::class, 'user_id');
    }

    public function getLastSeenAttribute()
    {
        if(\Illuminate\Support\Facades\Cache::has('user-is-online-' . $this->id)){
            return true;
        }else{
            return false;
        }
    }
}
