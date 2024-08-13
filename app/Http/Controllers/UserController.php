<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $input = $request->validate([
            'name' => ['string', 'required'],
            'phone' => ['required', 'string', 'unique:users,phone', 'min:9', 'max:10'],
            'gender' => ['required', 'in:male,female'],
            'DOB' => ['required', 'date_format:Ymd'],
            'password' => ['required', 'string'],
            'avatar_url' => ['nullable']
        ]);
        User::create([
            'name' => $input['name'],
            'phone' => $input['phone'],
            'gender' => $input['gender'],
            'DOB' => $input['DOB'],
            'avatar_url' => $input['avatar_url'],
            'password' => Hash::make($input['password'])
        ]);

        return response()->json([
            'data' => 'user registered successfully'
        ]);
    }
}
