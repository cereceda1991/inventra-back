<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $perPage = 50; // Número de usuarios por página
        $users = User::paginate($perPage);
    
        $response = [
            'status' => 'success',
            'message' => 'Users found!',
            'data' => [
                'users' => $users->items(),
                'currentPage' => $users->currentPage(),
                'perPage' => $users->perPage(),
                'totalPages' => $users->lastPage(),
                'totalCount' => $users->total(),
            ],
        ];

        return response()->json($response, Response::HTTP_OK);
    }
        
    public function show($id)
    {
        $user = User::find($id);
    
        if (!$user) {
            return response()->json(['message' => 'User not found.', 'type' => 'error'], 404);
        }
    
        return response()->success($user, 'User found!');
    }
    
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
    
            $validator = Validator::make($request->all(), [
                'email' => ['email', Rule::unique('users')->ignore($user)],
                'password' => 'nullable|min:6',
                'role' => 'nullable|string',
                'currency' => 'nullable|string',
                'decimals' => 'nullable|boolean',
                'darkmode' => 'nullable|boolean',
                'profile' => 'nullable|string',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }
    
            // Actualiza los campos con los valores proporcionados en la solicitud
            if ($request->has('name')) {
                $user->name = $request->name;
            }
    
            if ($request->has('email')) {
                $user->email = $request->email;
            }
    
            if ($request->has('password')) {
                $user->password = bcrypt($request->password);
            }
    
            // Actualiza los campos 'currency', 'decimals' y 'darkmode' si se proporcionan
            if ($request->has('currency')) {
                $user->currency = $request->currency;
            }
    
            if ($request->has('decimals')) {
                $user->decimals = $request->decimals;
            }
    
            if ($request->has('role')) {
                $user->role = $request->role;
            }
    
            if ($request->has('darkmode')) {
                $user->darkmode = $request->darkmode;
            }
    
            if ($request->has('profile')) {
                $user->profile = $request->profile;
            }
    
            $user->save();
    
            return response()->success($user, 'User has been successfully updated');
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], 500);
        }
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    
        return response()->json(['message' => 'User deleted successfully'], Response::HTTP_OK);
    }
}
