<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use App\User;
use App\Grade;
use App\Student;
use App\Teacher;
use App\Department;

use Validator;

use App\Http\Controllers\Controller;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class SchoolController extends Controller
{
	public function __construct()
	{
		$user = JWTAuth::parseToken()->authenticate();
		$this->user_id = $user['id'];
		$this->group_id = $user['group_id'];
	}

    public function indexClass()
    {
    	$grades = DB::table('grades')
    		->join('users', 'grades.teacher_id', '=', 'users.id')
    		->select('grades.id', 'grades.name', 'grades.no_students', 'users.firstname', 'users.lastname')
    		->get();
    	if (is_null($grades)) {
    		return response()->json(['result' => false, 'reason' => 'db empty']);
    	}
    	return response()->json(['result' => true, 'data' => $grades]);
    }

    public function storeClass(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[
				'name' => 'required|string|max:255',
				'no_students' => 'required|max:255',
				'teacher_id' => 'required|max:255',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('name')) {
				$returnArray = array('result' => false, 
					'message' => 'name!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('no_students')) {
				$returnArray = array('result' => false, 
					'message' => 'no_students'
				);
				return response()->json($returnArray);
			};

			if($errors->has('teacher_id')) {
				$returnArray = array('result' => false, 
					'message' => 'teacher_id'
				);
				return response()->json($returnArray);
			};

		}

		$check_teacher = DB::table('teachers')->where('id', $request->teacher_id)->get();

		if (is_null($check_teacher)) {
			return response()->json(['result' => false, 'reason' => 'teacher not exist!!']);
		}

		$check_exist = DB::table('grades')->where('name', $request->name)->get();

		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => 'class exist!!']);
		}	
		
		$grade = new Grade;

		$grade->name = $request->name;
		$grade->no_students = $request->no_students;
		$grade->teacher_id = $request->teacher_id;

		$check_save = $grade->save();

		if (count($check_save) == 0) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}

		return response()->json(['result' => true]);
    }

    public function showClass($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $check = Grade::find($id);

        if (is_null($check)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }
    	
    	$teacher = DB::select('select name from teachers where id = ?', [$check->teacher_id]);

    	$return_array = array();

    	array_merge($return_array, $check);
    	if (count($teacher) == 0) {
    		$return_array['teacher'] = 'N/A';
    	}

    	$return_array['teacher'] = $teacher[0]->name;

    	return response()->json($return_array);
    }

    public function updateClass(Request $request, $id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $validator = Validator::make($request->all(), 
			[
				'name' => 'required|string|max:255',
				'no_students' => 'required|max:255',
				'teacher_id' => 'required|max:255',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('name')) {
				$returnArray = array('result' => false, 
					'message' => 'name!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('no_students')) {
				$returnArray = array('result' => false, 
					'message' => 'no_students'
				);
				return response()->json($returnArray);
			};

			if($errors->has('teacher_id')) {
				$returnArray = array('result' => false, 
					'message' => 'teacher_id'
				);
				return response()->json($returnArray);
			};
		}

		$grade = Grade::find($id);

        if (count($grade) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

		$check_teacher = DB::table('teachers')->where('id', $request->teacher_id)->get();

		if (is_null($check_teacher)) {
			return response()->json(['result' => false, 'reason' => 'teacher not exist!!']);
		}

		$check_exist = DB::table('grades')->where('name', $request->name)->get();

		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => 'class exist!!']);
		}	

        $grade->name = $request->name;
		$grade->no_students = $request->no_students;
		$grade->teacher_id = $request->teacher_id;

		$check_save = $grade->save();

		if (is_null($check_save)) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}
		return response()->json(['result' => true]);
    }

    public function destroyClass($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $grade = Grade::find($id);

        if (is_null($grade)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $grade->delete();
        DB::update('update students set class_id = null where class_id = ?', [$id]);

        return response()->json(['result' => true]);
    }



    public function indexDepartment()
    {
    	$departments = DB::select('select * from departments');
    	if (is_null($departments)) {
    		return response()->json(['result' => false, 'reason' => 'db empty']);
    	}
    	return response()->json(['result' => true, 'data' => $departments]);
    }

    public function storeDepartment(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[
				'name' => 'required|max:255',
				'phone' => 'required|max:255',
				'no_teachers' => 'required|max:255',
				'address' => 'required|max:255',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('name')) {
				$returnArray = array('result' => false, 
					'message' => 'name!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('phone')) {
				$returnArray = array('result' => false, 
					'message' => 'phone'
				);
				return response()->json($returnArray);
			};

			if($errors->has('no_teachers')) {
				$returnArray = array('result' => false, 
					'message' => 'no_teachers'
				);
				return response()->json($returnArray);
			};

			if($errors->has('address')) {
				$returnArray = array('result' => false, 
					'message' => 'address'
				);
				return response()->json($returnArray);
			};

		}

		$check_exist = DB::table('departments')->where('name', $request->name)->get();

		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => 'departments exist!!']);
		}	
		
		$department = new Department;

		$department->name = $request->name;
		$department->phone = $request->phone;
		$department->no_teachers = $request->no_teachers;
		$department->address = $request->address;

		$check_save = $department->save();

		if (count($check_save) == 0) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}

		return response()->json(['result' => true]);
    }

    public function showDepartment($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $check = Department::find($id);

        if (is_null($check)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }
    	
    	$teachers = DB::select('select name from teachers where dept_id = ?', [$id]);

    	$return_array = array();

    	array_merge($return_array, $check);
    	if (count($teachers) == 0) {
    		$return_array['teachers'] = 'N/A';
    	} else {
    		foreach ($teacher as $key => $value) {
    			$temp = array();
    			$temp['id'] = $value->id;
    			$temp['teacher_name'] = $value->name;
    			$return_array['teachers'][] = $temp;
    		}
    	}
    	return response()->json(['result' => true, 'data' => $return_array]);
    }

    public function updateDepartment(Request $request, $id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $validator = Validator::make($request->all(), 
			[
				'name' => 'required|max:255',
				'no_teachers' => 'required|max:255',
				'phone' => 'required|max:255',
				'address' => 'required|max:255',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('name')) {
				$returnArray = array('result' => false, 
					'message' => 'name!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('no_teachers')) {
				$returnArray = array('result' => false, 
					'message' => 'no_teachers'
				);
				return response()->json($returnArray);
			};

			if($errors->has('phone')) {
				$returnArray = array('result' => false, 
					'message' => 'phone'
				);
				return response()->json($returnArray);
			};

			if($errors->has('address')) {
				$returnArray = array('result' => false, 
					'message' => 'address'
				);
				return response()->json($returnArray);
			};
		}

		$department = Department::find($id);

        if (count($department) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

		$check_exist = DB::table('departments')->where('name', $request->name)->get();

		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => 'departments exist!!']);
		}	

        $department->name = $request->name;
		$department->no_teachers = $request->no_teachers;
		$department->phone = $request->phone;
		$department->address = $request->address;

		$check_save = $department->save();

		if (count($check_save) == 0) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}
		return response()->json(['result' => true]);
    }

    public function destroyDepartment($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 10000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $department = Department::find($id);

        if (count($department) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $department->delete();
        DB::delete('delete from teachers where dept_id = ?', [$id]);

        return response()->json(['result' => true]);
    }
}
