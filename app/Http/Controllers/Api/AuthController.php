<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!Auth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Error creating token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = Auth::user();
        $user->makeHidden('password'); 
        $token = JWTAuth::fromUser($user);

        return response()->success(['token' => $token,'user' => $user ], 'Successful login!');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'nullable'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
    
        // Asignar el valor por defecto 'administrator' si el campo 'role' está vacío o no se proporciona en la solicitud
        $user->role = $request->has('role') ? $request->input('role') : 'administrator';
    
        $user->save();
    
        $token = JWTAuth::fromUser($user);
    
        return response()->success(['token' => $token, 'user' => $user], 'User successfully registered!');
    }    
    
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            
            return response()->success('Logout successful');
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to sign out'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
