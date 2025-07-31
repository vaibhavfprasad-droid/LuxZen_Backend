<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * This method acts as a gatekeeper for the request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // THIS IS THE FIX.
        // Change this to 'true' to allow anyone (guests) to attempt a registration.
        // If this is 'false', Laravel will immediately stop and return a 403 Forbidden error.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * These rules are checked only if authorize() returns true.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            // Validate the 'username' sent from the frontend. It must be unique in the 'name' column of the 'users' table.
            'username' => 'required|string|max:255|unique:users,name', 
            
            // Validate the 'email'. It must be a valid email format and unique in the 'users' table.
            'email'    => 'required|string|email|max:255|unique:users,email',
            
            // Validate the 'password'. It must be a string with at least 8 characters.
            'password' => 'required|string|min:8',
            
            // Validate the 'phone_number'. It must be unique in the 'users' table and have between 10 and 15 digits.
            'phone_number' => 'required|string|min:10|max:15|unique:users,phone_number',
        ];
    }
}