<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Family;
use App\Models\User;
use App\Models\Globalaction;

class Familyaction extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
    'family_id', 'globalaction_id', 'field', 'source', 'target',
  ];

  public $timestamps = false;
  
  protected $appends = [
    'FamilyName'
  ];

  public function family() {
		return $this->belongsTo(Family::class);
	}

  public function globalaction() {
		return $this->belongsTo(Globalaction::class);
	}

  public function getFamilyNameAttribute()
  {
    if ($this->family){
      return $this->family->name;
    } else {
      return 'Не определено';
    }
  }
}