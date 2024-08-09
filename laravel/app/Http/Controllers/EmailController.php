<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Reminder;
use App\Models\Remind;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $validated = $request->validate([
            'replyTo' => 'required|string|max:64',
            'replyToEmail' => 'required|email',
            'content' => 'nullable|string',
        ]);

        $replyToEmail = $request->input('reply_to_email');

        $remind = Remind::create($validated);
        $reminder = new Reminder($remind);

        Mail::mailer('postmark')
        ->to("u3592864@connect.hku.hk")
        ->cc($replyToEmail)
        ->send($reminder);

        return response()->json(['message' => 'Email Sent']);
    }
}
