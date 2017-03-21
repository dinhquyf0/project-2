<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Http\Controllers\Controller;

use Validator;
use DB;
use App\Permission; 
use App\Group; 
use Redis;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth; 

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		//get the group information and controller id they owned from the mysql database
        $permissions = DB::select("select groups.name as groupName,
        	groups.id as groupId,
        	controllers.id as controllerId
        	from groups
        	left join permissions on permissions.group_id = groups.id
        	left join controllers on controllers.id = permissions.controller_id"
        	);
		
		//initialize the blank array to store permissions
		$permissionArray = array();
		
		foreach($permissions as $permission){
			//each group is the head of an sub array, with storing its group name and group id
			$permissionArray[$permission->groupName]['groupName'] =	$permission->groupName;
			$permissionArray[$permission->groupName]['groupId'] = (int)$permission->groupId;
			
			//if the group has an array permission, add new element to its array
			if($permission->controllerId != NULL){
				$permissionArray[$permission->groupName]['controllerId'][] = $permission->controllerId;
			}
			
			//if group has not had controllerid, initialize a new blank array
			else{
				$permissionArray[$permission->groupName]['controllerId'] = array();
			}
		}
		
		//get the controller and its id
		$controllerArray = array();
		$controllers = DB::select("select * from controllers");
		foreach($controllers as $controller){
			$controllerArray[] = array("controllerName" => $controller->name, 
				"controllerId" => $controller->id
			);
		}
		
		//combine 2 arrays
		$returnArray = array("permissions" => array_values($permissionArray),
			"controllers" => array_values($controllers));
		return response()->json($returnArray);		
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($groupid , $controllerid)
    {
        if (!is_int((int)$groupid)) {
            return response()->json(['result' => false, 'message' => 'groupid must be integer!']);
        }
        if ((int)$groupid > 100 || (int)$groupid < 0) {
            return response()->json(['result' => false, 'message' => 'groupid is not accept!']);
        }

        if (!is_int((int)$controllerid)) {
            return response()->json(['result' => false, 'message' => 'controllerid must be integer!']);
        }
        if ((int)$controllerid > 100 || (int)$controllerid < 0) {
            return response()->json(['result' => false, 'message' => 'controllerid is not accept!']);
        }
		//check if the requested groupid is existed or not
		$group = Group::find((int)$groupid);
		//$groupIdFromDB	=	DB::select("select id from groups where id = ?", [(int)$groupid]);
		
		//if it's not existed, terminate the request
		if($group == null){
			$returnArray = array("result" => false);
			return response()->json($returnArray , 400);
		}
		
		//check if the requested controllerid is existed or not
		$controllerIdFromDB = DB::select("select id from controllers where id = ?", [(int)$controllerid]);
		
		//if it's not existed, terminate the request
		if(count($controllerIdFromDB) != 1){
			$returnArray = array("result" => false);
			return response()->json($returnArray, 400);
		}
		
		//in case of "quản trị hệ thống", terminate the program
		if(((int)$groupid) == 1){
			$returnArray = array("result" => false);
			return response()->json($returnArray, 400);
		}

		//check if the permission is existed or not
		$permissionsFromDB = DB::select("select id from permissions where group_id = ? and controller_id = ?", [(int)$groupid, (int)$controllerid]);

		//if it's existed, delete it
		if(count($permissionsFromDB) >= 1){
			//delete the requested permission with groupid and controllerid
			$check = DB::table('permissions')
			->where('group_id',(int)$groupid)
			->where('controller_id',(int)$controllerid)
			->delete();

			//check if delete statement was done successfully or not
			if(!$check){
				$returnArray = array("result" => false);
				return response()->json($returnArray);			
			}

            //change permission into REDIS
            // $redis = new Redis();
            // $redis->connect('localhost');
            // $redis->hDel('permission', $groupid);
            $insertdata = array();
            $permissions = DB::select("select groups.name as groupName, groups.id as groupid, controllers.id as controllerid
			from controllers, groups, permissions
			where groups.id = permissions.group_id 
			and permissions.controller_id = controllers.id 
			and groups.id = ?" , [$groupid]
            );

            foreach($permissions as $permission){
                $insertdata['groupId'] = (int)$permission->group_id;
                $insertdata['groupName'] = base64_encode($permission->groupName);
                $insertdata['controllerId'][] = $permission->controller_id;
            }
            // $redis->hSet('permission', $permission->groupid, json_encode($insertdata));

			$returnArray = array("result" => true);
			return response()->json($returnArray);
		}
		
		//else add it to database
		$permission	= new Permission;
		$permission->group_id = (int)$groupid;
		$permission->controller_id =	(int)$controllerid;
		$check = $permission->save();
		//check if permission is not save or can't
		if(!$check){
			$returnArray = array("result" => false);
			return response()->json($returnArray);			
		}

		// //change permission into REDIS
		// $redis = new Redis();
		// $redis->connect('localhost');
		// $redis->hDel('permission', $permission->groupid);
		$insertdata = array();
		$permissions = DB::select("select groups.name as groupName, groups.id as groupid, controllers.id as controllerid
			from controllers, groups, permissions
			where groups.id = permissions.group_id 
			and permissions.controller_id = controllers.id 
			and groups.id = ?" , [$groupid]
			);

		foreach($permissions as $permission){
			$insertdata['groupId'] = (int)$permission->groupid;
			$insertdata['groupName'] = base64_encode($permission->groupName);
			$insertdata['controllerId'][] = $permission->controllerid;
		}
		// $redis->hSet('permission', $permission->groupid, json_encode($insertdata));
		
		$returnArray = array("result" => true);
		return response()->json($returnArray);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_private_permissions()
    {
		$user = JWTAuth::parseToken()->authenticate();
		if (!$user) {
			return response()->json(['result' => false, 'message' => 'no data']);
		}
		// $redis = new Redis();
		// $check = $redis->connect('localhost');

		// if (!$check) {
		// 	$this->getPermissionSQL($user->groupid);
		// }
        // $get = $redis->hMGet('permission', [$user->groupid]);
		// if($get[$user->groupid]) {
		// 	$result = array(json_decode($get[$user->groupid]));
		// 	$temps = (array)$result[0]->controllerId;
		// 	$result[0]->controllerId = array();
		// 	foreach ($temps as $value) {
  //               $result[0]->controllerId[] = (String) $value;
  //           }

		// 	$result[0]->groupName = base64_decode($result[0]->groupName);
	 //    	return response()->json(array_values($result));
  //   	}else{
    		$permissions = DB::select("select groups.name as groupName, groups.id as groupId, controllers.id as controllerId 
    			from controllers, groups, permissions 
    			where groups.id = permissions.group_id 
    			and permissions.controller_id = controllers.id 
    			and groups.id = ?", [$user->groupid]
    			);
			$returnArray = array();
			foreach($permissions as $permission){
				$returnArray[$permission->groupName]['groupName'] =	$permission->groupName;
				$returnArray[$permission->groupName]['groupId'] = (int)$permission->groupId;
				$returnArray[$permission->groupName]['controllerId'][] = (String)$permission->controllerId;
				$data = $returnArray[$permission->groupName];
				$data['groupName'] = base64_encode($data['groupName']);
				$redis->hSet('permission', $permission->groupId,json_encode($data));
			}
			return response()->json(array_values($returnArray));
		// }	
	}

	public function getPermissionSQL($groupid)
	{
        if (!is_int((int)$groupid)) {
            return response()->json(['result' => false, 'message' => 'groupid must be integer!']);
        }
        if ((int)$groupid > 100 || (int)$groupid < 0) {
            return response()->json(['result' => false, 'message' => 'groupid is not accept!']);
        }

		$permissions = DB::select("select groups.name as groupName, groups.id as groupId, 
			controllers.id as controllerId 
			from controllers, groups, permissions 
			where groups.id = permissions.groupid 
			and permissions.controllerid = controllers.id 
			and groups.id = ?", [$groupid]
        );
        $returnArray = array();
        foreach($permissions as $permission){
            $returnArray[$permission->groupName]['groupName'] =	$permission->groupName;
            $returnArray[$permission->groupName]['groupId']	= (int)$permission->groupId;
            $returnArray[$permission->groupName]['controllerId'][] = (String)$permission->controllerId;
        }
        return response()->json(array_values($returnArray));
	}
}
