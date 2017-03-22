<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CompanyRateController extends Controller
{
    public function index()
    {
    	$companies = DB::table('company_rates')
    	->join('users', 'company_rates.student_id = users.id')
		->join('users', 'company_rates.teacher_id = users.id')
		->select('company_rates.period', 'company_rates.point', 'company_rates.rate', 
			'users.name as student_name', 'users.name as teacher_name')
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
				'student_id' => 'required|max:100',
				'employee_id' => 'required|max:15',
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

			if($errors->has('employee_id')) {
				$returnArray = array('result' => false, 
					'message' => 'employee_id'
				);
				return response()->json($returnArray);
			};
		}

		$check_exist = DB::table('company_rates')
		->where([
			['student_id', $request->student_id],
			['employee_id', $request->employee_id],
			['point' => $request->point],
			['period' => $request->period],
			['rate' => $request->rate]
		])->get();

		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => 'company exist!!']);
		}	
		
		$company_rate = new CompanyRate;

		$company_rate->point = $request->point;
		$company_rate->period = $request->period;
		$company_rate->rate = $request->rate;
		$company_rate->student_id = $request->student_id;
		$company_rate->employee_id = $request->employee_id;

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

        $company_rate = DB::table('companu_rates')->where('id', $id)->first();
        if (is_null($company_rate)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
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
				'employee_id' => 'required|max:255',
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

		$check_exist = DB::table('company_rates')
			->where([
				['student_id', $request->student_id],
				['employee_id', $request->employee_id],
				['point' => $request->point],
				['period' => $request->period],
				['rate' => $request->rate]
			])->get();

		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => 'company exist!!']);
		}	
		

		$company_rate = CompanyRate::find($id);

        if (count($company) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

		$company_rate->point = $request->point;
		$company_rate->period = $request->period;
		$company_rate->rate = $request->rate;
		$company_rate->student_id = $request->student_id;
		$company_rate->employee_id = $request->employee_id;

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
