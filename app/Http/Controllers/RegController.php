<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\This;
use Validator;
use App\User;
use Hash;

class RegController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login']]);
    // }

    public function registration()
    {
        $credentials = request(['name', 'email', 'password', 'repassword']);


        // try to login after 'create' function
        // if (! $token = auth('api')->attempt($credentials)) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
        $validator = $this->validator($credentials);
        if ($validator->fails()){
            return response()->json(['error' => $validator->errors()]);
        }
        $this->create($credentials);
        return response()->json(['success'=>'Done!']);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'repassword' => 'required|same:password|min:3'
        ]);
        
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
