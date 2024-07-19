<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Level;

class LevelController extends BaseController
{

  public function __construct(){
  }

  protected function storeValidator(array $data){
    return Validator::make($data, [
      'name' => ['required'],
      'discription' => ['required'],
    ]);
  }


  public function index(Request $request){
    $Level = Level::all();
    return $Level;
  }

  public function store(Request $request){
    $this->storeValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);
    
    $CurrentLevel = Level::create([
      'name' 		        => $request['name'],
      'discription'			=> $request['discription'],
      'color'           => $request['color'],
    ]);
    return response()->json($CurrentLevel);
  }

  public function update(Request $request, $id) {
    $this->storeValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);
    
    $CurrentLevel = Level::Find($id);
    if ($CurrentLevel) {
      $CurrentLevel->name         = $request->input('name');
      $CurrentLevel->discription  = $request->input('discription');
      $CurrentLevel->color        = $request->input('color');
      $CurrentLevel->save();
      return $CurrentLevel;
    } else {
      return response()->json([
          'message' => 'Not found'
      ], 404);
    }
  }


}
