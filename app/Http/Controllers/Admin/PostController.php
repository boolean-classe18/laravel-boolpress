<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Post;
use App\Category;
use App\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'posts' => Post::all()
        ];
        return view('admin.posts.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'categories' => Category::all(),
            'tags' => Tag::all()
        ];
        return view('admin.posts.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'exists:tags,id',
            'image' => 'nullable|image|max:512'
        ]);
        $form_data = $request->all();
        $new_post = new Post();

        // verifico se è stata caricata un'immagine
        if(array_key_exists('image', $form_data)) {
            // salvo l'immagine e recupero la path
            $cover_path = Storage::put('post_covers', $form_data['image']);
            $form_data['cover'] = $cover_path;
        }

        $new_post->fill($form_data);
        // genero lo slug
        $slug = Str::slug($new_post->title);
        $slug_base = $slug;
        // verifico che lo slug non esista nel database
        $post_presente = Post::where('slug', $slug)->first();
        $contatore = 1;
        // entro nel ciclo while se ho trovato un post con lo stesso $slug
        while($post_presente) {
            // genero un nuovo slug aggiungendo il contatore alla fine
            $slug = $slug_base . '-' . $contatore;
            $contatore++;
            $post_presente = Post::where('slug', $slug)->first();
        }
        // quando esco dal while sono sicuro che lo slug non esiste nel db
        // assegno lo slug al post
        $new_post->slug = $slug;
        $new_post->save();
        // verifico se sono stati selezionati dei tag
        if(array_key_exists('tags', $form_data)) {
            // aggiungo i tag al post
            $new_post->tags()->sync($form_data['tags']);
        }

        return redirect()->route('admin.posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        if(!$post) {
            abort(404);
        }
        return view('admin.posts.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        if(!$post) {
            abort(404);
        }

        $data = [
            'post' => $post,
            'categories' => Category::all(),
            'tags' => Tag::all()
        ];

        return view('admin.posts.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'exists:tags,id',
            'image' => 'nullable|image|max:512'
        ]);


        $form_data = $request->all();
        // verifico se il titolo ricevuto dal form è diverso dal vecchio titolo
        if($form_data['title'] != $post->title) {
            // è stato modificato il titolo => devo modificare anche lo slug
            // genero lo slug
            $slug = Str::slug($form_data['title']);
            $slug_base = $slug;
            // verifico che lo slug non esista nel database
            $post_presente = Post::where('slug', $slug)->first();
            $contatore = 1;
            // entro nel ciclo while se ho trovato un post con lo stesso $slug
            while($post_presente) {
                // genero un nuovo slug aggiungendo il contatore alla fine
                $slug = $slug_base . '-' . $contatore;
                $contatore++;
                $post_presente = Post::where('slug', $slug)->first();
            }
            // quando esco dal while sono sicuro che lo slug non esiste nel db
            // assegno lo slug al post
            $form_data['slug'] = $slug;
        }

        // verifico se è stata caricata un'immagine
        if(array_key_exists('image', $form_data)) {
            // salvo l'immagine e recupero la path
            $cover_path = Storage::put('post_covers', $form_data['image']);
            $form_data['cover'] = $cover_path;
        }

        $post->update($form_data);
        // verifico se sono stati selezionati dei tag
        if(array_key_exists('tags', $form_data)) {
            // aggiungo i tag al post
            $post->tags()->sync($form_data['tags']);
        }
        return redirect()->route('admin.posts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->tags()->sync([]);
        $post->delete();
        return redirect()->route('admin.posts.index');
    }
}
