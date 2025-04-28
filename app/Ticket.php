<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;
use Carbon\Carbon;
use App\Rate;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Ticket extends Model
{
    use SoftDeletes;
    //
    protected $table = 'toll_tickets';

    public function rate(){
        return $this->belongsTo('App\Rate', 'rate_title', 'id');
    }

    public function agent(){
        return $this->belongsTo('\App\Agent', 'agent_name', 'id')->withTrashed();
    }

    public function station(){
        return $this->belongsTo('\App\Station', 'station_name', 'id');
    }

    public static function saveTicket($ticket, $rate) {

        if (Ticket::where('title', $ticket->title)->exists()) return false; //it means ticket has already been saved

        $ticket->amount = $rate->amount;

        $ticket->paid = !$rate->is_postpaid;

        Log::info($ticket);

        $ticket->save();

        //update the car_number_index table
        DB::insert('insert ignore into car_number_index (car_number, created_at, updated_at) values (?, ?, ?)', [$ticket->car_number, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);

        return $ticket;

    }

    public static function saveTraderPayment($ticket, $amount) {
        if (Ticket::where('title', $ticket->title)->exists()) return false; //it means ticket has already been saved

        $ticket->amount = floatval(str_replace(',', '', $amount));

        Log::info($ticket);

        $ticket->save();

        DB::insert('insert ignore into car_number_index (car_number, created_at, updated_at) values (?, ?, ?)', [$ticket->car_number, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);

        return $ticket;
    }

    public static function bulkSaveTicket($tickets) {

        $chunkedTickets = array_chunk($tickets, 500, true);

        foreach($chunkedTickets as $chunkedTicket) {
            try {
                Ticket::insert($chunkedTicket);
                DB::insert('insert ignore into car_number_index (car_number, created_at, updated_at) values (?, ?, ?)', [$chunkedTicket['car_number'], date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
            }catch(\Illuminate\Database\QueryException $e) {
                Log::info($e->getMessage());
            }
        }
        return true;
    }

    public static function getTickets($date) {

        return self::with('rate')->where('issued_date_time', '>=', $date)->orderBy('issued_date_time', 'desc')->get();

    }

    public static function getTicketsFromRange($startDate, $endDate) {

        $tickets = self::select('toll_tickets.id as id', 'toll_tickets.*', 'stations.id as station_id', 'stations.name')->join('stations', 'stations.id', '=', 'toll_tickets.station_name')->with(['rate', 'agent'])->whereBetween('issued_date_time', [$startDate, $endDate])->orderBy('issued_date_time', 'desc')->get();

        return $tickets;

    }

    public static function getTicketCount($date) {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        return self::whereBetween('issued_date_time',[$startOfDay, $endOfDay])->count();
    }

    public static function calculateRevenue($date) {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        $amountSum = self::whereBetween('issued_date_time', [$startOfDay, $endOfDay])
                        ->where('paid', 1)->sum('amount');

        return $amountSum;
    }

    public static function calculateUnpaidTickets($date) {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        $unpaidAmount = self::whereBetween('issued_date_time',[$startOfDay, $endOfDay])
                        ->where('paid', 0)->sum('amount');

        return $unpaidAmount;
    }

    public static function countUnpaidTickets($date) {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        $unpaidTickets = self::whereBetween('issued_date_time', [$startOfDay, $endDate])
                    ->where('paid', 0)->count();

        return $unpaidAmount;
    }

    public static function getThirdPartyTickets($from, $to) {

        $thirdPartyTickets = self::join('rates_v2', 'toll_tickets.rate_title', '=', 'rates_v2.id')->whereBetween('issued_date_time', [$from, $to])
            ->where('rates_v2.is_postpaid', 1)->orderBy('toll_tickets.issued_date_time', 'desc')
            ->with(['rate', 'agent'])
            ->select([
                'toll_tickets.id',
                'toll_tickets.title',
                'toll_tickets.rate_title',
                'toll_tickets.car_number',
                'toll_tickets.issued_date_time',
                'toll_tickets.agent_name',
                'toll_tickets.amount',
                'toll_tickets.paid',
                'rates_v2.id as rate_id',
                'rates_v2.title as rate_title',
                'rates_v2.is_postpaid',
                'rates_v2.amount as rate_amount'
            ])
            ->get();

        return $thirdPartyTickets;
    }

    public static function makePayment($dateRange, $rateTitle) {

        $totalTicketsForRateClient = self::whereBetween('issued_date_time',[$dateRange->from, $dateRange->to])
                                        ->where('rate_title', $rateTitle)
                                        ->where('paid', 0)
                                        ->update(['paid' => 1]);

        return $totalTicketsForRateClient;
    }

    public static function getTaskforceTicketsByDateRange($form, $to) {

        $taskForceTickets = self::join('rates_v2', 'rates_v2.id', '=', 'toll_tickets.rate_title')
                                ->join('users', 'users.id', '=', 'toll_tickets.agent_name')
            ->where('rates_v2.title', 'LIKE', 'Taskforce%')
            ->whereBetween('issued_date_time', [$form, $to])
            ->select([
            'toll_tickets.id',
            'toll_tickets.title',
            'toll_tickets.rate_title',
            'toll_tickets.car_number',
            'toll_tickets.issued_date_time',
            'toll_tickets.agent_name',
            'toll_tickets.amount',
            'rates_v2.title',
            'users.username',
            'users.fname',
            'users.lname',
            'users.phone',
                ])->get();

        return $taskForceTickets;
    }

    public static function getTicketsGroupByAgents($date) {

        if (!$date) {
            $date = date('Y-m-d');
        }
        $from = Carbon::parse($date)->startOfDay();
        $to = Carbon::parse($date)->endOfDay();

        $ticketsByAgent = self::join('users', 'users.id', '=', 'toll_tickets.agent_name')
                            ->whereBetween('issued_date_time', [$from, $to])
                            ->select('toll_tickets.agent_name', 'users.fname', DB::raw('sum(toll_tickets.amount) as total, count(toll_tickets.id) as tickets_issued'))
                            ->groupBy('toll_tickets.agent_name', 'users.fname')->get();


        return $ticketsByAgent;
    }

    public static function searchAutoCompleteTickets($searchTerm, $field) {

        $results = [];
        switch($field) {
            case 'car_number':
                $results = self::where('car_number', 'LIKE', "$searchTerm%")->distinct('car_number')->orderBy('issued_date_time', 'desc')->take(10)->pluck('car_number')->toArray();
                break;
            case 'agent':
                $results = Agent::where('fname', 'LIKE', "$searchTerm%")->orWhere('lname', 'LIKE', "$searchTerm%")->select(DB::raw('CONCAT(id," ",fname," ",lname) AS full_name'))->take(10)->pluck('full_name')->toArray();
                break;
            case 'ticket_id':
                $results = self::where('title', 'LIKE', "$searchTerm%")->orderBy('issued_date_time', 'desc')->take(10)->pluck('title')->toArray();
                break;
        }

        return $results;
    }

    public static function search($searchTerm, $field){
        $results = null;

        switch($field) {
            case 'car_number':
                $results = self::select('toll_tickets.id as id', 'toll_tickets.*', 'stations.id as station_id', 'stations.name')->join('stations', 'stations.id', '=', 'toll_tickets.station_name')->with(['rate', 'agent'])->where('car_number', 'LIKE', $searchTerm)->orderBy('issued_date_time', 'desc')->get();
                break;
            case 'agent':
                $id = explode(" ", $searchTerm)[0];
                $agentId = Agent::whereId($id)->withTrashed()->first()->id;
                $results = self::select('toll_tickets.id as id', 'toll_tickets.*', 'stations.id as station_id', 'stations.name')->join('stations', 'stations.id', '=', 'toll_tickets.station_name')->with(['rate', 'agent'])->where('agent_name', $agentId)->orderBy('issued_date_time', 'desc')->get();
                break;
            case 'ticket_id':
                $results = self::select('toll_tickets.id as id', 'toll_tickets.*', 'stations.id as station_id', 'stations.name')->join('stations', 'stations.id', '=', 'toll_tickets.station_name')->with(['rate', 'agent'])->where('title', $searchTerm)->orderBy('issued_date_time', 'desc')->get();
                break;
        }

        return $results;
    }
}
