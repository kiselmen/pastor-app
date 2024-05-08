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

    $CurrentLevel = Level::create([
      'name' 		        => $request['name'],
      'discription'			=> $request['discription'],
    ]);
    return response()->json($CurrentLevel);
  }

}
