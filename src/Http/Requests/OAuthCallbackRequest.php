<?php

namespace Sharpie89\LaravelOAuthClient\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OAuthCallbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string'
            ],
            'state' => [
                'required',
                'string',
                'exists:oauth_providers,state'
            ]
        ];
    }
}
