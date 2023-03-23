<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;
use App\Models\Recipe;
use App\Models\Review;
use App\Models\Notification;


use Illuminate\Support\Facades\Log;
use App\Models\Blocklistt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Session;
use Twilio\Exceptions\RestException;


class UserController extends Controller
{
 



    ///////////// REGISTER ////////////////


    public function register(Request $request){


        $obj = new \stdClass();
        $validator = Validator::make($request->all(),
            [
                'platform'=> 'required'
                
            ]);
            if ($validator->fails())
            { 
                 return response()->json([
                 'status' => false,
                 'action' => "Register failed",
                 'data' => $obj,
                 'errors' => $validator->errors()->all()
             ]);
            }
            if($request->platform == 'normal'){
                $validator = Validator::make($request->all(),
                [
                    'email'=> 'required|email|unique:users,email',
                    'type'=> 'required',
                    'password'=>'required',
                    'platform'=> 'required',
                    'full_name'=>'required'
                ]);
                if ($validator->fails())
                { 
                     return response()->json([
                     'status' => false,
                     'action' => "Register failed",
                     'data' => $obj,
                     'error' => $validator->errors()->all()
                 ]);
                }
                else{
    
    
                    $user = new User();
                    $user->full_name= $request['full_name'];
                    $user->email = $request['email'];
                    $user->password = \Hash::make($request['password']);
                    $user->platform = $request['platform'];
                    $user->type = $request['type'];
        
                    $user->save(); 
                    $obj1 = User::where('email',$request->email)->first();
                    
                    return response()->json([
                        'status' => true,
                        'action' => "Register successfully",
                        'data' => $obj1,
                        'error' => []
                    ]);  
                    
                }    
            }
            elseif($request->platform == 'apple' || $request->platform == 'google'){
                $validator = Validator::make($request->all(),
                [
                    'email'=> 'required|email|unique:users,email',
                    'type'=> 'required',
                    'platform'=> 'required',
                    'full_name'=>'required'
                ]);
    
                if ($validator->fails())
                {
                   
                    return response()->json([
                    'status' => false,
                    'action' => "Register failed",
                    'data' => $obj,
                    'error' => $validator->errors()->all()
                 ]);
                }
                else{
    
                    $user = new User();
                    $user->full_name= $request['full_name'];
                    $user->email = $request['email'];
                    $user->platform = $request['platform'];
                    $user->type = $request['type'];
                    $user->save(); 
                    $obj1 = User::where('email',$request->email)->first();
    
                    return response()->json([
                        'status' => true,
                        'action' => "Register successfully",
                        'data' => $obj1,
                        'error' => []
                    ]); 
                }
    
            }
        }



    ///////////// LOGIN ////////////////


    public function login(Request $request){

        $obj = new \stdClass(); 
        $validator = Validator::make($request->all(),
            
            [
                'platform'=> 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'action' => "Login failed",
                    'data' => $obj,
                    'error' => $validator->errors()->all()
                ]);
            }

