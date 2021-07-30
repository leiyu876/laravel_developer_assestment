<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmWithPin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login()
    {
        request()->validate([
            'user_name' => ['required'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt(['user_name' => request('user_name'), 'password' => request('password')])) {
            return response([
                'message' => 'Credentials do not match our record.',
            ], 422);    
        }

        if(is_null(auth()->user()->registered_at)) {
            
            Auth::logout();

            return response([
                'message' => 'Please confirm registration using 6 digit pin emailed to you.',
            ], 422); 
        }
        
        $user = User::find(auth()->user()->id);

        $user->update(['api_token' => Str::random(60)]);

        return response([
            'message' => 'Successfully Login.',
            'token' => $user->refresh()->api_token
        ], 200);
    }

    public function register () {
        
        $data = request()->validate([
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|min:4|max:20',
            // 'avatar' => 'required|string', // must be dimension: 256px x 256px
            // 'user_role' => 'required|string', // must be in constants user_roles
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $data['password'] = Hash::make(request('password'));
        $data['api_token'] = Str::random(60);
        $data['email_confirmation_pin'] = mt_rand(100000, 999999);
        
        User::create($data);

        Mail::to([$data['email']])
            ->send(new ConfirmWithPin($data['name'], $data['email_confirmation_pin']));

        return response(['message' => 'User successfully created.'], 201);
    }

    public function confirm(User $user) 
    {
        request()->validate([
            'pin' => [
                function ($attribute, $value, $fail) use($user) {
                    if ($user->email_confirmation_pin != $value) $fail($attribute.' not valid.');
                },
            ]
        ]);

        $user->update(['registered_at' => now()]);

        return response(['message' => 'Account verified.'], 200);
    }
}
