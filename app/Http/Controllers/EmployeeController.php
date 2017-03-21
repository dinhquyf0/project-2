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

class EmployeeController extends Controller
{
	public function __construct(){
		$user = JWTAuth::parseToken()->authenticate();
		$this->user_id = $user['id'];
		$this->group_id = $user['groupid'];
	}

    public function indexEmployees()
    {
    	$employees = DB::table('employees')->get();
    	return $employees;
    }

    public function storeEmployees(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[
				'employee_id' => 'required|string|max:255',
				'dept' => 'required|string|max:255',
				'position' => 'required|string|max:255',
				'company_id' => 'required|max:255',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('emplyee_id')) {
				$returnArray = array('result' => false, 
					'message' => 'emplyee_id'
				);
				return response()->json($returnArray);
			};

			if($errors->has('dept')) {
				$returnArray = array('result' => false, 
					'message' => 'dept'
				);
				return response()->json($returnArray);
			};

			if($errors->has('position')) {
				$returnArray = array('result' => false, 
					'message' => 'position'
				);
				return response()->json($returnArray);
			};

			if($errors->has('company_id')) {
				$returnArray = array('result' => false, 
					'message' => 'company_id'
				);
				return response()->json($returnArray);
			};
		}

		$employee = new Employee;

		$employee->id = $this->user_id;
		$employee->employee_id = $request->employee_id;
		$employee->dept = $request->dept;
		$employee->position = $request->position;
		$employee->company_id = $request->company_id;

		$employee->save();

		return response()->json(['result' => true]);

    }

    public function showEmployees($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }

        $employee = DB::table('employees')->where('id', $id)
        	->join('users', 'users.id = employees.id')
        	->join('companies', 'companies.id = employees.company_id')
        	->select('users.name', 'companies.name', 'employees.employee_id', 'employees.dept', 'employees.position', 'employees.company_id');
        	->first();
        
        return response()->json($employee);
    }

    public function updateEmployees(Request $request, $id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }

        $validator = Validator::make($request->all(), 
			[
				'emplyee_id' => 'required|string|max:255',
				'dept' => 'required|string|max:255',
				'position' => 'required|string|max:255',
				'company_id' => 'required|max:255',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('emplyee_id')) {
				$returnArray = array('result' => false, 
					'message' => 'emplyee_id'
				);
				return response()->json($returnArray);
			};

			if($errors->has('dept')) {
				$returnArray = array('result' => false, 
					'message' => 'dept'
				);
				return response()->json($returnArray);
			};

			if($errors->has('position')) {
				$returnArray = array('result' => false, 
					'message' => 'position'
				);
				return response()->json($returnArray);
			};

			if($errors->has('company_id')) {
				$returnArray = array('result' => false, 
					'message' => 'company_id'
				);
				return response()->json($returnArray);
			};
		}

		$employee = Employee::find($id);

		if (is_null($employee)) {
			return response()->json(['result' => false, 'reason' => 'employee not found!!']);
		}

		$employee->id = $this->user_id;
		$employee->employee_id = $request->employee_id;
		$employee->dept = $request->dept;
		$employee->position = $request->position;
		$employee->company_id = $request->company_id;

		$employee->save();

		return response()->json(['result' => true]);
    }

    public function destroyEmployees($id)
    {
	    if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }	

        $employee = Employee::find($id);

        if (is_null($employee)) {
        	return response()->json(['result' => false, 'reason' => 'employee not exist!!!']);
        }

        $employee->delete();

        // DB::table('assign_interns')->where('topic_id', $id)->delete();

        return response()->json(['result' => true]);
    }

    public function indexCompanies()
    {
    	$companies = DB::table('companies')->get();

    	return response()->json($companies);
    }

    public function storeCompany(Request $request)
    {
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
		
		$company = new Company;

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
