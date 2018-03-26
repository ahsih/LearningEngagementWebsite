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
Route::get('/reward','RewardController@index');
Auth::routes();

//Add the module
Route::post('/addModule', 'ModuleController@addModule');
Route::post('/changeLiveChatModule', 'ModuleController@changeLiveChatModule');
Route::post('/selectModule','ModuleController@selectModule');

//Conversation controller
Route::post('/sendLiveChatText','ConversationController@sendLiveChatMessage');
Route::get('/getChatMessage','ConversationController@getMessage');
Route::post('/deleteMessage','ConversationController@deleteMessage');

//Management controller
Route::post('/acceptRequest','ManagementController@acceptRequest');
Route::post('/addStudentToModule','ManagementController@addStudentToModule');
Route::post('/deleteStudentInModule','ManagementController@deleteStudentInModule');
Route::post('/createTutor','ManagementController@createTutor');

//Polling controller
Route::post('/createPoll','PollingController@createPoll');
Route::post('/createLesson','PollingController@createLesson');
Route::post('/saveResponse','PollingController@saveResponse');
Route::post('/createLessonPointer','PollingController@createLessonPointer');
Route::get('/getClassroomPolling','PollingController@getClassroomPolling');
Route::get('/getTotalAmountLesson','PollingController@getTotalAmountLesson');
Route::get('/getLessonsFromModule','PollingController@getLessonsFromModule');
Route::get('/getAllLessonsFromModule','PollingController@getAllLessonsFromModule');
Route::get('/getQuestionsFromLesson','PollingController@getQuestionsFromLesson');