<?php

namespace App\Jobs;

use App\Models\MostUsedWord;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class MostUsedWordDetector implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $date;
    private int $all;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $date = 'now', bool $all = false)
    {
        $this->connection = 'sync';
        $this->all = $all;
        $this->date = date('Y-m-d', strtotime($date));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // for previous day
        $context = Post::whereDate('article_date', $this->date)->selectRaw('group_concat(title, " ", excerpt separator " ") as context')->first();

        if ($this->all) {
            // for all days (needs to run one time)
            $contexts = Post::selectRaw('DATE(article_date) as date, group_concat(title, " ", excerpt separator " ") as context')->groupBy('date')->get();
        }

        $words = str_word_count(strtolower($context), 1);
        $words_count = [];

        foreach($words as $word) {
            if (strlen($word) > 4) {
                $words_count[$word] = isset($words_count[$word]) ? $words_count[$word] + 1 : 1;
            }
        }

        $count = max($words_count);
        foreach($words_count as $word => $word_count) {
            if ($word_count == $count) {
                $most_used_word = $word;
                break;
            }
        }

        MostUsedWord::createOrUpdate([
            'date' => $this->date
        ],[
            'word' => $most_used_word,
            'count' => $count,
            'date' => $this->date
        ]);
    }

    private function detectAndSave($date, $string) {
        $words = str_word_count(strtolower($context), 1);
        $words_count = [];

        foreach($words as $word) {
            if (strlen($word) > 4) {
                $words_count[$word] = isset($words_count[$word]) ? $words_count[$word] + 1 : 1;
            }
        }

        $count = max($words_count);
        foreach($words_count as $word => $word_count) {
            if ($word_count == $count) {
                $most_used_word = $word;
                break;
            }
        }

        MostUsedWord::createOrUpdate([
            'date' => $this->date
        ],[
            'word' => $most_used_word,
            'count' => $count,
            'date' => $this->date
        ]);
    }
}
