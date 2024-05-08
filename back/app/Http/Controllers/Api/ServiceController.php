<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Service;

class ServiceController extends BaseController
{

  public function __construct() {
  }

  protected function storeValidator(array $data) {
    return Validator::make($data, [
      'name' => ['required'],
      'discription' => ['required'],
    ]);
  }


  public function index(Request $request) {
    $Service = Service::all();
    return $Service;
  }

  public function store(Request $request) {
    $this->storeValidator($request->all())->validate();

    $CurrentService = Service::create([
      'name' 		        => $request['name'],
      'discription'			=> $request['discription'],
      'status_id'       => $request['status_id'],
    ]);
    return response()->json($CurrentService);
  }

  public function update(Request $request, $id) {
    $CurrentService = Service::Find($id);
    if ($CurrentService) {
      $CurrentService->name         = $request->input('name');
      $CurrentService->discription  = $request->input('discription');
      $CurrentService->status_id    = $request->input('status_id');
      $CurrentService->save();
      return $CurrentService;
    } else {
      return response()->json([
          'message' => 'Not found'
      ], 404);
    }
  }

  public function delete(Request $request) {
    $IDs = $request['ids'];
    foreach ($IDs as $id) {
      $CurrentService = Service::Find($id);
      $stack = array();
      if ($CurrentService) {
        array_push($stack, $CurrentService->id);
        $CurrentService->delete();
      }  
    };
    $Service = Service::all();
    return $Service;
  }
}
