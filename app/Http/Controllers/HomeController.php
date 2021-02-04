<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lead;
use Illuminate\Support\Facades\Mail;
use App\Mail\MessageFromWebsite;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('guest.home');
    }

    public function contatti() {
        return view('guest.contatti');
    }

    public function contattiSent(Request $request) {
        $form_data = $request->all();
        $new_lead = new Lead();
        $new_lead->fill($form_data);
        $new_lead->save();
        Mail::to('commerciale@boolpress.com')
            ->send(new MessageFromWebsite($new_lead));
        return redirect()->route('contatti.thank-you');
    }

    public function thankYou() {
        return view('guest.thank-you');
    }
}
