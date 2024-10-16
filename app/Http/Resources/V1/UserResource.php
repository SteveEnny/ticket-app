<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'type' => 'user',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'email' => $this->mergeWhen($request->routeIs('authors.*'), [
                    'emailVerifiedAt' => $this->email_verified_at,
                    'createAt' => $this->created_at,
                    'updatedAt' => $this->updated_at,
                ]),
                ],
                'include' => TicketResource::collection($this->whenLoaded('tickets')),
            'links' => [
                ['self' => route('authors.show', ['user' => $this->id])]
            ]
        ];
    }
}