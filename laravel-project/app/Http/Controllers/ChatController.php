<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

// class ChatController extends Controller
// {
//     //
// }

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Chat;

class ChatController extends Controller
{
    public function chat(Request $req)
    {
        $logid = $req->session()->get('logid');
        if (!$logid) {
            return redirect()->back()->with('error', 'Log not found.');
        }

        $query = $req->input('query');

        $chat = new Chat;
        $chat->logid = $logid;
        $chat->content = $query;
        $chat->role = 1;
        $chat->active = 1;
        $chat->save();

        try {

            $response = Http::timeout(50)->post('http://localhost:5000/chat', [
                'query' => $query
            ]);
            $responseData = $response->json();
            $message = $responseData['message'] ?? 'No message found';

        } catch (\Exception $e) {

            return response()->json(['error' => 'Request timed out or failed: ' . $e->getMessage()], 500);

        }

        $chat = new Chat;
        $chat->logid = $logid;
        $chat->content = $message;
        $chat->role = 0;
        $chat->active = 1;
        $chat->save();

        return response()->json($response->json());
    }

    public function index(Request $req)
    {
        $uid = $req->session()->get('uid');
        $sysname = $req->session()->get('sysname');
        $logid = $req->session()->get('logid');
    
        if (!$uid) {
            return redirect()->route('login.page')->with('error', 'Login is required.');
        }
    
        $chats = Chat::where('logid', $logid)->get();
    
        return view('chat.index', [
            'chats' => $chats,
            'logid' => $logid,
            'uid' => $uid,
            'sysname' => $sysname,
        ]);
    }

    public function add(Request $req){
        $chat = new Chat;
        $chat->logid = $req->logid;
        $chat->content = $req->content;
        $chat->role = $req->role;
        $chat->save();
        return redirect()->back();
    }
    
    public function delete(Request $req){
        $chat = Chat::find($req->id);
        $chat->delete();
        return redirect()->back();
    }

    public function edit(Request $req){
        $chat = Chat::find($req->id);
        return view('edit')->with("chat",$chat);
    }

    public function update(Request $req){
        $chat = Chat::find($req->chatid);
        $chat->update([
            'content' => $req->content,
        ]);
        $chats = Chat::all();
        return  redirect()->route('chat.page');
    }

}
