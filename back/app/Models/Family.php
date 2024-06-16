<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\People;

class Family extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'name' ,'discription', 'head_id'
  ];

  protected $appends = [
    'HeadName', 'FamilyComposition'
  ];

  public function head()
  {
		return $this->hasOne(People::class, 'id', 'head_id');
  }

  public function people()
  {
		return $this->hasMany(People::class);
  }

  public function getHeadNameAttribute()
  {
    if ($this->head){
      return $this->head->first_name;
    } else {
      return '';
    }
  }

  public function getFamilyCompositionAttribute()
  {
    if ($this->people){
      return count($this->people);
    } else {
      return 0;
    }
  }
  
}