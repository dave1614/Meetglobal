<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use App\Models\MainModel;

class Kernel extends ConsoleKernel
{


    // public function __construct(){
    //     $this->main_model = new MainModel();
    // }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\DemoCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function(){
            $date = date("j M Y");
            $time = date("h:i:sa");
            // DB::table('test')->insert(array('date' => $date,'time' => $time));
            // $query = DB::table('users')->where('id','<',15000)->get();
            // if($query->count() > 0 ){
            //     foreach($query as $row){
            //         DB::table('users_test')->insert((Array) $row);
            //     }
            // }
            // $this->main_model->insertRecord();
            $main_model = new MainModel(); 
// echo $x->helloWorld();
            $main_model->insertRecord();

            $query = DB::table('users')->where('coop_db_id','!=',NULL)->select('id','coop_db_id')->get();
            if($query->count() > 0){
                $str = json_encode($query);
                $main_model->addMinifyAccountWebhookJsonData($str,$date,$time);
                foreach($query as $row){
                    $user_id = $row->id;
                    $coop_db_id = $row->coop_db_id;
                    $main_model->performWeeklyChecksOnUsersCoopInvEarnings($user_id,$coop_db_id,$date,$time);
                }
            }

            // $main_model->performWeeklyChecksOnUsersCoopInvEarnings($user_id,$coop_db_id,$date,$time);
            // $date = "04 Jun 2022";
            // $query = DB::table('users')->where('coop_db_id','!=',NULL)->select("id","coop_db_id")->get();
            // if($query->count() > 0 ){
            //     foreach($query as $row){
            //         $user_id = $row->id;
            //         $coop_db_id = $row->coop_db_id;
            //         $this->main_model->performWeeklyChecksOnUsersCoopInvEarnings($user_id,$coop_db_id,$date,$time);
            //     }
            // }
        })->dailyAt('23:59');
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
