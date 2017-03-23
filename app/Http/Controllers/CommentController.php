<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use Validator;

use App\User;
use App\Comment;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentController extends Controller
{
    public function __construct()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $this->user_id = $user['id'];
        $this->group_id = $user['groupid'];
    }
    public function create(Request $request){
        //validate the input variable from the post request
        $validator = Validator::make($request->all(),
            [
                'comment' => 'required|string|max:255',
                'place' => 'required|numeric|max:5',
            ]
        );

        //if the validation fails, terminate the program
        if ($validator->fails()) {
            $return_array = array('result' => false, 'message' => 'validate fails!!!');
            return response()->json($return_array);
        }
        //check user exist or not in mySql
        $user = User::find($this->user_id);
        if (is_null($user)) {
            return response()->json(['result' => false, 'message' => 'user does not exist!']);
        }

        //create a new comment object
        $comment = new Comment;

        //assign each input with its respective value in the object
        $comment->userid = $this->user_id;
        $comment->comment = $request->comment;
        $comment->place = $request->place;

        //save the object's value into the database
        $saved = $comment->save();
        if(!$saved){
            $return_array = array('result' => false);
            return response()->json($return_array);
        }
        //return the true array so client could know the program is done.
        $return_array = array('result' => true);
        return response()->json($return_array);
    }

    public function show($id){

        $comments = DB::table('comments')
            ->where('place', $id)
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->select('users.firstname','users.lastname',  'users.avatar',
                'comments.id', 'comments.user_id',
                'comments.comment', 'comments.place', 'comments.updated_at')
            ->get();

        return $comments;
    }

    public function update(Request $request, $id){
        //check userid is number or not
        if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        //check if userid is too big
        if ((int)$id > 1000000000000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!']);
        }

        //validate the input variable from the post request
        $validator = Validator::make($request->all(),
            [
                'comment' => 'required|string|max:255',
            ]
        );
        //if the validation fails, terminate the program
        if ($validator->fails()) {
            $returnArray = array('result' => false);
            return response()->json($returnArray);
        }

        $comment = Comment::find($id);
        //check if url is exist or not
        if (is_null($comment)) {
            return response()->json(['result' => false, 'message' => 'id not found!']);
        }

        $user = User::find($this->user_id);
        //check user is exit or not
        if (is_null($user)) {
            return response()->json(['result' => false, 'message' => 'userid not found!']);
        }

        if ($comment->user_id != $this->user_id){
            return response()->json(['result' => false, 'message' => 'comment not belong to this user']);
        }
        //assign new comment
        $comment->comment = $request->comment;
        //save and check save sequences
        $check = $comment->save();
        if (!$check) {
            return response()->json(['result' => false, 'message' => 'fails to save!']);
        }
        return response()->json(['result' => true]);
    }

    public function delete($id){
        if (!is_int((int)$id)) {
            return response()->json(['result' => false, 'message' => 'id must be integer!']);
        }
        if ((int)$id > 10000000000000 || (int)$id < 0) {
            return response()->json(['result' => false, 'message' => 'id is not accept!!!']);
        }

        //find the group by id
        $data = Comment::find($id);
        if ($data->user_id != $this->user_id){
            return response()->json(['result' => false, 'message' => 'comment not belong to this user']);
        }
        //delete the group if it is found
        if($data != null){
            $data->delete();
        }
        //terminate the request if group could not be found,
        else{

            $returnArray = array('result' => false,
                'reason' => 'couldn\'t find the id'
            );
            return response()->json($returnArray, 400);
        }

        $returnArray = array('result' => true);
        return response()->json($returnArray);
    }
}
