<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Ticket;
use DB;

class UpdateCarNumberIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:index_car_numbers';

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
     * @return mixed
     */
    public function handle()
    {
        //
        //Get all car numbers in chunks of 1000 insert them into the car_number_index table
        $carNumbers = Ticket::select('car_number')->chunk(1000, function($carNumbers) {
            foreach($carNumbers as $carNumber) {
                // DB::table('car_number_index')->insertOrIgnore(['car_number' => $carNumber->car_number]);
                DB::insert('insert ignore into car_number_index (car_number) values (?)', [$carNumber->car_number]);
            }
        });

    }
}
