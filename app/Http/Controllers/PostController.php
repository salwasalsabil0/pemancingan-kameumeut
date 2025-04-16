<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use DOMDocument;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')
            ->where('user_id', auth()->user()->id)
            ->get();

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg',
        ], [
            'title.required' => 'Judul harus diisi',
            'description.required' => 'Deskripsi harus diisi',
            'category.required' => 'Kategori harus diisi',
            'thumbnail.required' => 'Thumbnail harus diisi',
            'thumbnail.image' => 'File harus berupa gambar',
            'thumbnail.mimes' => 'File harus berupa jpeg, png, atau jpg',
        ]);

        $domDescription = $request->description;
        $dom = new DomDocument();
        $dom->loadHtml($domDescription, 9);
        $description = $dom->saveHTML();

        $thumbnailPath = $request->file('thumbnail')->store('thumbnail-posts', 'public');

        Post::create([
            'title' => $request->title,
            'description' => $description,
            'category' => $request->category,
            'user_id' => $request->user_id,
            'thumbnail' => $thumbnailPath
        ]);

        session()->flash('success', 'Data post berhasil ditambahkan');
        return redirect()->route('admin.postIndex');
    }

    public function show($id)
    {
        $post = Post::find($id);

        if (!$post || $post->user_id != auth()->user()->id) {
            return view('admin.404');
        }

        return view('admin.posts.detail', compact('post'));
    }
    public function edit($id)
    {
        $post = Post::find($id);

        if (!$post || $post->user_id != auth()->user()->id) {
            return view('admin.404');
        }

        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'thumbnail' => 'image|mimes:jpeg,png,jpg',
        ], [
            'title.required' => 'Judul harus diisi',
            'description.required' => 'Deskripsi harus diisi',
            'category.required' => 'Kategori harus diisi',
            'thumbnail.image' => 'File harus berupa gambar',
            'thumbnail.mimes' => 'File harus berupa jpeg, png, atau jpg',
        ]);

        $post = Post::find($id);

        $domDescription = $request->description;
        $dom = new DomDocument();
        $dom->loadHtml($domDescription, 9);
        $description = $dom->saveHTML();


        if ($request->hasFile('thumbnail')) {
            // Hapus gambar lama dari sistem penyimpanan
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }

            // Simpan gambar baru dan dapatkan path-nya
            $thumbnailPath = $request->file('thumbnail')->store('thumbnail-posts', 'public');
            $post->thumbnail = $thumbnailPath;
        }


        $post->update([
            'title' => $request->title,
            'description' => $description,
            'category' => $request->category,
            'user_id' => $request->user_id,
        ]);

        session()->flash('success', 'Data postingan berhasil diperbarui');
        return redirect()->route('admin.postIndex');
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) {
            session()->flash('error', 'Data lapangan tidak ditemukan');
            return redirect()->back();
        }

        // Hapus file gambar dari penyimpanan
        if ($post->thumbnail != null) {
            Storage::disk('public')->delete($post->thumbnail);
        }

        $post->delete();
        session()->flash('success', 'Data postingan berhasil dihapus');
        return redirect()->route('admin.postIndex');
    }

    // User Berita
    public function getBerita()
    {
        $posts = Post::with('user')
        ->where('category', 'Berita')
        ->paginate(5);

        return view('user.berita.index', compact('posts'));
    }

    public function showBerita($id)
    {
        $post = Post::find($id);
        
        if(!$post) {
            abort(404);
        }

        return view('user.berita.detail', compact('post'));
    }
    // User Event
    public function getEvent()
    {
        $posts = Post::with('user')
        ->where('category', 'Event')
        ->paginate(5);

        return view('user.event.index', compact('posts'));
    }

    public function showEvent($id)
    {
        $post = Post::find($id);
        
        if(!$post) {
            abort(404);
        }

        return view('user.event.detail', compact('post'));
    }

    
}
