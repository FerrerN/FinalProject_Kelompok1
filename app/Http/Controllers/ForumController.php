<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

class ForumController extends Controller
{
    // 1. READ (Daftar Forum + API Eksternal)
    public function index(Request $request)
    {
        // A. Ambil Data Internal (Database Sendiri)
        $forums = Forum::with('user')->withCount('replies')->latest()->get();

        // B. Ambil Data Eksternal (Contoh: API Quote Random)
        // Kita gunakan try-catch agar jika API luar mati, web kita tidak ikut error
        $quote = "Tetap semangat kuliah!";
        $author = "Admin";

        try {
            // Memanggil API publik (Gratis, tanpa kunci)
            $response = Http::timeout(3)->get('https://api.quotable.io/random');
            
            if ($response->successful()) {
                $data = $response->json();
                $quote = $data['content'];
                $author = $data['author'];
            }
        } catch (\Exception $e) {
            // Jika gagal koneksi (internet mati/server sana down), biarkan default
        }

        // C. Integrasi API (Respon JSON untuk Mobile/Postman)
        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $forums,
                'daily_quote' => [ // Kita selipkan data eksternal di sini
                    'content' => $quote,
                    'author' => $author
                ]
            ], 200);
        }

        // D. Tampilan Web (Kirim variabel quote ke View)
        return view('forums.index', compact('forums', 'quote', 'author'));
    }

    // 2. CREATE (Form Buat)
    public function create() 
    { 
        return view('forums.create'); 
    }

    // 3. STORE (Simpan Baru)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required', 
            'category' => 'required'
        ]);

        $forum = Forum::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Topik berhasil diposting!',
                'data' => $forum
            ], 201);
        }

        return redirect()->route('forums.index')->with('success', 'Topik berhasil diposting!');
    }

    // 4. SHOW (Detail Forum)
    public function show(Request $request, $id)
    {
        $forum = Forum::with(['user', 'replies.user'])->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $forum
            ], 200);
        }

        return view('forums.show', compact('forum'));
    }

    // 5. EDIT (Form)
    public function edit($id)
    {
        $forum = Forum::findOrFail($id);
        if ($forum->user_id !== Auth::id()) {
            abort(403);
        }
        return view('forums.edit', compact('forum'));
    }

    // 6. UPDATE (Simpan Perubahan)
    public function update(Request $request, $id)
    {
        $forum = Forum::findOrFail($id);

        if ($forum->user_id !== Auth::id()) {
            return $request->wantsJson() 
                ? response()->json(['message' => 'Unauthorized'], 403) 
                : abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category' => 'required'
        ]);

        $forum->update($request->only(['title', 'content', 'category']));

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Topik berhasil diperbarui!',
                'data' => $forum
            ], 200);
        }

        return redirect()->route('forums.show', $forum->id)->with('success', 'Topik berhasil diperbarui!');
    }

    // 7. DESTROY (Hapus)
    public function destroy(Request $request, $id)
    {
        $forum = Forum::findOrFail($id);

        if ($forum->user_id !== Auth::id()) {
            return $request->wantsJson() 
                ? response()->json(['message' => 'Unauthorized'], 403) 
                : abort(403);
        }

        $forum->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Topik berhasil dihapus!'
            ], 200);
        }

        return redirect()->route('forums.index')->with('success', 'Topik berhasil dihapus!');
    }

    // 8. REPLY (Balas Komentar)
    public function reply(Request $request, $id)
    {
        $request->validate(['content' => 'required']);

        $reply = ForumReply::create([
            'user_id' => Auth::id(),
            'forum_id' => $id,
            'content' => $request->content
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Balasan terkirim!',
                'data' => $reply->load('user')
            ], 201);
        }

        return back()->with('success', 'Balasan terkirim!');
    }
}