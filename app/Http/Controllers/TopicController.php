<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use Validator;

use App\Topic;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class TopicController extends Controller
{
	public function __construct(){
		$user = JWTAuth::parseToken()->authenticate();
		$this->userid = $user['id'];
		$this->groupid = $user['groupid'];
	}

    public function index()
    {
    	$topic = DB::table('topics')->get();
    	return $topics;
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[
				'title' => 'required|string|max:255',
				'categories' => 'required|string|max:255',
				'description' => 'required|string|max:255',
				'no_interns' => 'required|numeric|max:255',
                'timelimit' => 'required|smallInteger|max:3',
				'start' => 'required|string|max:60',
				'stop' => 'required|string|max:60',
				'employeeid' => 'required|numeric|max:1000'
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('title')) {
				$returnArray = array('result' => false, 
					'message' => 'title'
				);
				return response()->json($returnArray);
			};

			if($errors->has('categories')) {
				$returnArray = array('result' => false, 
					'message' => 'categories'
				);
				return response()->json($returnArray);
			};

			if($errors->has('description')) {
				$returnArray = array('result' => false, 
					'message' => 'description'
				);
				return response()->json($returnArray);
			};

			if($errors->has('no_interns')) {
				$returnArray = array('result' => false, 
					'message' => 'no_interns'
				);
				return response()->json($returnArray);
			};

			if($errors->has('timelimit')) {
				$returnArray = array('result' => false, 
					'message' => 'timelimit'
				);
				return response()->json($returnArray);
			};

			if($errors->has('start')) {
				$returnArray = array('result' => false, 
					'message' => 'start'
				);
				return response()->json($returnArray);
			};

			if($errors->has('stop')) {
				$returnArray = array('result' => false, 
					'message' => 'stop'
				);
				return response()->json($returnArray);
			};

			if($errors->has('employeeid')) {
				$returnArray = array('result' => false, 
					'message' => 'employeeid'
				);
				return response()->json($returnArray);
			};
		}

		$topics = new Topic;

		$topics->title = $request->title;
		$topics->categories = $request->categories;
		$topics->description = $request->description;
		$topics->no_interns = $request->no_interns;
		$topics->timelimit = $request->timelimit;
		$topics->status = $request->status;
		$topics->stop = $request->stop;
		$topics->emplyeesid = $request->employeeid;

		$topics->save();

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

        $topic = DB::table('topics')->where('id', $id)
        	->join('users', 'users.id = topics.emplyeesid')
        	->select('users.name', 'topics.title', 'topics.categories', 'topics.description', 'topics.no_interns', 'topics.timelimit', 'topics.start', 'topics.status', 'topics.stop', 'topics.emplyeesid');
        	->first();
        
        return response()->json($topics);
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
				'title' => 'required|string|max:255',
				'categories' => 'required|string|max:255',
				'description' => 'required|string|max:255',
				'no_interns' => 'required|numeric|max:255',
                'timelimit' => 'required|smallInteger|max:3',
				'start' => 'required|string|max:60',
				'stop' => 'required|string|max:60',
				'employeeid' => 'required|numeric|max:1000'
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {	
			$errors = $validator->errors();
			if($errors->has('title')) {
				$returnArray = array('result' => false, 
					'message' => 'title'
				);
				return response()->json($returnArray);
			};

			if($errors->has('categories')) {
				$returnArray = array('result' => false, 
					'message' => 'categories'
				);
				return response()->json($returnArray);
			};

			if($errors->has('description')) {
				$returnArray = array('result' => false, 
					'message' => 'description'
				);
				return response()->json($returnArray);
			};

			if($errors->has('no_interns')) {
				$returnArray = array('result' => false, 
					'message' => 'no_interns'
				);
				return response()->json($returnArray);
			};

			if($errors->has('timelimit')) {
				$returnArray = array('result' => false, 
					'message' => 'timelimit'
				);
				return response()->json($returnArray);
			};

			if($errors->has('start')) {
				$returnArray = array('result' => false, 
					'message' => 'start'
				);
				return response()->json($returnArray);
			};

			if($errors->has('stop')) {
				$returnArray = array('result' => false, 
					'message' => 'stop'
				);
				return response()->json($returnArray);
			};

			if($errors->has('employeeid')) {
				$returnArray = array('result' => false, 
					'message' => 'employeeid'
				);
				return response()->json($returnArray);
			};
		}

		$topics = Topic::find($id);

		$topics->title = $request->title;
		$topics->categories = $request->categories;
		$topics->description = $request->description;
		$topics->no_interns = $request->no_interns;
		$topics->timelimit = $request->timelimit;
		$topics->status = $request->status;
		$topics->stop = $request->stop;
		$topics->emplyeesid = $request->employeeid;

		$topics->save();

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

        $topics = Topic::find($id);

        if (is_null($topics)) {
        	return response()->json(['result' => false, 'reason' => 'topic not exist!!!']);
        }

        $topics->delete();

        DB::table('assign_interns')->where('topic_id', $id)->delete();

        return response()->json(['result' => true]);
    }
}
