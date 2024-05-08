<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Status;

class StatusController extends BaseController
{

  public function __construct(){
  }

  protected function storeValidator(array $data){
    return Validator::make($data, [
      'name' => ['required'],
    ]);
  }

  public function index(Request $request){
    $Status = Status::all();
    return $Status;
  }

  // public function store(Request $request){
  //   $this->storeValidator($request->all())->validate();

  //   $CurrentService = Service::create([
  //     'name' 		        => $request['name'],
  //     'discription'			=> $request['discription'],
  //     'status_id'       => $request['status_id'],
  //   ]);
  //   return response()->json($CurrentService);
  // }

}
