<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\Pservice;
use App\Models\People;
use App\Models\Service;
use App\Models\Serviceaction;
use App\Models\Globalaction;

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

  protected function mergeValidator(array $data) {
    return Validator::make($data, [
      'people_id' => ['required'],
      'services' => ['required'],
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

    DB::beginTransaction();
    try {
      $CurrentPservice = Pservice::create([
        'people_id' 		  => $request['people_id'],
        'service_id'			=> $request['service_id'],
      ]);

      $CurService = Service::find($request['service_id']);

      $Globalaction = Globalaction::create([
        'user_id' 		          => $User->id,
        'action_id'		      	  => 10,
        'name'                  => 'Добавить служение',
        'date'                  => Carbon::now()->toDateString(),
        'discription'           => 'Добавить служение ' . $CurService->name . ' для ' . $Persone->FullName,
      ]);

      $CurrentAction = Serviceaction::create([
        'globalaction_id' => $Globalaction->id,
        'people_id' 		  => $request['people_id'],
        'source_id'			  => null,
        'target_id'			  => $request['target_id'],
      ]);
      DB::commit();
      return response()->json($CurrentPservice);
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

        DB::beginTransaction();
        try {
          $Globalaction = Globalaction::create([
            'user_id' 		          => $User->id,
            'action_id'		      	  => 11,
            'name'                  => 'Удалить служение',
            'date'                  => Carbon::now()->toDateString(),
            'discription'           => 'Удалить служение ' . $CurrentPservice->service->name . ' для ' . $Persone->FullName,
          ]);
      
          $CurrentAction = Serviceaction::create([
            'globalaction_id' => $Globalaction->id,
            'people_id' 		  => $CurrentPservice->people_id,
            'source_id' 			=> $CurrentPservice->service_id,
            'target_id'    			=> null,
          ]);
  
          $CurrentPservice->delete();
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
    $Persone = People::find($request['people_id'])->load('pservice');
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($permition->source_id == $Persone->prihod_id) $isAdmin = true;
      }
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    DB::beginTransaction();
    try {

      $baseServices = $Persone->pservice;
      $frontServices = $request['services'];

      // return $frontServices;
      $itemsToDelete = [];
      $itemsToAdd = [];

      foreach ($frontServices as $frontItem){
        $found = false;
        foreach ($baseServices as $baseItem){
          if ($frontItem["service_id"] === $baseItem["service_id"]) {
            $found = true;
          }
        }
        if (!$found) {
          array_push($itemsToAdd, $frontItem); 
        }
      }

      foreach ($baseServices as $baseItem) {
        $found = false;
        foreach ($frontServices as $frontItem){
          if ($frontItem["service_id"] === $baseItem["service_id"]) {
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
          'discription'           => 'Обновить служения для ' . $Persone->FullName,
        ]);

        foreach ($itemsToDelete as $item) {
          $CurrentPservice = Pservice::find($item);

          $CurrentAction = Serviceaction::create([
            'globalaction_id' => $Globalaction->id,
            'people_id' 		  => $CurrentPservice->people_id,
            'source_id'			  => $CurrentPservice->service_id,
            'target_id'			  => null,
          ]);
          $CurrentPservice->delete();
        }
  
        foreach ($itemsToAdd as $item) {
          $CurrentPservice = Pservice::create([
            'people_id' 		  => $Persone->id,
            'service_id'			=> $item['service_id'],
          ]);
    
          $CurrentAction = Serviceaction::create([
            'globalaction_id' => $Globalaction->id,
            'people_id' 		  => $Persone->id,
            'source_id'			  => null,
            'target_id'			  => $item['service_id'],
          ]);
        }
      }

      $Pservices = Pservice::where('people_id', $Persone->id)->get();

      DB::commit();
      return response()->json($Pservices);
    } catch (Exception $e){
      DB::rollBack();
      return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500); // разобраться с ошибкой
    }
  }

}
