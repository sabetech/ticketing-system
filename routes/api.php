<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// creates Routes in v2
Route::group(['prefix' => 'v2'], function() {
    Route::get('/health', function(Request $req) {
        return '[GET]I\'m Alive and Well!!!';
    });

    Route::post('/health', function(Request $req) {
        return '[POST]I\'m Alive and Well!!!';
    });

    Route::post('/login', 'Auth\LoginController@login')->name('login.api');
    Route::post('/register', 'API\RegisterController@register')->name('register.api');;

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('/logout', 'Auth\LoginController@logout');
        Route::get('/user', function(Request $req) {
            $user = Auth::guard('api')->user();
            Log::info($user);
            return $user;
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/all', 'API\UserController@getAllUsers');
            Route::get('/{id}/detail', "API\UserController@show");
            Route::get('/{id}/delete', 'API\UserController@deleteUser');
            Route::post('/create', 'API\UserController@createUser');
            Route::get('/roles', 'API\UserController@roles');

        });

        Route::group(['prefix' => 'agent'], function () {
            Route::get('/{id}/rates', 'API\RateController@getRatesForAgent');
            Route::get('/{id}/tickets', "API\TicketController@getAgentTickets");
            Route::get('/{id}/tickets/count', "API\TicketController@getAgentTicketsCount");
            Route::post('/{id}/ticket-submit', "API\TicketController@postTicketSubmit");
            Route::post('/{id}/bulk-sync', "API\TicketController@postSyncTicket");
            Route::post('/{id}/ping',  "API\AgentController@ping");
            Route::get('/count', "API\AgentController@agentCount");
            Route::get('/onlinestatus', "API\AgentController@agentOnlineStatus");
            Route::get('/all', "API\AgentController@getAllAgents");
            Route::get('/{id}/detail', "API\AgentController@show");
        });

        Route::group(['prefix' => 'ticket'], function () {
            Route::post('/bulk-sync', "API\TicketController@postSyncTicket");
            Route::get('/all', "API\TicketController@getTicketByDate");
            Route::get('/range', "API\TicketController@getTicketByDateRange");
            Route::get('/count', "API\TicketController@getTicketCountByDate");
            Route::get('/taskforce', 'API\TicketController@getTaskforceTicketsByDateRange');
            Route::get('/revenue', "API\TicketController@calculateTicketRevenueByDate");
            Route::get('/unpaidAmount', "API\TicketController@calculateUnpaidTickets");
            Route::get('/unpaidTickets', "API\TicketController@countUnpaidTickets");
            Route::get('/third-party-tickets', "API\TicketController@getThirdPartyTickets");
            Route::get('/agentaggregates', "API\TicketController@getAgentAggregate");
            Route::delete('/{id}/delete', "API\TicketController@deleteTicket");
            Route::post('/{id}/edit', 'API\TicketController@editTicket');
        });

        Route::group(['prefix' => 'rates'], function () {
            Route::get('agent/{id}', "API\RateController@index");
            Route::get('/', "API\RateController@listRates");
            Route::post('/{id}/delete', "API\RateController@delete");
            Route::post('/create', "API\RateController@create");
            Route::post('/{id}/edit', "API\RateController@edit");
            Route::delete('/{id}', "API\RateController@delete");
            Route::post('/makepayment', 'API\RateController@makePayment');
        });
    });

    Route::group(['prefix' => 'station'], function () {
        Route::get('/all', "API\StationController@getAllStations");
    });
});

