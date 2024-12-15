<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\People;
use App\Models\User;
use App\Models\Globalaction;

class Peopleaction extends Model
{
  // {id: 0, name: 'Ввод начальных данных'},
  // {id: 1, name: 'Создание новой семьи'},
  // {id: 2, name: 'Вхождение в веру'},
  // {id: 3, name: 'Переехал из другой церкви'},
  
  protected $hidden = [
  ];

  protected $fillable = [
    'people_id', 'globalaction_id', 'field', 'source', 'target'
  ];

  public $timestamps = false;
  
  protected $appends = [
    'PersoneName', 'Persone'
  ];

  // public function persone() {
	// 	return $this->belongsTo(People::class);
	// }

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
      return $curPersone;
    } else {
      return null;
    }
  }
}