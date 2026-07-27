<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Publication;
use App\Models\Topic;
use App\Models\UserTopicPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $bookmarks = Bookmark::with(['publication.container', 'publication.topics'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $preferredTopics = $request->user()->topicPreferences()->with('topic')->get();

        return view('bookmarks.index', compact('bookmarks', 'preferredTopics'));
    }

    public function __invoke(Request $request)
    {
        if (! $request->user()) {
            return Redirect::back()->with('message', 'Silakan login dulu untuk menyimpan bookmark.');
        }

        $publication = Publication::findOrFail($request->input('publication_id'));

        $bookmark = Bookmark::where('user_id', $request->user()->id)
            ->where('publication_id', $publication->id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();

            return Redirect::back()->with('message', 'Bookmark dihapus.');
        }

        Bookmark::create([
            'user_id' => $request->user()->id,
            'publication_id' => $publication->id,
        ]);

        return Redirect::back()->with('message', 'Bookmark disimpan.');
    }

    public function preference(Request $request, Topic $topic)
    {
        if (! $request->user()) {
            return Redirect::back()->with('message', 'Silakan login dulu.');
        }

        $existing = UserTopicPreference::where('user_id', $request->user()->id)
            ->where('topic_id', $topic->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return Redirect::back()->with('message', 'Preferensi topik dihapus.');
        }

        UserTopicPreference::create([
            'user_id' => $request->user()->id,
            'topic_id' => $topic->id,
            'preference_type' => 'interest',
        ]);

        return Redirect::back()->with('message', 'Preferensi topik disimpan.');
    }
}
