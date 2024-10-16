<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filter\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Models\Ticket;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\User;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        // [
        //     'include' => 'author',
        //     'filter' => [
        //         'status' => 'C',
        //         'title' => 'title filter',
        //         'createdAt' => ' '
        //     ]
        // ]
        return TicketResource::collection(Ticket::filter($filters)->paginate());
        // if($this->include('author')) {
        //     return TicketResource::collection(Ticket::with('user')->paginate());
        // }
        // return TicketResource::collection(Ticket::paginate());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        try {
            // $user = User::findOrFail($request->input('data.attributes.author.data.id')); already checked in the request. Model not found is been handled in our validaton rules

            // policy
            $this->isAble('store', Ticket::class);

            // TODO create ticket
            return new TicketResource(Ticket::create($request->mappedAttributes()));

        }
        //  catch(ModelNotFoundException $exception){
        //     return $this->ok('User not found', [
        //         'error' => 'The provided user id does not exists'
        //     ]);
        // }
        catch(AuthorizationException $ex) {
            return $this->error('You are not authorized to update that resource', 401);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $ticket_id)
    {
        try{
            $ticket = Ticket::findOrFail($ticket_id);
            if($this->include('authors')) {
                return new TicketResource($ticket->load('authors'));
            }
            return new TicketResource($ticket);
        } catch(ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request,$ticket_id)
    {
        //PATCH
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            // policy
            $this->isAble('update', $ticket);

            
            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        } catch(ModelNotFoundException $exception){
            return $this->ok('User not found', [
                'error' => 'The provided user id does not exists'
            ]);
        }catch(AuthorizationException $ex) {
            return $this->error('You are not authorized to update that resource', 401);
        }
    }

    public function replace(ReplaceTicketRequest $request, $ticket_id) {

        try {
            $ticket = Ticket::findOrFail($ticket_id);

            // policy 
            $this->isAble('replace', $ticket);
            // $model = [
            //     'title' => $request->input('data.attributes.title'),
            //     'description' => $request->input('data.attributes.description'),
            //     'status' => $request->input('data.attributes.status'),
            //     'user_id' => $request->input('data.relationships.author.data.id')
            // ];
            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        } catch(ModelNotFoundException $exception){
            return $this->ok('User not found', [
                'error' => 'The provided user id does not exists'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $ticket_id)
    {
        try{
            // $ticket = Ticket::where('ticket_id', $ticket->id);
            $ticket = Ticket::findOrFail($ticket_id);

            // policy
            $this->isAble('delete', $ticket);

            
            $ticket->delete();
            return $this->ok('Ticket successfully deleted');
        } catch(ModelNotFoundException $exception){
            return $this->error("Ticket cannot be found", 404);
        }
    }
}