<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use App\User;
use App\CompanyRate;
use App\Employee;
use App\TimeCheckingManager;

use Validator;

use App\Http\Controllers\Controller;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyRateController extends Controller
{
	public function __construct(){
		$user = JWTAuth::parseToken()->authenticate();
		$this->user_id = $user['id'];
		$this->group_id = $user['group_id'];
	}
    public function index()
    {

        $company_rates = DB::table('company_rates')
        	->join('users', 'company_rates.student_id', '=', 'users.id')
	    	->join('users', 'company_rates.employee_id', '=', ' users.id')
	    	->join('topics', 'topics.id', '=', 'assign_interns.topic_id')
	    	->select('company_rates.id','company_rates.student_id', 'company_rates.employee_id', 'company_rates.topic_id', 'company_rates.rate', 'company_rates.point', 'company_rates.period')
        	->get();
    	$return_array = array();

    	if (is_null($company_rates) {
    		return response()->json(['result' => false, 'reason' => 'db empty']);
    	}

        foreach ($company_rates as $key => $value) {
        	$temp = array();

        	$student = DB::table('users')->where('id', $value->student_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['student_id'] = $student->id;
        	$temp['student_first_name'] = $student->firstname;
        	$temp['student_last_name'] = $student->lastname;

        	$employee = DB::table('users')->where('id', $value->employee_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['employee_id'] = $employee->id;
        	$temp['employee_first_name'] = $employee->firstname;
        	$temp['employee_last_name'] = $employee->lastname;

        	$topic = DB::table('topics')->where('id', $value->topic_id)->select('id', 'title')->first();
        	$temp['topic_id'] = $topic->id;
        	$temp['topic_title'] = $topic->title;

        	$temp['point'] = $value->point;
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
				'point' => 'required|max:10',
				'period' => 'required|max:5',
				'rate' => 'required|string|max:255',
				'student_id' => 'required|max:100',
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

		}

		$check_exist = DB::table('company_rates')
		->where([
			['student_id', $request->student_id],
			['employee_id', $this->user_id],
			['point' => $request->point],
			['period' => $request->period],
			['rate' => $request->rate]
		])->get();

		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => 'company rate exist!!']);
		}	
		
		$company_rate = new CompanyRate;

		$company_rate->point = $request->point;
		$company_rate->period = $request->period;
		$company_rate->rate = $request->rate;
		$company_rate->student_id = $request->student_id;
		$company_rate->employee_id = $this->user_id;

		$check_save = $company_rate->save();

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

        $company_rate = DB::table('companu_rates')->where('id', $id)
        	->join('users', 'company_rates.student_id', '=', 'users.id')
	    	->join('users', 'company_rates.employee_id', '=', ' users.id')
	    	->join('topics', 'topics.id', '=', 'assign_interns.topic_id')
	    	->select('company_rates.id', 'company_rates.student_id', 'company_rates.employee_id', 'company_rates.topic_id', 'company_rates.rate', 'company_rates.point', 'company_rates.period')
        	->first();
        if (is_null($company_rate)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }
    	$return_array = array();

        foreach ($company_rate as $key => $value) {

        	$temp = array();

        	$student = DB::table('users')->where('id', $value->student_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['student_id'] = $student->id;
        	$temp['student_first_name'] = $student->firstname;
        	$temp['student_last_name'] = $student->lastname;

        	$employee = DB::table('users')->where('id', $value->employee_id)->select('id', 'firstname', 'lastname')->first();
        	$temp['employee_id'] = $employee->id;
        	$temp['employee_first_name'] = $employee->firstname;
        	$temp['employee_last_name'] = $employee->lastname;

        	$topic = DB::table('topics')->where('id', $value->topic_id)->select('id', 'title')->first();
        	$temp['topic_id'] = $topic->id;
        	$temp['topic_title'] = $topic->title;

        	$temp['point'] = $value->point;
        	$temp['rate'] = $value->rate;
        	$temp['period'] = $value->period;

        	$return_array[] = $temp;
        }
    	
    	return response()->json($company_rate);
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

			if($errors->has('employee_id')) {
				$returnArray = array('result' => false, 
					'message' => 'employee_id'
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

		$company_rate = CompanyRate::find($id);

        if (is_null($company_rate)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

		$company_rate->point = $request->point;
		$company_rate->period = $request->period;
		$company_rate->rate = $request->rate;
		$company_rate->student_id = $request->student_id;
		$company_rate->employee_id = $this->user_id;

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

        $company_rate = CompanyRate::find($id);

        if (is_null($company_rate)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $company_rate->delete();

        return response()->json(['result' => true]);
    }
}
