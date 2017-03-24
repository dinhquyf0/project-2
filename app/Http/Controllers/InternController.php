<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use Validator;

use App\User;
use App\StudentCv;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class InternController extends Controller
{
	public function __construct()
	{
		$user = JWTAuth::parseToken()->authenticate();
        $this->user_id = $user['id'];
        $this->group_id = $user['group_id'];
	}

    public function indexCv()
    {
    	$cvs = DB::table('student_cvs')->get();
    	if (is_null($cvs)) {
    		return response()->json(['result' => false, 'reason' => 'db empty']);
    	}
    	return response()->json(['result' => true, 'data' => $cvs]);
    }

    public function storeCv(Request $request)
    {
	   	$validator = Validator::make($request->all(), 
			[
				'name' => 'required|string|max:255',
				'avatar' => 'required|string|max:255',
				'position' => 'required|string|max:255',
				'dateofbirth' => 'required|max:255',
	            'gender' => 'required|smallInteger|max:3',
				'phone' => 'required|numeric|max:20',
				'email' => 'required|email|max:100',
				'intent' => 'required|string|max:1000',
				'skill' => 'required|max:1000',
				'hobby' => 'required|max:1000',
				'year_start' => 'required|max:4',
				'year_stop' => 'required|max:4',
				'grade' => 'required|max:5',
				'school' => 'required|max:1000',
				'major' => 'required|max:1000',
				'cpa' => 'required|numeric|max:3',
				'majorskill' => 'required|max:1000',
				'otherskill' => 'required|max:1000',

			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('name')) {
				$returnArray = array('result' => false, 
					'message' => 'name'
				);
				return response()->json($returnArray);
			};

			if($errors->has('avatar')) {
				$returnArray = array('result' => false, 
					'message' => 'avatar'
				);
				return response()->json($returnArray);
			};

			if($errors->has('position')) {
				$returnArray = array('result' => false, 
					'message' => 'position'
				);
				return response()->json($returnArray);
			};

			if($errors->has('dateofbirth')) {
				$returnArray = array('result' => false, 
					'message' => 'dateofbirth'
				);
				return response()->json($returnArray);
			};

			if($errors->has('gender')) {
				$returnArray = array('result' => false, 
					'message' => 'gender'
				);
				return response()->json($returnArray);
			};

			if($errors->has('phone')) {
				$returnArray = array('result' => false, 
					'message' => 'phone'
				);
				return response()->json($returnArray);
			};

			if($errors->has('email')) {
				$returnArray = array('result' => false, 
					'message' => 'email'
				);
				return response()->json($returnArray);
			};

			if($errors->has('intent')) {
				$returnArray = array('result' => false, 
					'message' => 'intent'
				);
				return response()->json($returnArray);
			};

			if($errors->has('skill')) {
				$returnArray = array('result' => false, 
					'message' => 'skill'
				);
				return response()->json($returnArray);
			};

			if($errors->has('hobby')) {
				$returnArray = array('result' => false, 
					'message' => 'hobby'
				);
				return response()->json($returnArray);
			};

			if($errors->has('year_start')) {
				$returnArray = array('result' => false, 
					'message' => 'year_start'
				);
				return response()->json($returnArray);
			};

			if($errors->has('year_stop')) {
				$returnArray = array('result' => false, 
					'message' => 'year_stop'
				);
				return response()->json($returnArray);
			};

			if($errors->has('grade')) {
				$returnArray = array('result' => false, 
					'message' => 'grade'
				);
				return response()->json($returnArray);
			};

			if($errors->has('school')) {
				$returnArray = array('result' => false, 
					'message' => 'school'
				);
				return response()->json($returnArray);
			};

			if($errors->has('major')) {
				$returnArray = array('result' => false, 
					'message' => 'major'
				);
				return response()->json($returnArray);
			};

			if($errors->has('cpa')) {
				$returnArray = array('result' => false, 
					'message' => 'cpa'
				);
				return response()->json($returnArray);
			};

			if($errors->has('majorskill')) {
				$returnArray = array('result' => false, 
				'message' => 'majorskill'
				);
				return response()->json($returnArray);
			};

			if($errors->has('otherskill')) {
				$returnArray = array('result' => false, 
					'message' => 'otherskill'
				);
				return response()->json($returnArray);
			};
		}

		$check_exist = StudentCv::find($user_id);
		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => '1 student have only 1 cv']);
		}

		$student_cv = new StudentCv;
		$student_cv->user_id = $this->user_id;
		$student_cv->name = $request->name;
		$student_cv->avatar = $request->avatar;
		$student_cv->position = $request->position;
		$student_cv->dateofbirth = $request->dateofbirth;
		$student_cv->gender = $request->gender;
		$student_cv->phone = $request->phone;
		$student_cv->email = $request->email;
		$student_cv->intent = $request->intent;
		$student_cv->skill = $request->skill;
		$student_cv->hobby = $request->hobby;
		$student_cv->year_start = $request->year_start;
		$student_cv->year_stop = $request->year_stop;
		$student_cv->grade = $request->grade;
		$student_cv->school = $request->school;
		$student_cv->major = $request->major;
		$student_cv->cpa = $request->cpa;
		$student_cv->majorskill = $request->majorskill;
		$student_cv->otherskill = $request->otherskill;


		$student_cv->save();

		return response()->json(['result' => true]);
    }

    public function showCv($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $student_cv = StudentCv::find($id);
        if (is_null($student_cv)) {
        	return response()->json(['result' => false, 'reason' => 'id not found']);
        }

        return response()->json(['result' => treu, 'data' => $student_cv]);

    }

    public function updateCv(Request $request, $id)
    {

    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

    	$validator = Validator::make($request->all(), 
			[
				'name' => 'required|string|max:255',
				'avatar' => 'required|string|max:255',
				'position' => 'required|string|max:255',
				'dateofbirth' => 'required|max:255',
	            'gender' => 'required|smallInteger|max:3',
				'phone' => 'required|numeric|max:20',
				'email' => 'required|email|max:100',
				'intent' => 'required|string|max:1000',
				'skill' => 'required|max:1000',
				'hobby' => 'required|max:1000',
				'year_start' => 'required|max:4',
				'year_stop' => 'required|max:4',
				'grade' => 'required|max:5',
				'school' => 'required|max:1000',
				'major' => 'required|max:1000',
				'cpa' => 'required|numeric|max:3',
				'majorskill' => 'required|max:1000',
				'otherskill' => 'required|max:1000',

			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('name')) {
				$returnArray = array('result' => false, 
					'message' => 'name'
				);
				return response()->json($returnArray);
			};

			if($errors->has('avatar')) {
				$returnArray = array('result' => false, 
					'message' => 'avatar'
				);
				return response()->json($returnArray);
			};

			if($errors->has('position')) {
				$returnArray = array('result' => false, 
					'message' => 'position'
				);
				return response()->json($returnArray);
			};

			if($errors->has('dateofbirth')) {
				$returnArray = array('result' => false, 
					'message' => 'dateofbirth'
				);
				return response()->json($returnArray);
			};

			if($errors->has('gender')) {
				$returnArray = array('result' => false, 
					'message' => 'gender'
				);
				return response()->json($returnArray);
			};

			if($errors->has('phone')) {
				$returnArray = array('result' => false, 
					'message' => 'phone'
				);
				return response()->json($returnArray);
			};

			if($errors->has('email')) {
				$returnArray = array('result' => false, 
					'message' => 'email'
				);
				return response()->json($returnArray);
			};

			if($errors->has('intent')) {
				$returnArray = array('result' => false, 
					'message' => 'intent'
				);
				return response()->json($returnArray);
			};

			if($errors->has('skill')) {
				$returnArray = array('result' => false, 
					'message' => 'skill'
				);
				return response()->json($returnArray);
			};

			if($errors->has('hobby')) {
				$returnArray = array('result' => false, 
					'message' => 'hobby'
				);
				return response()->json($returnArray);
			};

			if($errors->has('year_start')) {
				$returnArray = array('result' => false, 
					'message' => 'year_start'
				);
				return response()->json($returnArray);
			};

			if($errors->has('year_stop')) {
				$returnArray = array('result' => false, 
					'message' => 'year_stop'
				);
				return response()->json($returnArray);
			};

			if($errors->has('grade')) {
				$returnArray = array('result' => false, 
					'message' => 'grade'
				);
				return response()->json($returnArray);
			};

			if($errors->has('school')) {
				$returnArray = array('result' => false, 
					'message' => 'school'
				);
				return response()->json($returnArray);
			};

			if($errors->has('major')) {
				$returnArray = array('result' => false, 
					'message' => 'major'
				);
				return response()->json($returnArray);
			};

			if($errors->has('cpa')) {
				$returnArray = array('result' => false, 
					'message' => 'cpa'
				);
				return response()->json($returnArray);
			};

			if($errors->has('majorskill')) {
				$returnArray = array('result' => false, 
				'message' => 'majorskill'
				);
				return response()->json($returnArray);
			};

			if($errors->has('otherskill')) {
				$returnArray = array('result' => false, 
					'message' => 'otherskill'
				);
				return response()->json($returnArray);
			};
		}

		$student_cv = StudentCv::find($id);

		if ($this->user_id != $student_cv->user_id) {
			return response()->json(['result' => false, 'reason' => 'this cv doesn\'t belong to this user']);
		}

		
		$student_cv->user_id = $this->user_id;
		$student_cv->name = $request->name;
		$student_cv->avatar = $request->avatar;
		$student_cv->position = $request->position;
		$student_cv->dateofbirth = $request->dateofbirth;
		$student_cv->gender = $request->gender;
		$student_cv->phone = $request->phone;
		$student_cv->email = $request->email;
		$student_cv->intent = $request->intent;
		$student_cv->skill = $request->skill;
		$student_cv->hobby = $request->hobby;
		$student_cv->year_start = $request->year_start;
		$student_cv->year_stop = $request->year_stop;
		$student_cv->grade = $request->grade;
		$student_cv->school = $request->school;
		$student_cv->major = $request->major;
		$student_cv->cpa = $request->cpa;
		$student_cv->majorskill = $request->majorskill;
		$student_cv->otherskill = $request->otherskill;


		$student_cv->save();

		return response()->json(['result' => true]);
    }

    public function destroyCv($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $cv = StudentCv::find($id);

        if ($this->user_id != $cv->user_id) {
			return response()->json(['result' => false, 'reason' => 'this cv doesn\'t belong to this user']);
		}
        if (is_null($cv)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $cv->delete();

        return response()->json(['result' => true]);
    }
}
