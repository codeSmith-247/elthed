<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use Illuminate\Http\Request;

class LawyerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //

        $filters = $request->validate([
            'title' => '',
            'order' => '',
            'categories' => '',
        ]);

        // return response()->json([
        //     ...$filters
        // ]);

        $lawyers = Lawyer::where('name', 'like', "%{$filters['title']}%");


        if($filters['order'] == 'latest') {
            $lawyers->orderBy('created_at', 'desc');
        }
        else if($filters['order'] == 'oldest') {
            $lawyers->orderBy('created_at', 'asc');
        }
        else if($filters['order'] == 'views') {
            $lawyers->orderBy('views_count', 'desc');
        }
        else if($filters['order'] == 'noViews') {
            $lawyers->orderBy('views_count', 'asc');
        }


        return $lawyers->paginate(15);
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
        $lawyer = $request->validate([
            'name'          => 'required',
            'position'      => 'required',
            'description'   => 'required',
            'cover_image'   => 'required|image',
        ]);

        

        $save_map = [
            'name'       => $lawyer['name'],
            'description' => $lawyer['description'],
            'position'      => $lawyer['position']
        ];

        if($request->file('cover_image') && $cover_image = $request->file('cover_image')->store('public/lawyers')) {
            $save_map['image'] = str_replace("public/", "storage/", $cover_image);
        }

        $new_lawyer = Lawyer::create($save_map);

        return response()->json([
            'message' => 'lawyer created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        
        $lawyer = $request->validate([
            'id' => 'required'
        ]);



        return Lawyer::find($lawyer['id']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lawyer $lawyer)
    {
        //
        $lawyer = $request->validate([
            'name'          => 'required',
            'position'      => 'required',
            'description'   => 'required',
            'cover_image'   => 'image',
            'id'            => 'required'
        ]);

        $save_map = [
            'name'       => $lawyer['name'],
            'description' => $lawyer['description'],
            'position'      => $lawyer['position']
        ];

        if($request->file('cover_image') && $cover_image = $request->file('cover_image')->store('public/lawyers')) {
            $save_map['image'] = str_replace("public/", "storage/", $cover_image);
        }


        Lawyer::whereId($lawyer['id'])->update($save_map);


        return response()->json([
            'message' => 'lawyer updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $lawyer = $request->validate([
            'id' => 'required'
        ]);


        Lawyer::destroy($lawyer['id']);

        return response()->json([
            'message' => 'lawyer deleted successfully',
        ]);
    }
}
