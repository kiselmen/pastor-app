<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Prihod;

class PrihodController extends BaseController
{

  public function __construct(){
  }

  protected function storeValidator(array $data){
    return Validator::make($data, [
      'name' => ['required'],
      'discription' => ['required'],
      'number' => ['required'],
    ]);
  }


  public function index(Request $request){
    $Prihod = Prihod::all();
    return $Prihod;
  }

  public function store(Request $request){
    $this->storeValidator($request->all())->validate();

    $CurrentPrihod = Prihod::create([
      'name' 		        => $request['name'],
      'discription'			=> $request['discription'],
      'number'    			=> $request['number'],
    ]);
    return response()->json($CurrentPrihod);
  }

  public function update(Request $request, $id){
    $this->storeValidator($request->all())->validate();
    $CurrentPrihod = Prihod::find($id);
    $CurrentPrihod->name 		      = $request['name'];
    $CurrentPrihod->number 		    = $request['number'];
    $CurrentPrihod->discription 	= $request['discription'];
    $CurrentPrihod->save();

    return $CurrentPrihod;
  }

}
