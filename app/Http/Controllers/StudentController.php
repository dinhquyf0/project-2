<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use App\User;
use App\Grade;
use App\Student;

use Validator;

use App\Http\Controllers\Controller;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentController extends Controller
{
	public function __construct()
	{
		$user = JWTAuth::parseToken()->authenticate();
		$this->userid = $user['id'];
		$this->groupid = $user['groupid'];
	}
    public function index()
    {
    	$return_array = array();
    	$students = DB::select('select * from students');
    	foreach ($students as $key => $value) {
    		$temp = array();
    		$temp['id'] = $value->id;
    		$temp['studentid'] = $value->studentid;
    		$temp['schoolyear'] = $value->schoolyear;
    		$temp['grade'] = $value->grade;
    		$temp['fromyear'] = $value->fromyear;
    		$temp['toyear'] = $value->toyear;
    		$temp['major'] = $value->major;
    		$temp['classid'] = $value->classid;
    		$class_name = DB::select('select name from classes where id = ?', [$value->classid]);
    		$temp['classname'] = $class_name[0]->name;
    		$return_array[] = $temp;
    	}
    	return response()->json($teachers);
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[	
				'studentid' => 'required|max:255',
				'schoolyear' => 'required|max:255',
				'grade' => 'required|max:255',
				'fromyear' => 'required|max:255',
                'toyear' => 'required|max:15',
                'major' => 'required|string|max:255',
                'classid' => 'required'
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('studentid')) {
				$returnArray = array('result' => false, 
					'message' => 'studentid!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('schoolyear')) {
				$returnArray = array('result' => false, 
					'message' => 'schoolyear'
				);
				return response()->json($returnArray);
			};

			if($errors->has('grade')) {
				$returnArray = array('result' => false, 
					'message' => 'grade'
				);
				return response()->json($returnArray);
			};

			if($errors->has('fromyear')) {
				$returnArray = array('result' => false, 
					'message' => 'fromyear'
				);
				return response()->json($returnArray);
			};

			if($errors->has('toyear')) {
				$returnArray = array('result' => false, 
					'message' => 'toyear'
				);
				return response()->json($returnArray);
			};

			if($errors->has('major')) {
				$returnArray = array('result' => false, 
					'message' => 'major'
				);
				return response()->json($returnArray);
			};

			if($errors->has('classid')) {
				$returnArray = array('result' => false, 
					'message' => 'classid'
				);
				return response()->json($returnArray);
			};

		}

		$check_class = Grade::find($request->classid);

		if (count($check_class) == 0) {
			return response()->json(['result' => false, 'reason' => 'class not exist!!']);
		}

		$check_student_info = Student::find($request->studentid);

		if (count($check_student_info) > 0) {
			return response()->json(['result' => false, 'reason' => 'student exist!!']);
		}	
		

		$student = new Student;
		$student->id = $this->userid;
		$student->studentid = $request->studentid;
		$student->schoolyear = $request->schoolyear;
		$student->grade = $request->grade;
		$student->fromyear = $request->fromyear;
		$student->toyear = $request->toyear;
		$student->major = $request->major;
		$student->classid = $request->classid;

		$check_save = $student->save();

		if (count($check_save) == 0) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}

		return response()->json(['result' => true]);
    }

    public function show($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $check = Student::find($id);

        if (count($check) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }
    	
    	$class = DB::select('select name from classes where id = ?', [$check->classid]);

    	$return_array = array();

    	array_merge($return_array, $check);
    	if (count($class) == 0) {
    		$return_array['class_name'] = 'N/A';
    	}

    	$return_array['class_name'] = $class[0]->name;

    	return response()->json($return_array);
    }

    public function update(Request $request, $id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int)$id  < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $validator = Validator::make($request->all(), 
			[	
				'studentid' => 'required|max:255',
				'schoolyear' => 'required|max:255',
				'grade' => 'required|max:255',
				'fromyear' => 'required|email|max:255',
                'toyear' => 'required|max:15',
                'major' => 'required|string|max:255',
                'classid' => 'required'
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('studentid')) {
				$returnArray = array('result' => false, 
					'message' => 'studentid!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('schoolyear')) {
				$returnArray = array('result' => false, 
					'message' => 'schoolyear'
				);
				return response()->json($returnArray);
			};

			if($errors->has('grade')) {
				$returnArray = array('result' => false, 
					'message' => 'grade'
				);
				return response()->json($returnArray);
			};

			if($errors->has('fromyear')) {
				$returnArray = array('result' => false, 
					'message' => 'fromyear'
				);
				return response()->json($returnArray);
			};

			if($errors->has('toyear')) {
				$returnArray = array('result' => false, 
					'message' => 'toyear'
				);
				return response()->json($returnArray);
			};

			if($errors->has('major')) {
				$returnArray = array('result' => false, 
					'message' => 'major'
				);
				return response()->json($returnArray);
			};

			if($errors->has('classid')) {
				$returnArray = array('result' => false, 
					'message' => 'classid'
				);
				return response()->json($returnArray);
			};

		}

		$student = Student::find($id);

        if (count($student) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

		$student->studentid = $request->studentid;
		$student->schoolyear = $request->schoolyear;
		$student->grade = $request->grade;
		$student->fromyear = $request->fromyear;
		$student->toyear = $request->toyear;
		$student->major = $request->major;
		$student->classid = $request->classid;

		$check_save = $student->save();

		if (count($check_save) == 0) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}

		return response()->json(['result' => true]);

    }

    public function destroy($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $student = Student::find($id);

        if (count($student) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $student->delete();
        DB::delete('delete from student_cvs where studetnid = ?', [$id]);

        return response()->json(['result' => true]);
    }
}
