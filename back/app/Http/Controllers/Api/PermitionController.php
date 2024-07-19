<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Permition;
// use App\Models\People;

class PermitionController extends BaseController
{

  public function __construct() {
  }

  protected function storeValidator(array $data) {
    return Validator::make($data, [
      'type' => ['required'],
      'user_id' => ['required'],
      'source_id' => ['required'],
    ]);
  }

  public function store(Request $request) {
    $this->storeValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;

    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $CurrentPermition = Permition::create([
      'user_id'    		  => $request['user_id'],
      'type' 		        => $request['type'],
      'source_id'			  => $request['source_id'],
    ]);
    return response()->json($CurrentPermition);
  }

  public function delete(Request $request) {
    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;

    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $IDs = $request['ids'];
    foreach ($IDs as $id) {
      $CurrentPermition = Permition::Find($id);

      if ($CurrentPermition) {
        $CurrentPermition->delete();
      }  
    };
    return response()->json('done');
  }
}
