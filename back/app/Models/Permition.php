<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Permition extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'user_id' ,'type', 'source_id', 'created_at', 'updated_at',
  ];

  public function users(){
		return $this->belogsTo(User::class);
	}
}