            else{

                if($request->platform == 'google' || $request->platform == 'apple') {
                    $validator = Validator::make($request->all(),
                    
                    [
                        'email'=> 'required|email',
                    ]);
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'action' => "Login failed",
                            'data' => $obj,
                            'error' => $validator->errors()->all()
                         ]);
                    }
                    else{
                        $user=User::where('email',$request->email)->where('platform',$request->platform)->first();
                        if($user){
                            return response()->json([
                                'status' => true,
                                'action' => "Login successfully",
                                'data' => $user,
                                'error' => []
                            ]);  
                        }else{
             
                            $obj1 = new \stdClass();    
                            $obj1->user = ["User not found"];
                            return response()->json([
                                'status' => false,
                                'action' => "Login failed",
                                'data' => $obj,
                                'error' => ['User not found']
                            ]);
                        }
                     
                    }
                }
                elseif($request->platform == 'normal')
                {
                    $validator = Validator::make($request->all(),
                    
                    [
                        'email'=> 'required|email',
                        'password'=>'required'
                    ]);
        
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'action' => "Login failed",
                            'data' => $obj,
                            'error' => $validator->errors()->all()
                         ]);
                    }
                    else{
                        $user=User::where('email',$request->email)->where('platform',$request->platform)->first();
                        if($user){
        
                            if(\Hash::check($request->password,$user->password)){
                                return response()->json([
                                    'status' => true,
                                    'action' => "Login successfully",
                                    'data' => $user,
                                    'error' => []
                                 ]);
                            }
                            else{
                            
                              
                                return response()->json([
                                    'status' => false,
                                    'action' => "Login failed",
                                    'data' => $obj,
                                    'error' => ['Wrong password']
                                 ]);
                            }
                          
                        }else{
             
                            $obj1 = new \stdClass();    
                            $obj1->user = ["User not found"];
                            return response()->json([
                                'status' => false,
                                'action' => "Login failed",
                                'data' => $obj,
                                'error' => ['User not found']
                            ]);
                        }
                     
                    }
                
                }       
            }      
    }

    ///////////// RESET ACCOUNT VERIFY ////////////////



    public function resetAccountVerify(Request $request){
        $obj = new \stdClass();    

        $validator = Validator::make($request->all(), 
        [
           'email'=> 'required|email',
        ]);
        if ($validator->fails()) {
           return response()->json([
               'status' => false,
               'action' => "Account verify failed",
               'data' => $obj,
               'error' => $validator->errors()->all()
            ]);
        }
        else{
            $user = User::where('email',$request->email)->first();
            if($user){
                $obj->code = 123456;
                return response()->json([
                    'status' => true,
                    'action' => "Account verify",
                    'data' => $obj,
                    'error' => $validator->errors()->all()
                 ]);
            }
            else{
                return response()->json([
                    'status' => false,
                    'action' => "Account not verify",
                    'data' => $obj,
                    'error' => ['Account not found']
                 ]);
            }
        }
    }

    ///////////// RESET PASSWORD ////////////////



    public function resetPassword(Request $request){
        $obj = new \stdClass();

        $validator = Validator::make($request->all(), 
        [
           'email'=> 'required | email',
           'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => "New password not set",
                'data' => $obj,
                'error' => $validator->errors()->all()
             ]);
        }

        $user=User::where('email',$request->email)->first();

        if($user){

                $user->update([
                    'password' => \Hash::make($request->password)
                ]);
                return response()->json([
                    'status' => true,
                    'action' => 'New password set',
                    'data' => $obj,
                    'error' => []
                 ]);
            
        }
    }



    ///////////// CHANGE PASSWORD ////////////////


    Public function chnagePassword(Request $request){
        $obj = new \stdClass();

        $validator = Validator::make($request->all(), 
        [
           'email'=> 'required | email',
           'oldPassword' => 'required',
           'newPassword' => 'required'

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => "Password not change",
                'data' => $obj,
                'error' => $validator->errors()->all()
             ]);
        }
        else{
            $user=User::where('email',$request->email)->first();
            if($user){ 
                if(\Hash::check($request->oldPassword,$user->password)){

                    if(\Hash::check($request->newPassword,$user->password))
                    {
                        return response()->json([
                            'status' => true,
                            'action' => "Password not change",
                            'data' => $obj,
                            'error' =>['New password is same as old password']
                        ]);
                    }
                    else{
                        $user->update([
                            'password' => \Hash::make($request->newPassword)
                        ]);
                    }
                   
                    return response()->json([
                        'status' => true,
                        'action' => "Password change",
                        'data' => $obj,
                        'error' => []
                    ]);
                
                }else{
                    return response()->json([
                        'status' => true,
                        'action' => "Password not change",
                        'data' => $obj,
                        'error' =>['Old Password is inccorect']
                    ]);
                }             
            }
            
        }
    }


    ///////////// DELETE ACCOUNT ////////////////


    public function deleteAccount($userId){
        $obj = new \stdClass();
        
    
            $user=User::find($userId);
            if($user){
                $user->followers()->delete();
                $user->followings()->delete();
                $user->blockedBy()->delete();
                $user->blockedUsers()->delete();
                $user->notificationsReceived()->delete();
                $user->notificationsSent()->delete();
                $user->recipe()->delete();
                $user->sender()->delete();
                $user->reciver()->delete();
                $user->reviews()->delete();

                

                $user->delete();
                
                return response()->json([
                    'status' => true,
                    'action' => "Account  delete",
                    'data' => $obj,
                    'error' => []
                ]);
            }
            else{
                return response()->json([
                    'status' => false,
                    'action' => "Account not  delete",
                    'data' => $obj,
                    'error' => ['No account found']
                ]);
            }
           
    
    }

    ///////////// EDIT PROFILE ////////////////

    public function editProfile(Request $request){
        $obj = new \stdClass();
        $user = User::find($request->id);
        
        if($user){
            $validator = Validator::make($request->all(),[ 
                'image' => 'mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);   
    
            if($validator->fails()) {          
                return response()->json([
                    'status' => true,
                    'action' => "Data updated",
                    'data' => $obj,
                    'error' => $validator->errors()->all()
                ]);                        
            }  
    
          
    
            if ($request->has('image')) {

                $image = $request->file('image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $path = $request->file('image')->storeAs('public/file', $filename);
                $imagePath = str_replace("public/", "", $path);

                $user->update([
                    'image' => $imagePath
                ]);
            }
        
            if ($request->has('full_name')) {
                $user->update([
                    'full_name' => $request->full_name
                ]);
            }
        
            if ($request->has('bio')) {
                $user->update([
                    'bio' => $request->bio
                ]);
            }
            
            if ($request->has('boat_name')) {
                $user->update([
                    'boat_name' => $request->boat_name
                ]);
            }
            if ($request->has('location')) {
                $user->update([
                    'Location' => $request->location
                ]);
            }   
            if ($request->has('type')) {
                $user->update([
                    'type' => $request->type
                ]);
            }  
              
                
              return response()->json([
                'status' => true,
                'action' => "Data updated",
                'data' => $user,
                'error' => []
            ]);
    
            
        }
        else{
            return response()->json([
                'status' => false,
                'action' => "Data updated fialed",
                'data' => $obj,
                'error' => ['Account not found']
            ]);
        }

    }


    ///////////// SEARCH USER ////////////////


    public function search(Request $request,$userId)
    {
        $searchQuery = $request->name;
        $users = [];
        if ($searchQuery) {

            $users = User::select('id', 'full_name','platform','type','location')
                ->where('full_name', 'like', "%$searchQuery%")
                ->whereNotIn('id', [$userId])
                ->whereNotIn('id', function($query) use ($userId) {
                    $query->select('to_user_id')
                          ->from('blocklistts')
                          ->where('id', $userId);
                })
                ->whereNotIn('id', function($query) use ($userId) {
                    $query->select('to_user_id')
                          ->from('blocklistts')
                          ->where('from_user_id', $userId);
                })
                ->whereNotIn('id', function($query) use ($userId) {
                    $query->select('from_user_id')
                          ->from('blocklistts')
                          ->where('to_user_id', $userId);
                })
                ->get();
        }
    
        $usersWithFollowInfo = [];
    
        foreach ($users as $user) {
            $followed = Follow::where('from_user_id', $userId)
                ->where('to_user_id', $user->id)
                ->exists();
            $user->followed = $followed;
    
            $usersWithFollowInfo[] = $user;
        }
    
    
        
            return response()->json([
                'status' => true,
                'action' => 'Users',
                'data' => $usersWithFollowInfo,
                'error' => []
            ]);
            



    }



    ///////////// BLOCK USER ////////////////

    public function blockUser(Request $request){
        $obj = new \stdClass();
        $validator = Validator::make($request->all(), [
            'from_id' => 'required|exists:users,id',
            'to_id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => 'Follow failed',
                'errors' => $validator->errors()
            ]);
        }
        else{
            $user = Blocklistt::where('from_user_id',$request->from_id)->where('to_user_id',$request->to_id)->first();
        if($user){
            $user->delete();
            return response()->json([
                'status' => true,
                'action' => 'User unblock',
                'data' => $obj,
                'error' => []
            ]);
        }
        else{ 
            $user = new Blocklistt;
            $user->from_user_id= $request['from_id'];
            $user->to_user_id = $request['to_id'];
            $user->save();
        
            return response()->json([
                'status' => true,
                'action' => 'User block',
                'data' => $obj,
                'error' => []
            ]);
                    
        }

        }
    }

    ///////////// BLOCK LIST ////////////////


    public function blocklist($userId){
        
        $userverify = User::where('id',$userId);
        if($userverify->count() >0){
            $user = Blocklistt::where('from_user_id',$userId)->select('to_user_id')->get();
        $blockedUserIds = $user; 
        
        $blockedUsers = DB::table('blocklistts')
            ->select('users.id','users.full_name')
            ->whereIn('to_user_id', $blockedUserIds)
            ->where('from_user_id', $userId)
            ->join('users', 'users.id', '=', 'blocklistts.to_user_id')
            ->get();
       
        
           if($blockedUsers){
            return response()->json([
                'status' => true,
                'action' => 'Blocklist',
                'data' => $blockedUsers,
                'error' => []
            ]);
        }
        }
        else{
            return response()->json([
                'status' => false,
                'action' => 'user not found',
            ]);   
        }
    }


    ///////////// FOLLOW USER ////////////////


    public function followUser(Request $request){
        $obj = new \stdClass();
        $validator = Validator::make($request->all(), [
            'from_id' => 'required|exists:users,id',
            'to_id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => 'Follow failed',
                'errors' => $validator->errors()
            ]);
        }
        else{
            $user = Follow::where('from_user_id',$request->from_id)->where('to_user_id',$request->to_id)->first();
        if($user){
            $user->delete();
            return response()->json([
                'status' => true,
                'action' => 'You unfollow',
                'data' => $obj,
                'error' => []
            ]);
        }
        else{ 
            $user = new Follow;
            $user->from_user_id= $request['from_id'];
            $user->to_user_id = $request['to_id'];
            $user->save();

            
                $notification = new Notification();
                $notification->type = 'follow';
                $notification->user_id = $request->to_id;
                $notification->person_id = $request->from_id;
                $notification->body = 'start following to you';
                $notification->save();
        
            return response()->json([
                'status' => true,
                'action' => 'You follow',
                'data' => $obj,
                'error' => []
            ]);


                    
        }   
        }
    }

    ///////////// FOLLOWERS LISR ////////////////


    public function followersList(Request $request,$userId){
        
        $followerIds = Follow::where('to_user_id', $userId)
                          ->whereNotIn('from_user_id', function($query) use ($userId) {
                              $query->select('from_user_id')
                                    ->from('blocklistts')
                                    ->where('to_user_id', $userId);
                          })
                          ->pluck('from_user_id');

        $followers = User::select('id','full_name','image','location')->whereIn('id', $followerIds)
                      ->whereNotIn('id', function($query) use ($userId) {
                          $query->select('to_user_id')
                                ->from('blocklistts')
                                ->where('from_user_id', $userId);
                      })
                      ->get();
       
        
           if($followers){
            return response()->json([
                'status' => true,
                'action' => 'Followers List',
                'data' => $followers,
                'error' => []
            ]);
           }
    }

    ///////////// FOLLOWINGS LIST ////////////////


    public function followingsList($userId){
        
        $followingIds = Follow::where('from_user_id', $userId)
                           ->whereNotIn('to_user_id', function($query) use ($userId) {
                               $query->select('to_user_id')
                                     ->from('blocklistts')
                                     ->where('from_user_id', $userId);
                           })
                           ->pluck('to_user_id');

        $following = User::select('id','full_name','image','location')->whereIn('id', $followingIds)
                      ->whereNotIn('id', function($query) use ($userId) {
                          $query->select('from_user_id')
                                ->from('blocklistts')
                                ->where('to_user_id', $userId);
                      })
                      ->get();


        if($following){
            return response()->json([
                'status' => true,
                'action' => 'Followings List',
                'data' => $following,
                'error' => []
            ]);
        }
    }

    public function userRecipeList($userId){

        $recipe = Recipe::where('user_id',$userId)->get();

        if ($recipe->count() > 0) {
            return response()->json([
                'status' => true,
                'action' => 'Recipe List',
                'data' => $recipe,
                'error' => []
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'action' => 'Recipe List',
                'data' => $recipe,
                'error' => ['No recipe found']
            ]);
        }
        
    }

    
    
    public function sendOtp(Request $request)
    {
        $obj = new \stdClass();
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => 'Validation Error',
                'errors' => $validator->errors()
            ]);
        }
        else{
            $token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_sid = getenv("TWILIO_SID");
            $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
            $twilio = new Client($twilio_sid, $token);
    
            try {
                $twilio->verify->v2->services($twilio_verify_sid)
                ->verifications
                ->create($request->phone_number, "sms");
    
                return response()->json([
                    'status' => true,
                    'action' => 'Otp send',
                    'data' => $obj,
                    'error' => []
                ]);
            } catch (RestException $e) {
                return response()->json([
                    'status' => false,
                    'action' => 'Otp send fialed',
                    'data' => $obj,
                    'error' => [$e->getMessage()]
                ]);
            }
        }
        
    }

    

    public function verifyOtp(Request $request)
    {
        $obj = new \stdClass();

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'verification_code' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => 'Validation Error',
                'errors' => $validator->errors()
            ]);
        }
    
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
    
        $twilio = new Client($twilio_sid, $twilio_auth_token);
    
        try {
            $verification = $twilio->verify->v2->services($twilio_verify_sid)
                ->verificationChecks
                ->create([
                    'to' => $request->phone_number,
                    'code' => $request->verification_code
                ]);
    
        } catch (RestException $e) {
            return response()->json([
                'status' => false,
                'action' => 'Error verifying code',
                'data' => $obj,
                'error' => ['Time out']
            ]);
        }
    
        if ($verification->status == 'approved') {
            return response()->json([
                'status' => true,
                'action' => 'Verification successful',
                'data' => $obj,
                'error' =>[]
            ]);
        } else {
            return response()->json([
                'status' => true,
                'action' => 'Error verifying code',
                'data' => $obj,
                'error' => 'Inavlid code'
            ]);
        }
    
         
    }


    public function listChef(Request $request){
        $obj = new \stdClass();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => 'No chef found',
                'errors' => $validator->errors()->all()
            ]);
        }else{
            $currentUserId = $request->id;

            $blockedUsers = DB::table('blocklistts')
            ->select('to_user_id')
            ->where('from_user_id', '=', $currentUserId)
            ->get()
            ->pluck('to_user_id')
            ->toArray();

            
            $chefs = User::select('id', 'full_name', 'email','type')
            ->where('type', 'chef')
            ->whereNotIn('id', array_merge([$currentUserId], $blockedUsers))
            ->get();

            if($chefs->count() > 0){
                return response()->json([
                    'status' => true,
                    'action' => 'List of chefs',
                    'data'   =>  $chefs,
                    'errors' => []
             ]);
            }else{
                return response()->json([
                    'status' => true,
                    'action' => 'List of chefs',
                    'data'   =>  $chefs,
                    'errors' => $validator->errors()->all()
             ]);
            }

            
        
        }
    }

    public function profile(Request $request,$userId){


        $followingCount = Follow::where('from_user_id', $userId)
                         ->whereNotExists(function ($query) use ($userId) {
                             $query->select(DB::raw(1))
                                   ->from('blocklistts')
                                   ->where(function ($query) use ($userId) {
                                       $query->where('from_user_id', $userId)
                                             ->whereRaw('to_user_id = follows.to_user_id');
                                   })
                                   ->orWhere(function ($query) use ($userId) {
                                       $query->whereRaw('from_user_id = follows.from_user_id')
                                             ->whereRaw('to_user_id = ?', [$userId]);
                                   })
                                   ->orWhere(function ($query) use ($userId) {
                                       $query->whereRaw('from_user_id = ? and to_user_id = follows.from_user_id', [$userId]);
                                   })
                                   ->orWhere(function ($query) use ($userId) {
                                       $query->whereRaw('from_user_id = follows.to_user_id and to_user_id = ?', [$userId]);
                                   });
                         })
                         ->count();


        $followerCount = Follow::where('to_user_id', $userId)
                         ->whereNotExists(function ($query) use ($userId) {
                             $query->select(DB::raw(1))
                                   ->from('blocklistts')
                                   ->where(function ($query) use ($userId) {
                                       $query->where('from_user_id', $userId)
                                             ->whereRaw('to_user_id = follows.from_user_id');
                                   })
                                   ->orWhere(function ($query) use ($userId) {
                                       $query->whereRaw('from_user_id = follows.to_user_id')
                                             ->whereRaw('to_user_id = ?', [$userId]);
                                   })
                                   ->orWhere(function ($query) use ($userId) {
                                       $query->whereRaw('from_user_id = ? and to_user_id = follows.from_user_id', [$userId]);
                                   })
                                   ->orWhere(function ($query) use ($userId) {
                                       $query->whereRaw('from_user_id = follows.to_user_id and to_user_id = ?', [$userId]);
                                   });
                         })
                         ->count();

                         
        $dishes = Recipe::where('user_id',$userId)->count();

        
        $user = User::where('id',$userId)->first();
        $user->followings  =$followingCount;
        $user->followers  =$followerCount;
        $user->dishes  =$dishes;


        return $user;
    }


    public function addRecipe(Request $request,$userId){
        $obj = new \stdClass();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'images.*' => 'required|mimes:jpeg,jpg,png,gif|required|max:10000',
            'title' => 'required',
            'catgory' => 'required',
            'dietary' => 'required',
            'time' => 'required',
            'serves' => 'required',
            'ingredients' => 'required',
            'method' => 'required',
            'method_audio' => 'nullable|mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav',

        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => 'Add recipe failed',
                'errors' => $validator->errors()->all()
            ]);
        }else{

            $category = $request->catgory;
            $dietary = $request->dietary;
            $allergies = $request->allergies;


           

            $images = $request->file('images');
            
            foreach($images as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('public/photos', $filename);
                $imagePaths[] = str_replace("public/", "", $path); 
            }


            $recipe = new Recipe();
            $recipe->user_id= $request['user_id'];
            $recipe->title = $request['title'];

            $recipe->image = implode(',', $imagePaths);
            $recipe->catgory = $category;
            $recipe->dietary = $dietary;
            $recipe->time = $request['time'];
            $recipe->serves = $request['serves'];
            $recipe->method_audio = $request->hasFile('method_audio') ? $request->file('method_audio')->store('public/audio') : '';
            $recipe->allergies = $allergies ?? '';

            $recipe->ingredients = $request['ingredients'];
            $recipe->method = $request['method'];
            $recipe->save();
            return response()->json([
                'status' => true,
                'action' => 'Recipe added',
                'data'   => $obj,
                'errors' => []
            ]);
        }
    }


    public function recipelist(Request $request){

        $recipes = Recipe::orderBy('created_at', 'desc')->get();

        foreach($recipes as $recipe ){
            $user_id = $recipe->user_id;

            $user = DB::table('users')
            ->select('id','full_name','image')
            ->where('id', $user_id)
            ->first();
            $recipe->user =$user;
        }


        foreach($recipes as $recipe){

            $image =  $recipe->image;
            $image = explode(',',$image);

            $recipe->image = $image;


        }


        foreach($recipes as $recipe){
            $catgory =  $recipe->catgory;
            $catgory = explode(',',$catgory);
            
            $name = DB::table('categories_recipe')
            ->select('id','name')
            ->whereIn('id', $catgory)
            ->get();
            $recipe->catgory = $name;
        }

        foreach($recipes as $recipe){
            $dietary =  $recipe->dietary;
            $dietary = explode(',',$dietary);
            
            $name1 = DB::table('dietary_recipe')
            ->select('id','name')
            ->whereIn('id', $catgory)
            ->get();
            $recipe->dietary = $name1;
        }

        foreach($recipes as $recipe){
            $allergies =  $recipe->allergies;
            $allergies = explode(',',$allergies);
            
            $name2 = DB::table('allergies_recipe')
            ->select('id','name')
            ->whereIn('id', $allergies)
            ->get();
            $recipe->allergies = $name2;
        }


        if($recipes->count() > 0){
            return response()->json([
                'status' => true,
                'action' => 'Recipe list',
                'data'   => $recipes,
                'errors' => []
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'action' => 'Recipe list',
                'data'   => $recipes,
                'errors' => []
            ]);
        }  
    }



    public function addReview(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'images.*' => 'mimes:jpeg,jpg,png,gif|required|max:10000',
            'rating' => 'required',
            'description' => 'required',
            'recipe_id' => '|exists:recipes,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => 'Add rievew failed',
                'errors' => $validator->errors()->all()
            ]);
        }else{
                    
                    if($images = $request->file('images')){
                    foreach($images as $image) {
                        $filename = time() . '_' . $image->getClientOriginalName();
                        $path = $image->storeAs('public/reviews', $filename);
                        $imagePaths[] = str_replace("public/", "", $path); 
                        $imgetoStore = implode(',',$imagePaths);
                    }
                    }
                    else{
                        $imgetoStore = '';
                    }
                    
                    $recipeOwner = Recipe::select('user_id')->where('id',$request->recipe_id)->first();

                    $review = new Review();
                    $review->rating= $request['rating'];
                    $review->description = $request['description'];
                    $review->user_id = $request['user_id'];
                    $review->recipe_id = $request['recipe_id'];
                    $review->images = $imgetoStore;
                    $review->save();

                    $notification = new Notification();
                    $notification->type = 'rating';
                    $notification->user_id = $recipeOwner->user_id;
                    $notification->person_id = $request->user_id;
                    $notification->body = 'rate you';
                    $notification->recipe_id = $request->recipe_id;
                    $notification->save();


                    return response()->json([
                        'status' => true,
                        'action' => 'Rievew added',
                        'errors' => []
                    ]);
            }
    }

    public function recipeReview($recipeId){

        $reviews = DB::table('reviews')
        ->select('reviews.*')
        ->where('recipe_id',$recipeId)
        ->orderBy('reviews.created_at', 'desc')
        ->get();

        $count = DB::table('reviews')
        ->select('reviews.*')
        ->where('recipe_id',$recipeId)
        ->orderBy('reviews.created_at', 'desc')
        ->count();


        $averageRating = DB::table('reviews')
        ->where('recipe_id', $recipeId)
        ->avg('rating');

        
    
    foreach ($reviews as $review) {

        $review->images = explode(',', $review->images);
    }

    foreach ($reviews as $review) {
        $user_id = $review->user_id;
        $user = DB::table('users')->where('id',$user_id)->select('id','full_name','image')->first(); 
        $review->user = $user;
    }

        
    return response()->json([
        'status' => true,
        'action' => 'Notifications',
        'count'  => $count,
        'rating' => $averageRating,
         'data' => $reviews
    ]);
    }


    

    public function getNotification(Request $request,$userId){
        
        $notifications = DB::table('notifications')
        ->select('notifications.*', 'users.full_name AS person_name', 'users.image AS person_image', DB::raw('SUBSTRING_INDEX(recipes.image, ",", 1) AS recipe_image'))
        ->join('users', 'users.id', '=', 'notifications.person_id')
        ->leftJoin('recipes', 'recipes.id', '=', 'notifications.recipe_id')
        ->where('notifications.user_id', $userId)
        ->orderBy('notifications.created_at', 'desc')
        ->get();

        if($notifications->count() >0){
            return response()->json([
                'status' => true,
                'action' => 'Notifications',
                'data' => $notifications
            ]);
        }
        else{
            return response()->json([
                'status' => true,
                'action' => 'No notifications',
            ]);
        }
       
    }


    public function readNotification($userId){
        $notifications = Notification::where('user_id', $userId)->where('is_read', false)->get();

        $user = User::find($userId);
            if($user){
                if($notifications->count() >0){
                    foreach ($notifications as $notification) {
                        $notification->is_read = true;
                        $notification->save();
                    }
            
                    return response()->json([
                        'status' => true,
                        'action' => 'Notification read',
                    ]);
                }
                else{
                    return response()->json([
                        'status' => true,
                        'action' => 'No notification',
                    ]);
                }
            }
            else{
                return response()->json([
                    'status' => false,
                    'action' => 'Invalid user',
                ]);
            }
    }


    
}





?>






