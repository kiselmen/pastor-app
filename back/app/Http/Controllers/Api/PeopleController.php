<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\People;
use App\Models\Family;
use App\Models\Prihod;
use App\Models\Peopleaction;
use App\Models\Prihodaction;
use App\Models\Familyaction;
use App\Models\Globalaction;

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
      // 'baptism_date' => ['required'],
      // 'live_index' => ['required'], 
      'live_town' => ['required'], 
      'live_street' => ['required'], 
      'live_house' => ['required'], 
      // 'home_phone' => ['required'], 
      // 'mobile_phone' => ['required'], 
      // 'email' => ['required'],
      'prihod_id' => ['required', 'numeric'],
      'action_id' => ['required', 'numeric'],
      'sex_id' => ['required', 'numeric'],
      'relation_id' => ['required', 'numeric'],
      'family_id' => ['required'],
    ]);
  }

  protected function updateValidator(array $data){
    return Validator::make($data, [
      'first_name' => ['required'],
      'name' => ['required'], 
      'patronymic' => ['required'], 
      'birthday_date' => ['required'], 
      // 'baptism_date' => ['required'],
      // 'live_index' => ['required'], 
      'live_town' => ['required'], 
      'live_street' => ['required'], 
      'live_house' => ['required'], 
      // 'home_phone' => ['required'], 
      // 'mobile_phone' => ['required'], 
      'email' => ['required'],
      'prihod_id' => ['required', 'numeric'],
      'sex_id' => ['required', 'numeric'],
      'family_id' => ['required', 'numeric'],
    ]);
  }

  protected function createFamilyValidator(array $data){
    return Validator::make($data, [
      'pair_id' => ['required', 'numeric'],
    ]);
  }

  protected function familyNameActionValidator(array $data){
    return Validator::make($data, [
      'familyNameAction_id' => ['required', 'numeric'],
    ]);
  }

  protected function MergePersonesNamesValidator(array $data){
    return Validator::make($data, [
      'man_new_name' => ['required'],
      'woman_new_name' => ['required'],
    ]);
  }
  
  protected function MergePersonesHusbandValidator(array $data){
    return Validator::make($data, [
      'man_first_name' => ['required'],
      'man_name' => ['required'], 
      'man_patronymic' => ['required'], 
      'man_birthday_date' => ['required'], 
      // 'man_live_index' => ['required'], 
      'man_live_town' => ['required'], 
      'man_live_street' => ['required'], 
      'man_live_house' => ['required'], 
      'man_sex_id' => ['required', 'numeric'],
      'man_relation_id' => ['required', 'numeric'],
    ]);
  }

  protected function MergePersonesHusbandIDValidator(array $data){
    return Validator::make($data, [
      'man_id' => ['required', 'numeric'],
    ]);
  }
  
  protected function MergePersoneWifeValidator(array $data){
    return Validator::make($data, [
      'woman_first_name' => ['required'],
      'woman_name' => ['required'], 
      'woman_patronymic' => ['required'], 
      'woman_birthday_date' => ['required'], 
      // 'woman_live_index' => ['required'], 
      'woman_live_town' => ['required'], 
      'woman_live_street' => ['required'], 
      'woman_live_house' => ['required'], 
      'woman_sex_id' => ['required', 'numeric'],
      'woman_relation_id' => ['required', 'numeric'],
    ]);
  }

  protected function MergePersonesWifeIDValidator(array $data){
    return Validator::make($data, [
      'woman_id' => ['required', 'numeric'],
    ]);
  }
  
  protected function SplitPersonsValidator(array $data){
    return Validator::make($data, [
      'family_name' => ['required'],
      // 'discription' => ['required'],
      'persone_id' => ['required', 'numeric'],
      'childrens' => ['required'],
    ]);
  }

  protected function MoveToNewPrihod(array $data){
    return Validator::make($data, [
      'prihod_id' => ['required', 'numeric'],
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

  public function indexAll(Request $request){
    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) $isAdmin = true;
    }
    if ($isAdmin) {
      $allPeople = People::query()
        ->select('id', 'name', 'first_name', 'patronymic', 'birthday_date', 'death_date', 'image_url', 'prihod_id', 'family_id', 'sex_id', 'relation_id')
        ->get()
        ->load('family');
      return $allPeople;
    } else {
      return [];
    }   
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

  public function bornReport(Request $request){
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

    DB::beginTransaction();
    try {
      if($file) {
        $img_name = md5(Carbon::now() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
        $filePath = Storage::disk('public')->putFileAs('img/peoples', $file, $img_name);
      }
      
      $actionID = $request['action_id'];
      if ($actionID == 0) {
        $actionName = 'Ввод начальных данных';
      } else if ($actionID == 1) {
        $actionName = 'Рождение';
      } else if ($actionID == 2) {
        $actionName = 'Вхождение в веру';
      } else if ($actionID == 3) {
        $actionName = 'Переехал из другой церкви';
      }
  
      $Globalaction = Globalaction::create([
        'user_id' 		          => $User->id,
        'action_id'		      	  => $actionID,
        'name'                  => $actionName,
        'date'                  => Carbon::now()->toDateString(),
        'discription'           => '',
      ]);

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
          'live_index'            => $request['live_index'],
          'live_town'             => $request['live_town'],
          'live_street'           => $request['live_street'],
          'live_house'            => $request['live_house'],
          'live_flat'             => $request['live_flat'],
          'home_phone'            => $request['home_phone'],
          'mobile_phone'          => $request['mobile_phone'],
          'discription'           => $request['discription'],
          'email'                 => $request['email'],
          'image_url'             => $filePath,
          'prihod_id'             => $request['prihod_id'],
          'target_id'             => $request['target_id'],
          'family_id'             => $request['family_id'],
          'sex_id'                => $request['sex_id'],
          'relation_id'           => $request['relation_id'],
          // 'relation_id'           => $relation, // Разобраться с этим полем.
        ]);
      } else {
        $this->familyValidator($request->all())->validate(); 

        $sex = $request['sex_id'];
        if ($sex == 1) {
          $relation = 0;
        } else {
          $relation = 1;
        }
        
        $CurrentFamily = Family::create([
          'name' 		        => $request['family_name'],
          'discription'			=> $request['family_discription'],
          // 'head_id'         => $request['head_id'],
        ]);
        $CurrentFamilyAction = Familyaction::create([
          'family_id'               => $CurrentFamily->id,
          'globalaction_id'         => $Globalaction->id,
          'field'                   => 'id',
          'source'                  => null,
          'target'                  => $CurrentFamily->id,
          ]);
        $CurrentPersone = People::create([
          'first_name' 		        => $request['first_name'],
          'name'		      	      => $request['name'],
          'patronymic'			      => $request['patronymic'],
          'birthday_date'         => $request['birthday_date'],
          'baptism_date'		      => $request['baptism_date'],
          'death_date'            => $request['death_date'],
          'image_url'             => $request['image_url'],
          'live_index'            => $request['live_index'],
          'live_town'             => $request['live_town'],
          'live_street'           => $request['live_street'],
          'live_house'            => $request['live_house'],
          'live_flat'             => $request['live_flat'],
          'home_phone'            => $request['home_phone'],
          'mobile_phone'          => $request['mobile_phone'],
          'discription'           => $request['discription'],
          'email'                 => $request['email'],
          'image_url'             => $filePath,
          'prihod_id'             => $request['prihod_id'],
          'target_id'             => $request['target_id'],
          'family_id'             => $CurrentFamily->id,
          'sex_id'                => $request['sex_id'],
          'relation_id'           => $relation,
        ]);
        $CurrentFamily->head_id = $CurrentPersone->id;
        $CurrentFamily->save(); 
      }
  
      $CurrentPersoneAction = Peopleaction::create([
        'people_id'               => $CurrentPersone->id,
        'globalaction_id'         => $Globalaction->id,
        'field'                   => 'id',
        'source'                  => null,
        'target'                  => $CurrentPersone->id,
      ]);

      $Globalaction->discription = $actionName . ' ' . $CurrentPersone->FullName;
      $Globalaction->save();

      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500);
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

    DB::beginTransaction();
    try {
      if($file) {
        $img_name = md5(Carbon::now() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
        $filePath = Storage::disk('public')->putFileAs('img/peoples', $file, $img_name);
      } 

      $actionID = $request['action_id'];
      if ($actionID == 4) {
        $actionName = 'Изменить данные';
      } else if ($actionID == 5) {
        $actionName = 'Оформить смерть';
      } else if ($actionID == 6) {
        $actionName = 'Выделить в новую семью';
      } else if ($actionID == 9) {
        $actionName = 'Сменить участок';
      } else if ($actionID == 14) {
        $actionName = 'Выбытие в другую церковь';
      }

      $Globalaction = Globalaction::create([
        'user_id' 		          => $User->id,
        'action_id'		      	  => $actionID,
        'name'                  => $actionName,
        'date'                  => Carbon::now()->toDateString(),
        'discription'           => '',
      ]);

      $fields = [];
      $familyFields = [];
      $CurrentPersone = People::find($id)->load('family');

      $sex = $CurrentPersone->sex_id;
      if ($sex == 1) {
        $relation = 0;
      } else {
        $relation = 1;
      }
      
      if ($actionID == 4) {
        if ($CurrentPersone->first_name !== $request['first_name']) {
          array_push($fields, ['id' => $id, 'key' => 'first_name', 'source' => $CurrentPersone->first_name, 'target' => $request['first_name']]);
          $CurrentPersone->first_name 		  = $request['first_name'];
        }
        if ($CurrentPersone->name !== $request['name']) {
          array_push($fields, ['id' => $id, 'key' => 'name', 'source' => $CurrentPersone->name, 'target' => $request['name']]);
          $CurrentPersone->name 	      	  = $request['name'];
        }
        if ($CurrentPersone->patronymic !== $request['patronymic']) {
          array_push($fields, ['id' => $id, 'key' => 'patronymic', 'source' => $CurrentPersone->patronymic, 'target' => $request['patronymic']]);
          $CurrentPersone->patronymic 		  = $request['patronymic'];
        }
        if (!is_null($request['birthday_date']) && $request['birthday_date'] !== 'null' && $CurrentPersone->birthday_date !== $request['birthday_date']) {
          array_push($fields, ['id' => $id, 'key' => 'birthday_date', 'source' => $CurrentPersone->birthday_date, 'target' => $request['birthday_date']]);
          $CurrentPersone->birthday_date 		= $request['birthday_date'];
        }
        if (!is_null($request['baptism_date']) && $request['baptism_date'] !== 'null' && $CurrentPersone->baptism_date !== $request['baptism_date']) {
          array_push($fields, ['id' => $id, 'key' => 'baptism_date', 'source' => $CurrentPersone->baptism_date, 'target' => $request['baptism_date']]);
          $CurrentPersone->baptism_date 		= $request['baptism_date'];
        }
        if ($CurrentPersone->live_index !== $request['live_index']) {
          array_push($fields, ['id' => $id, 'key' => 'live_index', 'source' => $CurrentPersone->live_index, 'target' => $request['live_index']]);
          $CurrentPersone->live_index 		  = $request['live_index'];
        }
        if ($CurrentPersone->live_town !== $request['live_town']) {
          array_push($fields, ['id' => $id, 'key' => 'live_town', 'source' => $CurrentPersone->live_town, 'target' => $request['live_town']]);
          $CurrentPersone->live_town 		  = $request['live_town'];
        }
        if ($CurrentPersone->live_street !== $request['live_street']) {
          array_push($fields, ['id' => $id, 'key' => 'live_street', 'source' => $CurrentPersone->live_street, 'target' => $request['live_street']]);
          $CurrentPersone->live_street 		  = $request['live_street'];
        }
        if ($CurrentPersone->live_house !== $request['live_house']) {
          array_push($fields, ['id' => $id, 'key' => 'live_house', 'source' => $CurrentPersone->live_house, 'target' => $request['live_house']]);
          $CurrentPersone->live_house 		  = $request['live_house'];
        }
        if ($CurrentPersone->live_flat !== $request['live_flat']) {
          array_push($fields, ['id' => $id, 'key' => 'live_flat', 'source' => $CurrentPersone->live_flat, 'target' => $request['live_flat']]);
          $CurrentPersone->live_flat 		  = $request['live_flat'];
        }
        if (!is_null($request['home_phone']) && $request['home_phone'] != 'null') {
          $home_phone = $request['home_phone'];
        } else {
          $home_phone = null;
        }
        if ($CurrentPersone->home_phone !== $home_phone) {
          array_push($fields, ['id' => $id, 'key' => 'home_phone', 'source' => $CurrentPersone->home_phone, 'target' => $home_phone]);
          $CurrentPersone->home_phone 		  = $home_phone;
        }
        if (!is_null($request['mobile_phone']) && $request['mobile_phone'] != 'null') {
          $mobile_phone = $request['mobile_phone'];
        } else {
          $mobile_phone = null;
        }
        if ($CurrentPersone->mobile_phone !== $mobile_phone) {
          array_push($fields, ['id' => $id, 'key' => 'mobile_phone', 'source' => $CurrentPersone->mobile_phone, 'target' => $mobile_phone]);
          $CurrentPersone->mobile_phone 		= $mobile_phone;
        }
        if ($CurrentPersone->discription !== $request['discription']) {
          array_push($fields, ['id' => $id, 'key' => 'discription', 'source' => $CurrentPersone->discription, 'target' => $request['discription']]);
          $CurrentPersone->discription 		  = $request['discription'];
        }
        if (!is_null($request['email']) && $request['email'] !== "null" && $CurrentPersone->email !== $request['email']) {
          array_push($fields, ['id' => $id, 'key' => 'email', 'source' => $CurrentPersone->email, 'target' => $request['email']]);
          $CurrentPersone->email 		        = $request['email'];
        }
        if (!is_null($request['family_id']) && $request['family_id'] !== "null" && $CurrentPersone->family_id !== (int)$request['family_id']) {
          array_push($fields, ['id' => $id, 'key' => 'family_id', 'source' => $CurrentPersone->family_id, 'target' => $request['family_id']]);
          $CurrentPersone->family_id        = $request['family_id'];
        }
        if ($CurrentPersone->sex_id !== (int)$request['sex_id']) {
          array_push($fields, ['id' => $id, 'key' => 'sex_id', 'source' => $CurrentPersone->sex_id, 'target' => $request['sex_id']]);
          $CurrentPersone->sex_id           = $request['sex_id'];
        }
        if ($file) {
          if ($CurrentPersone->image_url) {
            Storage::disk('public')->delete($CurrentPersone->image_url);
          }
          array_push($fields, ['id' => $id, 'key' => 'image_url', 'source' => $CurrentPersone->image_url, 'target' => $filePath]);
          $CurrentPersone->image_url 		    = $filePath;
        }
        $CurrentPersone->save();
      } else if ($actionID == 5) {
        if (!is_null($request['death_date']) && $request['death_date'] !== "null" && $CurrentPersone->death_date !== $request['death_date']) {
          array_push($fields, ['id' => $id, 'key' => 'death_date', 'source' => $CurrentPersone->death_date, 'target' => $request['death_date']]);
          $CurrentPersone->death_date 		= $request['death_date'];
        }
        $CurrentPersone->save();
      } else if ($actionID == 6) {
        $this->familyValidator($request->all())->validate(); 

        $CurrentFamily = Family::create([
          'name' 		        => $request['family_name'],
          'discription'			=> $request['family_discription'],
          'head_id'         => $CurrentPersone->id,
        ]);
        
        array_push($familyFields, ['id' => $CurrentFamily->id, 'key' => 'id', 'source' => null, 'target' => $CurrentFamily->id]);

        array_push($fields, ['id' => $id, 'key' => 'family_id', 'source' => $CurrentPersone->family_id, 'target' => $CurrentFamily->id]);
        array_push($fields, ['id' => $id, 'key' => 'relation_id', 'source' => $CurrentPersone->relation_id, 'target' => $relation]);

        $CurrentPersone->family_id = $CurrentFamily->id;
        $CurrentPersone->relation_id = $relation;
        $CurrentPersone->save(); 
      } else if ($actionID == 9 || $actionID == 14) {

        $this->MoveToNewPrihod($request->all())->validate();

        $Prihod = Prihod::find($request['prihod_id']);

        if ($actionID == 9) {
          if ($Prihod->is_global) {
            return response()->json(['message' => 'Используйте выбытие в другоую церковь'], 403);
          }
        }

        if ($actionID == 14) {
          if (!$Prihod->is_global) {
            return response()->json(['message' => 'Используйте сменить участок'], 403);
          }
        }
        
        $PrihodAction = Prihodaction::create([
          'globalaction_id'         => $Globalaction->id,
          'people_id'               => $CurrentPersone->id,
          'source_id'               => $CurrentPersone->prihod_id,
          'target_id'               => $request['prihod_id'],
       ]);
       array_push($fields, ['id' => $id, 'key' => 'prihod_id', 'source' => $CurrentPersone->prihod_id, 'target' => $request['prihod_id']]);
       $CurrentPersone->prihod_id = $request['prihod_id'];
       $CurrentPersone->save(); 
      }
      if (count($fields)) {
        foreach ($fields as $field){
          $CurrentPersoneAction = Peopleaction::create([
            'people_id'               => $field['id'],
            'globalaction_id'         => $Globalaction->id,
            'field'                   => $field['key'],
            'source'                  => $field['source'],
            'target'                  => $field['target'],
          ]);
        }
      }  
      if (count($familyFields)) {
        foreach ($familyFields as $field){
          $CurrentFamilyAction = Familyaction::create([
            'family_id'               => $field['id'],
            'globalaction_id'         => $Globalaction->id,
            'field'                   => $field['key'],
            'source'                  => $field['source'],
            'target'                  => $field['target'],
          ]);
        }
      }
      $Globalaction->discription = $actionName . ' ' . $CurrentPersone->FullName;
      $Globalaction->save();
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500); // разобраться с ошибкой
    }

    $Persone = People::find($CurrentPersone->id)->load('pservice', 'plevel', 'family');

    return $Persone;
  }

  public function mergePeoples(Request $request){
    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) $isAdmin = true;
    }
    if (!$isAdmin) {
      return response()->json(['message' => 'Do not have permitions'], 403);
    }
    DB::beginTransaction();
    $fields = [];
    $familyFields = [];
    $prihodFields = [];
    $actionID = 8;
    $actionName = 'Оформить развод';
    $PeopleForReturn = [];

    try {
      $Globalaction = Globalaction::create([
        'user_id' 		          => $User->id,
        'action_id'		      	  => $actionID,
        'name'                  => $actionName,
        'date'                  => Carbon::now()->toDateString(),
        'discription'           => '',
      ]);
      $Husband = null;

      if ($request->has('man_id')) {
        $this->MergePersonesHusbandIDValidator($request->all())->validate();
        $HusbandID = $request['man_id'];
        $Husband = People::find($HusbandID);
        if (!$Husband) return response()->json(['message' => 'Persone with ID ' . $HusbandID . ' is not present '], 403);
        if ($Husband->Under18) return response()->json(['message' => $Husband->FullName.' must be more than 18 yers old'], 403);
        if (count($Husband->Pair)) return response()->json(['message' => 'Persone ' . $Husband->FullName.' already have pair '], 403);
        if (count($Husband->ChildrenMore18YearsOld)) return response()->json(['message' => 'Persone ' . $Husband->FullName.' have childrens more then 18 years old '], 403);

        if ($request->has('woman_id')) {
          $this->MergePersonesWifeIDValidator($request->all())->validate();
          $WifeID = $request['woman_id'];
          $Wife = People::find($WifeID);
          if (!$Wife) return response()->json(['message' => 'Persone with ID ' . $WifeID . ' is not present '], 403);
          if ($Wife->Under18) return response()->json(['message' => $Wife->FullName.' must be more than 18 yers old'], 403);
          if (count($Wife->Pair)) return response()->json(['message' => 'Persone ' . $Wife->FullName.' already have pair '], 403);
          if (count($Wife->ChildrenMore18YearsOld)) return response()->json(['message' => 'Persone ' . $Wife->FullName.' have childrens more then 18 years old '], 403);

          $WifeFamily = Family::find($Wife->family_id);
          // array_push($familyFields, ['id' => $Husband->family_id, 'key' => null, 'value' => null]);
          array_push($familyFields, ['id' => $Wife->family_id, 'key' => 'is_passive', 'source' => $WifeFamily->is_passive, 'target' => 1, ]);
          $WifeFamily->is_passive = 1;
          $WifeFamily->save();

          $this->MergePersonesNamesValidator($request->all())->validate();
          if ($request['man_new_name'] != $Husband->name) {
            array_push($fields, ['id' => $Husband->id, 'key' => 'name', 'source' => $Husband->name, 'target' => $request['man_new_name']]);
            array_push($PeopleForReturn, $Husband->id);
            $Husband->name = $request['man_new_name'];
            $Husband->save();
          }

          foreach ($Wife->AllChildrens as $child) {
            array_push($fields, ['id' => $child->id, 'key' => 'family_id', 'source' => $child->family_id, 'target' => $Husband->family_id]);
            array_push($PeopleForReturn, $child->id);
            $CurrChild = People::find($child->id);
            $CurrChild->family_id = $Husband->family_id;
            //   Разобраться с приходами !!!
            if ($Husband->prihod_id != $Wife->prihod_id) {
              array_push($fields, ['id' => $child->id, 'key' => 'prihod_id', 'source' => $child->prihod_id, 'target' => $Husband->prihod_id]);
              array_push($prihodFields, ['people_id' => $child->id, 'source_id' => $child->prihod_id, 'target_id' => $Husband->prihod_id]);
            }
            $CurrChild->save();
          }

          array_push($fields, ['id' => $Wife->id, 'key' => 'family_id', 'source' => $Wife->family_id, 'target' => $Husband->family_id]);
          array_push($PeopleForReturn, $Wife->id);
          $Wife->family_id = $Husband->family_id;
          if ($Husband->prihod_id != $Wife->prihod_id) {
            array_push($fields, ['id' => $child->id, 'key' => 'prihod_id', 'source' => $Wife->prihod_id, 'target' => $Husband->prihod_id]);
            array_push($prihodFields, ['people_id' => $Wife->id, 'source_id' => $Wife->prihod_id, 'target_id' => $Husband->prihod_id]);
          }
          if ($request['woman_new_name'] != $Wife->name) {
            array_push($fields, ['id' => $Wife->id, 'key' => 'name', 'source' => $Wife->name, 'target' => $request['woman_new_name']]);
            array_push($PeopleForReturn, $Wife->id);
            $Wife->name = $request['woman_new_name'];
          }  
          $Wife->save();

        } else {
          $this->MergePersoneWifeValidator($request->all())->validate();

          $womanFilePath = null;
          $file = $request->woman_image;
          if($file) {
            $woman_img_name = md5(Carbon::now() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $womanFilePath = Storage::disk('public')->putFileAs('img/peoples', $file, $woman_img_name);
          }

          $Wife = People::create([
            'first_name' 		        => $request['woman_first_name'],
            'name'		      	      => $request['woman_name'],
            'patronymic'			      => $request['woman_patronymic'],
            'birthday_date'         => $request['woman_birthday_date'],
            'baptism_date'		      => $request['woman_baptism_date'],
            'death_date'            => $request['woman_death_date'],
            'image_url'             => $request['woman_image_url'],
            'live_index'            => $request['woman_live_index'],
            'live_town'             => $request['woman_live_town'],
            'live_street'           => $request['woman_live_street'],
            'live_house'            => $request['woman_live_house'],
            'live_flat'             => $request['woman_live_flat'],
            'home_phone'            => $request['woman_home_phone'],
            'mobile_phone'          => $request['woman_mobile_phone'],
            'discription'           => $request['woman_discription'],
            'email'                 => $request['woman_email'],
            'image_url'             => $womanFilePath,
            'prihod_id'             => $Husband->prihod_id,
            'family_id'             => $Husband->family_id,
            'sex_id'                => $request['woman_sex_id'],
            'relation_id'           => $request['woman_relation_id'],
          ]);
          array_push($fields, ['id' => $Wife->id, 'key' => 'id', 'source' => null, 'target' => $Wife->id]);
          array_push($fields, ['id' => $Wife->id, 'key' => 'family_id', 'source' => null, 'target' => $Husband->family_id]);
          array_push($PeopleForReturn, $Wife->id);
          array_push($prihodFields, ['people_id' => $Wife->id, 'source_id' => null, 'target_id' => $Husband->prihod_id]);
        }
      } else {
        $this->MergePersonesWifeIDValidator($request->all())->validate();
        $this->MergePersonesHusbandValidator($request->all())->validate();
        $WifeID = $request['woman_id'];
        $Wife = People::find($WifeID);
        if (!$Wife) return response()->json(['message' => 'Persone with ID ' . $WifeID . ' is not present '], 403);
        if ($Wife->Under18) return response()->json(['message' => $Wife->FullName.' must be more than 18 yers old'], 403);
        if (count($Wife->Pair)) return response()->json(['message' => 'Persone ' . $Wife->FullName.' already have pair '], 403);
        if (count($Wife->ChildrenMore18YearsOld)) return response()->json(['message' => 'Persone ' . $Wife->FullName.' have childrens more then 18 years old '], 403);
  
        $manFilePath = null;
        $file = $request->man_image;
        if($file) {
          $man_img_name = md5(Carbon::now() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
          $manFilePath = Storage::disk('public')->putFileAs('img/peoples', $file, $man_img_name);
        }

        $Husband = People::create([
          'first_name' 		        => $request['man_first_name'],
          'name'		      	      => $request['man_name'],
          'patronymic'			      => $request['man_patronymic'],
          'birthday_date'         => $request['man_birthday_date'],
          'baptism_date'		      => $request['man_baptism_date'],
          'death_date'            => $request['man_death_date'],
          'image_url'             => $request['man_image_url'],
          'live_index'            => $request['man_live_index'],
          'live_town'             => $request['man_live_town'],
          'live_street'           => $request['man_live_street'],
          'live_house'            => $request['man_live_house'],
          'live_flat'             => $request['man_live_flat'],
          'home_phone'            => $request['man_home_phone'],
          'mobile_phone'          => $request['man_mobile_phone'],
          'discription'           => $request['man_discription'],
          'email'                 => $request['man_email'],
          'image_url'             => $manFilePath,
          'prihod_id'             => $Wife->prihod_id,
          'family_id'             => $Wife->family_id,
          'sex_id'                => $request['man_sex_id'],
          'relation_id'           => $request['man_relation_id'],
        ]);
        array_push($fields, ['id' => $Husband->id, 'key' => 'id', 'source' => null, 'target' => $Husband->id]);
        array_push($fields, ['id' => $Husband->id, 'key' => 'family_id', 'source' => null, 'target' => $Wife->family_id]);
        array_push($PeopleForReturn, $Husband->id);
        array_push($prihodFields, ['people_id' => $Husband->id, 'source_id' => null, 'target_id' => $Wife->prihod_id]);

        if ($request['woman_new_name'] != $Wife->name) {
          array_push($fields, ['id' => $Wife->id, 'key' => 'name', 'source' => $Wife->name, 'target' => $request['woman_new_name']]);
          array_push($PeopleForReturn, $Wife->id);
          $Wife->name = $request['woman_new_name'];
          $Wife->save();
        } 

        array_push($familyFields, ['id' => $Wife->family_id, 'key' => 'head_id', 'source' => $Wife->family->head_id, 'target' => $Husband->id]);
        $WifeFamily = Family::find($Wife->family_id);
        $WifeFamily->head_id = $Husband->id;
        $WifeFamily->save();

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
      $Globalaction->discription = 'Оформлен брак между ' . $Husband->FullName . ' и ' . $Wife->FullName;
      $Globalaction->save();

      DB::commit();
    } catch (Exception $e){
      DB::rollBack();
      return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500); // разобраться с ошибкой
    }
    $uniqueIds = array_unique($PeopleForReturn);
    $updatedPeoples = People::whereIn('id', $uniqueIds)->get()->load('pservice', 'ptarget', 'plevel', 'family');    
    return $updatedPeoples;
  }

  public function splitPeoples(Request $request){
    $User = auth()->user()->load('permition');
    $Permitions = $User->permition;
    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
      if ($permition->type == 1) $isAdmin = true;
    }
    if (!$isAdmin) {
      return response()->json(['message' => 'Do not have permitions'], 403);
    }
    DB::beginTransaction();
    $fields = [];
    $familyFields = [];
    $actionID = 8;
    $actionName = 'Оформить развод';
    $PeopleForReturn = [];

    try {
      $Globalaction = Globalaction::create([
        'user_id' 		          => $User->id,
        'action_id'		      	  => $actionID,
        'name'                  => $actionName,
        'date'                  => Carbon::now()->toDateString(),
        'discription'           => '',
      ]);

      $this->SplitPersonsValidator($request->all())->validate();
      $PersoneID = $request['persone_id'];
      $Persone = People::find($PersoneID);
      if (!$Persone) return response()->json(['message' => 'Persone with ID ' . $PersoneID . ' is not present '], 403);
      if ($Persone->Under18) return response()->json(['message' => $Persone->FullName.' must be more than 18 yers old'], 403);
      if (!count($Persone->Pair)) return response()->json(['message' => 'Persone ' . $Persone->FullName.' did not have a pair '], 403);
      if (count($Persone->ChildrenMore18YearsOld)) return response()->json(['message' => 'Persone ' . $Persone->FullName.' have childrens more then 18 years old '], 403);

      $PersoneFamily = Family::find($Persone->family_id);
      foreach ($PersoneFamily->people as $fPersone) {
        array_push($PeopleForReturn, $fPersone->id);
      }
  
      $CurrentFamily = Family::create([
        'name' 		        => $request['family_name'],
        'discription'			=> $request['family_discription'],
        'head_id'         => $Persone->id,
      ]);
      array_push($familyFields, ['id' => $CurrentFamily->id, 'key' => 'id', 'source' => null, 'target' => $CurrentFamily->id]);

      array_push($fields, ['id' => $PersoneID, 'key' => 'family_id', 'source' => $Persone->family_id, 'target' => $CurrentFamily->id]);
      $Persone->family_id = $CurrentFamily->id;
      $Persone->save();

      $Childrens = $request['childrens'];
      foreach ($Childrens as $Child) {
        $currChild = People::find($Child['id']);
        array_push($fields, ['id' => $currChild->id, 'key' => 'family_id', 'source' => $currChild->family_id, 'target' => $CurrentFamily->id]);
        $currChild->family_id = $CurrentFamily->id;
        $currChild->save();
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

      $Globalaction->discription = 'Оформлен развод ' . $Persone->FullName;
      $Globalaction->save();

      DB::commit();
    } catch (Exception $e){
      DB::rollBack();
      return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500); // разобраться с ошибкой
    }
    $uniqueIds = array_unique($PeopleForReturn);
    $updatedPeoples = People::whereIn('id', $uniqueIds)->get()->load('pservice', 'ptarget', 'plevel', 'family');    
    return $updatedPeoples;
  }

}
