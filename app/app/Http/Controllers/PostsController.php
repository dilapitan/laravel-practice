<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Post;

class PostsController extends Controller
{
    
    /** 
     * Create a new controller instance
     * 
     * @return void
    */
    public function __contruct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(10);
        return view('posts.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        /** 
         * Method for storing a post after creation
        */

        // form validation
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'cover_image' => 'image | nullable | max: 1999'
        ]);

        // handle file upload
        if ($request->hasFile('cover_image')) {
            
            // get file name with extension
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();

            // get just file name
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            // get extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();

            // file name to store
            $filenameToStore = $filename . '_' . time() . '.' . $extension;
            
            // upoad the image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $filenameToStore);
        } else {
            $filenameToStore = 'noimage.jpg'; // if user did not upload an image
        }

        // creating post
        $post = new Post;
        $post->title = $request->input('title');
        $post->body = $request->input('body');

        // request are only for form
        $post->user_id = auth()->user()->id;
        $post->cover_image = $filenameToStore;
        $post->save();

        // redirecting to a page after post creation
        return redirect('/posts')->with('success', 'Post Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        return view('posts.show')->with('post', $post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // find which post to edit
        $post = Post::find($id);

        // check for correct user
        if (auth()->user()->id !== $post->user_id) {
            return redirect('/posts')->with('error', 'Unauthorized Page');
        }

        return view('posts.edit')->with('post', $post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // form validation
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required'
        ]);

        // handle file upload
        if ($request->hasFile('cover_image')) {
            
            // get file name with extension
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();

            // get just file name
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            // get extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();

            // file name to store
            $filenameToStore = $filename . '_' . time() . '.' . $extension;
            
            // upoad the image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $filenameToStore);
        }

        // creating post
        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->body = $request->input('body');

        if ($post->cover_image !== 'noimage.jpg') {
            // delete image
            Storage::delete('public/cover_images/' . $post->cover_image);
        }

        if ($request->hasFile('cover_image')) {
           $post->cover_image = $filenameToStore; 
        }

        $post->save();

        // redirecting to a page after post creation
        return redirect('/posts')->with('success', 'Post Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        // check for correct user
        if (auth()->user()->id !== $post->user_id) {
            return redirect('/posts')->with('error', 'Unauthorized Page');
        }

        if ($post->cover_image !== 'noimage.jpg') {
            // delete image
            Storage::delete('public/cover_images/' . $post->cover_image);
        }

        $post->delete();

        // redirecting to a page after post deletion
        return redirect('/posts')->with('success', 'Post Removed');
    }
}
