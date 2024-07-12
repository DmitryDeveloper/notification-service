<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'channels' => 'required|array',
            'channels.*' => 'string|in:email,sms,push',
            'sender_uuid' => 'required|uuid',
            'recipient_uuid' => 'required|uuid',
            'recipient_address' => 'required|string|max:50',
            'message' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
        ];
    }
}
