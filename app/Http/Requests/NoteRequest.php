<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NoteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'array'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Заголовок обязателен.',
            'content.required' => 'Содержание обязательно.'
        ];
    }
}
