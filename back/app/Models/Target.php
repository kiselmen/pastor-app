<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Target extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'name' ,'discription',
  ];

  // public function users(){
	// 	return $this->belogsTo(User::class);
	// }
}