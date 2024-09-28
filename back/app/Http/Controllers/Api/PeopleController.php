<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\People;
use App\Models\Family;

class PeopleController extends BaseController
{
    // use AuthorizesRequests, ValidatesRequests;

  public function __construct(){
  }

  protected function familyValidator(array $data){
    return Validator::make($data, [
      'family_name' => ['required'],
      'family_discription' => ['required'], 
    ]);
  }

  protected function storeValidator(array $data){
    return Validator::make($data, [
      'first_name' => ['required'],
      'name' => ['required'], 
      'patronymic' => ['required'], 
      'birthday_date' => ['required'], 
      'baptism_date' => ['required'],
      'live_addres' => ['required'], 
      'home_phone' => ['required'], 
      'mobile_phone' => ['required'], 
      'email' => ['required'],
      'prihod_id' => ['required', 'numeric'],
      'sex_id' => ['required', 'numeric'],
      'family_id' => ['required'],
    ]);
  }

  protected function updateValidator(array $data){
    return Validator::make($data, [
      'first_name' => ['required'],
      'name' => ['required'], 
      'patronymic' => ['required'], 
      'birthday_date' => ['required'], 
      'baptism_date' => ['required'],
      'live_addres' => ['required'], 
      'home_phone' => ['required'], 
      'mobile_phone' => ['required'], 
      'email' => ['required'],
      'prihod_id' => ['required', 'numeric'],
      'sex_id' => ['required', 'numeric'],
      'family_id' => ['required', 'numeric'],
    ]);

  }

