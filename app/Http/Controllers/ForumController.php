<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class ForumController extends Controller
{
    // 1. READ (Daftar Forum)
    public function index()
    {
        $forums = Forum::with('user')->withCount('replies')->latest()->get();
        return view('forums.index', compact('forums'));
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

        Forum::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category
        ]);

        return redirect()->route('forums.index')->with('success', 'Topik berhasil diposting!');
    }

    // 4. SHOW (Detail Forum)
    public function show($id)
    {
        $forum = Forum::with(['user', 'replies.user'])->findOrFail($id);
        return view('forums.show', compact('forum'));
    }

    // 5. EDIT (Form Edit - FITUR BARU)
    public function edit($id)
    {
        $forum = Forum::findOrFail($id);

        // Security: Cek Pemilik
        if ($forum->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengedit topik ini.');
        }

        return view('forums.edit', compact('forum'));
    }

    // 6. UPDATE (Simpan Perubahan - FITUR BARU)
    public function update(Request $request, $id)
    {
        $forum = Forum::findOrFail($id);

        if ($forum->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category' => 'required'
        ]);

        $forum->update([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category
        ]);

        return redirect()->route('forums.show', $forum->id)->with('success', 'Topik berhasil diperbarui!');
    }

    // 7. DESTROY (Hapus - FITUR BARU)
    public function destroy($id)
    {
        $forum = Forum::findOrFail($id);

        if ($forum->user_id !== Auth::id()) {
            abort(403);
        }

        $forum->delete();
        return redirect()->route('forums.index')->with('success', 'Topik berhasil dihapus!');
    }

    // 8. REPLY (Balas Komentar)
    public function reply(Request $request, $id)
    {
        $request->validate(['content' => 'required']);
        ForumReply::create([
            'user_id' => Auth::id(),
            'forum_id' => $id,
            'content' => $request->content
        ]);
        return back()->with('success', 'Balasan terkirim!');
    }
}