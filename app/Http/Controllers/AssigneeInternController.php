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
    	->join('users', 'users.id = assign_interns.student_id')
    	->join('users', 'users.id = assign_interns.teacher_id')
		->join('users', 'users.id = assign_interns.employee_id')
		->join('topics', 'topics.id = assign_interns.topic_id')
		->select('users.name as student_name', 'users.name as teacher_name', 'users.name as employee_name',
			'topics.title as tilte', 'assign_interns.period as period')
    	->get();
    	return $assigns;
    }

    public function store(Request $request)
    {
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

			if($errors->has('employee_id')) {
				$returnArray = array('result' => false, 
					'message' => 'employee_id'
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
		$assign->teacher_id = $request->teacher_id;
		$assign->employee_id = $request->employee_id;
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

        $assign = DB::table('assign_interns')->where('id', $id)
        	->join('users', 'users.id = assign_interns.student_id')
	    	->join('users', 'users.id = assign_interns.teacher_id')
			->join('users', 'users.id = assign_interns.employee_id')
			->join('topics', 'topics.id = assign_interns.topic_id')
			->select('users.name as student_name', 'users.name as teacher_name', 'users.name as employee_name',
			'topics.title as tilte', 'assign_interns.period as period')
        	->first();
        
        return response()->json($assign);
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
}
