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
    	$grades = DB::select('select * from grades');
    	foreach ($grades as $key => $value) {
    		$temp = array();
    		$temp['id'] = $value->id;
    		$temp['name'] = $value->name;
    		$temp['no_students'] = $value->no_students;
    		$temp['teacher_id'] = $value->teachersid;

    		$teacher_name = DB::select('select name from teachers where id = ?', [$value->teacher_id]);
    		$temp['teacher_name'] = $teacher_name[0]->name;
    		$return_array[] = $temp;
    	}
    	return response()->json($return_array);
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

		$check_teacher = Teacher::find($request->teacher_id);

		if (count($check_teacher) == 0) {
			return response()->json(['result' => false, 'reason' => 'teacher not exist!!']);
		}

		$check_exist = Grade::find($request->name);

		if (count($check_exist) > 0) {
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

        if (count($check) == 0) {
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

        $check_exist = Grade::find($request->name);

		if (count($check_exist) > 0) {
			return response()->json(['result' => false, 'reason' => 'class exist!!']);
		}	

        $grade->name = $request->name;
		$grade->no_students = $request->no_students;
		$grade->teacher_id = $request->teacher_id;

		$check_save = $grade->save();

		if (count($check_save) == 0) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}

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

        if (count($grade) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $grade->delete();
        DB::delete('delete from students where class_id = ?', [$id]);

        return response()->json(['result' => true]);
    }



    public function indexDepartment()
    {
    	$teachers = DB::select('select * from teachers');
    	return response()->json($teachers);
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

		$check_department = Department::find($request->name);

		if (count($check_department) > 0) {
			return response()->json(['result' => false, 'reason' => 'department exist!!']);
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

        if (count($check) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }
    	
    	$teachers = DB::select('select name from teachers where department_id = ?', [$id]);

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
    	return response()->json($return_array);
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

        $check_exist = Department::find($request->name);
        if (count($check_exist) > 0) {
        	return response()->json(['result' => false, 'reason' => 'name exist']);
        }

        $department->name = $request->name;
		$department->no_teachers = $request->no_teachers;
		$department->phone = $request->phone;
		$department->address = $request->address;

		$check_save = $department->save();

		if (count($check_save) == 0) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}

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
        DB::delete('delete from teachers where department_id = ?', [$id]);

        return response()->json(['result' => true]);
    }
}
