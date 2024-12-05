<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Relation;

class RelationController extends BaseController
{

  public function __construct(){
  }

  protected function storeValidator(array $data){
    return Validator::make($data, [
      'name'        => ['required'],
      'discription' => ['required'],
      'sex_id'      => ['required'],
      'pair'        => ['required'],
    ]);
  }

  public function index(Request $request){
    $Relation = Relation::all();
    return $Relation;
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
    
    $CurrentRelation = Relation::create([
      'name' 		        => $request['name'],
      'discription'			=> $request['discription'],
      'sex_id'    			=> $request['sex_id'],
      'pair'      			=> $request['pair'],
    ]);
    return response()->json($CurrentRelation);
  }

}
