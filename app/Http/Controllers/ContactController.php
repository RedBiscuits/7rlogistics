<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required'
        ]);

        // TODO: Handle the form submission (e.g., send email, save to database, etc.)

        return redirect()->back()->with('success', 'Thank you for your message. We will contact you soon!');
    }
}
