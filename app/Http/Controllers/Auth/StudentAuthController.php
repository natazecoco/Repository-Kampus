<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'npm' => ['required', 'string', 'digits:8'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'npm' => $credentials['npm'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'npm' => ['NPM atau password salah.'],
            ]);
        }

        $user = Auth::user();

        if ($user?->role !== 'student') {
            Auth::logout();

            throw ValidationException::withMessages([
                'npm' => ['Akun ini tidak dapat masuk melalui halaman login mahasiswa.'],
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $bookmarks = $user->bookmarks()->with(['publication.container', 'publication.topics'])->latest()->get();
        $preferredTopics = $user->topicPreferences()->with('topic')->get();

        $totalBookmarks = $bookmarks->count();

        $topBookmarkedTopics = Topic::select('topics.id', 'topics.name', DB::raw('count(*) as bookmark_count'))
            ->join('publication_topic', 'topics.id', '=', 'publication_topic.topic_id')
            ->join('bookmarks', 'publication_topic.publication_id', '=', 'bookmarks.publication_id')
            ->where('bookmarks.user_id', $user->id)
            ->groupBy('topics.id', 'topics.name')
            ->orderByDesc('bookmark_count')
            ->limit(5)
            ->get();

        $trendingPublications = Publication::with('container')
            ->orderByDesc('views_count')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $popularMethods = Publication::whereNotNull('research_method')
            ->where('research_method', '!=', '')
            ->select('research_method', DB::raw('count(*) as total'))
            ->groupBy('research_method')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('research_method');

        return view('auth.dashboard', compact(
            'user',
            'bookmarks',
            'preferredTopics',
            'totalBookmarks',
            'topBookmarkedTopics',
            'trendingPublications',
            'popularMethods',
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->fill($data);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('student.dashboard')->with('message', 'Profil berhasil diperbarui.');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'npm' => ['required', 'string', 'digits:8', 'unique:users,npm'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'npm' => $data['npm'],
            'role' => 'student',
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
