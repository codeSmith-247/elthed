<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Posts;
use App\Models\Message;
use App\Models\Lawyer;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{

    public function info() {

        return response()->json([
            'posts'     => Posts::count(),
            'messages'  => Message::count(),
            'lawyers'   => Lawyer::count(),
            'images'    => Photo::count(),
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $request->user();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user_information = $request->validate([
            'name'  => 'required',
            'email' => 'required|email:rfc,filter|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()]
        ]);

        User::create([
            ...$request->only(['name', 'email']),
            'password' => Hash::make($request->password),
        ]);

        Auth::attempt($request->only(['email', 'password']));

        if($request->session)
            $request->session->regenerate();

        return response()->json([
            'message' => "user created successfully"
        ]);
    }

    /**
     * Authenticate a particular user
     */
    public function login(Request $request)
    {
        //
        $user_information = $request->validate([
            'email' => 'required|email:rfc,filter',
            'password' => ['required', Password::defaults()]
        ]);

        if(Auth::attempt($request->only(['email', 'password']))) {

            if($request->session)
                $request->session->regenerate();
    
            return response()->json([
                'message' => "Logged in successfully"
            ]);

        }

        return response()->json([
            'title'   => 'Invalid Credentials',
            'message' => 'Please provide the right inputs and try again'
        ], 422);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function logout(Request $request)
    {
        //
        Auth::guard('web')->logout().
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();

        return response()->json([
            'title' => 'Logged Out',
            'message' => '',
        ]);
    }
}
