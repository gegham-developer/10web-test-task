<?php

namespace App\Console\Commands;

use App\Jobs\MostUsedWordDetector;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MostUsedWordDetectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'most_used_word:detect {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect most used word for day';

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
        $is_all = $this->option('all');
        $date = Carbon::yesterday();
        $date = '2018-08-24';
        
        dispatch(new MostUsedWordDetector($date, $is_all));

        return Command::SUCCESS;
    }
}
