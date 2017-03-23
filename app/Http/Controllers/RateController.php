<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use Validator;

use App\Employee;
use App\User;
use App\Company;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class RateController extends Controller
{
	public function __construct()
	{
		$user = JWTAuth::parseToken()->authenticate();
		$this->user_id = $user['id'];
		$this->group_id = $user['group_id'];
	}
	public function index()
    {
    	$rates = DB::table('rates')
    		->join('users', 'rates.student_id = users.id')
    		->join('users', 'rates.teacher_id = users.id')
    		->get();
    	$return_array = array();

        foreach ($rates as $key => $value) {
        	$temp = array();
        	$student = DB::table('users')->where('id', $value->student_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['student_id'] = $student->id;
        	$temp['student_first_name'] = $student->firstname;
        	$temp['student_last_name'] = $student->lastname;

        	$teacher = DB::table('users')->where('id', $value->teacher_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['teacher_id'] = $teacher->id;
        	$temp['teacher_first_name'] = $teacher->firstname;
        	$temp['teacher_last_name'] = $teacher->lastname;

        	$temp['mid_point'] = $value->mid_point;
        	$temp['final_point'] = $value->final_point;
        	$temp['rate'] = $value->rate;
        	$temp['period'] = $value->period;
        	$return_array[] = $temp;
        }
    	
    	return response()->json($company_rate);
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[
				'period' => 'required|max:5',
				'student_id' => 'required|max:10000',
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
		}

		$check_exist = DB::table('rates')
		->where([
			['student_id', $request->student_id],
			['teacher_id', $this->user_id],
			['mid_point' => $request->mid_point],
			['final_point' => $request->final_point],
			['period' => $request->period],
			['rate' => $request->rate]
		])->get();

		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => 'rate exist!!']);
		}	
		
		$rate = new Rate;

		$rate->mid_point = $request->mid_point;
		$rate->final_point = $request->final_point;
		$rate->period = $request->period;
		$rate->rate = $request->rate;
		$rate->student_id = $request->student_id;
		$rate->teacher_id = $this->user_id;
		$rate->editable = 0;

		$check_save = $rate->save();

		if (is_null($check_save)) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}

		return response()->json(['result' => true]);
    }

    public function show($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $rates = DB::table('rates')->where('id', $id)->first();
        if (is_null($rate)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }
    	
        foreach ($rates as $key => $value) {
        	$temp = array();
        	$student = DB::table('users')->where('id', $value->student_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['student_id'] = $student->id;
        	$temp['student_first_name'] = $student->firstname;
        	$temp['student_last_name'] = $student->lastname;

        	$teacher = DB::table('users')->where('id', $value->teacher_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['teacher_id'] = $teacher->id;
        	$temp['teacher_first_name'] = $teacher->firstname;
        	$temp['teacher_last_name'] = $teacher->lastname;

        	$temp['mid_point'] = $value->mid_point;
        	$temp['final_point'] = $value->final_point;
        	$temp['rate'] = $value->rate;
        	$temp['period'] = $value->period;
        	$return_array[] = $temp;
        }
    	
    	return response()->json($company_rate);
    }

    public function update(Request $request, $id)
    {
    	if ($this->group_id != 2 || $this->group_id != 3) {
    		return response()->json(['result' => false, 'resaon' => 'this user do not have permission on this secsion']);
    	}
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $validator = Validator::make($request->all(), 
			[
				'student_id' => 'required|max:255',
				'period' => 'required|max:5',
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

		$check_exist = DB::table('rates')
			->where([
				['student_id', $request->student_id],
				['teacher_id', $this->user_id],
				['mid_point' => $request->mid_point],
				['final_point' => $request->final_point],
				['period' => $request->period],
				['rate' => $request->rate]
			])->get();

		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => 'rate exist!!']);
		}	
		

		$rate = Rate::find($id);

        if (count($company) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

		$rate->mid_point = $request->mid_point;
		$rate->final_point = $request->final_point;
		$rate->period = $request->period;
		$rate->rate = $request->rate;
		$rate->student_id = $request->student_id;
		$rate->teacher_id = $this->user_id;
		$rate->editable = $rate->editable + 1;
		if ($rate->editable > 2) {
			return response()->json(['result' => false, 'reason' => 'rate is limit time']);
		}

		$check_save = $rate->save();

		if (is_null($check_save)) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}

		return response()->json(['result' => true]);

    }

    public function destroy($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $rate = Rate::find($id);

        if (is_null($rate)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $rate->delete();

        return response()->json(['result' => true]);
    }

}
