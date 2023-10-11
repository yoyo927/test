<?php

namespace App\Console;
use DB;;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //  
        Commands\DeleteChartRecords::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /** $schedule->command('inspire')->hourly();
        *注意時區配置 app\config\app.php
        *在Linux上可以執行 php /path/to/artisan schedule:run 1>> /dev/null 2>&1 向伺服器的Crontab檔案新增一個記錄
        *在windows要去公作排程設定
        */
        
        $schedule->command('DeleteChartRecords:name')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
