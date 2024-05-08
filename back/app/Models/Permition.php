<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Permition extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'user_id' ,'type', 'sourse_id',
  ];

  public function users(){
		return $this->belogsTo(User::class);
	}
}