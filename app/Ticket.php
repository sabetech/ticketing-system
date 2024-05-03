<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;
use Carbon\Carbon;
use App\Rate;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;
    //
    protected $table = 'toll_tickets';

    public function rate(){
        return $this->belongsTo('App\Rate', 'rate_title', 'id');
    }

    public function agent(){
        return $this->belongsTo('\App\Agent', 'agent_name', 'id');
    }

    public static function saveTicket($ticket, $rate) {

        if (Ticket::where('title', $ticket->title)->exists()) return false; //it means ticket has already been saved

        $ticket->amount = $rate->amount;

        $ticket->paid = !$rate->is_postpaid;

        Log::info($ticket);

        $ticket->save();
        return $ticket;

    }

    public static function saveTraderPayment($ticket, $amount) {
        if (Ticket::where('title', $ticket->title)->exists()) return false; //it means ticket has already been saved

        $ticket->amount = $amount;

        Log::info($ticket);

        $ticket->save();

        return $ticket;
    }

    public static function bulkSaveTicket($tickets) {

        $chunkedTickets = array_chunk($tickets, 500, true);

        foreach($chunkedTickets as $chunkedTicket) {

            Ticket::insert($chunkedTicket);

        }
        return true;
    }

    public static function getTickets($date) {

        return self::with('rates_v2')->where('issued_date_time', '>=', $date)->orderBy('issued_date_time', 'desc')->get();

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
            ->where('rates_v2.is_postpaid', 1)->orderBy('toll_tickets.issued_date_time', 'desc')->get();

        return $thirdPartyTickets;
    }

    public static function makePayment($dateRange, $amount, $rateTitle) {
        $rate = Rate::find($rateTitle);

        $totalTicketsForRateClient = self::whereBetween('issued_date_time',[$dateRange->from, $dateRange->to])
                                        ->where('rate_title', $rateTitle)
                                        ->where('paid', 0)->orderBy('issued_date_time')->get();

        $numberOfTickets = intval($amount / $rate->amount);
        $count = 0;

        foreach($totalTicketsForRateClient as $unPaidTicket) {
            if ($count >= $numberOfTickets) {
                break;
            }
            $unPaidTicket->paid = 1;
            $unPaidTicket->save();
            $count++;
        }

        return $count;

    }

    public static function getTaskforceTicketsByDateRange($form, $to) {
        // $taskForceTickets = self::with(['rate', 'agent'])
        //                         ->where('rates_v2.title', 'LIKE', 'Taskforce%')
        //                         ->whereBetween('issued_date_time', [$form, $to])
        //                         ->select([
        //                                     'toll_tickets.id',
        //                                     'toll_tickets.title',
        //                                     'toll_tickets.rate_title',
        //                                     'toll_tickets.car_number',
        //                                     'toll_tickets.issued_date_time',
        //                                     'toll_tickets.agent_name',
        //                                     'toll_tickets.amount',
        //                                     'rates_v2.title',
        //                                     'users.username',
        //                                     'users.fname',
        //                                     'users.lname',
        //                                     'users.phone',
        //                         ])->get();

        $taskForceTickets = self::join('rates_v2', 'rates_v2.id', '=', 'toll_tickets.rate_title')
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

}
