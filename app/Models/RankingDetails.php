<?php

namespace App\Models;

use App\Http\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankingDetails extends Model
{
    use HasFactory, Translatable;
    protected $guarded = ['id'];

    public function rankings(){
        return $this->belongsTo(Ranking::class, 'ranking_id');
    }
}
