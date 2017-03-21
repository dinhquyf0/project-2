<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use App\User;
use App\Teacher;
use App\Department;

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


}
