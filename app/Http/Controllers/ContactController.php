<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use App\Rules\InvalidEmail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{

    public function index()
    {
        $contacts = auth()->user()->contacts()->get();
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', 'exists:users,email', Rule::notIn([auth()->user()->email]), new InvalidEmail],
        ]);
        $user = User::where('email', $request->email)->first();
        $newContact = Contact::create([
            'name' => $request->name,
            'contact_id' => $user->id,
            'user_id' => auth()->user()->id,
        ]);

        session()->flash('flash.banner', 'El contacto se ha creado correctamente');
        session()->flash('flash.bannerStryle', 'success');
        return redirect()->route('contacts.edit', $newContact);
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }


    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', 'exists:users,email', Rule::notIn([auth()->user()->email]), new InvalidEmail($contact->user->email)],
        ]);
        $user = User::where('email', $request->email)->first();
        $contact->update([
            'name' => $request->name,
            'contact_id' => $user->id,
        ]);

        session()->flash('flash.banner', 'El contacto se ha editado correctamente');
        session()->flash('flash.bannerStryle', 'success');
        return redirect()->route('contacts.index');
    }


    public function destroy(Contact $contact)
    {
        $contact->delete();
        session()->flash('flash.banner', 'El contacto se elimino correctamente');
        session()->flash('flash.bannerStryle', 'success');
        return redirect()->route('contacts.index');
    }
}
