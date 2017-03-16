<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use App\User;
use App\Company;
use App\


use Validator;

use App\Http\Controllers\Controller;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyController extends Controller
{
    	public function __construct()
	{
		$user = JWTAuth::parseToken()->authenticate();
		$this->userid = $user['id'];
		$this->groupid = $user['groupid'];
	}

    public function indexClass()
    {
    	$grades = DB::select('select * from grades');
    	foreach ($grades as $key => $value) {
    		$temp = array();
    		$temp['id'] = $value->id;
    		$temp['name'] = $value->name;
    		$temp['no_students'] = $value->no_students;
    		$temp['teachersid'] = $value->teachersid;

    		$teacher_name = DB::select('select name from teachers where id = ?', [$value->teachersid]);
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
				'teachersid' => 'required|max:255',
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

			if($errors->has('teachersid')) {
				$returnArray = array('result' => false, 
					'message' => 'teachersid'
				);
				return response()->json($returnArray);
			};

		}

		$check_teacher = Teacher::find($request->teachersid);

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
		$grade->teachersid = $request->teachersid;

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
    	
    	$teacher = DB::select('select name from teachers where id = ?', [$check->teachersid]);

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
				'teachersid' => 'required|max:255',
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

			if($errors->has('teachersid')) {
				$returnArray = array('result' => false, 
					'message' => 'teachersid'
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
		$grade->teachersid = $request->teachersid;

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
        DB::delete('delete from students where classid = ?', [$id]);

        return response()->json(['result' => true]);
    }
}
