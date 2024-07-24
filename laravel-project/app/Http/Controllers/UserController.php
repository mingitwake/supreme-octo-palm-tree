<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $req){
        $users = User::all();
        return  view('user.index')->with("users",$users);
    }

    public function login(Request $req)
    {
        $validated = $req->validate([
            'sysname' => 'required|string|max:25',
        ]);
    
        $user = User::where('sysname', $req->sysname)->first();
    
        if ($user) {
            $req->session()->put(['uid'=>$user->uid, 'sysname'=>$user->sysname]);
            return redirect()->route('home.page');
        } else {
            return redirect()->back()->with('error', 'Username not found');
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login.page');
    }

    public function add(Request $req){
        $user = new User;
        $user->sysname = $req->sysname;
        $user->role = $req->role;
        $user->save();
        return redirect()->back();
    }

    public function delete(Request $req){
        $user = User::find($req->uid);
        $user->delete();
        return redirect()->back();
    }
    public function edit(Request $req){
        $user = User::find($req->uid);
        return view('user.edit')->with("user",$user);
    }
    public function update(Request $req){
        $user = User::find($req->uid);
        $user->update([
            'sysname' => $req->sysname,
            'role' => $req->role,
        ]);
        return redirect()->back();
    }
}