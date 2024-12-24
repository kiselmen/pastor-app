<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\People;
use App\Models\Service;
use App\Models\Globalaction;

class Serviceaction extends Model
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
		return $this->belongsTo(Service::class);
	}

  public function target() {
		return $this->belongsTo(Service::class);
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
        $services = DB::table('pservices')->where('people_id', $curPersone->id)->get();
        $curPersone->pservice = $services;
      }
      return $curPersone;
    } else {
      return null;
    }
  }

  // public function getServiceNameAttribute()
  // {
  //   if ($this->service){
  //     return $this->service->name;
  //   } else {
  //     return 'Не определено';
  //   }
  // }

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