<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Rate;
use App\PostpaidCustomer;

class GeneratePostpaidCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-postpaid-customers';

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
        $rates = Rate::where('is_postpaid', true)->get();
        foreach($rates as $rate) {
            PostpaidCustomer::create([
                'name' => $rate->title,
                'rate_id' => $rate->id,
                'station_id' => $rate->station->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
