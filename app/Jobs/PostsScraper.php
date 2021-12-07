<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\ScraperService\ScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PostsScraper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $all_posts = [];
    private $from;
    private $to;
    private $limit;
    private $scraper;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(bool $from = false, bool $to = false, int $limit = 0, bool $is_sync = false)
    {
        $is_sync ? $this->connection = 'sync' : null;
        $this->from = $from;
        $this->to = $to;
        $this->limit = $limit;

        $this->scraper = new ScraperService($from, $to, $limit);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('test');
        $this->all_posts = $this->scraper->getAll();
        
        \Log::info(DB::table(Post::getTableName())->truncate());
        DB::table(Post::getTableName())->insert($this->all_posts);
    }
}
