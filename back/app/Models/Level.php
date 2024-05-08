<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Level extends Model
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