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
           $inbox =  DB::table('messages')
           ->select('messages.from_id', 'messages.to_id', 'messages.message', 'messages.type','messages.attachment','messages.is_read')
           ->join('users', function ($join) use ($userId) {
               $join->on('users.id', '=', 'messages.from_id')
                    ->orWhere('users.id', '=', 'messages.to_id')
                    ->where('users.id', '<>', $userId);
           })
           ->whereIn(DB::raw('(LEAST(from_id, to_id), GREATEST(from_id, to_id), messages.id)'), function($query) {
               $query->select(DB::raw('LEAST(from_id, to_id) as a, GREATEST(from_id, to_id) as b, MAX(messages.id) as max_id'))
                     ->from('messages')
                     ->groupBy('a', 'b');
           })
           ->where('messages.delete_by', '<>', $userId)
           ->orderBy('messages.id', 'desc')
           ->get()
           ->groupBy(function($item) {
               return $item->from_id < $item->to_id ? $item->from_id.'_'.$item->to_id : $item->to_id.'_'.$item->from_id;
           })
           ->map(function($grouped) use ($userId) {
               $latest = $grouped->first();
               $other_user_id = $latest->from_id == $userId ? $latest->to_id : $latest->from_id;
               $latest->other_user = DB::table('users')->select('id','full_name','image')->where('id', $other_user_id)->first();
               return $latest;
           })
           ->sortByDesc('created_at')
           ->values();

           return response()->json([
            'status' => true,
            'data' => $inbox
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
            $conversations = Message::where(function ($query) use ($request) {
                $query->where('from_id', $request->from)
                    ->where('to_id', $request->to)
                    ->where(function ($subquery) use ($request) {
                        $subquery->whereNull('delete_by')
                            ->orWhere('delete_by', '!=', $request->from);
                    });
            })
            ->orWhere(function ($query) use ($request) {
                $query->where('from_id', $request->to)
                    ->where('to_id', $request->from)
                    ->where(function ($subquery) use ($request) {
                        $subquery->whereNull('delete_by')
                            ->orWhere('delete_by', '!=', $request->from);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
           
            // $conversations = Message::where('from_id', $request->from)
            //     ->where('to_id', $request->to)
            //     ->orWhere('from_id',$request->to)
            //     ->where('to_id',$request->from)
            //     ->orderBy('created_at', 'desc')
            //     ->get();
    
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
        $user = User::find($userId);
        if($user){
            $messages = Message::where('from_id', $request->from_id)->where('to_id', $userId)->where('is_read', 0)->update([
                'is_read' => true
            ]);

        // foreach($messages as $message){
        //     if(!$message->is_read){
        //         $message->update([
        //             'is_read' => true
        //         ]);
        //     }
        // }
        
        return response()->json([
            'status' => true,
            'action' => 'Message read'
        ]);
        }
        else{
            return response()->json([
                'status' => false,
                'action' => 'Invalid user'
            ]);
        }
        
    }

    public function messageCount($userId){
        $obj = new \stdClass();

        $user = User::find($userId);
        if($user){
            $count = Message::where('to_id',$userId)->where('is_read',0)->where('delete_by','<>',$userId)->count();
            return response()->json([
                'status' => true,
                'action' => 'Messages',
                'count'  => $count
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'action' => 'Invalid user'
            ]);
        }

    }

    public function deleteMessage($userId,$msgId){
        $user = User::find($userId);
        if($user){
            $msg = Message::find($msgId);
            if($msg){
                if($msg->delete_by == 0){
                    $msg->delete_by = $userId;
                    $msg->save();
                }
                else{
                    $msg->delete();

                }
                
                return response()->json([
                    'status' => true,
                    'action' => 'Message deleted'
                ]);
            }
            else{
                return response()->json([
                    'status' => false,
                    'action' => 'Invalid message'
                ]);
            }
        }
        else{
            return response()->json([
                'status' => false,
                'action' => 'Invalid user'
            ]);
        } 
    }
    public function deleteConversation(Request $request){
        $messages = Message::where('from_id',$request->from)->where('to_id',$request->to)
        ->orWhere('from_id',$request->to)->where('to_id',$request->from)->get();
        foreach ($messages as $message) {
            if($message->delete_by == 0){
                $message->delete_by = $request->from;
                $message->save();
            }
            else{
                $message->delete();
            }
            
        }
        return response()->json([
            'status' => true,
            'action' => 'Conversation deleted'
        ]);
    }

    
}




// DB::table('messages')
//             ->select('from_id', 'to_id', 'message')
//             ->where(function($query) use ($user_id) {
//                 $query->where('from_id', $user_id)
//                       ->orWhere('to_id', $user_id);
//             })
//             ->whereIn(DB::raw('(LEAST(from_id, to_id), GREATEST(from_id, to_id), id)'), function($query) {
//                 $query->select(DB::raw('LEAST(from_id, to_id) as a, GREATEST(from_id, to_id) as b, MAX(id) as max_id'))
//                       ->from('messages')
//                       ->groupBy('a', 'b');
//             })
//             ->orderBy('id', 'desc')
//             ->get();

