<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warning;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WarningController extends Controller
{
    public function getMyWarnings(Request $request) {
        $array = ['error'=>''];

        $property = $request->input('property');

        if($property) {
            $user = auth()->user();
            
            $unit = Unit::where('id', $property)
            ->where('id_owner', $user['id'])
            ->count();

            if($unit > 0){

                $warnings = Warning::where('id_unit', $property)
                ->orderBy('datecreated', 'DESC')
                ->orderBy('id', 'DESC')
                ->get();

                foreach($warnings as $warnKey => $warnValue) {
                    $warnings[$warnKey]['datecreated'] = date('d/m/Y', strtotime($warnValue['datecreated']));
                    $photoList = [];
                    $photos = explode(',', $warnValue['photos']);
                        if(!empty($photo)){
                            $photoList[] = asset('storage/'.$photo);
                        }

                    $warnings[$warnKey]['photos'] = $photoList;
                }

                $array['list'] = $warnings;

            }else{
                $array['error'] = 'Esta unidade não é sua.';
            }

        }else{
            $array['error'] = 'A propriedade é necessária';
        }
        

        return $array;
    }

    public function addWarningFile(Request $request) {
        $array = ['error'=> ''];
        
        $validator = Validator::make($request->all(), [
            'photo'=> 'required|file|mimes:jpg,png'
        ]); 

        if(!$validator->fails()){
            $file = $request->file('photo')->store('public');

            $array['photo'] = asset(Storage::url($file));

        }else{
            $array['error'] = $validator->errors->first();
            return $array;
        }
        return $array;
    }

    public function setWarnings(Request $request) {
        $array = ['error'=>''];

        $validator = Validator::make($request->all(), [
            'title'=>'required',
            'property'=>'required'
        ]);

        if(!$validator->fails()){
            $title = $request->input('title');
            $property = $request->input('property');
            $list = $request->input('list');

            $newWarning = new Warning();
            $newWarning->id_unit = $property;
            $newWarning->title = $title;
            $newWarning->status = 'IN_REVIEW';
            $newWarning->datecreated = date('Y-m-d');
            $newWarning->save();

            if($list && is_array($list)) {
                $photos = [];

                foreach($list as $listItem){
                    $url = explode('/', $listItem);
                    $photos[] = end($url);
                }

                $newWarning->photos = implode(',',$photos);
            }else{
                $newWarning->photos = '';
            }

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }
}
