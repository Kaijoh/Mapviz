<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function loadRegister()
    {
        if(Auth::check()){
            return redirect('home');
        }
        return view('register');
    }

    public function userRegister(Request $request)
    {
        $request->validate([
            'name' => 'string|required|min:1',
            'email' => 'string|required|email|max:100|unique:users',
            'password' => 'string|required|min:6|confirmed'
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'registration complete.');
    }

    public function loadLogin()
    {
        if(Auth::check()){
            return redirect('home');
        }
        return view('login');
    }

    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'string|required|email',
            'password' => 'string|required'
        ]);

        $userCredential = $request->only('email','password');

        if(Auth::attempt($userCredential))
        {
            return redirect('/home');
        }
        else{
            return back()->with('error','incorrect');
        }
    }

    public function home()
    {
        if(Auth::check()){
            $reports = Report::where('user_id',Auth::id())->get();
            return view('home',compact('reports'));
        }
        else{
            return redirect('/');
        }
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        Auth::logout();
        return redirect("/");
    }
}
