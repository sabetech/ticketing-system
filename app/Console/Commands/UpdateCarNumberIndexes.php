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
    protected $description = 'This command will index all car numbers in the car_number_index table';

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
        $count = 0;
        $total = Ticket::count();
        //
        //Get all car numbers in chunks of 1000 insert them into the car_number_index table
        Ticket::select('car_number')->chunk(1000, function($carNumbers) use (&$count, $total) {
            $count += 1000;
            $this->info("Processing chunk of 1000 car numbers: $count/$total");
            foreach($carNumbers as $carNumber) {
                DB::insert('insert ignore into car_number_index (car_number, created_at, updated_at) values (?, ?, ?)', [$carNumber->car_number, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
            }
        });

    }
}
