<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactRequests;
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

        ContactRequests::create($validated);
        
        return redirect()->back()->with('success', 'Thank you for your message. We will contact you soon!');
    }
}
