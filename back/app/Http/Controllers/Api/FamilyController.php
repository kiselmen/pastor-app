<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\Family;
use App\Models\People;
use App\Models\Prihodaction;
use App\Models\Peopleaction;
use App\Models\Familyaction;
use App\Models\Globalaction;

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

  protected function moveValidator(array $data){
    return Validator::make($data, [
      'id' => ['required', 'numeric'],
      'action_id' => ['required', 'numeric'],
      'action_name' => ['required'],
      'prihod_id' => ['required', 'numeric'],
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

    $Family = Family::query();
    if ($isAdmin) {
      $Family = $Family->where('is_passive', 0);
    } else {
      $Family = $Family->whereHas('head', function ($query) use ($prihodIDs) {
          return $query->whereIn('prihod_id', $prihodIDs);
        })
        ->where('is_passive', 0);
    }

    $prihodFilter = $request->query('_prihod');
    if ($prihodFilter) {
      $Family = $Family->whereHas('head', function ($query) use ($prihodFilter) {
        $query->where('prihod_id', $prihodFilter);
      });
    } 

    $searchFilter = $request->query('_search');
    if ($searchFilter) {
      $searchFilter = strtolower($searchFilter);
      $searchFilter = preg_replace("#([%_?+])#", "\\$1", $searchFilter);
      $Family = $Family->where('name', 'LIKE', '%'.$searchFilter.'%');
    }

    return $Family->get();

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
    $actionID = 17;
    $actionName = 'Изменить данные семьи';
    $wasChanged = false;
    $familyFields = [];

    try {
      $Globalaction = Globalaction::create([
        'user_id' 		          => $User->id,
        'action_id'		      	  => $actionID,
        'name'                  => $actionName,
        'date'                  => Carbon::now()->toDateString(),
        'discription'           => $actionName . ' ' . $CurrentFamily->name,
      ]);
      if ($CurrentFamily->name != $request['name']) {
        array_push($familyFields, ['id' => $CurrentFamily->id, 'key' => 'name', 'source' => $CurrentFamily->name, 'target' => $request['name']]);
        $CurrentFamily->name = $request['name'];
        $wasChanged = true;
      }
      if ($CurrentFamily->discription != $request['ndiscriptionme']) {
        array_push($familyFields, ['id' => $CurrentFamily->id, 'key' => 'discription', 'source' => $CurrentFamily->discription, 'target' => $request['discription']]);
        $CurrentFamily->discription = $request['discription'];
        $wasChanged = true;
      }
      $CurrentFamily->save();

      if (count($familyFields)) {
        foreach ($familyFields as $field){
          $CurrentFamilyAction = Familyaction::create([
            'globalaction_id'         => $Globalaction->id,
            'family_id'               => $field['id'],
            'field'                   => $field['key'],
            'source'                  => $field['source'],
            'target'                  => $field['target'],
          ]);
        }
      }

      if ($wasChanged === true) {
        DB::commit();
      } else {
        DB::rollBack();
      }
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500); // разобраться с ошибкой
    }

    // $Family = Family::find($request['id']);
    return $CurrentFamily;
  }

  public function move(Request $request){
    $this->moveValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    
    $Family = Family::find($request['id']);
    $FamilyHead = People::find($Family->head_id);

    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($FamilyHead) {
          if ($permition->source_id == $FamilyHead->prihod_id) $isAdmin = true;
        }
      }
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    DB::beginTransaction();
    $fields = [];
    $prihodFields = [];
    $familyFields = [];
    $actionID = $request['action_id'];
    $actionName = $request['action_name'];

    try {
      $Globalaction = Globalaction::create([
        'user_id' 		          => $User->id,
        'action_id'		      	  => $actionID,
        'name'                    => $actionName,
        'date'                  => Carbon::now()->toDateString(),
        'discription'           => '',
      ]);
      array_push($familyFields, ['id' => $Family->id, 'key' => 'prihod_id', 'source' => $Family->head->prihod_id, 'target' => $request['prihod_id']]);

      foreach ($Family->people as $persone){
        array_push($prihodFields, ['people_id' => $persone->id, 'source_id' => $persone->prihod_id, 'target_id' => $request['prihod_id']]);
        array_push($fields, ['id' => $persone->id, 'key' => 'prihod_id', 'source' => $persone->prihod_id, 'target' => $request['prihod_id']]);
        $CurrentPersone = People::find($persone->id);
        $CurrentPersone->prihod_id = $request['prihod_id'];
        $CurrentPersone->save(); 
      }

      if (count($fields)) {
        foreach ($fields as $field){
          $CurrentPersoneAction = Peopleaction::create([
            'globalaction_id'         => $Globalaction->id,
            'people_id'               => $field['id'],
            'field'                   => $field['key'],
            'source'                  => $field['source'],
            'target'                  => $field['target'],
          ]);
        }
      }  
      if (count($prihodFields)) {
        foreach ($prihodFields as $field){
          $CurrentPrihodAction = Prihodaction::create([
            'globalaction_id'         => $Globalaction->id,
            'people_id'               => $field['people_id'],
            'source_id'               => $field['source_id'],
            'target_id'               => $field['target_id'],
          ]);
        }
      }
      if (count($familyFields)) {
        foreach ($familyFields as $field){
          $CurrentFamilyAction = Familyaction::create([
            'globalaction_id'         => $Globalaction->id,
            'family_id'               => $field['id'],
            'field'                   => $field['key'],
            'source'                  => $field['source'],
            'target'                  => $field['target'],
          ]);
        }
      }

      $Globalaction->discription = $actionName . ' ' . $Family->name;
      $Globalaction->save();

      DB::commit();

    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500); // разобраться с ошибкой
    }

    $Family = Family::find($request['id']);
    return $Family;
  }
}
