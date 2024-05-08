<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use App\Models\User;

class UserController extends BaseController
{
    // use AuthorizesRequests, ValidatesRequests;

  public function __construct(){
  }

  public function index(Request $request){
    // return $request->user();
    $id = $request->user()->id;
    $user = User::with('permition')->find($id);
    return $user;
  }

}
