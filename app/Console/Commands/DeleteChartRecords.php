<?php

namespace App\Console\Commands;

use DB;     
use Illuminate\Console\Command;


class DeleteChartRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DeleteChartRecords:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        DB::delete('delete from chart');
        $this->info('Delete Chart finished');
    }
   
}
