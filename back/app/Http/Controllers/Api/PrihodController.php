<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Prihod;

class PrihodController extends BaseController
{

  public function __construct(){
  }

  protected function storeValidator(array $data){
    return Validator::make($data, [
      'name' => ['required'],
      'discription' => ['required'],
      'number' => ['required'],
    ]);
  }


  public function index(Request $request){
    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    $prihodIDs = [];
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        array_push($prihodIDs, $permition->source_id);
      }
    }

    if ($isAdmin) {
      $Prihod = Prihod::all();
      return $Prihod;
    } else {
      $Prihod = Prihod::whereIn('id', $prihodIDs)->get();
      return $Prihod;
    }
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

    $CurrentPrihod = Prihod::create([
      'name' 		        => $request['name'],
      'discription'			=> $request['discription'],
      'number'    			=> $request['number'],
      'is_global'  			=> $request['is_global'],
    ]);
    return response()->json($CurrentPrihod);
  }

  public function update(Request $request, $id){
    $this->storeValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($permition->source_id == $id) $isAdmin = true;
      }
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $CurrentPrihod = Prihod::find($id);
    $CurrentPrihod->name 		      = $request['name'];
    $CurrentPrihod->number 		    = $request['number'];
    $CurrentPrihod->discription 	= $request['discription'];
    $CurrentPrihod->is_global 	  = $request['is_global'];
    $CurrentPrihod->save();

    return $CurrentPrihod;
  }

}
