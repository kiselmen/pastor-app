<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\People;
use App\MOdels\Target;
use App\Models\Globalaction;

class Targetaction extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
    'people_id', 'globalaction_id', 'source_id', 'target_id',
  ];

  public $timestamps = false;
  
  protected $appends = [
    'PersoneName', 'Persone', 'SourceName', 'TargetName'
  ];

  public function persone() {
		return $this->belongsTo(People::class);
	}

  public function source() {
		return $this->belongsTo(Target::class);
	}

  public function target() {
		return $this->belongsTo(Target::class);
	}

  public function globalaction() {
		return $this->belongsTo(Globalaction::class);
	}

  public function getPersoneNameAttribute()
  {
    if ($this->people_id){
      $curPersone = DB::table('people')->find($this->people_id);
      return $curPersone->name.' '.$curPersone->first_name.' '.$curPersone->patronymic;
    } else {
      return 'Не определено';
    }
  }

  public function getPersoneAttribute()
  {
    if ($this->people_id){
      $curPersone = DB::table('people')->find($this->people_id);
      if ($curPersone) {
        $targets = DB::table('ptargets')->where('people_id', $curPersone->id)->get();
        $curPersone->ptarget = $targets;
      }
      return $curPersone;
    } else {
      return null;
    }
  }

  public function getSourceNameAttribute()
  {
    if ($this->source){
      return $this->source->name;
    } else {
      return 'Не определено';
    }
  }

  public function getTargetNameAttribute()
  {
    if ($this->target){
      return $this->target->name;
    } else {
      return 'Не определено';
    }
  }  
}
