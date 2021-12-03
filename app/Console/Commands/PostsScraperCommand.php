<?php

namespace App\Console\Commands;

use App\Jobs\PostsScraper;
use App\Models\Post;
use App\Services\ScraperService\ScraperService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PostsScraperCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:scrap_posts {--from=false} {--to=false} {--limit=0} {--S|sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scraps posts from 10Web\'s blog website.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ['sync' => $sync, 'from' => $from, 'to' => $to, 'limit' => $limit] = $this->options();
        
        dispatch(new PostsScraper($from, $to, $limit, $sync));

        return Command::SUCCESS;
    }
}
