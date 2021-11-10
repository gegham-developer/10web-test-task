<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\ScraperService\PostsScraperService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(PostsScraperService $scraper)
    {
        $this->scraper = $scraper;
    }

    public function index() {
        $posts = Post::all();
        dd($posts);
        return view('welcome', compact('posts'));
    }
}
