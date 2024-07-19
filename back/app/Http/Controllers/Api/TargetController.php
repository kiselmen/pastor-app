<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Target;

class TargetController extends BaseController
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
    $Target = Target::all();
    return $Target;
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
    
    $CurrentTarget = Target::create([
      'name' 		        => $request['name'],
      'discription'			=> $request['discription'],
    ]);
    return response()->json($CurrentTarget);
  }

}
