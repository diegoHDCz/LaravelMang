<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doc;
use Illuminate\Support\Facades\Storage;

class DocController extends Controller
{
    public function getAll() {
        $array = ['error'=>''];

        $docs = Doc::all();

        foreach($docs as $docKey => $docValue) {
            $docs[$docKey]['fielurl'] = asset('storage/'.$docValue['fileurl']);
        }

        $array['list'] =$docs;

        return $array;
    }
}
