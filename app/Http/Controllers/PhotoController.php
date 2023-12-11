<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoController extends Controller
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

        $messages = new Photo();

        if(isset($filters['search'])) {
            $messages =  Photo::where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");

            return $messages->paginate(15);
        }
        
        return $messages->paginate(15);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $post = $request->validate([
            'cover_image'   => 'required|image',
            'title'         => 'required',
            'description'   => 'required',
        ]);

        $cover_image = $request->file('cover_image')->store('public/cover_images');

        $cover_image = str_replace("public/", "storage/", $cover_image);

        Photo::create([
            'title' => $post['title'],
            'description' => $post['description'],
            'image' => $cover_image
        ]);

        return response()->json([
            'message' => 'image saved successfully',
        ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
        $message = $request->validate([
            'id' => 'required'
        ]);

        return Photo::find($message['id']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

        $post = $request->validate([
            'cover_image'   => 'image',
            'title'         => 'required',
            'description'   => 'required',
            'id'            => 'required',
        ]);

        $save_map = [
            'title'       => $post['title'],
            'description' => $post['description'],
        ];

        if($request->file('cover_image') && $cover_image = $request->file('cover_image')->store('public/cover_images')) {
            $save_map['image'] = str_replace("public/", "storage/", $cover_image);
        }

        Photo::whereId($post['id'])->update($save_map);

        return response()->json([
            'message' => 'Image updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $post = $request->validate([
            'id' => 'required'
        ]);

        Photo::destroy($post['id']);

        return response()->json([
            'message' => 'image deleted successfully',
        ]);
    }
}
