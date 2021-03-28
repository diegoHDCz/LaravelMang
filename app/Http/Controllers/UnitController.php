<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\UnitPeople;
use App\Models\UnitVehicule;
use App\Models\UnitPet;


class UnitController extends Controller
{
    public function getInfo($id) {
        $array = ['error'=>''];
        $unit = Unit::find($id);

        if($unit) {
            $peoples = UnitPeople::where('id_unit', $id)->get();
            $vehicules = UnitVehicule::where('id_unit',$id)->get();
            $pets = UnitPet::where('id_unit',$id)->get();

            foreach($peoples as $peopleKey => $peopleValue) {
                $peoples[$peopleKey]['birthdate'] = date('d/m/Y', strtotime($peopleValue['birthdate']));
            }

            $array['peoples'] = $peoples;
            $array['vehicules'] = $vehicules;
            $array['pets'] = $pets;


        }else {
            $array['error'] = 'Propriedade inexistente';
            return $array;
        }

        return $array;
    }
    public function addPerson($id, Request $request) {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'birthdate'=>'required|date'
        ]);

        if(!$validator->fails()) {
            $name = $request->input('name');
            $birthdate = $request->input('birthdate');

            $newPerson = new UnitPeople();
            $newPerson->id_unit = $id;
            $newPerson->name = $name;
            $newPerson->birthdate = $birthdate;
            $newPerson->save();
    
        }else{
            $array['error'] = $validator->errors()->first();
        }

        return $array;
    }

    public function addVehicule($id, Request $request) {
        $array = ['error'=>''];

        $validator = Validator::make($request->all(), [
            'title'=>'required',
            'color'=>'required',
            'plate'=>'required'
        ]);

        if(!$validator->fails()) {
            $title = $request->input('title');
            $color = $request->input('color');
            $plate = $request->input('plate');

            $newCar = new UnitVehicule();
            $newCar->id_unit = $id;
            $newCar->title = $title;
            $newCar->color = $color;
            $newCar->plate = $plate;
            $newCar->save();
            

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function addPet($id, Request $request) {
        $array = ['error'=>''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'race' => 'required'
        ]);

        if(!$validator->fails()) {
            $name = $request->input('name');
            $race = $request->input('race');

            $newPet = new UnitPet();
            $newPet->id_unit = $id;
            $newPet->name = $name;
            $newPet->save();
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        
        return $array;
    }

    public function removePerson($id, Request $request){
        $array = ['error'=>''];

        $idItem = $request->input('id');
        if($idItem){
            UnitPeople::where('id', $idItem)
            ->where('id_unit', $id)
            ->delete();

        }else{
            $array['error'] = 'Pessoa inexsitente';
            return $array;
        }

        return $array;
    }

    public function removeVehicule($id, Request $request){
        $array = ['error'=>''];

        $idItem = $request->input('id');
        if($idItem){
            UnitVehicule::where('id', $idItem)
            ->where('id_unit', $id)
            ->delete();

        }else{
            $array['error'] = 'Veículo inexsitente';
            return $array;
        }

        return $array;
    }

    public function removePet($id, Request $request){
        $array = ['error'=>''];

        $idItem = $request->input('id');
        if($idItem){
            UnitPet::where('id', $idItem)
            ->where('id_unit', $id)
            ->delete();

        }else{
            $array['error'] = 'Veículo inexsitente';
            return $array;
        }

        return $array;
    }
}
