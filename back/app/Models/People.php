<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Http\Response;

// use DB;
use App\Models\Prihod;
use App\Models\Sex;
// use App\Models\Target;
use App\Models\Family;
use App\Models\Pservice;
use App\Models\Plevel;
use App\Models\Ptarget;


class People extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'first_name' ,'name', 'patronymic', 'birthday_date', 'baptism_date', 'death_date', 'image_url', 'live_addres', 'home_phone', 'mobile_phone', 
      'email', 'prihod_id', 'family_id', 'sex_id', 'created_at', 'updated_at'
  ];

  protected $appends = [
    'PrihodName', 'SexName'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */

  public function prihod()
  {
		return $this->belongsTo(Prihod::class);
  }

  // public function target()
  // {
	// 	return $this->belongsTo(Target::class);
  // }

  public function sex()
  {
		return $this->belongsTo(Sex::class);
  }

  public function pservice()
  {
    return $this->hasMany(Pservice::class);
  }

  public function ptarget()
  {
    return $this->hasMany(Ptarget::class);
  }

  public function plevel()
  {
    $User = Auth::user()->load('permition');
    $Permitions = $User->permition;
    $prihodIDs = [];
    // $serviceIDs = [];
    // Log::error('$Permitions '. json_encode($Permitions));
    $isAdmin = false;

    foreach ($Permitions as $permition) {
      if ($permition->type == 0) {
        $isAdmin = true;
        break;
      }
      if ($permition->type == 1) {
        array_push($prihodIDs, $permition->source_id);
      }    
    }

    if ($isAdmin) {
      return $this->hasMany(Plevel::class);
    } else {
      return $this->hasMany(Plevel::class)->where(function ($query) use ($User, $prihodIDs) {
        $query->whereIn('people_id', function ($subQuery) use ($prihodIDs) {
            $subQuery->select('id')
                     ->from('people')
                     ->whereIn('prihod_id', $prihodIDs);
        })
        ->orWhere('user_id', $User->id);
      });
    }
  }

  public function family()
  {
		return $this->belongsTo(Family::class);
  }

  public function getPrihodNameAttribute()
  {
    if ($this->prihod){
      return $this->prihod->name;
    } else {
      return '';
    }
  }

  // public function getTargetNameAttribute()
  // {
  //   if ($this->target){
  //     return $this->target->name;
  //   } else {
  //     return '';
  //   }
  // }

  public function getSexNameAttribute()
  {
    if ($this->sex){
      return $this->sex->name;
    } else {
      return '';
    }
  }
}
