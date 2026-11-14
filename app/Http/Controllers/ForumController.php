<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ForumController extends Controller
{
    public function index(): Response
    {
        $threads = ForumThread::query()
            ->with('user:id,name')
            ->withCount('replies')
            ->orderByDesc('is_pinned')
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (ForumThread $thread): array => [
                'id' => $thread->id,
                'title' => $thread->title,
                'excerpt' => str($thread->body)->limit(160)->value(),
                'author' => $thread->user->name,
                'replies_count' => $thread->replies_count,
                'is_locked' => $thread->is_locked,
                'is_pinned' => $thread->is_pinned,
                'updated_at' => $thread->updated_at->translatedFormat('d M Y H:i'),
            ]);

        return Inertia::render('Forum/Index', [
            'threads' => $threads,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:120'],
            'body' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $thread = $request->user()->forumThreads()->create($data);

        return redirect()->route('forum.show', $thread)->with('success', 'Diskusi dibuat.');
    }

    public function show(Request $request, ForumThread $thread): Response
    {
        $thread->load('user:id,name')->loadCount('replies');

        $replies = $thread->replies()
            ->with('user:id,name')
            ->oldest()
            ->paginate(30)
            ->withQueryString()
            ->through(fn (ForumReply $reply): array => [
                'id' => $reply->id,
                'body' => $reply->body,
                'author' => $reply->user->name,
                'user_id' => $reply->user_id,
                'created_at' => $reply->created_at->translatedFormat('d M Y H:i'),
            ]);

        return Inertia::render('Forum/Show', [
            'thread' => [
                'id' => $thread->id,
                'title' => $thread->title,
                'body' => $thread->body,
                'author' => $thread->user->name,
                'user_id' => $thread->user_id,
                'is_locked' => $thread->is_locked,
                'is_pinned' => $thread->is_pinned,
                'created_at' => $thread->created_at->translatedFormat('d M Y H:i'),
                'replies_count' => $thread->replies_count,
            ],
            'replies' => $replies,
            'is_owner' => $thread->user_id === $request->user()->id,
        ]);
    }

    public function reply(Request $request, ForumThread $thread): RedirectResponse
    {
        if ($thread->is_locked) {
            return back()->with('error', 'Diskusi ini dikunci. Balasan baru tidak bisa ditambahkan.');
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:5000'],
        ]);

        $thread->replies()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        $thread->touch();

        return back()->with('success', 'Balasan dikirim.');
    }

    public function destroy(Request $request, ForumThread $thread): RedirectResponse
    {
        $this->authorizeOwnerOrAdmin($request, $thread->user_id);

        $thread->delete();

        return redirect()->route('forum.index')->with('success', 'Diskusi dihapus.');
    }

    public function destroyReply(Request $request, ForumReply $reply): RedirectResponse
    {
        $this->authorizeOwnerOrAdmin($request, $reply->user_id);

        $reply->delete();

        return back()->with('success', 'Balasan dihapus.');
    }

    public function toggleLock(Request $request, ForumThread $thread): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $thread->update(['is_locked' => ! $thread->is_locked]);

        return back()->with('success', $thread->is_locked ? 'Diskusi dikunci.' : 'Diskusi dibuka.');
    }

    public function togglePin(Request $request, ForumThread $thread): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $thread->update(['is_pinned' => ! $thread->is_pinned]);

        return back()->with('success', $thread->is_pinned ? 'Diskusi disematkan.' : 'Sematan dilepas.');
    }

    private function authorizeOwnerOrAdmin(Request $request, int $ownerId): void
    {
        abort_unless($request->user()->id === $ownerId || $request->user()->isAdmin(), 403);
    }
}
