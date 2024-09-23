<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\People;
use App\Models\Level;
use App\Models\User;

class Plevel extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'date', 'people_id' ,'level_id', 'user_id', 'discription'
  ];

  protected $appends = [
    'LevelName', 'UserName',
  ];

  public function persone() {
		return $this->belongsTo(People::class);
	}

  public function level() {
		return $this->belongsTo(Level::class);
	}

  public function user() {
		return $this->belongsTo(User::class);
	}

  public function getLevelNameAttribute()
  {
    if ($this->level){
      return $this->level->name;
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