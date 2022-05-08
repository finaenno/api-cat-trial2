<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request){
        try{
            $request->validate([
                'name' => ['required','string','max:255'],
                'username' => ['required','string','max:255','unique:users'],
                'phone_number' => ['required','string','max:255'],
                'email' => ['email','required','string','max:255','unique:users'],
                'password' => ['required','string', new Password]
            ]);

            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user = User::where('email', $request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');
        }catch(Exception $error){
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function login(Request $request){
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email','password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                    'message' => 'Unauthorized',
                ],'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->first();

            if(!Hash::check($request->password, $user->password,[])){
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'Authenticated');

        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function show(Request $request){
        $user = User::with('cats','posts','followers')->find($request->user());
        return ResponseFormatter::success($user, 'User data retrieved successfully');
    }

    public function profile(Request $request){
        try {
            $validation = Validator::make($request->all(),[
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users,id,'.$request->user()->id],
                'phone_number' => ['required', 'string', 'max:255'],
                'email' => ['email', 'required', 'string', 'max:255', 'unique:users,id,' . $request->user()->id],
                'profile_photo_path' => ['nullable','image']
            ]);

            if($validation->fails()){
                $error = $validation->errors()->all()[0];
                return ResponseFormatter::error([
                    'message' => 'Profile Failed to change',
                    'error' => $error
                ], 'Profile Failed to change',422);
            }else{
                $user = User::find($request->user()->id);
                $user->name = $request->name;
                $user->username = $request->username;
                $user->phone_number = $request->phone_number;
                $user->email = $request->email;
                if($request->profile_photo_path && $request->profile_photo_path->isValid()){
                    $slug = Str::slug($request->username);
                    $fileName = 'photo-'.$slug.'-'.time().'.'.$request->profile_photo_path->extension();
                    $request->profile_photo_path->storeAs('public/profile-photos', $fileName);
                    $path = "profile-photos/$fileName";
                    $user->profile_photo_path = $path;
                }
                $user->update();
                return ResponseFormatter::success($user, 'Profile Updated');
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function logout(Request $request){
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token,'Token Revoked');
    }
}
