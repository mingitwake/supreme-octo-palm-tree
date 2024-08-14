<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Confirmation;
use App\Mail\Notification;
use App\Models\Email;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $validated = $request->validate([
            'recipient' => 'required|string',
            'recipientEmail' => 'required|email',
            'subject' => 'nullable|string',
            'content' => 'nullable|string',
        ]);

        $recipientEmail = $request->input('recipientEmail');

        $mail = Email::create($validated);
        $notification = new Notification($mail);
        $confirmation = new Confirmation($mail);

        Mail::mailer('postmark')
        ->to($recipientEmail)
        ->send($notification);

        Mail::mailer('postmark')
        ->to('u3592864@connect.hku.hk')
        ->send($confirmation);

        return response()->json(['message' => 'OK']);
    }
}
