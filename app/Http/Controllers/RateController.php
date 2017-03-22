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
    	$companies = DB::table('rates')
    		->join('users', 'rates.student_id = users.id')
    		->join('users', 'rates.teacher_id = users.id')
    		->select('rates.period', 'rates.point', 'rates.rate', 'users.name as student_name', 'users.name as teacher_name')
    		->get();

    	return response()->json($companies);
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[
				'point' => 'required|max:10',
				'period' => 'required|max:5',
				'rate' => 'required|string|max:255',
				'student_id' => 'required|max:10000',
				'teacher_id' => 'required|max:10000',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('point')) {
				$returnArray = array('result' => false, 
					'message' => 'point!'
				);
				return response()->json($returnArray);
			};

			if($errors->has('period')) {
				$returnArray = array('result' => false, 
					'message' => 'period'
				);
				return response()->json($returnArray);
			};

			if($errors->has('rate')) {
				$returnArray = array('result' => false, 
					'message' => 'rate'
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
		}

		$check_exist = DB::table('rates')
		->where([
			['student_id', $request->student_id],
			['teacher_id', $request->teacher_id],
			['point' => $request->point],
			['period' => $request->period],
			['rate' => $request->rate]
		])->get();

		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => 'rate exist!!']);
		}	
		
		$rate = new Rate;

		$rate->point = $request->point;
		$rate->period = $request->period;
		$rate->rate = $request->rate;
		$rate->student_id = $request->student_id;
		$rate->teacher_id = $request->teacher_id;

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

        $rate = DB::table('rates')->where('id', $id)->first();
        if (is_null($rate)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }
    	
    	return response()->json($rate);
    }

    public function update(Request $request, $id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $validator = Validator::make($request->all(), 
			[
				'student_id' => 'required|max:255',
				'teacher_id' => 'required|max:255',
				'point' => 'required|max:10',
				'period' => 'required|max:5',
				'rate' => 'required|string|max:255',
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

			if($errors->has('teacher_id')) {
				$returnArray = array('result' => false, 
					'message' => 'teacher_id'
				);
				return response()->json($returnArray);
			};

			if($errors->has('point')) {
				$returnArray = array('result' => false, 
					'message' => 'point'
				);
				return response()->json($returnArray);
			};

			if($errors->has('period')) {
				$returnArray = array('result' => false, 
					'message' => 'period'
				);
				return response()->json($returnArray);
			};

			if($errors->has('rate')) {
				$returnArray = array('result' => false, 
					'message' => 'rate'
				);
				return response()->json($returnArray);
			};

		}

		$check_exist = DB::table('rates')
			->where([
				['student_id', $request->student_id],
				['teacher_id', $request->teacher_id],
				['point' => $request->point],
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

		$rate->point = $request->point;
		$rate->period = $request->period;
		$rate->rate = $request->rate;
		$rate->student_id = $request->student_id;
		$rate->employee_id = $request->teacher_id;

		$check_save = $company_rate->save();

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
