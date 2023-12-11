<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class PostsController extends Controller
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

        $posts = Posts::where('title', 'like', "%{$filters['title']}%")->withCount('comments')->withCount('views');

        if(isset($filters['categories']) && count($filters['categories']) > 0)

        $posts->whereHas('categories', function (Builder $query) use($filters) {

            $count = 0;

            foreach($filters['categories'] as $category) {

                if($count == 0)
                $query->where('name', 'like', "%{$category['name']}%");
                else
                $query->orWhere('name', 'like', "%{$category['name']}%");

                $count++;
            }

        });


        if($filters['order'] == 'latest') {
            $posts->orderBy('created_at', 'desc');
        }
        else if($filters['order'] == 'oldest') {
            $posts->orderBy('created_at', 'asc');
        }
        else if($filters['order'] == 'views') {
            $posts->orderBy('views_count', 'desc');
        }
        else if($filters['order'] == 'noViews') {
            $posts->orderBy('views_count', 'asc');
        }


        return $posts->paginate(15);
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

        $post = $request->validate([
            'title'         => 'required|unique:posts',
            'description'   => 'required',
            'content'       => 'required',
            'cover_image'   => 'image',
            'categories'    => 'required',
            'save_type'     => 'required',
        ]);

        

        $save_map = [
            'title'       => $post['title'],
            'description' => $post['description'],
            'author_id'   => $request->user()->id
        ];

        if($request->file('cover_image') && $cover_image = $request->file('cover_image')->store('public/cover_images')) {
            $save_map['image'] = str_replace("public/", "storage/", $cover_image);
        }

        if($post['save_type'] == 'publish') {
            $save_map['content']       = $post['content'];
            $save_map['draft_content'] = $post['content'];
        }
        else {
            $save_map['draft_content'] = $post['content'];
        }

        $new_post = Posts::create($save_map);

        foreach( $post['categories'] as $category ) {
            PostCategory::create([
                'post_id' => $new_post->id,
                'category_id' => $category['id'],
            ]);
        }

        return response()->json([
            'message' => 'post created successfully',
        ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        
        $post = $request->validate([
            'id' => 'required'
        ]);



        $posts =  Posts::with('categories')->find($post['id']);

        $categories = $posts->categories;

        $relatedPosts = Posts::whereHas('categories', function ($query) use ($categories) {
            $query->whereIn('categories.id', $categories->pluck('id'));
        })
        ->where('id', '!=', $post['id']) // Exclude the current post
        ->take(3) // Limit to 4 related posts
        ->get();

        $posts->related = $relatedPosts;

        return $posts;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Posts $posts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Posts $posts)
    {
        //
        $post = $request->validate([
            'title'         => 'required',
            'description'   => 'required',
            'content'       => 'required',
            'cover_image'   => 'image',
            'categories'    => 'required',
            'save_type'     => 'required',
            'id'            => 'required'
        ]);


        $check_title = Posts::where('title', $post['title'])->where('id', '!=', $post['id'])->exists();

        if($check_title) {
            $request->validate([
                'title' => 'required|unique:posts'
            ]);
        }


        $save_map = [
            'title'       => $post['title'],
            'description' => $post['description'],
        ];

        if($request->file('cover_image') && $cover_image = $request->file('cover_image')->store('public/cover_images')) {
            $save_map['image'] = str_replace("public/", "storage/", $cover_image);
        }

        if($post['save_type'] == 'publish') {
            $save_map['content']       = $post['content'];
            $save_map['draft_content'] = $post['content'];
        }
        else {
            $save_map['draft_content'] = $post['content'];
        }

        Posts::whereId($post['id'])->update($save_map);

        PostCategory::where('post_id', $post['id'])->delete();

        foreach( $post['categories'] as $category ) {
            PostCategory::create([
                'post_id' => $post['id'],
                'category_id' => $category['id'],
            ]);
        }

        return response()->json([
            'message' => 'post updated successfully',
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

        PostCategory::where('post_id', $post['id'])->delete();
        Posts::destroy($post['id']);

        return response()->json([
            'message' => 'post deleted successfully',
        ]);
    }
}
