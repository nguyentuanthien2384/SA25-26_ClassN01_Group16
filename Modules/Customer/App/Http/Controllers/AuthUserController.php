<?php

namespace Modules\Customer\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthUserController extends Controller
{
    public function login(){
        return view(('auth.login'));
    }
    
    public function register(){
        return view(('auth.register'));
    }
    
    public function postRegister(Request $request){
        $request->merge(['password'=>Hash::make($request->password)]);
        try{
            User::create($request->all());
        }catch(\Throwable $th){
        }
        return redirect()->route('login');
    }
    
    public function postLogin(Request $request){   
        $credentials = $request->only('email', 'password');
 
        if (Auth::attempt($credentials)) {
            return redirect()->intended(route('home'));
        }
        return redirect()->back()->with('error','sai pass rồi');
    }
    
    public function logout(){
        Auth::logout();
        return redirect()->route('home');
    }
}
