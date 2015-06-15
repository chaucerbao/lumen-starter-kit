<?php

namespace App\Http\Controllers\Auth;

use App\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::all();

        return view('tag.index', compact('tags'));
    }

    /**
     * Store a newly created tag in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, Tag::$rules);

        $tag = Tag::create($request->all());

        return redirect()->route('tag.index');
    }

    /**
     * Update the specified tag in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, Tag::$rules);

        $tag = Tag::findOrFail($id);
        $tag->update($request->all());

        return redirect()->route('tag.index');
    }

    /**
     * Remove the specified tag from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return redirect()->route('tag.index');
    }

    /**
     * Show the form for creating a tag.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tag = new Tag();

        return view('tag.form', compact('tag'));
    }

    /**
     * Show the form for editing the specified tag.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tag = Tag::findOrFail($id);

        return view('tag.form', compact('tag'));
    }
}
