<?php

use Illuminate\Support\Facades\Route;
use App\Events\MyEvent;
use App\User;
use App\Events\mark;

Route::get('/', function () {
    return view('home');
});

Route::group(['namespace' => 'web'], function(){
    Route::get('register', [
        'as' => 'register',
        'uses' => 'UserrController@index'
      ]);
      Route::post('register', [
        'as' => '',
        'uses' => 'UserrController@Register'
      ]);
    
      Route::get('login', [
        'as' => 'login',
        'uses' => 'UserrController@userLoginIndex'
      ]);
      Route::post('login', [
            'as' => '',
            'uses' => 'UserrController@Login'
      ]);
      Route::post('logout', [
        'as' => 'logout',
        'uses' => 'UserrController@logout'
      ]);
      Route::get('dashboard', 'UserrController@dashboard');
});

Route::group(['namespace' => 'web','prefix' => 'doctor'],function ()
{
    Route::get('register', 'DoctorrController@index');
    Route::post('user-store', 'DoctorrController@Register');
    Route::get('login', 'DoctorrController@userLoginIndex');
    Route::post('login', 'DoctorrController@Login');
    Route::get('{id}/dashboard', 'DoctorrController@dashboard')->name('doctor.dashboard');
    Route::get('logout', 'DoctorrController@logout');
});

//Route::get('post/{id}','web\NewsController@show')->middleware('auth:web');
//Route::post('post','web\NewsController@store')->middleware('auth:web');

//////////////////////////////////////////////////////////////////////////////////////////////////////////

//List news
Route::get('news','web\NewsController@index');
//List dailyprogram
Route::get('program','web\DailyProgramController@index');
//List courses
Route::get('courses','web\CourseController@index');

//---------------------------session based---------------------------//

Route::group(['namespace' => 'web'],function ()
{
////Student
Route::get('student/{id}','UserrController@dashboard')->name('std.dashboard');
Route::get('student/{id}/profile','UserrController@profile')->name('std.profile');

//List std's attending
Route::get('student/{id}/attendings','UserrController@attendings')->where('id','[0-9]+');
//new std's attending
Route::post('attending','UserrController@NewAttending');
});


////Admin
//Create new post
Route::post('post',['middleware' => ['auth:web', 'admin_web']],'web\NewsController@store')->middleware('admin_web');
//Edit post
Route::put('post',['middleware' => ['auth:web', 'admin_web']],'web\NewsController@store')->middleware('admin_web');
//new dailyprogram
Route::post('program',['middleware' => ['auth:web', 'admin_web']],'web\DailyProgramController@store')->middleware('admin_web');
//delete a post
Route::get('/del/program/{id}',['middleware' => ['auth:web', 'admin_web']],'web\DailyProgramController@destroy')->middleware('admin_web');
//delete a program
Route::get('/del/post/{id}',['middleware' => ['auth:web', 'admin_web']],'web\NewsController@destroy')->middleware('admin_web');


/////Doctor
//new std's mark
Route::post('mark','web\MarkController@store')->middleware('auth:doctors_web');
//new std's attending
Route::post('doctor/attending','web\DoctorrController@NewAttending');
//List std's attending
Route::get('doctor/student/{id}/attendings','web\DoctorrController@attendings')->where('id','[0-9]+');
//Create new course
Route::post('course','web\CourseController@store')->middleware('auth:doctors_web');
//new lecture
Route::post('upload/lecture','web\CourseController@lecture')->middleware('assign.guard:doctors');
//download lecture
Route::post('download/lecture','web\CourseController@downloadlecture');
//about page
Route::get('/about', function(){
    return view('about');
});
Route::get('/student/dashboard1', function(){
    return view('std.dashboard');
});
//list std's mark
Route::get('marks/{universityID}','web\Markcontroller@show')->name('std.grades');
//////////////////////////////////////////////////////////////////////////////////////////////////////////
    Route::get('/broadcast', function() {
        
    // New Pusher instance with our config data
    $pusher = new \Pusher\Pusher(config('broadcasting.connections.pusher.key'),
     config('broadcasting.connections.pusher.secret'), 
     config('broadcasting.connections.pusher.app_id'),
      config('broadcasting.connections.pusher.options'));
    
    // Enable pusher logging - I used an anonymous class and the Monolog
    $pusher->set_logger(new class {
                    public function log($msg)
                    {
                        \Log::info($msg);
                    }
                });
    
    // Your data that you would like to send to Pusher
    $data = ['text' => 'hello hello from me'];
    
    // Sending the data to channel: "test_channel" with "my_event" event
    $pusher->trigger( 'std_'.app()->user()->stdID(), 'my-event', $data);
    
    return 'ok'; 
    });
