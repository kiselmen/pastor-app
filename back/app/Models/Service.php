<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Status;

class Service extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'name' ,'discription', 'status_id'
  ];

  protected $appends = [
    'StatusName',
  ];

  public function status() {
		return $this->belongsTo(Status::class);
	}

  public function getStatusNameAttribute()
  {
    if ($this->status){
      return $this->status->name;
    } else {
      return 'Не определен';
    }
  }
}