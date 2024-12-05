<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use DB;
use Carbon\Carbon;
use App\Models\Prihod;
use App\Models\Sex;
use App\Models\Relation;
use App\Models\Family;
use App\Models\Pservice;
use App\Models\Plevel;
use App\Models\Ptarget;
use App\Models\Paction;

class People extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'first_name' ,'name', 'patronymic', 'birthday_date', 'baptism_date', 'death_date', 'image_url', 'live_addres', 'home_phone', 'mobile_phone', 
      'email', 'prihod_id', 'family_id', 'sex_id', 'relation_id', 'created_at', 'updated_at'
  ];

  protected $appends = [
    'FullName', 'PrihodName', 'SexName', 'RelationName', 'Pair', 'Under18', 'ChildrenMore18YearsOld', 'AllChildrens',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */

  public function prihod()
  {
		return $this->belongsTo(Prihod::class);
  }

  public function relation()
  {
		return $this->belongsTo(Relation::class);
  }

  public function sex()
  {
		return $this->belongsTo(Sex::class);
  }

  public function pservice()
  {
    return $this->hasMany(Pservice::class);
  }

  public function ptarget()
  {
    return $this->hasMany(Ptarget::class);
  }

  public function plevel()
  {
    $User = Auth::user()->load('permition');
    $Permitions = $User->permition;
    $prihodIDs = [];
    $isAdmin = false;

    foreach ($Permitions as $permition) {
      if ($permition->type == 0) {
        $isAdmin = true;
        break;
      }
      if ($permition->type == 1) {
        array_push($prihodIDs, $permition->source_id);
      }    
    }

    if ($isAdmin) {
      return $this->hasMany(Plevel::class);
    } else {
      return $this->hasMany(Plevel::class)->where(function ($query) use ($User, $prihodIDs) {
        $query->whereIn('people_id', function ($subQuery) use ($prihodIDs) {
            $subQuery->select('id')
                     ->from('people')
                     ->whereIn('prihod_id', $prihodIDs);
        })
        ->orWhere('user_id', $User->id);
      });
    }
  }

  public function family()
  {
		return $this->belongsTo(Family::class);
  }

  public function paction()
  {
		return $this->hasMany(Paction::class);
  }

  public function getFullNameAttribute(){
    return $this->name. ' ' . $this->first_name. ' ' . $this->patronymic; 
  }

  public function getPairAttribute()
  {
    if ($this->relation_id > 1) return [];
    $family = DB::table('families')->find($this->family_id);
    if (!$family) return [];
    $familyPeople = DB::table('people')->where('family_id', $family->id)->where('id', '!=', $this->id)->get();
    if (!$familyPeople) return [];
    $pair = [];
    foreach ($familyPeople as $persone) {
      if ($persone->relation_id === 0 || $persone->relation_id === 1) array_push($pair, $persone);
    }
    return $pair;
  }

  public function getUnder18Attribute(){
    $now = Carbon::now();
    $birthday = Carbon::parse($this->birthday_date);
    $age = $birthday->diffInYears($now);
    return $age < 18; 
  }

  public function getChildrenMore18YearsOldAttribute(){
    if ($this->relation_id > 1) return [];
    $family = DB::table('families')->find($this->family_id);
    if (!$family) return [];
    $familyPeople = DB::table('people')->where('family_id', $family->id)->where('id', '!=', $this->id)->get();
    if (!$familyPeople) return [];
    $childrensMoreThen18 = [];
    foreach ($familyPeople as $persone) {
      if ($persone->relation_id > 1) {
        $now = Carbon::now();
        $birthday = Carbon::parse($persone->birthday_date);
        $age = $birthday->diffInYears($now);
        if  ($age >= 18) array_push($childrensMoreThen18, $persone);
      }
    }
    return $childrensMoreThen18;
  }

  public function getAllChildrensAttribute(){
    if ($this->relation_id > 1) return [];
    $family = DB::table('families')->find($this->family_id);
    if (!$family) return [];
    $familyPeople = DB::table('people')->where('family_id', $family->id)->where('id', '!=', $this->id)->get();
    if (!$familyPeople) return [];
    $childrens = [];
    foreach ($familyPeople as $persone) {
      if ($persone->relation_id > 1) array_push($childrens, $persone);
    }
    return $childrens;
  }

  public function getPrihodNameAttribute()
  {
    if ($this->prihod){
      return $this->prihod->name;
    } else {
      return '';
    }
  }

  public function getRelationNameAttribute()
  {
    if ($this->relation){
      return $this->relation->name;
    } else {
      return '';
    }
  }

  public function getSexNameAttribute()
  {
    if ($this->sex){
      return $this->sex->name;
    } else {
      return '';
    }
  }
}
