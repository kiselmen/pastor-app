<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\People;

class PeopleController extends BaseController
{
    // use AuthorizesRequests, ValidatesRequests;

  public function __construct(){
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
      'family_id' => ['required', 'numeric'],
    ]);
  }


  public function index(Request $request){
    $People = People::all()->load('pservice', 'family');
    return $People;
  }

  public function store(Request $request){
    $this->storeValidator($request->all())->validate();

    $filePath = null;
    $file = $request->image;

    if($file) {
      $img_name = md5(Carbon::now() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
      $filePath = Storage::disk('public')->putFileAs('img/peoples', $file, $img_name);
    } 

    // return response()->json();

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

    $Persone = People::find($CurrentPersone->id)->load('pservice', 'family');
    return response()->json($Persone);
  }

  public function update(Request $request, $id){
    $this->storeValidator($request->all())->validate();

    // return response()->json($request->input('death_date'));
    // return response()->json(!is_null($request->input('death_date')));

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
    $Persone = People::find($CurrentPersone->id)->load('pservice', 'family');

    return $Persone;
  }

}
