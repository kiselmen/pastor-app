<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Family;
use App\Models\People;

class FamilyController extends BaseController
{

  public function __construct(){
  }

  protected function storeValidator(array $data){
    return Validator::make($data, [
      'name' => ['required'],
      // 'discription' => ['required'],
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
      $Family = Family::all();
      return $Family;
    } else {
      $Family = Family::
        whereHas('head', function ($query) use ($prihodIDs) {
          return $query->whereIn('prihod_id', $prihodIDs);
        })
        ->get();
      return $Family;
    }

  }

  public function show($id){
    $findFilmily = Family::find($id);

    if ($findFilmily) {
      $findFilmily = $findFilmily->load('head', 'people');;
    } else {
      return response()->json(['message' => 'Family not found'], 404);
    }

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;

    $prihodIDs = [];
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($findFilmily->head) {
          if ($permition->source_id == $findFilmily->head->prihod_id) $isAdmin = true;
        }
      }
    }

    if ($isAdmin) {
      return $findFilmily;
    } else {
      return response()->json(['message' => 'Do not have permitions'], 403);
    }
  }

  public function store(Request $request){
    $this->storeValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    $prihodIDs = [];
    
    $RequestMainPersone = $request['head_id'];
    $FamilyHead = People::find($RequestMainPersone);

    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($FamilyHead) {
          if ($permition->source_id == $FamilyHead->prihod_id) $isAdmin = true;
        }
      }
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $CurrentFamily = Family::create([
      'name' 		        => $request['name'],
      'discription'			=> $request['discription'],
      'head_id'       => $request['head_id'],
    ]);

    if (!is_null($RequestMainPersone)) {
      $MainPersone = People::find($RequestMainPersone);
      if ($MainPersone) {
        $MainPersone->family_id = $CurrentFamily->id;
        $MainPersone->save();
      }
    }

    $Candidates =  $request['candidates'];
    foreach ($Candidates as $Candidate){
    $Persone = People::find($Candidate['persone_id']);
      if ($Persone) {
        $Persone->family_id = $CurrentFamily->id;
        $Persone->save();
      }
    }

    return response()->json($CurrentFamily);
  }

  public function update(Request $request, $id){
    $this->storeValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    $prihodIDs = [];
    
    $RequestMainPersone = $request['head_id'];
    $FamilyHead = People::find($RequestMainPersone);

    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($FamilyHead) {
          if ($permition->source_id == $FamilyHead->prihod_id) $isAdmin = true;
        }
      }
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $CurrentFamily = Family::find($id);

    if (!is_null($CurrentFamily->head_id)) {
      $MainPersone = People::find($CurrentFamily->head_id);
      if ($MainPersone) {
        $MainPersone->family_id = null;
        $MainPersone->save();
      }
    }

    $CurrentFamily->name 		        = $request['name'];
    $CurrentFamily->discription 		= $request['discription'];
    $CurrentFamily->head_id 		    = $request['head_id'];

    $CurrentFamily->save();

    // $RequestMainPersone = $request['head_id'];
    if (!is_null($RequestMainPersone)) {
      $MainPersone = People::find($RequestMainPersone);
      if ($MainPersone) {
        $MainPersone->family_id = $CurrentFamily->id;
        $MainPersone->save();
      }
    }

    People::where('family_id', $id)->where('id', '!=', $request['head_id'])->update(['family_id' => null]);
    $Candidates =  $request['candidates'];
    foreach ($Candidates as $Candidate){
    $Persone = People::find($Candidate['persone_id']);
      if ($Persone) {
        $Persone->family_id = $id;
        $Persone->save();
      }
    }

    return $CurrentFamily;
  }
}
