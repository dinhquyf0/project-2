<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use Validator;

use App\Topic;
use App\User;
use App\Company;
use App\Employee;
use App\Student;
use App\StudentCv;
use App\Teacher;
use App\AssignIntern;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class AssigneeInternController extends Controller
{
	public function __construct(){
		$user = JWTAuth::parseToken()->authenticate();
		$this->user_id = $user['id'];
		$this->group_id = $user['groupid'];
	}

    public function index()
    {
    	$assigns = DB::table('assign_interns')
    	->join('users', 'users.id',' = ','assign_interns.student_id')
    	->join('users', 'users.id',' = ','assign_interns.teacher_id')
		->join('users', 'users.id',' = ','assign_interns.employee_id')
		->join('topics', 'topics.id',' =',' assign_interns.topic_id')
		->get();
		$return_array = array();

        foreach ($company_rate as $key => $value) {
        	$temp = array();

        	$student = DB::table('users')->where('id', $value->student_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['student_id'] = $student->id;
        	$temp['student_first_name'] = $student->firstname;
        	$temp['student_last_name'] = $student->lastname;

        	$teacher = DB::table('users')->where('id', $value->teacher_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['teacher_id'] = $teacher->id;
        	$temp['teacher_first_name'] = $teacher->firstname;
        	$temp['teacher_last_name'] = $teacher->lastname;

        	$employee = DB::table('users')->where('id', $value->employee_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['employee_id'] = $employee->id;
        	$temp['employee_first_name'] = $employee->firstname;
        	$temp['employee_last_name'] = $employee->lastname;


        	$topic = DB::table('topics')->where('id', $value->topic_id)->select('id', 'title')->first();
        	$temp['topic_id'] = $topic->id;
        	$temp['topic_title'] = $topic->title;

        	$temp['period'] = $value->period;

        	$return_array[] = $temp;
        }
    	
    	return response()->json($company_rate);
    	->get();
    	return $assigns;
    }

    public function store(Request $request)
    {
    	if ($this->group_id != 3 || $this->group_id != 4) {
    		return response()->json(['result' => false, 'reason' => 'only teacher manager cant user this']);
    	}
    	$validator = Validator::make($request->all(), 
			[
				'period' => 'required|string|max:255',
				'student_id' => 'required|string|max:255',
                'topic_id' => 'required|max:3',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('period')) {
				$returnArray = array('result' => false, 
					'message' => 'period'
				);
				return response()->json($returnArray);
			};

			if($errors->has('student_id')) {
				$returnArray = array('result' => false, 
					'message' => 'student_id'
				);
				return response()->json($returnArray);
			};


			if($errors->has('topic_id')) {
				$returnArray = array('result' => false, 
					'message' => 'topic_id'
				);
				return response()->json($returnArray);
			};

		}

		$assign = new AssignIntern;

		$assign->period = $request->period;
		$assign->student_id = $request->student_id;
		if ($this->group_id == 3) {
			$assign->teacher_id = $this->user_id;
			$assign->employee_id = $request->employee_id;
		} else {
			$assign->teacher_id = $request->teacher_id;
			$assign->employee_id = $this->user_id;
		}
		
		$assign->topic_id = $request->topic_id;

		$topics->save();

		return response()->json(['result' => true]);

    }

    public function show($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }

        $assigns = DB::table('assign_interns')->where('id', $id)
        	->join('users', 'users.id', '=', 'assign_interns.student_id')
	    	->join('users', 'users.id', '=', ' assign_interns.teacher_id')
			->join('users', 'users.id', '=', 'assign_interns.employee_id')
			->join('topics', 'topics.id', '=', 'assign_interns.topic_id')
        	->first();
       	$return_array = array();
        foreach ($assigns as $key => $value) {
        	$temp = array();
        	$student = DB::table('users')->where('id', $value->student_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['student_id'] = $student->id;
        	$temp['student_first_name'] = $student->firstname;
        	$temp['student_last_name'] = $student->lastname;
        	$teacher = DB::table('users')->where('id', $value->teacher_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['teacher_id'] = $teacher->id;
        	$temp['teacher_first_name'] = $teacher->firstname;
        	$temp['teacher_last_name'] = $teacher->lastname;
        	$employee = DB::table('users')->where('id', $value->employee_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['employee_id'] = $employee->id;
        	$temp['employee_first_name'] = $employee->firstname;
        	$temp['employee_last_name'] = $employee->lastname;
        	$topic = DB::table('topics')->where('id', $value->topic_id)->select('id', 'title')->first();
        	$temp['topic_id'] = $topic->id;
        	$temp['topic_title'] = $topic->title;
        	$return_array[] = $temp;
        }
        
        return response()->json($return_array);
    }

    public function update(Request $request, $id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }

        $validator = Validator::make($request->all(), 
			[
				'period' => 'required|string|max:255',
				'student_id' => 'required|string|max:255',
				'teacher_id' => 'required|string|max:255',
				'employee_id' => 'required|max:255',
                'topic_id' => 'required|max:3',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('period')) {
				$returnArray = array('result' => false, 
					'message' => 'period'
				);
				return response()->json($returnArray);
			};

			if($errors->has('student_id')) {
				$returnArray = array('result' => false, 
					'message' => 'student_id'
				);
				return response()->json($returnArray);
			};

			if($errors->has('teacher_id')) {
				$returnArray = array('result' => false, 
					'message' => 'teacher_id'
				);
				return response()->json($returnArray);
			};

			if($errors->has('emplyee_id')) {
				$returnArray = array('result' => false, 
					'message' => 'emplyee_id'
				);
				return response()->json($returnArray);
			};

			if($errors->has('topic_id')) {
				$returnArray = array('result' => false, 
					'message' => 'topic_id'
				);
				return response()->json($returnArray);
			};

		}

		$assign = AssignIntern::find($id);

		$assign->period = $request->period;
		$assign->student_id = $request->student_id;
		$assign->teacher_id = $request->teacher_id;
		$assign->emplyee_id = $request->emplyee_id;
		$assign->topic_id = $request->topic_id;

		$topics->save();

		return response()->json(['result' => true]);
    }

    public function destroy($id)
    {
	    if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }	

        $assign = AssignIntern::find($id);

        if (is_null($assign)) {
        	return response()->json(['result' => false, 'reason' => 'topic not exist!!!']);
        }

        $assign->delete();

        // DB::table('assign_interns')->where('topic_id', $id)->delete();

        return response()->json(['result' => true]);
    }

    public function compare()
    {
    	$student_cvs = DB::table('student_cvs')->select('student_id', 'skill')->get();

    	$company_require = DB::table('topics')->select('id', 'skill_required')->get();

    	$return_array = array();
    	foreach ($student_cvs as $key => $value) {
    		$temp = array();
    		$temp['id'] = $value->id;
    		$temp['student_id'] = $value->student_id;
    		$student_skill = $value->skill;

    		foreach ($company_require as $key => $value) {
    			$compare = array();
    			$compare['id'] = $value->id;
    			similar_text($student_skill, $value->skill_required, $percent);
    			$compare['compare_percentage'] = $percent
    			$temp['compare'][] = $compare;
    		}

   //  		$student_skill_array = explode(',', $value->skill);

   //  		$skill_array = array();

   //  		foreach ($student_skill_array as $key => $value) {
   //  			$skill = array();

   //  			$skill_value = explode(':', $value);

   //  			$skill[$skill_value[0]] = $skill_value[1];

   //  			$skill_array[] = $skill;
   //  		}
			// // $skill_required_array = array();
   //  		foreach ($company_require as $key => $value) {
   //  			$temp_required = $value['id'];
			// 	$skill_required_array = explode(',', $value->skill_required);

			// 	foreach ($student_skill_array as $key => $value) {
	  //   			$skill = array();

	  //   			$skill_value = explode(':', $value);

	  //   			$skill[$skill_value[0]] = $skill_value[1];

	  //   			$skill_array[] = $skill;
   //  			}
   //  		}
    		$return_array[] = $temp;
    	}

    	return response()->json($return_array);
    }

}
