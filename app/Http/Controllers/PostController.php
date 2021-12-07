<?php

namespace App\Http\Controllers;

use App\Jobs\PostsScraper;
use App\Models\Post;
use App\Services\ScraperService\ScraperService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function __construct()
    {
        // $this->post = new Post;
    }

    public function index(Request $request) {
        $posts = Post::selectRaw('DATE(article_date) as date, group_concat(title, " ", excerpt separator " ") as context')->groupBy('date')->get();
        dd($posts);
        $posts = Post::query();

        // checking if request have date filter
        if ($request->date) {
            $posts->whereDate('article_date', date(Post::DATE_FORMAT, strtotime($request->date)));
        }

        // checking if request have searched string
        if ($request->search) {
            $posts->where(function($q) use($request) {
                $searched_words = explode(' ', $request->search);

                $q->where('title', 'LIKE', '%' . ($first_word = array_splice($searched_words, 0, 1)[0]) . '%');
                $q->orWhere('author', 'LIKE', '%' . $first_word . '%');
                $q->orWhere('excerpt', 'LIKE', '%' . $first_word . '%');
                
                foreach($searched_words as $word) {
                    $q->orWhere('title', 'LIKE', '%' . $word . '%');
                    $q->orWhere('author', 'LIKE', '%' . $word . '%');
                    $q->orWhere('excerpt', 'LIKE', '%' . $word . '%');
                }
            });
        }

        $posts = $posts->get();
        // dd($posts);
        return inertia('Posts/Index', compact('posts'));
    }

    function test() {
        $post = Post::first();
        $words = str_word_count(strtolower("$post->title $post->excerpt"), 1);
        $words = array_filter($words, function($word) {
            return strlen($word) > 4;
        });
        $words = array_count_values($words);
        $max = max($words);
        $word = array_keys(array_filter($words, function ($word) use ($max) {
            return $word == $max;
        }))[0];
        dd($word);
    }

    function detectMostUsedWord() {

        dd(DB::table('posts')->limit(10)->get());

        $posts = Post::all();
        dd($posts); 
    }
}
