<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CompanyRateController extends Controller
{
    public function index()
    {
    	$companies = DB::table('company_rates')->get();

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

    public function showCompany($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $company = DB::table('companies')->where('id', $id)->first();
        if (is_null($company)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }
    	
    	return response()->json($company);
    }

    public function updateCompany(Request $request, $id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $validator = Validator::make($request->all(), 
			[
				'name' => 'required|string|max:255',
				'description' => 'required|max:255',
				'foundedyear' => 'required|max:4',
				'address' => 'required|max:255',
				'phone' => 'required|max:15',
				'email' => 'required|email|max:50'
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

			if($errors->has('description')) {
				$returnArray = array('result' => false, 
					'message' => 'description'
				);
				return response()->json($returnArray);
			};

			if($errors->has('foundedyear')) {
				$returnArray = array('result' => false, 
					'message' => 'foundedyear'
				);
				return response()->json($returnArray);
			};

			if($errors->has('address')) {
				$returnArray = array('result' => false, 
					'message' => 'address'
				);
				return response()->json($returnArray);
			};

			if($errors->has('phone')) {
				$returnArray = array('result' => false, 
					'message' => 'phone'
				);
				return response()->json($returnArray);
			};

			if($errors->has('email')) {
				$returnArray = array('result' => false, 
					'message' => 'email'
				);
				return response()->json($returnArray);
			};
		}

		$check_exist = DB::table('companies')->where('name', $request->name)->get();

		if (!is_null($check_exist)) {
			return response()->json(['result' => false, 'reason' => 'company exist!!']);
		}	
		

		$company = Company::find($id);

        if (count($company) == 0) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

		$company->name = $request->name;
		$company->description = $request->description;
		$company->foundedyear = $request->foundedyear;
		$company->address = $request->address;
		$company->phone = $request->phone;
		$company->email = $request->email;

		$check_save = $company->save();

		if (is_null($check_save)) {
			return response()->json(['result' => false, 'reason' => 'save fails!!!']);
		}

		return response()->json(['result' => true]);

    }

    public function destroyCompany($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'reason' => 'id must be integer!!']);
        }

        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'reason' => 'id is not accept!!']);
        }

        $company = Company::find($id);

        if (is_null($company)) {
        	return response()->json(['result' => false, 'reason' => 'id not exist']);
        }

        $company->delete();

        return response()->json(['result' => true]);
    }
}
