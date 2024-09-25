<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Ptarget;
use App\Models\People;

class PtargetController extends BaseController
{

  public function __construct() {
  }

  protected function storeValidator(array $data) {
    return Validator::make($data, [
      'people_id' => ['required'],
      'target_id' => ['required'],
    ]);
  }


  // public function index(Request $request) {
  //   $Ptarget = Ptarget::all();
  //   return $Ptarget;
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

    $CurrentPtarget = Ptarget::create([
      'people_id' 		  => $request['people_id'],
      'target_id'			=> $request['target_id'],
    ]);
    return response()->json($CurrentPtarget);
  }

  public function delete(Request $request) {
    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;

    $IDs = $request['ids'];
    foreach ($IDs as $id) {
      $CurrentPtarget = Ptarget::Find($id);

      if ($CurrentPtarget) {
        $isAdmin = false;
        $Persone = People::find($CurrentPtarget->people_id);
        foreach ($Permitions as $permition) {
          if ($permition->type == 0) $isAdmin = true;
          if ($permition->type == 1) {
            if ($permition->source_id == $Persone->prihod_id) $isAdmin = true;
          }
        }
    
        if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

        $CurrentPtarget->delete();
      }  
    };
    return response()->json('done');
  }
}
