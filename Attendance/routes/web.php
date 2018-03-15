<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index');
Route::get('/home','HomeController@index');
Route::get('/live-chat','LiveChatController@index');
Route::get('/management','ManagementController@index');
Route::get('/deleteDeclineRequest','ModuleController@deleteDeclineRequest');
Route::get('/polling','PollingController@index');
Auth::routes();

Route::post('/addModule', 'ModuleController@addModule');
Route::post('/changeLiveChatModule', 'ModuleController@changeLiveChatModule');
Route::post('/sendLiveChatText','ConversationController@sendLiveChatMessage');
Route::get('/getChatMessage','ConversationController@getMessage');
Route::post('/selectModule','ModuleController@selectModule');
Route::post('/acceptRequest','ManagementController@acceptRequest');
Route::post('/addStudentToModule','ManagementController@addStudentToModule');
Route::post('/deleteStudentInModule','ManagementController@deleteStudentInModule');
Route::post('/deleteMessage','ConversationController@deleteMessage');
Route::post('/createPoll','PollingController@createPoll');
Route::post('/createTutor','ManagementController@createTutor');
Route::post('/saveResponse','PollingController@saveResponse');
Route::get('/getClassroomPolling','PollingController@getClassroomPolling');