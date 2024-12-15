<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Globalaction;

class GlobalActionsController extends BaseController
{

  public function __construct(){
  }

  protected function indexValidator(array $data){
    return Validator::make($data, [
      '_start' => ['required'],
      '_end' => ['required'],
    ]);
  }


  public function index(Request $request){

    $this->indexValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;

    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($permition->source_id == $findPersone->prihod_id) $isAdmin = true;
      }
    }

    if ($isAdmin) {

      $date_start  = $request['_start'];
      $date_end    = $request['_end'];


      $Globalactions = Globalaction::
        where('date','>=',$date_start)
        ->where('date','<=',$date_end)
        ->get()
        ->load('peopleaction', 'prihodaction', 'familyaction', 'serviceaction', 'targetaction');
      return $Globalactions;
    } else {
      return response()->json(['message' => 'Do not have permitions'], 403);
    }
  }

}
