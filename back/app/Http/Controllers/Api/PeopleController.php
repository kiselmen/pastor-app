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
      'target_id' => ['required', 'numeric'],
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
      'target_id' => ['required', 'numeric'],
      'family_id' => ['required', 'numeric'],
    ]);
  }

  public function index(Request $request){
    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
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
    $serviceFilter = $request->query('_service');

    if ($isAdmin) {
      if ($serviceFilter) {
        $collection = People::whereHas('pservice', function ($query) use ($serviceFilter) {
            return $query->whereIn('service_id', [intval($serviceFilter)]);
          });
      } else {
        $collection = People::query();
      }
    } else {
      if ($serviceFilter) {
        $serviceIDs = [intval($serviceFilter)];
        $collection = People::
          whereHas('pservice', function ($query) use ($serviceIDs) {
            return $query->whereIn('service_id', $serviceIDs);
          });
      } else {
        $collection = People::
          whereIn('prihod_id', $prihodIDs)
          ->orWhereHas('pservice', function ($query) use ($serviceIDs) {
            return $query->whereIn('service_id', $serviceIDs);
          });
      }
    }
    $prihodFilter = $request->query('_prihod');
    if ($prihodFilter) {
      $collection->where('prihod_id', $prihodFilter);
    }

    $targetFilter = $request->query('_target');
    if ($targetFilter) {
      $collection->where('target_id', $targetFilter);
    }

    $searchFilter = $request->query('_search');
    if ($searchFilter) {
      $searchFilter = strtolower($searchFilter);
      $searchFilter = preg_replace("#([%_?+])#", "\\$1", $searchFilter);
      $collection->where('name', 'LIKE', '%'.$searchFilter.'%');
    }

    return $collection->get()->load('pservice', 'plevel', 'family');
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
    $Persone = People::find($CurrentPersone->id)->load('pservice', 'plevel', 'family');
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
