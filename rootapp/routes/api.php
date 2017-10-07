<?php

use Illuminate\Http\Request;

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


//get
Route::group(['prefix' => '/api_villa'], function() {
    Route::get('/list',"VillaController@apiList");
    Route::get('/{id?}', "VillaController@apiCreate");
});


Route::group(['prefix' => 'api/bill'],function() {

    Route::get('/list',["uses" => "ContractBillController@apiGetList", "roles" => ["account"]]);
    Route::get('/create/{contractNo}',["uses" => "ContractBillController@apiCreate", "roles" => ["contract","account"]]);
    Route::get('/edit/{billNo?}',["uses" => "ContractBillController@apiEdit","roles" => ["account"]]);
    Route::get('/search/{filter?}/{value?}',["uses" => "ContractBillController@apiSearch", "roles" => ["account"]]);
    Route::get('/prepare',"ContractBillController@apiPrepareCheques");

});

//contract api
Route::group(['prefix' => 'api_contract'],
    function() {

        Route::get('/list/{status?}',"ContractController@apiGetList");
        Route::get('/create', "ContractController@apiCreate");
        Route::get('/renew/{id}', "ContractController@apiRenew");
        Route::get('/calendar',"ContractController@apiCalendar");

    });


//calendar api
Route::group(["prefix" => "calendar"],function() {
   Route::get("/contract", "CalendarController@contract");
});

/*----------------------------------------------
*   Mobile and Web Reports API
-----------------------------------------------*/
Route::get('reports/{reportId}',"ReportController@apiShow");





//Route mockup
Route::get("mockups/main","MockupMobileController@mockMain");
Route::get("mockups/calendars","MockupMobileController@mockCalendar");
Route::get("mockups/contracts/{status?}","MockupMobileController@mockContractList");


