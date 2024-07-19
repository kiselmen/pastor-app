<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Prihod;
use App\Models\Target;
use App\Models\Family;
use App\Models\Pservice;
use App\Models\Plevel;

class People extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'first_name' ,'name', 'patronymic', 'birthday_date', 'baptism_date', 'death_date', 'image_url', 'live_addres', 'home_phone', 'mobile_phone', 
      'email', 'prihod_id', 'target_id', 'family_id', 'created_at', 'updated_at'
  ];

  protected $appends = [
    'PrihodName', 'TargetName',
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

  public function target()
  {
		return $this->belongsTo(Target::class);
  }

  public function pservice()
  {
		return $this->hasMany(Pservice::class);
  }

  public function plevel()
  {
		return $this->hasMany(Plevel::class);
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

  public function getTargetNameAttribute()
  {
    if ($this->target){
      return $this->target->name;
    } else {
      return '';
    }
  }
}
