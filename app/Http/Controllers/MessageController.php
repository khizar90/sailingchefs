<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class MessageController extends Controller
{
    public function sendMessage(Request $request){
        $obj = new \stdClass();

        $validator = Validator::make($request->all(), [
            'from' => 'required|exists:users,id',
            'to' =>  'required|exists:users,id',
            'type' => 'required',
            'message' => 'required',
            'recipe_id' => 'exists:recipes,id',
            'attachment' => 'mimes:jpeg,jpg,png,gif|max:10000',

        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => 'Send message failed',
                'errors' => $validator->errors()->all()
            ]);
        }
        else{

            $message = new Message;
            $message->from_id = $request->from;
            $message->to_id = $request->to;
            $message->type = $request->type;
            $message->message = $request->message;
            if ($request->has('attachment')) {

                $image = $request->file('attachment');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $path = $request->file('attachment')->storeAs('public/message', $filename);
                $imagePath = str_replace("public/", "", $path);
                $message->attachment = $imagePath;

            }
            if ($request->has('recipe_id')) {
                $message->recipe_id = $request->recipe_id;
               
            }

            $message->save();
            
            $message = DB::table('messages')
            ->where('from_id', $request->from)
            ->where('to_id', $request->to)
            ->orderBy('created_at', 'desc')
            ->first();

            $sender = DB::table('users')->select('id','full_name','image')->where('id', $request->from)->first();
            $reciver = DB::table('users')->select('id','full_name','image')->where('id',$request->to)->first();
            if ($message->recipe_id != null) {
                $recipe = DB::table('recipes')->select('id','title','image')->where('id',$request->recipe_id)->first();
            }
            else{
                $recipe = new \stdClass();
            }

            $message->sender = $sender;
            $message->reciver = $reciver;
            $message->recipe = $recipe;

            return response()->json([
                'status' => true,
                'action' => 'Message send',

                'data' => $message,
            ]);

            
        }
    }

    public function inbox($userId){

        $user = User::find($userId);
        if($user){
            $users = User::whereIn('id', function ($query) use ($userId) {
                        $query->select(DB::raw('IF(from_id = '.$userId.', to_id, from_id) as user_id'))
                              ->from('messages')
                              ->where('from_id', $userId)
                              ->orWhere('to_id', $userId)
                              ->groupBy('user_id');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get(['id', 'full_name', 'image']);

                    return response()->json([
                        'status' => true,
                        'data' => $users
                    ]);
        }
        else{
            return response()->json([
            'status' => false,
            'error' => 'Invalid user',
        ]);
        }
    }


    public function conversation(Request $request) {
        $validator = Validator::make($request->all(), [
            'from' => 'required|exists:users,id',
            'to' =>  'required|exists:users,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => 'Send message failed',
                'errors' => $validator->errors()->all()
            ]);
        } else {
            $conversations = Message::where('from_id', $request->from)
                ->orWhere('to_id', $request->to)
                ->orderBy('created_at', 'desc')
                ->get();
    
            foreach ($conversations as $conversation) {
                $sender = DB::table('users')
                    ->select('id', 'full_name', 'image')
                    ->where('id', $conversation->from_id)
                    ->first();
                $conversation->sender = $sender;
    
                $receiver = DB::table('users')
                    ->select('id', 'full_name', 'image')
                    ->where('id', $conversation->to_id)
                    ->first();
                $conversation->receiver = $receiver;
    
                if ($conversation->recipe_id != null) {
                    $recipe = DB::table('recipes')
                        ->select('id', 'title', 'image')
                        ->where('id', $conversation->recipe_id)
                        ->first();
                } else {
                    $recipe = new \stdClass();
                }
    
                $conversation->recipe = $recipe;
            }
    
            return response()->json([
                'status' => true,
                'conversations' => $conversations
            ]);
        }
    }


    public function readMessage(Request $request,$userId){
        $messages = Message::where('from_id', $request->from_id)->where('to_id', $userId)->get();

        foreach($messages as $message){
            if(!$message->is_read){
                $message->update([
                    'is_read' => true
                ]);
            }
        }
        
        return 'messages read';
    }
    
}
