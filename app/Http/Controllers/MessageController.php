<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $filters = $request->validate([
            'search' => '',
            'page'  => '',
        ]);

        $messages = new Message();

        if(isset($filters['search'])) {
            $messages =  Message::where('email', 'like', "%{$filters['search']}%")
                    ->orWhere('message', 'like', "%{$filters['search']}%");

            return $messages->paginate(15);
        }
        
        return $messages->paginate(15);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $message = $request->validate([
            'email' => 'required',
            'message' => 'required',
        ]);

        Message::create($message);

        return response()->json([
            'message' => 'Message sent successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
 
        $message = $request->validate([
            'id' => 'required'
        ]);

        return Message::find($message['id']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }
}
