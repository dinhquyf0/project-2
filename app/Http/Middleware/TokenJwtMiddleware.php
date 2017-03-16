<?php

namespace App\Http\Middleware;

use App\User; 
use DB;
use Closure;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth; 

class TokenJwtMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
		//get users from the requested token
		try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        }
		catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        }
		catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        }
		catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
		
		//get controller from route
		$currentAction = \Route::currentRouteAction();
		
		//explode the route above into method and controller
		list($controller, $method) = explode('@', $currentAction);

		//replace the redundant information
		$controller = preg_replace('/.*\\\/', '', $controller);	
		
		//check if the requested controller and user's group id are matched
		$permissionID	=	DB::select("select permissions.id from permissions, controllers 
										where permissions.groupid = ?
										and permissions.controllerid = controllers.id
										and controllers.name = ?" 
										,	[(int)$user->groupid , $controller]
									); 
		//if nothing found, terminate the request
		if($permissionID == NULL){
			$returnArray	=	array(	"result"	=>	false,
										"reason"	=>	"Permission denied"
										);
			return response()->json($returnArray , 400);			
		}
		
        return $next($request);
    }
}
