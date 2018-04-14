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

// Navigation bar
Route::get('/', 'HomeController@index');
Route::get('/home','HomeController@index');
Route::get('/live-chat','LiveChatController@index');
Route::get('/management','ManagementController@index');
Route::get('/deleteDeclineRequest','ModuleController@deleteDeclineRequest');
Route::get('/polling','PollingController@index');
Route::get('/reward','RewardController@index');
Route::get('/attendance','AttendanceController@index');
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
Route::post('/changeToStudent','ManagementController@changeToStudent');
Route::post('/addListOfStudents','ManagementController@addListOfStudents');

//Polling controller
Route::post('/createPoll','PollingController@createPoll');
Route::post('/createLesson','PollingController@createLesson');
Route::post('/saveResponse','PollingController@saveResponse');
Route::post('/createActiveLesson','PollingController@createActiveLesson');
Route::post('/createGraph','PollingController@createGraph');
Route::get('/nextLessonQuestion','PollingController@nextLessonQuestion');
Route::get('/getUpdatePolling','PollingController@getUpdatePolling');
Route::get('/getTotalAmountLesson','PollingController@getTotalAmountLesson');
Route::get('/getLessonsFromModule','PollingController@getLessonsFromModule');
Route::get('/getAllLessonsFromModule','PollingController@getAllLessonsFromModule');
Route::get('/getQuestionsFromLesson','PollingController@getQuestionsFromLesson');
Route::get('/stopLesson','PollingController@stopLesson');

//Reward controller
Route::post('/createReward','RewardController@createReward');
Route::post('/deleteReward','RewardController@deleteReward');
Route::post('/claimReward','RewardController@claimReward');
Route::post('/removeAward','RewardController@removeAward');

//Attendance controller
Route::get('/recordAttendance','AttendanceController@recordAttendance');
Route::get('/getLiveUsers','AttendanceController@getLiveUsers');
Route::post('/setAttendanceSetting','AttendanceController@setAttendanceSetting');