<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use Validator;
use App\Group; 

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = DB::select("select * from groups");
		return response()->json($groups);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
		//validate the input variable from the post request
		$validator = Validator::make($request->all(), 
			[
				'name' => 'required|max:255',
				'description' => 'required',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {
			$returnArray = array("result" => false);
			return response()->json($returnArray);
		}

		//create a new group object
		$group = new Group;
		
		//assign each input with its respective value in the object
		$group->name = $request->name;
		$group->description = $request->description;
		
		//save the object's value into the database
		$saved = $group->save();
		if(!$saved){
			$returnArray = array("result" => false);
			return response()->json($returnArray);			
		}
		//add new group to redis
		$redis = new Redis();
   		$redis->connect('localhost', 6379);

		$groupid = DB::select("select * from groups where name = ? ", [$request->name]);
		$data = array();
		$data['groupId'] = $groupid[0]->id;
		$data['groupName'] =  base64_encode($groupid[0]->name);
		$data['controllerId'] = array();
		$redis->hSet('permission', $data['groupId'] , json_encode($data));


		//return the true array so client could know the program is done.
		$returnArray = array("result" => true);
		return response()->json($returnArray);	
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ((int)$id > 10000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }
        $group = DB::select('select * from groups where id = ?', [$id]);
        if (count($group) == 0) {
        	return response()->json(['result' => fasle, 'message' => 'id not exist!!']);
        }
		return response()->json($group[0]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    	//validate the input variable from the post request
		$validator = Validator::make($request->all(), 
			[
				'name' => 'required|max:255',
				'description' => 'required',
			]
		);
		
		//if the validation fails, terminate the program
		if ($validator->fails()) {
			$returnArray = array("result" => false);
			return response()->json($returnArray);
		}
        if (!is_int($id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ($id > 10000 || $id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }
		$group = Group::find($id);
		if($group == null){
			$returnArray = array("result" => false,
				"reason" => "couldn't find the id"
				);
			return response()->json($returnArray, 400);
		}
		//assign each input with its respective value in the object
		$group->name = $request->name;
		$group->description = $request->description;

		//save the object's value into the database
		$saved = $group->save();
		//check if save false return result false
		if(!$saved){
			$returnArray = array("result" => false);
			return response()->json($returnArray);			
		}

		//update data to redis
		$redis = new Redis();
   		$redis->connect('localhost', 6379);
   		$redis->hDel('permission',$id);
		$groupid = DB::select("select * from groups where id = ? ", [$id]);
		$data = array();
		$data['groupId'] = $groupid[0]->id;
		$data['groupName'] = base64_encode($groupid[0]->name);
		$data['controllerId'] = array();

		$list_permission = DB::select("select controllerid from permissions where groupid = ? ", [$id]);
		if (!$list_permission) {
			return response()->json(['result' => false]);
		}
		foreach ($list_permission as $permission) {
			array_push($data['controllerId'], $permission->controllerid);
		}
		$redis->hSet('permission', $id , json_encode($data));
		//return the true array so client could know the program is done.
		$returnArray = array("result" => true);
		return response()->json($returnArray);	       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ((int)$id > 10000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }
		//default group ids => they could not be deleted
		if(((int)$id ==	1) || ((int)$id	== 2) || ((int)$id == 3)||(int)$id == 13){
			$returnArray = array("result" => false,
				"reason" => "Group couldn't be deleted"
			);
			return response()->json($returnArray, 403);			
		}
		
		//find the group by id
		$data =	Group::find($id);
		
		//delete the group if it is found
		if($data != null){
			$data->delete();
		}
		//terminate the request if group could not be found, 
		else{
			$returnArray = array("result" => false,
				"reason" => "couldn't find the id"
			);
			return response()->json($returnArray, 400);
		}
		
		$redis = new Redis();
   		$redis->connect('localhost', 6379);
   		$redis->hDel('permission',$id);

		$returnArray = array("result" => true);
		return response()->json($returnArray);	
    }

    public function createController(Request $request){
    	DB::insert('INSERT INTO controllers (name, description) value(?, ?) ', [$request->name, $request->description]);
    }
}
