<?php

namespace App\Console\Commands;
use App\Models\Master\Bus;
use Illuminate\Console\Command;
use Session;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

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
        $i =0;
        $id = random_int(10,50);
        
        for($i = 0; $i < random_int(10,50); $i++)
        {
       $bus = new Bus ;
       
       $bus->user_id = $id;
       $bus->session_id = 1;
       $bus->branch_id = 1;
       $bus->save();
        }
        \Log::info("Cron is working fine!".' entry saved : '.$i);
    }
}
