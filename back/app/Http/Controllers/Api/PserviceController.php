<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Pservice;

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


  public function index(Request $request) {
    $Pservice = Pservice::all();
    return $Pservice;
  }

  public function store(Request $request) {
    $this->storeValidator($request->all())->validate();

    $CurrentPservice = Pservice::create([
      'people_id' 		  => $request['people_id'],
      'service_id'			=> $request['service_id'],
    ]);
    return response()->json($CurrentPservice);
  }

  public function delete(Request $request) {
    $IDs = $request['ids'];
    foreach ($IDs as $id) {
      $CurrentPservice = Pservice::Find($id);
      // $stack = array();
      if ($CurrentPservice) {
        // array_push($stack, $CurrentPservice);
        $CurrentPservice->delete();
      }  
    };
    // $Service = Service::all();
    return response()->json('done');
  }
}
