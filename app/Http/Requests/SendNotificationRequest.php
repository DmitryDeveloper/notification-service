<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
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
            'recipient_email' => 'email',
            'recipient_phone' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'recipient_device_token' => 'string|max:100',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ];
    }
}
