<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Family;
use App\Models\People;

class FamilyController extends BaseController
{

  public function __construct(){
  }

  protected function storeValidator(array $data){
    return Validator::make($data, [
      'name' => ['required'],
      // 'discription' => ['required'],
    ]);
  }


  public function index(Request $request){
    $Family = Family::all();
    return $Family;
  }

  public function store(Request $request){
    $this->storeValidator($request->all())->validate();

    $CurrentFamily = Family::create([
      'name' 		        => $request['name'],
      'discription'			=> $request['discription'],
      'head_id'       => $request['head_id'],
    ]);

    $RequestMainPersone = $request['head_id'];
    if (!is_null($RequestMainPersone)) {
      $MainPersone = People::find($RequestMainPersone);
      if ($MainPersone) {
        $MainPersone->family_id = $CurrentFamily->id;
        $MainPersone->save();
      }
    }

    $Candidates =  $request['candidates'];
    foreach ($Candidates as $Candidate){
    $Persone = People::find($Candidate['persone_id']);
      if ($Persone) {
        $Persone->family_id = $CurrentFamily->id;
        $Persone->save();
      }
    }

    return response()->json($CurrentFamily);
  }

  public function update(Request $request, $id){
    $this->storeValidator($request->all())->validate();

    $CurrentFamily = Family::find($id);

    if (!is_null($CurrentFamily->head_id)) {
      $MainPersone = People::find($CurrentFamily->head_id);
      if ($MainPersone) {
        $MainPersone->family_id = null;
        $MainPersone->save();
      }
    }

    $CurrentFamily->name 		        = $request['name'];
    $CurrentFamily->discription 		= $request['discription'];
    $CurrentFamily->head_id 		    = $request['head_id'];

    $CurrentFamily->save();

    $RequestMainPersone = $request['head_id'];
    if (!is_null($RequestMainPersone)) {
      $MainPersone = People::find($RequestMainPersone);
      if ($MainPersone) {
        $MainPersone->family_id = $CurrentFamily->id;
        $MainPersone->save();
      }
    }

    People::where('family_id', $id)->where('id', '!=', $request['head_id'])->update(['family_id' => null]);
    $Candidates =  $request['candidates'];
    foreach ($Candidates as $Candidate){
    $Persone = People::find($Candidate['persone_id']);
      if ($Persone) {
        $Persone->family_id = $id;
        $Persone->save();
      }
    }

    return $CurrentFamily;
  }
}
