<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

// Route::group(['middleware' => ['web']], function () {
//     //
// });


Route::post('/connect' , 'UserController@connect');
Route::get('/redirect' , 'UserController@redirectLogin');
Route::get('/users', 'UserController@index');
	// ->middleware(['jwt.auth', 'jwtToken']);

Route::get('/user', 'UserController@show');
	// ->middleware(['jwt.auth']);
	
Route::get('/user/{id}', 'UserController@show_user');
	// ->middleware(['jwt.auth' , 'jwtToken']);

Route::post('/signUp', 'UserController@signUp');

Route::get('/resendConfirmEmail', 'UserController@getResendConfirmEmail');
Route::post('/resendConfirmEmail', 'UserController@postResendConfirmEmail');

Route::get('/confirmEmail/token/{token}/email/{email}', 'UserController@confirmEmail');

Route::delete('/delete_user/{id}', 'UserController@destroy')
	->where('id', '[0-9]+')
	->middleware(['jwt.auth', 'jwtToken']);

Route::post('/create_user', 'UserController@store');
	// ->middleware(['jwt.auth', 'jwtToken']);

Route::post('/auth', 'UserController@auth');

Route::post('/update_user/{id}', 'UserController@update')
	->where('id' , '[0-9]+')
	->middleware(['jwt.auth', 'jwtToken']);

Route::post('/update_user_info', 'UserController@updateInfo')
	->middleware(['jwt.auth']);

Route::post('/update_user_pwd', 'UserController@updatePwd')
	->middleware(['jwt.auth']);


Route::post('/change_status_user/{userid}', 'UserController@changeStatusUser')
    ->middleware(['jwt.auth']);

Route::post('/reset_password_default/{userid}', 'UserController@resetPasswordDefault')
    ->middleware(['jwt.auth']);
//GroupController route
Route::get('/groups', 'GroupController@index');
    // ->middleware(['jwt.auth', 'jwtToken']);

Route::get('/group/{id}', 'GroupController@show')
    ->where('id', '[0-9]+');
    // ->middleware(['jwt.auth', 'jwtToken']);

Route::delete('/delete_group/{id}', 'GroupController@destroy')
    ->where('id', '[0-9]+');
    // ->middleware(['jwt.auth', 'jwtToken']);

Route::post('/create_group', 'GroupController@store');
    // ->middleware(['jwt.auth', 'jwtToken']);

Route::post('/update_group/{id}', 'GroupController@update')
    ->where('id', '[0-9]+');
    // ->middleware(['jwt.auth', 'jwtToken']);
//PermissionController route
Route::get('/permissions', 'PermissionController@index');
    // ->middleware(['jwt.auth', 'jwtToken']);

Route::post('/change_permissions/{groupid}/{controllerid}', 'PermissionController@store')
    ->where(array('groupid' => '[0-9]+', 'controllerid'	=> '[0-9]+'));
    // ->middleware(['jwt.auth', 'jwtToken']);

Route::get('/user_permissions', 'PermissionController@get_private_permissions');

Route::post('/create_controller', 'GroupController@createController');
    // ->middleware(['jwt.auth']);

Route::get('/controllers', 'GroupController@getControllers');


//Route for student
Route::post('/create_student', 'StudentController@store');

Route::get('/students', 'StudentController@index');

Route::get('/show_student/{id}', 'StudentController@show');

Route::post('/update_student/{id}', 'StudentController@update');

Route::delete('/delete_student/{id}', 'StudentController@destroy');

Route::post('/create_intent', 'StudentController@createIntent');

Route::post('/update_intent/{id}', 'StudentController@updateIntent');

//Route for teacher
Route::get('/teachers', 'TeacherController@index');

Route::post('/create_teacher', 'TeacherController@store');

Route::get('/show_teacher/{id}', 'TeacherController@show');

Route::post('/update_teacher/{id}', 'TeacherController@update');

Route::delete('/delete_teacher/{id}', 'TeacherController@destroy');

//Route for class
Route::get('/classes', 'SchoolController@indexClass');

Route::post('/create_class', 'SchoolController@storeClass');

Route::get('/show_class/{id}', 'SchoolController@showClass');

Route::post('/update_class/{id}', 'SchoolController@updateClass');

Route::delete('/delete_class/{id}', 'SchoolController@destroyClass');

//Route for department
Route::get('/departments', 'SchoolController@indexDepartment');

Route::post('/create_department', 'SchoolController@storeDepartment');

Route::get('/show_department/{id}', 'SchoolController@showDepartment');

Route::post('/update_department/{id}', 'SchoolController@updateDepartment');

Route::delete('/delete_department/{id}', 'SchoolController@destroyDepartment');


//Route for Company
Route::get('/companies', 'EmployeeController@indexCompanies');

Route::post('/create_company', 'EmployeeController@storeCompany');

Route::get('/show_company/{id}', 'EmployeeController@showCompany');

Route::post('/update_company/{id}', 'EmployeeController@updateCompany');

Route::delete('/delete_company/{id}', 'EmployeeController@destroyCompany');

//Route for Employee
Route::get('/employees', 'EmployeeController@indexEmployees');

Route::post('/create_employee', 'EmployeeController@storeEmployee');

Route::get('/show_employee/{id}', 'EmployeeController@showEmployee');

Route::post('/update_employee/{id}', 'EmployeeController@updateEmployee');

Route::delete('/delete_employee/{id}', 'EmployeeController@destroyEmployee');

//Route for Topic
Route::get('/topics', 'TopicController@index');

Route::post('/create_topic', 'TopicController@storeEmployee');

Route::get('/show_topic/{id}', 'TopicController@showEmployee');

Route::post('/update_topic/{id}', 'TopicController@updateEmployee');

Route::delete('/delete_topic/{id}', 'TopicController@destroyEmployee');

Route::get('/update_topic_accept/{id}', 'TopicController@updateAcceptStatus');

Route::get('/show_topics_accept', 'TopicController@showTopicAccept');

//Route for cv
Route::get('/cvs', 'InternController@indexCv');

Route::post('/create_cv', 'InternController@storeCv');

Route::post('/update_cv', 'InternController@updateCv');

Route::delete('/delete_cv/{id}', 'InternController@destroyCv');


//Route for comment
Route::get('/comments/{id}', 'CommentController@show');

Route::post('/create_comment', 'CommentController@store');

Route::post('/update_comment', 'CommentController@update');

Route::delete('/delete_comment/{id}', 'CommentController@delete');

