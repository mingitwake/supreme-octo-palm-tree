<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Log;

class LogController extends Controller
{
    public function index(Request $req)
    {
        $uid = $req->session()->get('uid');
        $sysname = $req->session()->get('sysname');
    
        if (!$uid) {
            return redirect()->route('login.page')->with('error', 'Login is required.');
        }
    
        $logs = Log::where('uid', $uid)->get();
    
        return view('chatlog.index', [
            'logs' => $logs,
            'uid' => $uid,
            'sysname' => $sysname,
        ]);
    }
    
    public function add(Request $req)
    {
        $validated = $req->validate([
            'title' => 'nullable|string|max:50',
        ]);
    
        $uid = $req->session()->get('uid');
        if (!$uid) {
            return redirect()->route('login.page')->with(['error' => 'Unknown']);
        }
    
        $log = new Log;
        $log->title = $req->filled('title') ? $req->title : 'New Chat';
        $log->uid = $uid;
        $log->active = 1;
    
        $log->save();

        $log = Log::where('uid', $uid)->latest('created_at')->first();
        $req->session()->put(['logid' => $log->logid]);

        return redirect()->route('chat.page');
    }

    public function retrieve(Request $req){
        $req->session()->put(['logid' => $req->logid]);
        return redirect()->route('chat.page');
    }

    public function delete(Request $req)
    {
        $log = Log::find($req->logid);
        $log->delete();
        return redirect()->back();
    }

    public function edit(Request $req)
    {
        $log = Log::find($req->logid);
        return view('chatlog.edit')->with(['log'=>$log]);
    }

    public function update(Request $req)
    {
        $validated = $req->validate([
            'title' => 'required|string|max:50',
        ]);
        $log = Log::find($req->logid);
        $log->update([
            'title' => $req->title,
        ]);
        return redirect()->route('home.page');
    }
}
