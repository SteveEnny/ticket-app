<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filter\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AuthorTicketsController extends ApiController
{
    public function index($author_id, TicketFilter $filters) {
        return TicketResource::collection(Ticket::where('user_id', $author_id)->filter($filters)->paginate());
    }

    public function store(string $author_id,StoreTicketRequest $request)
    {
       
        return new TicketResource(Ticket::create($request->mappedAttributes()));
    }

    public function update(UpdateTicketRequest $request, string $author_id, string $ticket_id)
    {
        //PATCH
        try {

            $ticket = Ticket::findOrFail($ticket_id);
            
            if($author_id === $ticket->user_id) {

                $ticket->update($request->mappedAttributes());

                return new TicketResource(resource: $ticket);            
        } 

        // TODO : ticket doesn't belong to user 

        } catch(ModelNotFoundException $exception){
            return $this->ok('User not found', [
                'error' => 'The provided user id does not exists'
            ]);
        }
       
    }

    public function replace(ReplaceTicketRequest $request, string $author_id ,string $ticket_id) {

        try {

            $ticket = Ticket::findOrFail($ticket_id);
        if($author_id === $ticket->user_id) {

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);            
        } 

        // TODO : ticket doesn't belong to user 

        } catch(ModelNotFoundException $exception){
            return $this->ok('User not found', [
                'error' => 'The provided user id does not exists'
            ]);
        }
    }


    public function destroy(string $author_id ,string $ticket_id)
    {
        try{
            $ticket = Ticket::findOrFail($ticket_id);

            if($author_id === $ticket->user_id) {
            $ticket->delete();
                return $this->ok('Ticket successfully deleted');
            }
            return $this->error("Ticket cannot be found", 404);
        } catch(ModelNotFoundException $exception){
            return $this->error("Ticket cannot be found", 404);
        }
    }
}