  public function index(Request $request){
    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    $onlyUserOperation = false;
    $prihodIDs = [];
    $serviceIDs = [];
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        array_push($prihodIDs, $permition->source_id);
      }
      if ($permition->type == 2) {
        array_push($serviceIDs, $permition->source_id);
      }
    }

    if ($isAdmin) {
        $collection = People::query();
        $collectionService = null;
    } else {
        $collection = People::whereIn('prihod_id', $prihodIDs);
        $collectionService = People::WhereHas('pservice', function ($query) use ($serviceIDs) {
            return $query->whereIn('service_id', $serviceIDs);
          });
    }

    $targetFilter = $request->query('_target');
    // if ($targetFilter) {
    //   $collection = $collection->where('target_id', '=', $targetFilter);
    //   if ($collectionService) $collectionService = $collectionService->where('target_id', '=', $targetFilter);
    // }

    if ($targetFilter) {
      $collection = $collection->
        whereHas('ptarget', function ($query) use ($targetFilter) {
          return $query->whereIn('target_id', [intval($targetFilter)]);
        });
      if ($collectionService) {
        $collectionService = $collectionService->
          whereHas('ptarget', function ($query) use ($targetFilter) {
            return $query->whereIn('target_id', [intval($targetFilter)]);
          });
      }
    }

    $prihodFilter = $request->query('_prihod');
    if ($prihodFilter) {
      $collection = $collection->where('prihod_id', $prihodFilter);
      if ($collectionService) $collectionService = $collectionService->where('prihod_id', $prihodFilter);
    }

    $serviceFilter = $request->query('_service');
    if ($serviceFilter) {
      $collection = $collection->   
        whereHas('pservice', function ($query) use ($serviceFilter) {
          return $query->whereIn('service_id', [intval($serviceFilter)]);
        });
  
      if ($collectionService) {
        $collectionService = $collectionService->   
          whereHas('pservice', function ($query) use ($serviceFilter) {
            return $query->whereIn('service_id', [intval($serviceFilter)]);
          });
      } 
    }

    $searchFilter = $request->query('_search');
    if ($searchFilter) {
      $searchFilter = strtolower($searchFilter);
      $searchFilter = preg_replace("#([%_?+])#", "\\$1", $searchFilter);
      $collection = $collection->where('name', 'LIKE', '%'.$searchFilter.'%');
      if ($collectionService) {
        $collectionService = $collectionService->where('name', 'LIKE', '%'.$searchFilter.'%');
      }
    }

    $collection = $collection->get()->load('pservice', 'plevel', 'ptarget', 'family');
    if ($collectionService) {
      $collectionService = $collectionService->get()->load('pservice', 'plevel', 'ptarget', 'family');
      $collection = $collection->merge($collectionService);
    }
    return $collection;
  }

  public function birthday(Request $request){
    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    $onlyUserOperation = false;
    $prihodIDs = [];
    $serviceIDs = [];
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        array_push($prihodIDs, $permition->source_id);
      }
      if ($permition->type == 2) {
        array_push($serviceIDs, $permition->source_id);
      }
    }

    if ($isAdmin) {
        $collection = People::query();
        $collectionService = null;
    } else {
        $collection = People::whereIn('prihod_id', $prihodIDs);
        $collectionService = People::WhereHas('pservice', function ($query) use ($serviceIDs) {
            return $query->whereIn('service_id', $serviceIDs);
          });
    }

    $collection = $collection->get()->load('pservice', 'ptarget', 'plevel', 'family');
    if ($collectionService) {
      $collectionService = $collectionService->get()->load('pservice', 'ptarget', 'plevel', 'family');
      $collection = $collection->merge($collectionService);
    }

    $currentDate = Carbon::now();
    $weekBefore = $currentDate->copy()->subWeek();
    $weekAfter = $currentDate->copy()->addWeek();

    $collection = $collection->filter(function ($model) use ($weekBefore, $weekAfter) {
      $birthday = Carbon::parse($model->birthday_date)->format('m-d');

      $before = $weekBefore->format('m-d');
      $after = $weekAfter->format('m-d');

      return $birthday >= $before && $birthday <= $after;
    });

    return [...$collection];
  }

  public function born_report(Request $request){
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
        $collection = People::query();
    } else {
        $collection = People::whereIn('prihod_id', $prihodIDs);
    }

    $start = $request['start'];    
    $end = $request['end'];
    $rip = $request['rip'];    

    if ($rip) {
      $collection = $collection->whereBetween('death_date', [$start, $end])->get()->load('family');
    } else {
      $collection = $collection->whereBetween('birthday_date', [$start, $end])->get()->load('family');
    }
    
    return $collection;
  }

  public function show($id){
    $findPersone = People::find($id);
    if ($findPersone) {
      $findPersone = $findPersone->load('pservice', 'ptarget', 'plevel', 'family');;
    } else {
      return response()->json(['message' => 'Persone not found'], 404);
    }

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;

    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($permition->source_id == $findPersone->prihod_id) $isAdmin = true;
      }
      if ($permition->type == 2) {
        $PServices = $findPersone->pservice;
        foreach ($PServices as $Pservice) {
          if ($permition->source_id == $Pservice->service_id) $isAdmin = true;
        }
        // array_push($serviceIDs, $permition->source_id);
      }
    }

    if ($isAdmin) {
      // $findPersone->pservice = $PersoneServices;
      return $findPersone;
    } else {
      return response()->json(['message' => 'Do not have permitions'], 403);
    }

  }

  public function store(Request $request){
    $this->storeValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($permition->source_id == $request['prihod_id']) $isAdmin = true;
      }
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $filePath = null;
    $file = $request->image;

    if($file) {
      $img_name = md5(Carbon::now() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
      $filePath = Storage::disk('public')->putFileAs('img/peoples', $file, $img_name);
    }
    
    $familyID = $request['family_id'];
    if ($familyID != 'null') {
      $CurrentPersone = People::create([
        'first_name' 		        => $request['first_name'],
        'name'		      	      => $request['name'],
        'patronymic'			      => $request['patronymic'],
        'birthday_date'         => $request['birthday_date'],
        'baptism_date'		      => $request['baptism_date'],
        'death_date'            => $request['death_date'],
        'image_url'             => $request['image_url'],
        'live_addres'           => $request['live_addres'],
        'home_phone'            => $request['home_phone'],
        'mobile_phone'          => $request['mobile_phone'],
        'email'                 => $request['email'],
        'image_url'             => $filePath,
        'prihod_id'             => $request['prihod_id'],
        'target_id'             => $request['target_id'],
        'family_id'             => $request['family_id'],
        'sex_id'                => $request['sex_id'],
      ]);
    } else {
      $this->familyValidator($request->all())->validate(); 
      
      $CurrentFamily = Family::create([
        'name' 		        => $request['name'],
        'discription'			=> $request['discription'],
        // 'head_id'         => $request['head_id'],
      ]);
      $CurrentPersone = People::create([
        'first_name' 		        => $request['first_name'],
        'name'		      	      => $request['name'],
        'patronymic'			      => $request['patronymic'],
        'birthday_date'         => $request['birthday_date'],
        'baptism_date'		      => $request['baptism_date'],
        'death_date'            => $request['death_date'],
        'image_url'             => $request['image_url'],
        'live_addres'           => $request['live_addres'],
        'home_phone'            => $request['home_phone'],
        'mobile_phone'          => $request['mobile_phone'],
        'email'                 => $request['email'],
        'image_url'             => $filePath,
        'prihod_id'             => $request['prihod_id'],
        'target_id'             => $request['target_id'],
        'family_id'             => $CurrentFamily->id,
      ]);
      $CurrentFamily->head_id = $CurrentPersone->id;
      $CurrentFamily->save(); 
    }
    $Persone = People::find($CurrentPersone->id)->load('pservice', 'ptarget', 'plevel', 'family');
    return response()->json($Persone);
  }

  public function update(Request $request, $id){
    $this->updateValidator($request->all())->validate();

    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) {
        if ($permition->source_id == $request['prihod_id']) $isAdmin = true;
      }
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $filePath = null;
    $file = $request->image;

    if($file) {
      $img_name = md5(Carbon::now() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
      $filePath = Storage::disk('public')->putFileAs('img/peoples', $file, $img_name);
    } 

    $CurrentPersone = People::find($id);
    $CurrentPersone->first_name 		  = $request['first_name'];
    $CurrentPersone->name 	      	  = $request['name'];
    $CurrentPersone->patronymic 		  = $request['patronymic'];
    $CurrentPersone->birthday_date 		= $request['birthday_date'];
    $CurrentPersone->baptism_date 		= $request['baptism_date'];
    if (!is_null($request['death_date']) || $request['death_date'] !== "null") {
      $CurrentPersone->death_date 		  = $request['death_date'];
    } 
    if (!is_null($request['live_addres']) || $request['death_date'] !== "null") {
      $CurrentPersone->live_addres 		  = $request['live_addres'];
    }
    if (!is_null($request['home_phone']) || $request['death_date'] !== "null") {
      $CurrentPersone->home_phone 		  = $request['home_phone'];
    }
    if (!is_null($request['mobile_phone']) || $request['death_date'] !== "null") {
      $CurrentPersone->mobile_phone 		= $request['mobile_phone'];
    }
    $CurrentPersone->email 		        = $request['email'];
    $CurrentPersone->prihod_id        = $request['prihod_id'];
    $CurrentPersone->target_id        = $request['target_id'];
    $CurrentPersone->family_id        = $request['family_id'];
    $CurrentPersone->sex_id           = $request['sex_id'];
    if ($file) {
      if ($CurrentPersone->image_url) {
        Storage::disk('public')->delete($CurrentPersone->image_url);
      }
      $CurrentPersone->image_url 		    = $filePath;
    }
    $CurrentPersone->save();
    $Persone = People::find($CurrentPersone->id)->load('pservice', 'plevel', 'family');

    return $Persone;
  }

}
