<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\Ptarget;
use App\Models\People;
use App\Models\Target;
use App\Models\Targetaction;
use App\Models\Globalaction;

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

  protected function mergeValidator(array $data) {
    return Validator::make($data, [
      'people_id' => ['required'],
      'targets' => ['required'],
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

    DB::beginTransaction();
    try {
      $CurrentPtarget = Ptarget::create([
        'people_id' 		=> $request['people_id'],
        'target_id'			=> $request['target_id'],
      ]);

      $CurTarget = Target::find($request['target_id']);

      $Globalaction = Globalaction::create([
        'user_id' 		          => $User->id,
        'action_id'		      	  => 12,
        'name'                  => 'Добавить целевую группу',
        'date'                  => Carbon::now()->toDateString(),
        'discription'           => 'Добавить целевую группу ' . $CurTarget->name . ' для ' . $Persone->FullName,
      ]);

      $CurrentAction = Targetaction::create([
        'globalaction_id' => $Globalaction->id,
        'people_id' 		  => $request['people_id'],
        'source_id'			  => null,
        'target_id'			  => $request['target_id'],
      ]);

      DB::commit();
      return response()->json($CurrentPtarget);
    } catch (Exception $e){
      DB::rollBack();
      return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500); // разобраться с ошибкой
    }
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

        DB::beginTransaction();
        try {
          $Globalaction = Globalaction::create([
            'user_id' 		          => $User->id,
            'action_id'		      	  => 13,
            'name'                  => 'Удалить целевую группу',
            'date'                  => Carbon::now()->toDateString(),
            'discription'           => 'Удалить целевую группу ' . $CurrentPtarget->target->name . ' для ' . $Persone->FullName,
          ]);
      
          $CurrentAction = Targetaction::create([
            'globalaction_id' => $Globalaction->id,
            'people_id' 		  => $CurrentPtarget->people_id,
            'source_id'			  => $CurrentPtarget->target_id,
            'target_id'			  => null,
          ]);

          $CurrentPtarget->delete();
          DB::commit();
        } catch (Exception $e){
          DB::rollBack();
          return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500); // разобраться с ошибкой
        }
      }  
    };
    return response()->json('done');
  }

  public function merge(Request $request) {
    $this->mergeValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    $Persone = People::find($request['people_id'])->load('ptarget');
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($permition->source_id == $Persone->prihod_id) $isAdmin = true;
      }
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    DB::beginTransaction();
    try {

      $baseTargets = $Persone->ptarget;
      $frontTargets = $request['targets'];

      // return $frontTargets;
      $itemsToDelete = [];
      $itemsToAdd = [];

      foreach ($frontTargets as $frontItem){
        $found = false;
        foreach ($baseTargets as $baseItem){
          if ($frontItem["target_id"] === $baseItem["target_id"]) {
            $found = true;
          }
        }
        if (!$found) {
          array_push($itemsToAdd, $frontItem); 
        }
      }

      foreach ($baseTargets as $baseItem) {
        $found = false;
        foreach ($frontTargets as $frontItem){
          if ($frontItem["target_id"] === $baseItem["target_id"]) {
            $found = true;
          }
        }
        if (!$found) {
          array_push($itemsToDelete, $baseItem->id); 
        }
      }

      if (!empty($itemsToDelete) || !empty($itemsToAdd)) {
        $Globalaction = Globalaction::create([
          'user_id' 		          => $User->id,
          'action_id'		      	  => 15,
          'name'                  => 'Обновить целевые группы',
          'date'                  => Carbon::now()->toDateString(),
          'discription'           => 'Обновить целевые группы для ' . $Persone->FullName,
        ]);

        foreach ($itemsToDelete as $item) {
          $CurrentPtarget = Ptarget::find($item);

          $CurrentAction = Targetaction::create([
            'globalaction_id' => $Globalaction->id,
            'people_id' 		  => $CurrentPtarget->people_id,
            'source_id'			  => $CurrentPtarget->target_id,
            'target_id'			  => null,
          ]);
          $CurrentPtarget->delete();
        }
  
        foreach ($itemsToAdd as $item) {
          $CurrentPtarget = Ptarget::create([
            'people_id' 		  => $Persone->id,
            'target_id'			  => $item['target_id'],
          ]);
    
          $CurrentAction = Targetaction::create([
            'globalaction_id' => $Globalaction->id,
            'people_id' 		  => $Persone->id,
            'source_id'			  => null,
            'target_id'			  => $item['target_id'],
          ]);
        }
      }

      $Ptargets = Ptarget::where('people_id', $Persone->id)->get();

      DB::commit();
      return response()->json($Ptargets);
    } catch (Exception $e){
      DB::rollBack();
      return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500); // разобраться с ошибкой
    }
  }
}
