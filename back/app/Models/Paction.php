<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\People;
use App\Models\User;
use App\Models\Globalaction;

class Paction extends Model
{
  // {id: 0, name: 'Ввод начальных данных'},
  // {id: 1, name: 'Создание новой семьи'},
  // {id: 2, name: 'Вхождение в веру'},
  // {id: 3, name: 'Переехал из другой церкви'},
  
  protected $hidden = [
  ];

  protected $fillable = [
    'people_id', 'global_id', 'user_id' ,'action_id', 'name', 'date', 'field', 'value'
  ];

  public $timestamps = false;
  
  protected $appends = [
    'UserName', 'PersoneName'
  ];

  public function persone() {
		return $this->belongsTo(People::class);
	}

  public function user() {
		return $this->belongsTo(User::class);
	}

  public function globalaction() {
		return $this->belongsTo(Globalaction::class);
	}

  public function getPersoneNameAttribute()
  {
    if ($this->persone){
      return $this->persone->name.' '.$this->persone->first_name.' '.$this->persone->patronymic;
    } else {
      return 'Не определено';
    }
  }

  public function getUserNameAttribute()
  {
    if ($this->user){
      return $this->user->name;
    } else {
      return 'Не определено';
    }
  }
}