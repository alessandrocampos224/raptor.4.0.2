<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Http\Requests\Gondola;

use App\Core\Landlord\Facades\Landlord;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreRequest
 * 
 * Classe de validação para requisições de criação de registros Gondola.
 * Define regras de validação e mensagens personalizadas.
 */
class StoreRequest extends BaseFormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     * 
     * @return bool Retorna true se autorizado, false caso contrário
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define as regras de validação aplicáveis à requisição.
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'height' => 'required|numeric',
            'width' => 'required|numeric',
            'base_height' => 'required|numeric',
            'thickness' => 'required|integer',
            'location' => 'nullable|string',
            'scale_factor' => 'required|numeric',
            'description' => 'nullable|string',
            'status' => 'required|string',
            // Adicione mais regras de validação conforme necessário
        ];
    }
    
    /**
     * Define mensagens personalizadas para erros de validação.
     * 
     * @return array Mensagens personalizadas
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'name.max' => 'O nome não pode ter mais de :max caracteres',
            'status.required' => 'O status é obrigatório',
            // Adicione mais mensagens personalizadas conforme necessário
        ];
    }
    
    /**
     * Opcionalmente, você pode preparar os dados antes da validação
     * sobrecarregando o método prepareForValidation() aqui
     * 
     * protected function prepareForValidation(): void
     * {
     *     $this->merge([
     *         'field' => transform_field($this->field),
     *     ]);
     * }
     */

       /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $tenantId = Landlord::getTenantId('tenant_id'); 
         //vamos convertir os valores de altura e largura para float
            $this->merge([
                'tenant_id' => $tenantId,
                'height' => (float) $this->input('height'),
                'width' => (float) $this->input('width'),
                'base_height' => (float) $this->input('base_height'),
                'thickness' => (int) $this->input('thickness'),
                'scale_factor' => (float) $this->input('scale_factor'),
            ]); 
        return $this->all();
    }
}
