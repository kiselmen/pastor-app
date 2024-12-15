<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use App\Models\People;

class Family extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'name' ,'discription', 'head_id', 'is_passive',
  ];

  protected $appends = [
    'HeadName', 'FamilyComposition', 'Prihod',
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
  
  public function getPrihodAttribute(){
    if ($this->head) {
      return $prihod = DB::table('prihods')->find($this->head->prihod_id);
    } else {
      return null;
    }
  }
}