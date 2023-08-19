<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:3'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors
            ], 400);
        }
        if ($validator->passes()) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'code'=>'200',
                'status'=>'Success',
                'message'=>'Registration done successfully',
                'data'=>$user,
                'access_token'=>$token,
                'token_type' => 'Bearer'
            ]);
        }        
    }
    //end 3

    //login
    public function login(Request $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;        
        return response()->json([
            'code'=>'200',
            'status'=>'success',
            'message'=>'Login done successfully',
            'data'=>$user,
            'access_token'=>$token,
            'token_type' => 'Bearer'
        ]);
    }
    //end login
}
