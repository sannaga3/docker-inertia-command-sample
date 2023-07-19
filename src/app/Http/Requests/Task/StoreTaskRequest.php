<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
   */
  public function rules(): array
  {
    return [
      'date' => ['required', 'date'],
      'title' => ['required', 'string', 'max:20'],
      'content' => ['required', 'string', 'max:100'],
      'finished' => 'boolean',
      'published' => 'boolean',
    ];
  }
}