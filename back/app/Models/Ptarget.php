<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\People;
use App\Models\Target;

class Ptarget extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'people_id' ,'target_id'
  ];

  protected $appends = [
    'TargetName',
  ];

  public function persone() {
		return $this->belongsTo(People::class);
	}

  public function target() {
		return $this->belongsTo(Target::class);
	}

  public function getTargetNameAttribute()
  {
    if ($this->target){
      return $this->target->name;
    } else {
      return 'Не определен';
    }
  }
}