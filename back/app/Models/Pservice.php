<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\People;
use App\Models\Service;

class Pservice extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'people_id' ,'service_id'
  ];

  protected $appends = [
    'ServiceName',
  ];

  public function persone() {
		return $this->belongsTo(People::class);
	}

  public function service() {
		return $this->belongsTo(Service::class);
	}

  public function getServiceNameAttribute()
  {
    if ($this->service){
      return $this->service->name;
    } else {
      return 'Не определен';
    }
  }
}