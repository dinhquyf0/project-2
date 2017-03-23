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
		$this->user_id = $user['id'];
		$this->group_id = $user['group_id'];
	}

    public function index()
    {
    	if ($this->group_id == 1 || $this->group_id == 3 || $this->group_id == 6) {
    		$topics = DB::table('topics')->get();
    		if (is_null($topics)) {
    			return response()->json(['result' => false, 'reason' => 'no topics to see!!']);
    		}
    		return response()->json($topics);
    	} else {
    		return response()->json(['result' => false, 'reason' => 'user do not have permission to see topics!!!']);
    	}
    	
    }

    public function showTopicAccept()
    {
		$topics = DB::table('topics')
			->where('is_accept', 1)
			->get();
		if (is_null($topics)) {
			return response()->json(['result' => false, 'reason' => 'no topics accept!!']);
		}
		return response()->json($topics);
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), 
			[
				'title' => 'required|string|max:255',
				'categories' => 'required|string|max:255',
				'description' => 'required|string|max:255',
				'no_interns' => 'required|max:255',
                'timelimit' => 'required|smallInteger|max:3',
				'start' => 'required|string|max:60',
				'stop' => 'required|string|max:60',
				'employee_id' => 'required|max:1000'
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

			if($errors->has('employee_id')) {
				$returnArray = array('result' => false, 
					'message' => 'employee_id'
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
		$topics->emplyee_id = $request->employee_id;
		$topics->is_accept = 0;

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
        	->join('users', 'users.id = topics.emplyee_id')
        	->select('users.name', 'topics.title', 'topics.categories', 'topics.description', 'topics.no_interns', 'topics.timelimit', 'topics.start', 'topics.status', 'topics.stop', 'topics.emplyee_id');
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
				'no_interns' => 'required|max:255',
                'timelimit' => 'required|smallInteger|max:3',
				'start' => 'required|string|max:60',
				'stop' => 'required|string|max:60',
				'employee_id' => 'required|max:1000'
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

			if($errors->has('employee_id')) {
				$returnArray = array('result' => false, 
					'message' => 'employee_id'
				);
				return response()->json($returnArray);
			};
		}

		$topics = Topic::find($id);

		if (is_null($topics)) {
			return response()->json(['result' => false, 'reason' => 'id not found!!']);
		}

		$topics->title = $request->title;
		$topics->categories = $request->categories;
		$topics->description = $request->description;
		$topics->no_interns = $request->no_interns;
		$topics->timelimit = $request->timelimit;
		$topics->status = $request->status;
		$topics->stop = $request->stop;
		$topics->emplyee_id = $request->employee_id;
		$topics->is_accept = 0;

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
