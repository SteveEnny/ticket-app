<?php

namespace App\Http\Requests\Api\V1;

use App\Permisson\V1\Abilities;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends BaseTicketRequest
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
    public function rules(): array
    {
        $rules =[
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
            'data.relationships.author.data.id' => 'required | integer | exists:user,id'
        ];

        $user = $this->user();
        if($this->routeIs('api_v1.tickets.store')) {
            if($user->tokenCan(Abilities::CreateOwnTicket)) {

                $rules['data.relationships.author.data.id'] .= '|size:' . $user->id;
            }
        }
        return $rules;
    }

    //this code explains that the request should check if the author_id === user->id when creating a request..

}