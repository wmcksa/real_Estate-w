<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = "languages";
    protected $guarded = ['id'];

    public function mailTemplates()
    {
        return $this->hasMany(EmailTemplate::class, 'language_id');
    }

    public function notifyTemplates()
    {
        return $this->hasMany(NotifyTemplate::class, 'language_id');
    }

    public function contentDetails()
    {
        return $this->hasMany(ContentDetails::class, 'language_id', 'id');
    }

    public function templateDetails()
    {
        return $this->hasMany(Template::class, 'language_id', 'id');
    }

    public function addressDetails()
    {
        return $this->hasMany(AddressDetails::class, 'language_id', 'id');
    }

    public function amenityDetails()
    {
        return $this->hasMany(AmenityDetails::class, 'language_id', 'id');
    }

    public function badgeDetails()
    {
        return $this->hasMany(BadgeDetails::class, 'language_id', 'id');
    }

    public function blogCategoryDetails()
    {
        return $this->hasMany(BlogCategoryDetails::class, 'language_id', 'id');
    }

    public function blogDetails()
    {
        return $this->hasMany(BlogDetails::class, 'language_id', 'id');
    }

    public function managePropertyDetails()
    {
        return $this->hasMany(ManagePropertyDetails::class, 'language_id', 'id');
    }

    public function rankingDetails()
    {
        return $this->hasMany(RankingDetails::class, 'language_id', 'id');
    }

}
