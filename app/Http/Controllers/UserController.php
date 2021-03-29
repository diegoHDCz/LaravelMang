<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function getInfo($id) {
        $array = ['error'=>''];

        $user = User::find($id);
        if($user) {
            $userlog = auth()->user();

            if($userlog) {
                $name = $user['name'];
                $email = $user['email'];
                $cpf = $user['cpf'];

                $array['name'] = $name;
                $array['email'] = $email;
                $array['cpf'] = $cpf;

            }else{
                $array['error'] = 'Usuário não está logado';
                return $array;
            }

        }else{
            $array['error'] = 'Usúario inexistente';
        }
       
        return $array;
    }

    public function update($id, Request $request) {
        $array = ['error'=> ''];

        $user = User::find($id);
        $userlog = auth()->user();

        if($user) {
            if($userlog['id'] === $user['id']) {
                $validator = Validator::make($request->all(), [
                    'name' => ['required'],
                    'email'=> ['required','email'],
                    'cpf'=>['required', 'digits:11']
                ]);
                if(!$validator->fails()) {
                    $name = $request->input('name');
                    $email = $request->input('email');
                    $cpf = $request->input('cpf');

                    $user->name = $name;
                    $user->email =$email;
                    $user->cpf = $cpf;
                    $user->save();

                    $array['user'] = $user;
                }else{
                    $array['error'] = $validator->errors()->first();
                }
            }
        }
    }

    public function newPassword($id, Request $request) {
        $array = ['error'=> ''];
        $user = User::find($id);
        $userlog = auth()->user();

        if($user) {
            $validator = Validator::make($request->all(), [
                'password'=>'required',
                'password_confirm'=>'required|same:password'
            ]);

            if(!$validator->fails()) {
                $password = $request->input('password');
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $user->password = $hash;
                $user->save();

            }else{
                $array['error'] = $validator->errors()->first();
            }

        }else{
            $array['error'] = 'Usuário não existente';
            return $array;
        }


        return $array;
    }
}
