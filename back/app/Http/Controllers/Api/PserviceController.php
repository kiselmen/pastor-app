<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Pservice;
use App\Models\People;

class PserviceController extends BaseController
{

  public function __construct() {
  }

  protected function storeValidator(array $data) {
    return Validator::make($data, [
      'people_id' => ['required'],
      'service_id' => ['required'],
    ]);
  }


  // public function index(Request $request) {
  //   $Pservice = Pservice::all();
  //   return $Pservice;
  // }

  public function store(Request $request) {
    $this->storeValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    $Persone = People::find($request['people_id']);
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($permition->source_id == $Persone->prihod_id) $isAdmin = true;
      }
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $CurrentPservice = Pservice::create([
      'people_id' 		  => $request['people_id'],
      'service_id'			=> $request['service_id'],
    ]);
    return response()->json($CurrentPservice);
  }

  public function delete(Request $request) {
    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;

    $IDs = $request['ids'];
    foreach ($IDs as $id) {
      $CurrentPservice = Pservice::Find($id);

      if ($CurrentPservice) {
        $isAdmin = false;
        $Persone = People::find($CurrentPservice->people_id);
        foreach ($Permitions as $permition) {
          if ($permition->type == 0) $isAdmin = true;
          if ($permition->type == 1) {
            if ($permition->source_id == $Persone->prihod_id) $isAdmin = true;
          }
        }
    
        if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

        $CurrentPservice->delete();
      }  
    };
    return response()->json('done');
  }
}
