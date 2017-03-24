<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use App\User;
use App\Grade;
use App\Student;
use App\StudentInternt;

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
		$this->groupid = $user['group_id'];
	}
    public function index()
    {
    	$return_array = array();
    	$students = DB::select('select * from students');
    	foreach ($students as $key => $value) {
    		$temp = array();
    		$temp['id'] = $value->id;
    		$temp['student_id'] = $value->studentid;
    		$temp['schoolyear'] = $value->schoolyear;
    		$temp['grade'] = $value->grade;
    		$temp['fromyear'] = $value->fromyear;
    		$temp['toyear'] = $value->toyear;
    		$temp['major'] = $value->major;
    		$temp['class_id'] = $value->classid;
    		$class_name = DB::select('select name from grades where id = ?', [$value->class_id]);
 			if (count($class_name) == 0) {
 				$temp['classname'] = 'N/A';
 			} else {
 				$temp['classname'] = $class_name[0]->name;
 			}
 	   		
    		$return_array[] = $temp;
    	}
    	return response()->json($return_array);
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[	
				'student_id' => 'required|max:255',
				'school_year' => 'required|max:255',
				'grade' => 'required|max:255',
				'from_year' => 'required|max:255',
                'to_year' => 'required|max:15',
                'major' => 'required|string|max:255',
                'class_id' => 'required'
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('student_id')) {
				$returnArray = array('result' => false, 
					'message' => 'student_id!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('school_year')) {
				$returnArray = array('result' => false, 
					'message' => 'school_year'
				);
				return response()->json($returnArray);
			};

			if($errors->has('grade')) {
				$returnArray = array('result' => false, 
					'message' => 'grade'
				);
				return response()->json($returnArray);
			};

			if($errors->has('from_year')) {
				$returnArray = array('result' => false, 
					'message' => 'from_year'
				);
				return response()->json($returnArray);
			};

			if($errors->has('to_year')) {
				$returnArray = array('result' => false, 
					'message' => 'to_year'
				);
				return response()->json($returnArray);
			};

			if($errors->has('major')) {
				$returnArray = array('result' => false, 
					'message' => 'major'
				);
				return response()->json($returnArray);
			};

			if($errors->has('class_id')) {
				$returnArray = array('result' => false, 
					'message' => 'classid'
				);
				return response()->json($returnArray);
			};

		}

		$check_class = Grade::find($request->class_id);

		if (count($check_class) == 0) {
			return response()->json(['result' => false, 'reason' => 'class not exist!!']);
		}

		$check_student_info = Student::find($request->student_id);

		if (count($check_student_info) > 0) {
			return response()->json(['result' => false, 'reason' => 'student id exist!!']);
		}	
		

		$student = new Student;
		$student->id = $this->userid;
		$student->student_id = $request->student_id;
		$student->school_year = $request->school_year;
		$student->grade = $request->grade;
		$student->from_year = $request->from_year;
		$student->to_year = $request->to_year;
		$student->major = $request->major;
		$student->class_id = $request->class_id;

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
    	
    	$class = DB::select('select name from classes where id = ?', [$check->class_id]);

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
				'student_id' => 'required|max:255',
				'school_year' => 'required|max:255',
				'grade' => 'required|max:255',
				'from_year' => 'required|email|max:255',
                'to_year' => 'required|max:15',
                'major' => 'required|string|max:255',
                'class_id' => 'required'
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('student_id')) {
				$returnArray = array('result' => false, 
					'message' => 'student_id!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('school_year')) {
				$returnArray = array('result' => false, 
					'message' => 'schooly_ear'
				);
				return response()->json($returnArray);
			};

			if($errors->has('grade')) {
				$returnArray = array('result' => false, 
					'message' => 'grade'
				);
				return response()->json($returnArray);
			};

			if($errors->has('from_year')) {
				$returnArray = array('result' => false, 
					'message' => 'from_year'
				);
				return response()->json($returnArray);
			};

			if($errors->has('to_year')) {
				$returnArray = array('result' => false, 
					'message' => 'to_year'
				);
				return response()->json($returnArray);
			};

			if($errors->has('major')) {
				$returnArray = array('result' => false, 
					'message' => 'major'
				);
				return response()->json($returnArray);
			};

			if($errors->has('class_id')) {
				$returnArray = array('result' => false, 
					'message' => 'class_id'
				);
				return response()->json($returnArray);
			};

		}

		$student = Student::find($id);

        if (count($student) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

		$student->student_id = $request->student_id;
		$student->school_year = $request->school_year;
		$student->grade = $request->grade;
		$student->from_year = $request->from_year;
		$student->to_year = $request->to_year;
		$student->major = $request->major;
		$student->class_id = $request->class_id;

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

        if (is_null($student)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $student->delete();
        DB::delete('delete from student_cvs where student_id = ?', [$id]);

        return response()->json(['result' => true]);
    }



    public function createIntent(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[	
				'student_id' => 'required|max:255',
				'period' => 'required|max:255',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('student_id')) {
				$returnArray = array('result' => false, 
					'message' => 'student_id!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('period')) {
				$returnArray = array('result' => false, 
					'message' => 'period'
				);
				return response()->json($returnArray);
			};

		}
    	if ($request->topic_1 == $request->topic_2 || $request->topic_2 == $request->topic_3 || $request->topic_3 == $request->topic_1) {
    		return response()->json(['result' => false, 'reason' => 'wrong topic']);
    	}

    	$check_exist = DB::table('student_interns')->where([['period', $request->period], ['student_id', $this->user_id]])->get();
    	if (is_null($check_exist)) {
    		return response()->json(['result' => false, 'reason' => 'student already create intern in this period']);
    	}

    	$student_internt = new StudentInternt;

    	$student_internt->student_id = $this->user_id;
    	$student_internt->topic_1 = $request->topic_1;
    	$student_internt->topic_2 = $request->topic_2;
    	$student_internt->topic_3 = $request->topic_3;
    	$student_internt->period = $request->period;

    	$check_save = $student_internt->save();
    	if (is_null($check_save)) {
    		return response()->json(['result' => false, 'reason' => 'save fails!!!']);
    	}

    	return response()->json(['result' => true]);
    }

    public function updateIntent(Request $request, $id)
    {
		if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 100000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }
        
    	$validator = Validator::make($request->all(), 
			[	
				'student_id' => 'required|max:255',
				'period' => 'required|max:255',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('student_id')) {
				$returnArray = array('result' => false, 
					'message' => 'student_id!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('period')) {
				$returnArray = array('result' => false, 
					'message' => 'period'
				);
				return response()->json($returnArray);
			};

		}

    	if ($request->topic_1 == $request->topic_2 || $request->topic_2 == $request->topic_3 || $request->topic_3 == $request->topic_1) {
    		return response()->json(['result' => false, 'reason' => 'wrong topic']);
    	}

    	$student_internt = StudentInternt::find($id);

    	if ($student_internt->user_id != $this->user_id) {
    		return response()->json(['result' => false, 'reason' => 'user do not have permission for this intern']);
    	}

    	$student_internt->student_id = $this->user_id;
    	$student_internt->topic_1 = $request->topic_1;
    	$student_internt->topic_2 = $request->topic_2;
    	$student_internt->topic_3 = $request->topic_3;
    	$student_internt->period = $request->period;

    	$check_save = $student_internt->save();
    	if (is_null($check_save)) {
    		return response()->json(['result' => false, 'reason' => 'save fails!!!']);
    	}

    	return response()->json(['result' => true]);
    }

    public function sendReport(Request $request)
    {
    	//send file report
    }
}
