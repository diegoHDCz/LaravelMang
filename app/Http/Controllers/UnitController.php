<?php

namespace App\Http\Controllers;

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
}
