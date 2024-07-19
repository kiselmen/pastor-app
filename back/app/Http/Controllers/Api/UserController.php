<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use App\Models\User;
use App\Models\People;
use App\Models\Permition;

class UserController extends BaseController
{
    // use AuthorizesRequests, ValidatesRequests;

  public function __construct(){
  }

  protected function storeValidator(array $data){
    return Validator::make($data, [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'persone_id' => ['required'],
    ]);
  }

  protected function passwordValidator(array $data){
    return Validator::make($data, [
      'user_id' => ['required'],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);
  }

  public function index(Request $request){
    $id = $request->user()->id;
    $user = User::with('permition')->find($id);
    return $user;
  }

  public function store(Request $request){
    $this->storeValidator($request->all())->validate();

    $CurUser = auth()->user()->load('permition');
    $Permitions = $CurUser->permition;
    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    $persone = People::find($request['persone_id']);
    $persone->user_id = $user->id;
    $persone->save();

    $permitions = $request['permitions'];
    foreach ($permitions as $permition) {
      Permition::create([
        'type' => $permition['id'],
        'user_id' => $user->id,
        'source_id' => $permition['source_id'],
      ]);
    };

    $persone = People::find($request['persone_id'])->load('pservice', 'plevel', 'family');
    return $persone;
  }

  public function change(Request $request){
    $this->passwordValidator($request->all())->validate();

    $CurUser = auth()->user()->load('permition');
    $Permitions = $CurUser->permition;
    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
    }

    if ($CurUser->id == $request['user_id']) $isAdmin = true;

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $UserForUpdate = User::find($request['user_id']);
    $UserForUpdate->password = Hash::make($request->password);
    $UserForUpdate->save();
    return $UserForUpdate;
  }

  public function permitions(Request $request, $id) {
    $CurUser = auth()->user()->load('permition');
    $Permitions = $CurUser->permition;
    $isAdmin = false;
    foreach ($Permitions as $permition) {
      if ($permition->type == 0) $isAdmin = true;
    }

    if (!$isAdmin) return response()->json(['message' => 'Do not have permitions'], 403);

    $UserForUpdate = User::find($id)->load('permition');
    return $UserForUpdate->permition;

  }

}
