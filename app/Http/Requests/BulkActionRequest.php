<?php
/**
 * @author Claudio Campos
 * @link https://www.sigasmart.com.br
 * @package App\Http\Requests
 */ 
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|string',
            'model' => 'required|string',
            'items' => 'array', 
        ];
    }
} 