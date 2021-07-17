<?php

namespace App\Http\Controllers;
use App\Http\Requests\RegisterAuthRequest;
use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use App\SendCode;
use DB;
class UserController extends Controller
{
    public $loginAfterSignUp = true;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'phone' => 'required|max:10|min:10|unique:users',
            'password' => 'required',
            'level' => 'required',
            'confirmPassword' => 'required|same:password',
            
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 400,
                'data' => 'Validation Error.',
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $user  = User::create([
            'fullname'=>$request->fullname,
            'phone'=>$request->phone,
            'password'=>bcrypt($request->password),
            'level'=>$request->level
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'Message'=>'Success',
            'Data' => $user,
            'Token'=>$token,
            'Status' => 200
            
        ], 200);
       
    }
 
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'phone' => 'required|min:10|max:10',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                
                'Data' => 'Validation Error.',
                'message' => $validator->errors(),
                'Status' => 400,
            ];
            return response()->json($response, 400);
        }
        $input = $request->only('phone', 'password');
        $jwt_token = null;
        $phone=$request->get('phone');
        try{
            $users = User::where('phone','=',$phone)->where('status','=',1)->get();
            if (!$jwt_token = JWTAuth::attempt($input)) {
                return response()->json([
                    'Message' => 'Invalid Credential',
                    'Status' => 401,
                ], 401);
            }
         } catch (JWTException $e){
            return response()->json(['Error'=>'could_not_create_token','Status'=>400],400);
            
        }
       
        $user = array();
        $usr = array();
        foreach($users as $us){
            $usr['message']='Success';
            $usr['id']=$us->id;
            $usr['fullname']=$us->fullname;
            $usr['level']=$us->level;
            $usr['token']=$jwt_token;
           
            array_push($user,$usr);
        }
        $count=$users->count();
        if($count>0){
        return response()->json($user);
        }else{
            return response()->json(['Message'=>'Your acount blocked','Status'=>401],401);
        }
    }
 
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            $response = [
                'message' => $validator->errors(),
                'Data' => 'Validation Error.',
                'Status' => 400,
            ];
            return response()->json($response, 400);
        }
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                
                'message' => 'User logged out successfully',
                'Status' => 200,
            ],200);
        } catch (JWTException $exception) {
            return response()->json([
                
                'Message' => 'Sorry, the user cannot be logged out',
                'Status' => 400,
            ], 400);
        }
    }
 
    public function getAuthUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required']);

        if ($validator->fails()) {
            $user = JWTAuth::authenticate($request->token);
            $response = [
                'message' => $validator->errors(),
                'Data' => 'Validation Error.',
                'Status' => 400,
            ];
        
        }
        return response()->json(['user' => $user],200);
    }

    
    public function sendCode(Request $request){
        $codes= new user();
        $send=$codes->phone=$request->phone;
        $usr=DB::table('users')->where('phone','=',$send)
        ->get();
        if($usr->count()>0){
        $code=rand(1111,9999);
        $nexmo=app('Nexmo\Client');
        $nexmo->message()->send([
            'to'=>'25'.$send,
            'from'=>"UNGUKA MUHINZI" ,
            'text'=>'Verify code: '.$code,
        ]);
        $user=DB::table('users')->where('phone','=',$send)->update(array('code'=>$code));
        return response()->json(['Message'=>'success','Data'=>$code,'Status'=>200],200);
        }else{
            return response()->json(['Message'=>'Error, This phone are not available','Status'=>400],400);
        }
    }
    public function viewCrops(){
           $crops=DB::table('crops')->get();
           $count=$crops->count();
           if($count>0){
               return response()->json(['Message'=>'Success','Data'=>$crops,'Data_returned'=>$count,'Status'=>200],200);
           }else{
            return response()->json(['Message'=>'Success','Data'=>'There is no any kind of crops you have','Status'=>200],200);
           }
       }
}
