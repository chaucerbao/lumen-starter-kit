<?php

namespace App\Http\Controllers;

use App\Post;
use Auth;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('published_at', 'DESC')->get();

        return view('post.index', compact('posts'));
    }

    /**
     * Display the specified post.
     *
     * @param string $slug
     *
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        return view('post.show', compact('post'));
    }

    /**
     * Store a newly created post in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->merge(['author_id' => Auth::user()->id]);
        $this->validate($request, Post::$rules);

        $post = Post::create($request->all());

        return redirect()->route('post.index');
    }

    /**
     * Update the specified post in storage.
     *
     * @param Request $request
     * @param string  $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $slug)
    {
        $this->validate($request, array_except(Post::$rules, ['author_id']));

        $post = Post::where('slug', $slug)->firstOrFail();
        $post->fill($request->except(['author_id']));
        $post->save();

        return redirect()->route('post.index');
    }

    /**
     * Remove the specified post from storage.
     *
     * @param string $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($slug)
    {
        $post = Post::where('slug', $slug)->delete();

        return redirect()->route('post.index');
    }

    /**
     * Show the form for creating a post.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $post = new Post();

        return view('post.form', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     *
     * @param string $slug
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        return view('post.form', compact('post'));
    }
}
