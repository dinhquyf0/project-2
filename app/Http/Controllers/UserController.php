<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\User;

use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		//get all users from database
        $users = DB::select('select users.name, users.username, users.email, users.firstname, users.lastname, users.phonenumber,
			groups.name as groupname
			from users, groups 
			where users.group_id = groups.id');
		return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$validator = Validator::make($request->all(), 
			[
				'firstname' => 'required|string|max:20',
				'lastname' => 'required||string|max:20',
				'username' => 'required|string|max:100',
				'email' => 'required|email|max:255',
                'phonenumber' => 'required|max:15',
				'password' => 'required|string|max:60',
				'group_id' => 'required|max:100',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('username')) {
				$returnArray = array('result' => false, 
					'message' => 'Tên đăng nhập đã được sử dụng để đăng ký tài khoản!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('email')) {
				$returnArray = array('result' => false, 
					'message' => 'Email đã được sử dụng để đăng ký tài khoản!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('password')) {
				$returnArray = array('result' => false, 
					'message' => 'Mật khẩu mới ít nhất phải từ 6 ký tự trở lên.'
				);
				return response()->json($returnArray);
			};

			if($errors->has('firstname')) {
				$returnArray = array('result' => false, 
					'message' => 'firstname'
				);
				return response()->json($returnArray);
			};

			if($errors->has('lastname')) {
				$returnArray = array('result' => false, 
					'message' => 'lastname'
				);
				return response()->json($returnArray);
			};

			if($errors->has('phonenumber')) {
				$returnArray = array('result' => false, 
					'message' => 'phonenumber'
				);
				return response()->json($returnArray);
			};

			if($errors->has('group_id')) {
				$returnArray = array('result' => false, 
					'message' => 'group_id'
				);
				return response()->json($returnArray);
			};
		}

		$check_group_exist = DB::table('groups')->where('id', $request->group_id)->first();
		if (is_null($check_group_exist)) {
			return response()->json(['result' => false, 'reason' => 'group_id not exist!!']);
		}

		$check = DB::select('select * from users where users.username = ? or users.email = ?', 
			[$request->username , $request->email]);
		
		if(count($check) != 0){
			$returnArray = array('result' => false);
			return response()->json($returnArray);			
		}
		// //create a new user object
		// $user = new User;
		
		// //assign each input with its respective value in the object
		// $user->username = $request->username;
		// $user->firstname = $request->firstname;
		// $user->lastname = $request->lastname;
		// $user->email = $request->email;
		// $user->password = bcrypt($request->password);
		// $user->phonenumber = $request->phonenumber;
		// $user->group_id = (int)$request->group_id;
		// $user->status = 1;
		
		// //save the object's value into the database
		// $user->save();

		$user = array();
		$user['username'] = $request->username;
		$user['firstname'] = $request->firstname;
		$user['lastname'] = $request->lastname;
		$user['email'] = $request->email;
		$user['password'] = bcrypt($request->password);
		$user['phonenumber'] = $request->phonenumber;
		$user['group_id'] = (int)$request->group_id;
		$user['status'] = 1;

		DB::table('users')->insert($users);
		
		//return the true array so client could know the program is done.
		$returnArray = array('result' => true);
		return response()->json($returnArray);
    }

    //function used for randomizing a random string whose length is 40
	public function generateRandomString($length)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
    /**
	Authentication API, used for creating api key for acurate authentication
     */	 
	public function auth(Request $request)
	{
		$validator = Validator::make($request->all(), 
			[
				'username' => 'required|string|max:60',
				'password' => 'required|string|max:60',
			]
		);	
		
		if($validator->fails()){
			$returnArray = array('result' => false);
			return response()->json($returnArray);			
		}
		
        $credentials = $request->only('username', 'password');
        // var_dump($credentials); die();
        try {			
			$user = array();
			$user['exp'] = time() + 28400;	

            // verify the credentials (by username or email and create a token for the user
            if (!($token = JWTAuth::attempt(
            	[
  					'email' => $credentials['username'],
                	'password'=> $credentials['password']
            	], $user)) 
            	&& !($token = JWTAuth::attempt(
        		[
  					'username' => $credentials['username'],
                	'password'=> $credentials['password']
            	], $user)) 
        	) {
                return response()->json(
                	[
                		'result' => false,
						'message' => 'Tên đăng nhập/email hoặc mật khẩu không chính xác'
					]
				);
            }
        }
		catch (JWTException $e) {
            // something went wrong
            return response()->json(
            	[
            		'result' => false,
					'error' => 'could_not_create_token'
				], 500
			);
        }
		
		$user =	DB::select('select id, username, status, group_id
			from users 
			where username = ? or email = ?',
			[$request->username, $request->username]
		);

		$userid = $user[0]->id;
		if ($user[0]->status == 0) {
        	return response()->json(
        		[
					'result' => false,
					'message' => 'Vui lòng kiểm tra mail kích hoạt tài khoản đã được gửi đến Email đã đăng ký của bạn!'
        		]
    		);
        }
		/*
		Save userpermission into MySQL when authenticate success
		*/
		$permissions = DB::select('select groups.name as groupName, 
			groups.id as groupId, 
			controllers.id as controllerId 
			from controllers, groups, permissions 
			where groups.id = permissions.group_id 
			and permissions.controller_id = controllers.id
			and groups.id = ?', [$user[0]->group_id]);
		//delete request reset password when user login success	
		// DB::delete('delete from reset_passwords where userid = ?', [$userid]);
        // if no errors are encountered we can return a JWT

        //save the lasttime when user login success
        $time = date('Y-m-d').'T'.date('H:i:s');
        // $check = DB::update('update users set lastlogin = ? where id = ?', [$time, $userid]);
        // if (!$check){
        //     return response()->json(['result' => false, 'message' => 'can\'t save lastlogin']);
        // }
		return response()->json(
			[
				'result' => true,
				'user' => $user[0]->username,
				'token' => $token
			]
		);
	}

	public function show()
    {
    	$user = JWTAuth::parseToken()->authenticate();
        if($user['id']) {
			$id = $user['id'];
        } else {
        	$returnArray = array('result' => false);
        	return response()->json($returnArray);
        }
        $user =	DB::select('select users.id, users.username, users.name, users.email, 
        	groups.name as groupName, 
        	groups.id as groupId
			from users, groups 
			where users.group_id = groups.id and users.id = ?', [$id]);
		return response()->json($user);
    }

    /**
     * Display the specified resource by id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */	
    public function show_user($id)
    {
    	// $user = JWTAuth::parseToken()->authenticate();
    	$returnArray = array();
   //      if($user['groupid'] != 1) {
			// $returnArray = array('result' => false);
   //      	return response()->json($returnArray);
   //      }

        $user =	DB::select('select users.id, users.username, users.name, users.email
			from users, groups 
			where users.group_id = groups.id and users.id = ?', [$id]);
        $group = DB::select('select groups.* 
			from users, groups 
			where users.group_id = groups.id and users.id = ?', [$id]);
        $user[0]->group = $group[0];
		return response()->json($user[0]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    	if (!is_int($id)) {
    		return response()->json(['result' => false, 'message' => 'id must be integer']);
    	}

    	if ((int)$id > 1000000 || (int)$id < 0) {
    		return response()->json(['result' => false, 'message' => 'id is not accept!!']);
    	}
		//validate the input variable from the post request
		$validator = Validator::make($request->all(), 
			[
				'name' => 'required|max:255',
				'email' => 'required|email|max:255',
				'group_id' => 'required|max:1000',
			]
		);
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$returnArray = array('result' => false);
			return response()->json($returnArray);
		}
		$user =	User::find($id);
		if($user == null){
			$returnArray = array('result' => false,
				'reason' => 'couldn\'t find the id'
			);
			return response()->json($returnArray, 400);
		}
		
 		$check_group_exist = DB::table('groups')->where('id', $request->group_id)->first();
		if (is_null($check_group_exist)) {
			return response()->json(['result' => false, 'reason' => 'group_id not exist!!']);
		}

		//check if the username and email is used or not
		$check = DB::select('select * from users where (users.username = ? or users.email = ?) and (users.id != ?)', 
			[$request->username, $request->email, $id]);
		
		if(count($check) != 0){
			$returnArray = array('result' => false);
			return response()->json($returnArray);			
		}
		
		//assign each input with its respective value in the object
		$user->name = $request->name;
		$user->email = $request->email;
		$user->group_id = (int)$request->group_id;
		
		//save the object's value into the database
		$check = $user->save();
		if (!$check) {
			return response()->json(['result' => false, 'message' => 'can not save this user!']);
		}
		
		//return the true array so client could know the program is done.
		$returnArray = array('result' => true);
		return response()->json($returnArray);	      
    }

    public function updateInfo(Request $request)
    {
		//validate the input variable from the post request
		$validator = Validator::make($request->all(), 
			[
				'name' => 'required|max:255',
				'email' => 'required|email|max:255',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$returnArray = array('result' => false);
			return response()->json($returnArray);
		}

    	$id = $this->getUserId();
		$user =	User::find($id);
		if($user == null){
			$returnArray = array('result' => false,
				'reason' => 'couldn\'t find the id'
			);
			return response()->json($returnArray, 400);
		}
		//check if the username and email is used or not
		$check = DB::select('select * from users where (users.username = ? or users.email = ?) and (users.id != ?)', 
			[$request->username, $request->email , $id]);
		
		if(count($check) != 0){
			$returnArray = array('result' => false);
			return response()->json($returnArray);			
		}
		
		//assign each input with its respective value in the object
		$user->name = $request->name;
		$user->email = $request->email;
		
		//save the object's value into the database
		$check = $user->save();
		if (!$check) {
			return response()->json(['result' => false, 'message' => 'can not save this user!']);
		}
		//return the true array so client could know the program is done.
		$returnArray = array('result' => true);
		return response()->json($returnArray);	       
    }

    public function updatePwd(Request $request)
    {
		//validate the input variable from the post request
		$validator = Validator::make($request->all(), 
			[
				'username' => 'required|max:60',
				'password' => 'required|max:60',
				'passwordNew' => 'required|max:60',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$returnArray = array('result' => false);
			return response()->json($returnArray);
		}

    	$id = $this->getUserId();
		$user =	User::find($id);
		if($user == null){
			$returnArray = array('result' => false,
				'reason' => 'couldn\'t find the id'
			);
			return response()->json($returnArray, 400);
		}
		
		// Tam code
		$credentials = $request->only('username', 'password');
		
		try {			
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['result' => false,
					'error' => 'invalid_credentials'], 401
				);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(
            	[
            		'result' => false,
					'error' => 'could_not_create_token'], 500
			);
        }
		
		//assign each input with its respective value in the object
		$user->password = bcrypt($request->passwordNew);
		
		//save the object's value into the database
		$check = $user->save();
		if (!$check) {
			return response()->json(['result' => false, 'message' => 'can not save this user!']);
		}
		
		//return the true array so client could know the program is done.
		$returnArray = array('result' => true);
		return response()->json($returnArray);
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['result' => false, 'message' => 'id must be number']);
        }

        if ((int)$id > 10000) {
            return response()->json(['result' => false, 'message' => 'id is too big']);
        }

        $data = User::find($id);
        $returnArray = array();
        if ($data != null) {

            DB::table('assignees')
                ->where('usercreate', $id)
                ->orWhere('userassign', $id)
                ->delete();

            DB::table('backups')
                ->where('userId', $id)
                ->delete();

            DB::table('comments')
                ->where('userId', $id)
                ->delete();

            DB::table('notifications')
                ->where('userId', $id)
                ->delete();

            DB::table('results')
                ->where('userId', $id)
                ->delete();
            DB::delete('delete from user_histories WHERE userid = ?', [$id]);
        } else {
            $returnArray = array('result' => false,
                'reason' => 'couldn\'t find the id'
            );
            return response()->json($returnArray, 400);
        }
        $returnArray = array('result' => true);
        return response()->json($returnArray);
    }
}
