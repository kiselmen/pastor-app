<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Prihod extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'name' ,'discription', 'number',
  ];

}