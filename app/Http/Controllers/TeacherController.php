<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use App\User;
use App\Teacher;
use App\InternStatus;
use App\Department;
use App\DeadLine;

use Validator;

use App\Http\Controllers\Controller;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;


class TeacherController extends Controller
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
    	$teachers = DB::select('select * from teachers');
    	foreach ($students as $key => $value) {
    		$temp = array();
    		$temp['id'] = $value->id;
    		$temp['teacherid'] = $value->teacherid;
    		$temp['start'] = $value->start;
    		$temp['position'] = $value->position;
    		$temp['degree'] = $value->degree;
    		$temp['departmentsid'] = $value->departmentsid;

    		$department_name = DB::select('select name from departments where id = ?', [$value->departmentsid]);
    		$temp['department_name'] = $department_name[0]->name;
    		$return_array[] = $temp;
    	}
    	return response()->json($return_array);
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[
				'teacherid' => 'required|max:255',
				'start' => 'required|max:255',
				'position' => 'required|max:255',
				'degree' => 'required|email|max:255',
                'departmentsid' => 'required|max:15',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('teacherid')) {
				$returnArray = array('result' => false, 
					'message' => 'teacherid!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('start')) {
				$returnArray = array('result' => false, 
					'message' => 'start'
				);
				return response()->json($returnArray);
			};

			if($errors->has('position')) {
				$returnArray = array('result' => false, 
					'message' => 'position'
				);
				return response()->json($returnArray);
			};

			if($errors->has('degree')) {
				$returnArray = array('result' => false, 
					'message' => 'degree'
				);
				return response()->json($returnArray);
			};

			if($errors->has('departmentsid')) {
				$returnArray = array('result' => false, 
					'message' => 'departmentsid'
				);
				return response()->json($returnArray);
			};

		}

		$check_department = Department::find($request->departmentsid);

		if (count($check_department) == 0) {
			return response()->json(['result' => false, 'reason' => 'department not exist!!']);
		}

		$check_teacher_info = Teacher::find($request->teacherid);

		if (count($check_teacher_info) > 0) {
			return response()->json(['result' => false, 'reason' => 'teacher exist!!']);
		}	
		
		$teacher = new Teacher;
		$teacher->id = $this->id;
		$teacher->teacherid = $request->teacherid;
		$teacher->start = $request->start;
		$teacher->position = $request->position;
		$teacher->degree = $request->degree;
		$teacher->departmentsid = $request->departmentsid;

		$check_save = $teacher->save();

		if (count($check_save) == 0) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}

		return response()->json(['result' => true]);
    }

    public function show($id)
    {
    	if (!is_numeric((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int) < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $check = Teacher::find($id);

        if (count($check) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }
    	
    	$department = DB::select('select name from departments where id = ?', [$check->departmentsid]);

    	$return_array = array();

    	array_merge($return_array, $check);
    	if (count($department) == 0) {
    		$return_array['department'] = 'N/A';
    	}

    	$return_array['department'] = $department[0]->name;

    	return response()->json($return_array);
    }

    public function update(Request $request, $id)
    {
    	if (!is_numeric((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int) < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $validator = Validator::make($request->all(), 
			[
				'teacherid' => 'required|max:255',
				'start' => 'required|max:255',
				'position' => 'required|max:255',
				'degree' => 'required|email|max:255',
                'departmentsid' => 'required|max:15',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('teacherid')) {
				$returnArray = array('result' => false, 
					'message' => 'teacherid!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('start')) {
				$returnArray = array('result' => false, 
					'message' => 'start'
				);
				return response()->json($returnArray);
			};

			if($errors->has('position')) {
				$returnArray = array('result' => false, 
					'message' => 'position'
				);
				return response()->json($returnArray);
			};

			if($errors->has('degree')) {
				$returnArray = array('result' => false, 
					'message' => 'degree'
				);
				return response()->json($returnArray);
			};

			if($errors->has('departmentsid')) {
				$returnArray = array('result' => false, 
					'message' => 'departmentsid'
				);
				return response()->json($returnArray);
			};

		}

		$teacher = Teacher::find($id);

        if (count($teacher) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $check = DB::select('select * from teachers where teacherid = ?', [$request->teacherid]);
        if (count($check) == 0) {
        	return response()->json(['result' => false, 'reason' => 'teacherid exist!!']);
        }

        $teacher->teacherid = $request->teacherid;
		$teacher->start = $request->start;
		$teacher->position = $request->position;
		$teacher->degree = $request->degree;
		$teacher->departmentsid = $request->departmentsid;

		$check_save = $teacher->save();

		if (count($check_save) == 0) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}

		return response()->json(['result' => true]);
    }

    public function destroy($id)
    {
    	if (!is_numeric((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int) < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $teacher = Teacher::find($id);

        if (count($teacher) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $teacher->delete();

        return response()->json(['result' => true]);
    }

    public function showDeadLine()
    {
    	$deadlines = DB::table('dead_lines')->get();
    	return response()->json($deadlines);
    }

    public function createDeadLine(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[
                'period' => 'required'
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			return response()->json(['result' => false, 'message' => 'validate fails!!']);
		}

		$deadlines = new DeadLine;

		$deadlines->company_register_topic = $request->company_register_topic;
		$deadlines->student_register_topic = $request->student_register_topic;
		$deadlines->company_rate = $request->company_rate;
		$deadlines->mark = $request->mark;
		$deadlines->company_report = $request->company_report;
		$deadlines->student_report = $request->student_report;
		$deadlines->period = $request->period;

		$check_save = $deadlines->save();

		if (is_null($check_save)) {
			return response()->json(['result' => false]);
		}

		return response()->json(['result' => true]);
    }

    public function updateDeadLine(Request $request, $id)
    {
    	if (!is_numeric((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int) < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

    	$validator = Validator::make($request->all(), 
			[
                'period' => 'required'
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			return response()->json(['result' => false, 'message' => 'validate fails!!']);
		}

		$deadlines = DeadLine::find($id);

		$deadlines->company_register_topic = $request->company_register_topic;
		$deadlines->student_register_topic = $request->student_register_topic;
		$deadlines->company_rate = $request->company_rate;
		$deadlines->mark = $request->mark;
		$deadlines->company_report = $request->company_report;
		$deadlines->student_report = $request->student_report;
		$deadlines->period = $request->period;

		$check_save = $deadlines->save();

		if (is_null($check_save)) {
			return response()->json(['result' => false]);
		}

		return response()->json(['result' => true]);
    }

    public function deleteDeadLine($id)
    {
    	if (!is_numeric((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int) < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $deadlines = DeadLine::find($id);

        if (is_null($deadlines)) {
        	return response()->json(['result' => false, 'reason' => 'id  not exist']);
        } else {
        	$deadlines->delete();
        	return response()->json(['result' => true]);
        }
    }

    public function indexInternStatus()
    {
    	if ($this->group_id != 3) {
    		return response()->json(['result' => false, 'reason' => 'only teacher manager can use this']);
    	}

    	$intern_statuses = DB::table('intern_statuses')
    		->join('users', 'intern_statuses.student_id', '=', 'users.id')
    		->select('users.firstname', 'users.lastname', 'intern_statuses.period', 'intern_statuses.status', 'intern_statuses.link_report')
    		->get();
    	return response()->json($intern_statuses);
    }

    public function storeInternStatus(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[
                'student_id' => 'required',
                'period' => 'required',
                'status' => 'required'
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			return response()->json(['result' => false, 'message' => 'validate fails!!']);
		}

		$intern_statuses = new InternStatus;

		$intern_statuses->student_id = $request->student_id;
		$intern_statuses->period = $request->period;
		$intern_statuses->status = $request->status;

		$intern_statuses->save();

		return response()->json(['result' => true]);
    }

    public function updateInternStatus(Request $request, $id)
    {
    	if (!is_numeric((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int) < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

    	$validator = Validator::make($request->all(), 
			[
                'student_id' => 'required'
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			return response()->json(['result' => false, 'reason' => 'validate fails!!']);
		}

		$intern_statuses = InternStatus::find($id);

		if (is_null($intern_statuses)) {
			return response()->json(['result' => false, 'reason' => 'id not found']);
		}

		$intern_statuses->student_id = $request->student_id;
		$intern_statuses->period = $request->period;
		$intern_statuses->status = $request->status;

		$intern_statuses->save();

		return response()->json(['result' => true]);
    }

    public function getStudentIntent()
    {
    	if ($this->groupid != 3) {
    		return response()->json(['result' => false, 'reason' => 'permission denie']);
    	}

    	$intents = DB::table('student_interns')
    		->join('users', 'student_interns.student_id', '=', 'users.id')
    		->select('users.firstname', 'users.lastname', 
    			'student_interns.topic_1', 'student_interns.topic_2', 'student_interns.topic_3'
    			'student_interns.period')
    		->get();

    	return response()->json($intents);
    }

    public function updateCompanyAccept($id)
    {
    	if ($this->group_id != 3) {
    		return response()->json(['result' => false, 'permission denie']);
    	}
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $company = Company::find($id);
        if (is_null($company)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $company->is_accept = abs($company->is_accept - 1);

        $check = $company->save();

        if (is_null($check)) {
        	return response()->json(['result' => false, 'reason' => 'update fails']);
        }

        return response()->json(['result' => true]);
    }

    public function updateTopicAccept($id)
    {
    	if ($this->group_id != 3) {
    		return response()->json(['result' => false, 'permission denie']);
    	}
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $topic = Topic::find($id);
        if (is_null($topic)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $topic->is_accept = abs($topic->is_accept - 1);

        $check = $topic->save();

        if (is_null($check)) {
        	return response()->json(['result' => false, 'reason' => 'update fails']);
        }

        return response()->json(['result' => true]);
    }
}
