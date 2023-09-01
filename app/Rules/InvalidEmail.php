<?php

namespace App\Rules;

use App\Models\Contact;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InvalidEmail implements ValidationRule
{

    public $email;
    public function __construct($email = null)
    {
        $this->email = $email;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {  //Give me all contacts of the current user
        $alreadyExistContactWithThisEmail = Contact::where('user_id', auth()->user()->id)
            //(relationship, function($query) as (value out of this function))
            ->whereHas('user', function ($query) use ($value) {
                $query->where('email', $value)
                    ->when($this->email, function($query) {
                        $query->where('email', '!=', $this->email);
                    });
            })->count() === 0;
        if (!$alreadyExistContactWithThisEmail) {
            $fail('Ya tienes un contacto registrado con este :attribute.');
        }
    }
}
