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
		$this->userid = $user['id'];
		$this->groupid = $user['groupid'];
	}

    public function index()
    {
    	$employees = DB::table('employees')->get();
    	return $employees;
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[
				'emplyeesid' => 'required|string|max:255',
				'dept' => 'required|string|max:255',
				'position' => 'required|string|max:255',
				'companyid' => 'required|numeric|max:255',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('emplyeesid')) {
				$returnArray = array('result' => false, 
					'message' => 'emplyeesid'
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

			if($errors->has('companyid')) {
				$returnArray = array('result' => false, 
					'message' => 'companyid'
				);
				return response()->json($returnArray);
			};
		}

		$employee = new Employee;

		$employee->id = $this->userid;
		$employee->employeeid = $request->employeeid;
		$employee->dept = $request->dept;
		$employee->position = $request->position;
		$employee->companiesid = $request->companyid;

		$employee->save();

		return response()->json(['result' => true]);

    }

    public function show($id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }

        $employee = DB::table('employees')->where('id', $id)
        	->join('users', 'users.id = employees.id')
        	->join('companies', 'companies.id = employees.companyid')
        	->select('users.name', 'companies.name', 'employees.employeeid', 'employees.dept', 'employees.position', 'employees.companiesid');
        	->first();
        
        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
    	if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ((int)$id > 1000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }

        $validator = Validator::make($request->all(), 
			[
				'emplyeesid' => 'required|string|max:255',
				'dept' => 'required|string|max:255',
				'position' => 'required|string|max:255',
				'companyid' => 'required|numeric|max:255',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('emplyeesid')) {
				$returnArray = array('result' => false, 
					'message' => 'emplyeesid'
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

			if($errors->has('companyid')) {
				$returnArray = array('result' => false, 
					'message' => 'companyid'
				);
				return response()->json($returnArray);
			};
		}

		$employee = Employee::find($id);

		if (is_null($employee)) {
			return response()->json(['result' => false, 'reason' => 'employee not found!!']);
		}

		$employee->id = $this->userid;
		$employee->employeeid = $request->employeeid;
		$employee->dept = $request->dept;
		$employee->position = $request->position;
		$employee->companiesid = $request->companyid;

		$employee->save();

		return response()->json(['result' => true]);
    }

    public function destroy($id)
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
}
