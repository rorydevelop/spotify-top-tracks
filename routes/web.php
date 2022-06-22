<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$session = new SpotifyWebAPI\Session(
    env('SPOTIFY_CLIENT_ID'),
    env('SPOTIFY_SECRET'),
    env('SPOTIFY_CALLBACK_URL')
);

$spotifyApi = new SpotifyWebAPI\SpotifyWebAPI();

Route::get('/', function (Request $request) use ($session, $spotifyApi) {
    $scopes = [
        'scope' => [
            'ugc-image-upload',
            // Spotify Connect
            'user-modify-playback-state',
            'user-read-playback-state',
            'user-read-currently-playing',
            // Follow
            'user-follow-modify',
            'user-follow-read',
            // Listening History
            'user-read-recently-played',
            'user-read-playback-position',
            'user-top-read',
            // Playlists
            'playlist-read-collaborative',
            'playlist-modify-public',
            'playlist-read-private',
            'playlist-modify-private',
            // Playback
            'app-remote-control',
            'streaming',
            // Users
            'user-read-email',
            'user-read-private',
            // Library
            'user-library-modify',
            'user-library-read',
        ],
    ];

    /**
     * If no token fetch request one from the API
     */
    if ($request->input('force') || !Cache::get('sptfy_token')) {
        return redirect($session->getAuthorizeUrl($scopes));
    }

    /**
     * Refresh token if expired
     * 
     */
    try {
        $spotifyApi->setAccessToken(Cache::get('sptfy_token'));
    } catch (\Throwable $th) {
        return redirect($session->getAuthorizeUrl($scopes));
    }

    /** 
     * The auth users meta
     * 
     * @var array 
     */
    $me =    $spotifyApi->me();

    /** 
     * The auth users top tracks
     * 
     * @var array 
     */
    $top =   $spotifyApi->getMyTop('tracks', ['limit' =>  10]);

    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'me' => $me,
        'top' =>  $top ?? [],
    ]);
})->name('home');

Route::get('/authtoken', function (Request $request) use ($session, $spotifyApi) {
    $code = $request->input('code');

    if ($code) {
        $session->requestAccessToken($code);

        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();

        Cache::remember('sptfy_token', 3000, function () use ($accessToken) {
            return $accessToken;
        });

        $spotifyApi->setAccessToken(Cache::get('sptfy_token'));
        print_r($spotifyApi->me());
    }

    return redirect(route('home'));
})->name('authtoken');


require __DIR__ . '/auth.php';
