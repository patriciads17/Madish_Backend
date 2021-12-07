<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\User; 
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $registrationData = $request->all();
        $validate = Validator::make($registrationData, [
            'name' => 'required|max:60',
            'username' => 'required|min:8',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required',
        ]); 

        if($validate->fails())
            return response(['message' => $validate->errors()],400); 

        $registrationData['password'] = bcrypt($request->password);
        $user = User::create($registrationData);
        return response([
            'message' => 'Register Success',
            'user' => $user
        ],200); 
    }

    public function login(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns' ,
            'password' => 'required'
        ]); 

        if ($validate->fails())
            return response(['message' => $validate->errors()],400); 

        if(!Auth::attempt($loginData))
            return response(['message' => 'Invalid Credentials'], 401); 
        
        $user = Auth::user();
        $token = $user->createToken('Authentication Token')->accessToken; 

        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' =>'Bearer',
            'access_token' => $token
        ]); 
    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function index()
    {
        $users = User::all(); 

        if(count($users)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $users
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); 

    }

    public function show($id)
    {
        $user = User::find($id); 

        if(!is_null($user)) {
            return response([
                'message' => 'Retrieve User Success',
                'data' => $user
            ], 200);
        } 

        return response([
            'message' => 'User Not Found',
            'data' => null
        ], 404); 
    }

    public function destroy($id)
    {
        $user = User::find($id); 
        
        if (is_null($user)) {
            return response([
                'message' =>'User Not Found',
                'data' => null
            ], 404);
        }

        if($user->delete()) {
            return response([
                'message' =>'Delete User Success',
                'data' => $user
            ], 200); 
        } 

        return response([
            'message' => 'Delete User Failed',
            'data' => null,
        ], 400); 

    }

    public function update(Request $request, $id)
    {
        $user = User::find($id); 
        if (is_null($user)) {
            return response([
                'message' =>'User Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all(); 
        if(is_null($updateData['password']))
        {
            $validate = Validator::make($updateData, [
                'name' => 'required|max:60|regex:/^[a-zA-Z]+$/|alpha',
                'address' => 'required',
                'phone_num' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
                'email' => "required|email:rfc,dns|unique:App\Models\User,email,$id",
                'username' => 'required|min:8'
            ]); 

            $user->name = $updateData['name'];
            $user->address = $updateData['address'];
            $user->email = $updateData['email'];
            $user->phone_num = $updateData['phone_num'];
            $user->username = $updateData['username'];

            if($validate->fails())
            return response(['message' => $validate->errors()], 400); 

            if($user->save()) {
                return response([
                    'message' => 'Update User Success',
                    'data' =>$user
                ], 200);
            } 

        }else{

            $validate = Validator::make($updateData, [
                'name' => 'required|max:60|regex:/^[a-zA-Z]+$/|alpha',
                'address' => 'required',
                'phone_num' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
                'email' => "required|email:rfc,dns|unique:App\Models\User,email,$id",
                'password' => 'required',
                'new_password' => 'required',
                'con_new_password' => 'required'
            ]); 

            
            if($validate->fails())
                return response(['message' => $validate->errors()], 400); 

            if ((Hash::check(request('password'), Auth::user()->password))==false)  {
                return response([
                    'message' => 'Check your old password',
                    'data' => null,
                ], 404);
            } else if ((Hash::check(request('new_password'), Hash::check(request('con_new_password'))==false))) {
                return response([
                    'message' => 'New Password and New Confirmation Password must be same',
                    'data' => $user,
                ], 404);
            } else {
                $user->name = $updateData['name'];
                $user->address = $updateData['address'];
                $user->email = $updateData['email'];
                $user->phone_num = $updateData['phone_num'];
                $user->username = $updateData['username'];
                $user->password = bcrypt($updateData['new_password']);
            }

            if($user->save()) {
                return response([
                    'message' => 'Update User Success',
                    'data' =>$user
                ], 200);
            } 
        }
        
        return response([
            'message' => 'Update User Failed',
            'data' => null,
        ], 400); 
    }
}
