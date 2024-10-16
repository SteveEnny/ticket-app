<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function mappedAttributes() 
    {
        $attributeMap =[
            // attributes        |      keys 
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.attributes.createAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',
            'data.relationships.author.data.id' => 'user_id',
        ];

        $attributeToUpdate = [];
         // NOTE : $this() is pointing to the request class
        foreach($attributeMap as $key => $attribute) {
            if($this->has($key)) {
                $attributeToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributeToUpdate;
    }

    public function messages() {
        return [
            'data.attributes.status' => 'The data.attributes.status value is invalid. Please use a A, C, H, 0r X.'
        ];
    }
}