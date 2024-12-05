<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sex;

class Relation extends Model
{
  protected $hidden = [
  ];

  public $timestamps = false;
  
  protected $fillable = [
      'name', 'discription', 'sex_id', 'pair',
  ];
  
  protected $appends = [
    'SexName',
  ];

  public function sex() {
		return $this->belongsTo(Sex::class);
	}

  public function getSexNameAttribute()
  {
    if ($this->sex){
      return $this->sex->name;
    } else {
      return 'Не определен';
    }
  }
}