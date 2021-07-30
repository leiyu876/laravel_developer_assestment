<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(User $user)
    {
        // Here I just update the name because I am out of time

        request()->validate([
            'name' => ['required'],
        ]);

        $user->update(['name' => request('name')]);

        return response(['message' => 'Profile Updated.'], 200);
    }
}
