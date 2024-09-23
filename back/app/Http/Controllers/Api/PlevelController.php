<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Plevel;
use App\Models\People;

class PlevelController extends BaseController
{

  public function __construct() {
  }

  protected function storeValidator(array $data) {
    return Validator::make($data, [
      'people_id' => ['required'],
      'level_id'  => ['required'],
      'date'      => ['required'],
    ]);
  }


  public function index(Request $request) {
    // $User = auth()->user()->load('permition');
    // $Permitions = $User->permition;
    // $isAdmin = false;
    // $onlyUserOperation = false;
    // $serviceIDs = [];
    // $Persone = People::find($request['people_id'])->load('pservice');
    // $pesoneServices = $Persone->pservice;
    // foreach ($pesoneServices as $personeService) {
    //   array_push($serviceIDs, $personeService->service_id);
    // }

    // foreach ($Permitions as $permition) {
    //   if ($permition->type == 0) {
    //     $isAdmin = true;
    //     break;
    //   }
    //   if ($permition->type == 1) {
    //     if ($permition->source_id == $Persone->prihod_id) {
    //       $isAdmin = true;
    //       break;
    //     }
    //   }
    //   if ($permition->type == 2) {
    //     $isPresent = count(array_filter($serviceIDs, function($item) use ($permition) { return  $item == $permition->source_id; }));
    //     if ($isPresent) {
    //       $isAdmin = true;
    //       $onlyUserOperation = true;
    //       break;
    //     }
    //   }
    // }
    // if ($isAdmin) {
    //   $Plevel = Plevel::all();
    //   if ($onlyUserOperation) {
    //     return response()->json('QQQQQQQQQ');
    //     $Plevel = $Plevel->where('user_id', $User->user_id);
    //   }
    //   return $Plevel;
    // } else {
    //   return response()->json([]);
    // } 
  }

  public function store(Request $request) {
    $this->storeValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    $serviceIDs = [];
    $Persone = People::find($request['people_id'])->load('pservice');
    $pesoneServices = $Persone->pservice;
    foreach ($pesoneServices as $personeService) {
      array_push($serviceIDs, $personeService->service_id);
    }

    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($permition->source_id == $Persone->prihod_id) $isAdmin = true;
      }
      if ($permition->type == 2) {
        $isPresent = count(array_filter($serviceIDs, function($item) use ($permition) { return  $item == $permition->source_id; }));
        if ($isPresent) $isAdmin = true;
      }
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $CurrentPlevel = Plevel::create([
      'date'       		  => $request['date'],
      'people_id' 		  => $request['people_id'],
      'level_id'			  => $request['level_id'],
      'discription'     => $request['discription'],
      'user_id'         => $User->id,
    ]);
    return response()->json($CurrentPlevel);
  }

  public function update(Request $request, $id){
    $this->storeValidator($request->all())->validate();

    $Plevel = Plevel::find($id);

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    $serviceIDs = [];
    $Persone = People::find($Plevel->people_id)->load('pservice');
    $pesoneServices = $Persone->pservice;
    foreach ($pesoneServices as $personeService) {
      array_push($serviceIDs, $personeService->service_id);
    }

    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($permition->source_id == $Persone->prihod_id) $isAdmin = true;
      }
      if ($permition->type == 2) {
        $isPresent = count(array_filter($serviceIDs, function($item) use ($permition) { return  $item == $permition->source_id; }));
        if ($isPresent) $isAdmin = true;
      }
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $Plevel->date         = $request['date'];
    $Plevel->people_id    = $request['people_id'];
    $Plevel->level_id     = $request['level_id'];
    $Plevel->discription  = $request['discription'];
    $Plevel->save();

    $Plevel = Plevel::find($id);
    return $Plevel;
  }

  public function delete(Request $request) {
    $IDs = $request['ids'];
    foreach ($IDs as $id) {
      $CurrentPlevel = Plevel::Find($id);

      if ($CurrentPlevel) {
        $User = auth()->user()->load('permition');
        $Permitions = $User->permition;
        $isAdmin = false;
        $serviceIDs = [];
        $Persone = People::find($CurrentPlevel->people_id)->load('pservice');
        $pesoneServices = $Persone->pservice;
        foreach ($pesoneServices as $personeService) {
          array_push($serviceIDs, $personeService->service_id);
        }
    
        foreach ($Permitions as $permition) {
          if ($permition->type == 0) $isAdmin = true;
          if ($permition->type == 1) {
            if ($permition->source_id == $Persone->prihod_id) $isAdmin = true;
          }
          if ($permition->type == 2) {
            $isPresent = count(array_filter($serviceIDs, function($item) use ($permition) { return  $item == $permition->source_id; }));
            if ($isPresent) $isAdmin = true;
          }
        }
    
        if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);
  
        $CurrentPlevel->delete();
      }  
    };
    return response()->json('done');
  }
}
