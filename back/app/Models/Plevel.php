<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\People;
use App\Models\Level;

class Plevel extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'date', 'people_id' ,'level_id', 'discription'
  ];

  protected $appends = [
    'LevelName',
  ];

  public function persone() {
		return $this->belongsTo(People::class);
	}

  public function level() {
		return $this->belongsTo(Level::class);
	}

  public function getLevelNameAttribute()
  {
    if ($this->level){
      return $this->level->name;
    } else {
      return 'Не определено';
    }
  }
}