<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterAuthRequest;
use JD\Cloudder\Facades\Cloudder;
use App\User;
use Illuminate\Http\Request;
use App\Farmer;
use App\Stock;
use App\Season;
use App\Order;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use Validator;
use Notifiable;
class FarmerController extends Controller
{

    protected $user;
    
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user=$this->user = JWTAuth::parseToken()->authenticate();
        $userid=$user->id;
        $validator=Validator::make($request->all(), [
            'photo' => 'required|image',
            'fname' => 'required',
            'lname' => 'required',
            'phone' => 'required|max:10|min:10',
            'identity'=>'required|min:16|max:16',  
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 400,
                'data' => 'Validation Error.',
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
       $image = $request->file('photo');

       $name = $request->file('photo')->getClientOriginalName();

       $image_name = $request->file('photo')->getRealPath();;

       Cloudder::upload($image_name, null);

       list($width, $height) = getimagesize($image_name);

       $image_url= Cloudder::show(Cloudder::getPublicId(), ["width" => $width, "height"=>$height]);

       //save to uploads directory
       $image->move(public_path("public/images"), $name);

        $check = Farmer::all()->where('user_id','=',$userid);
        if($check->count()<=0){
        $farmers = new Farmer();
        $farmers->fname = $request->fname;
        $farmers->lname = $request->lname;
        $farmers->phone = $request->phone;
        $farmers->identity=$request->identity;
        $farmers->photo = $image_url;
       
        if ($this->user->farmers()->save($farmers))
            return response()->json(['Message' =>'Farmer Registered','Status'=>200],200);
        else
            return response()->json([
                'Status' => 400,
                'message' => 'Sorry, To complete profile could not be accomplished'
            ], 400);
            
           }
           else{
            return response()->json(['Message' =>'you already completed your profile, you may update it',
            'Status'=>200], 200);
        }
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user=$this->user = JWTAuth::parseToken()->authenticate();
        $userid=$user->id;
        $farmers= $this->user
        ->farmers()
        ->get(['id','photo','fname','lname','phone','identity','created_at','updated_at'])
        ->toArray();
    if (!$farmers) {
        return response()->json([
            'Status' => 400,
            'message' => 'Sorry, farmer with id ' . $userid . ' cannot be found'
        ], 400);
    }
 
    return response()->json(['Message'=>'Success','Data'=>$farmers,'Status'=>200],200);
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'farmerid' => 'required|integer',
        ]);
        if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
        }
        $id = $request->get('farmerid');
        $validfarmer = Farmer::where('id','=',$id)->count();
        if ($validfarmer == 0) {
            $retunerror = array('message'=>'this farmer is currently unvailable','status'=>400);
        return response()->json($retunerror);
        }
        else{
           
            $imageurls = array();
            $existedrecords =Farmer::where('id','=',$request->get('farmerid'))->get();
        
            if($request->hasFile('photo')){
                $image = $request->file('photo');
                $name = $request->file('photo')->getClientOriginalName();
                $image_name = $request->file('photo')->getRealPath();
                Cloudder::upload($image_name, null);
                list($width, $height) = getimagesize($image_name);
                $image_url= Cloudder::show(Cloudder::getPublicId(), ["width" => $width, "height"=>$height]);
                    //save to uploads directory
                $image->move(public_path("public/images"), $name);
            }
            else{
             foreach($existedrecords as $existss){
                 $imageurls[] = $existss->photo;
            }
             $image_url = implode("",$imageurls);
            }

        $farmers = $this->user->farmers()->find($id);
        if (!$farmers) {
            return response()->json([
               
                'message' => 'Sorry, farmer with id ' . $id . ' cannot be found',
                'Status' => 400,
            ], 400);
        }
        $farmers->fname = $request->fname;
        $farmers->lname = $request->lname;
        $farmers->phone = $request->phone;
        $farmers->identity=$request->identity;
        $farmers->photo = $image_url;
        $farmers->save();
        return response()->json(['Message' =>'Farmer Updated','Status' => 200],200);
        }
 
        //
    }
    /*
    function which will help farmer to view all pending orders
    */
    public function myOrders(){
        $myorder=$this->user
        ->stocks()
        ->join('orders','stocks.id','=','orders.stockID')
        ->join('users','users.id','=','orders.user_id')
        ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->join('customers','customers.user_id','=','users.id')
        ->select('orders.id','crops.photo','stocks.id as stockID','crops.crops','farms.location','orders.quantity','customers.fname as client_name','customers.phone as contact_for_client','stocks.price as frw_per_kg',DB::raw('stocks.price*orders.quantity as Money_you_have_to_get'),'orders.status','orders.updated_at')
        ->where('orders.status','=','1')
        ->orderBy('orders.updated_at','DESC')
        ->get();
        $count=$myorder->count();
       if($count>0){
       return response()->json(['Message'=>'Success','Data'=>$myorder,'Status'=>200,'Returned_data'=>$count],200); //
      }else{
      return response()->json(['Message'=>'Success','Data'=>$myorder,'Status'=>200,'Returned_data'=>$count],200);
     }
    }
    /**
     * function for view current season
     */
    public function seasons(){
        
        $season=Season::all();
        $count=$season->count();
        if($count>0){
        return response()->json(['Message'=>'Success','Data'=>$season,'Status'=>200,'Returned_data'=>$count]); //
        }
        else{
        return response()->json(['Message'=>'Success','Data'=>$season,'Status'=>200,'Returned_data'=>$count],200);
       }

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * function for to change password
     */
    public function changePassword(Request $request){

       
        
        if (!(Hash::check($request->get('current_password'), JWTAuth::user()->password))) {
            // The passwords matches
            return response()->json(["Message"=>"error, Your current password does not matches with the password you provided. Please try again.","Status"=>400],400);
        }

        if(strcmp($request->get('current_password'), $request->get('new_password')) == 0){
            //Current password and new password are same
            return response()->json(['Message'=>"Error,New Password cannot be same as your current password. Please choose a different password.","Status"=>400],400);
        }

        $validator=Validator::make($request->all(),[
            'current_password' => 'required',
            'new_password' => 'required',
            'confirmPassword' => 'required|same:new_password',
        ]);
        if ($validator->fails()) {
            $response = [
               
                'data' => 'Validation Error.',
                'message' => $validator->errors(),
                'Status' => 401,
            ];
            return response()->json($response, 401);
        }
        //Change Password
        $user = JWTAuth::user();
        $user->password = bcrypt($request->get('new_password'));
        $user->save();

        return response()->json(["Message"=>"success,Password changed successfully !","Status"=>200],200);


    }
    /**
     * function for to change name or phone number
     */
    public function changePhone(Request $request){
        $validator=Validator::make($request->all(),[
            'fullname' => 'required|string|nullable',
            'phone' => 'required|unique:users|min:10|max:10',
        ]);
        if ($validator->fails()) {
            $response = [
                'message' => $validator->errors(),
                'data' => 'Validation Error.',
                'Status' => 400,
            ];
            return response()->json($response, 400);
        }
        $user = JWTAuth::user();
        $user->fullname=$request->fullname;// may be the screen name
        $user->phone= $request->phone;
        $user->save();

        return response()->json(["Message"=>"success,changes applied successfully !","Status"=>200],200);
    }
    /*
    function which will help farmer to check profit he/she may get once he/she sell
    */
    public function checkProfit(Request $request){
       $temprice=new Stock();
        $temprice= $request->temprice;
        $id=new Stock();
        $id=$request->stocid;
        $stock=$this->user
       
        ->stocks()
        ->join('users','stocks.user_id','=','users.id')
        ->JOIN('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->join('expenses','expenses.farmID','=','farms.id')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->join('seasons','seasons.id','=','cropfarms.seasonID')
        ->select('farms.id','stocks.quantity','crops.photo','crops.crops','farms.UPI','seasons.seasonLenght',
        'stocks.id','stocks.price','stocks.status',DB::raw('SUM(expenses.moneySpent) as Total_expenses'),
        DB::raw('stocks.quantity '.'*'.$temprice.' as Total_income'),
        DB::raw('stocks.quantity'.'*'.$temprice.'-'.'SUM(expenses.moneySpent) As profit'))
        ->where('stocks.id','=',$id)
        ->groupBy('farms.id','stocks.quantity','crops.photo','crops.crops','farms.UPI','seasons.seasonLenght',
        'stocks.id','stocks.price','stocks.status')
        ->get();
        $count=$stock->count();
        if($count>0){
        return response()->json(['Message'=>'Success','Data'=>$stock,'Status'=>200,'Returned_data'=>$count]); //
       }
       else{
       return response()->json(['Message'=>'Success','Data'=>$stock,'Status'=>200]);
      } 
    }
   
    /*
    function which will help farmer to view all processing orders
    */
    public function processing(){
        $myorder=$this->user
        ->stocks()
        ->join('orders','stocks.id','=','orders.stockID')
        ->join('users','users.id','=','orders.user_id')
        ->join('cropfarms','stocks.cropfarmID','=','cropfarms.id')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->join('customers','customers.user_id','=','users.id')
        ->select('orders.id as orderid','stocks.id as stockID','crops.photo','crops.crops','farms.location','orders.quantity','orders.status','customers.fname','customers.lname','customers.phone','stocks.price as frw_per_kg',DB::raw('stocks.price*orders.quantity as Money_you_have_to_get'),'orders.updated_at')
        ->where('orders.status','=','2')->orderBy('orders.updated_at','DESC')
        ->get();
        $count=$myorder->count();
       if($count>0){
       return response()->json(['Message'=>'Success','Processin_Orders'=>$myorder,'Status'=>200,'Returned_data'=>$count]); //
      }else{
      return response()->json(['Message'=>'Success','Processin_Orders'=>$myorder,'Status'=>200,'Returned_data'=>$count]);
     }
    }
    /*
    function which will help farmer to view all delivered orders
    */
    public function delivered(){
        $myorder=$this->user
        ->stocks()
        ->join('orders','stocks.id','=','orders.stockID')
        ->join('users','users.id','=','orders.user_id')
        ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->join('customers','customers.user_id','=','users.id')
        ->select('orders.id as orderid','crops.photo','stocks.id as stockID','crops.crops','farms.location','orders.quantity','customers.fname as client_name','customers.phone as contact_for_client','stocks.price as frw_per_kg',DB::raw('stocks.price*orders.quantity as Money_you_have_to_get'),'orders.status','orders.updated_at')
        ->where('orders.status','=','3')->orderBy('orders.updated_at','DESC')
        ->get();
        $count=$myorder->count();
       if($count>0){
       return response()->json(['Message'=>'Success','delivered_Orders'=>$myorder,'Status'=>200,'Returned_data'=>$count]); //
      }else{
      return response()->json(['Message'=>'Success','delivered_Orders'=>$myorder,'Status'=>200,'Returned_data'=>$count]);
     }
    }
    /*
    function which will help farmer to view all cancelled orders
    */
    public function cancelled(){
        $myorder=$this->user
        // ->stocks()
        // ->join('orders','stocks.id','=','orders.stockID')
        // ->join('users','users.id','orders.user_id')
        // ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        // ->join('crops','crops.id','=','cropfarms.cropsID')
        // ->join('farms','farms.id','=','cropfarms.farmID')
        // ->select('orders.id as orderid','stocks.id as stockID','crops.photo','crops.crops','farms.location','orders.quantity','orders.status','users.fullname as client_name','users.phone as contact_for_client','stocks.price as frw_per_kg',DB::raw('stocks.price*orders.quantity as Money_you_have_to_get'),'orders.updated_at')
        // ->where('orders.status','=','0')
        // ->get();
        ->stocks()
        ->join('orders','stocks.id','=','orders.stockID')
        ->join('users','users.id','=','orders.user_id')
        ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->join('customers','customers.user_id','=','users.id')
        ->select('orders.id as orderid','crops.photo','stocks.id as stockID','crops.crops','farms.location','orders.quantity','customers.fname as client_name','customers.phone as contact_for_client','stocks.price as frw_per_kg',DB::raw('stocks.price*orders.quantity as Money_you_have_to_get'),'orders.status','orders.updated_at')
        ->where('orders.status','=','0')->orderBy('orders.updated_at','DESC')
        ->get();
        $count=$myorder->count();
       if($count>0){
       return response()->json(['Message'=>'Success','cancelled_Orders'=>$myorder,'Status'=>200,'Returned_data'=>$count]); //
      }else{
      return response()->json(['Message'=>'Success','cancelled_Orders'=>$myorder,'Status'=>200,'Returned_data'=>$count]);
     }
    }
    /*
    function which will be used to accept order ordered by client
    */
    public function accept(Request $request){
        $id=new order();
        $id=$request->orderid;
        DB::table('orders')->where('orders.id','=',$id)->update(array('orders.status'=>'2'));
        return response()->json(['Message'=>'Accepted','Status'=>200],200);
    }

    /*
    function which will help farmer to cancel order ordered by client
    */
    public function cancel(Request $request){
        $id=new order();
        $id=$request->orderid;
        $select=DB::table('orders')->select('quantity')->where('orders.id','=',$id)
        ->get();
        if($select->count()>0){
        foreach($select as $selc){
            $qntnt = $selc->quantity;
        }
        $stock2=new stock();
        $stock=$this->user
        ->stocks()
        ->join('users','stocks.user_id','=','users.id')
        ->join('orders','stocks.id','=','orders.stockID')
        ->where('stocks.id','=',$stock2->id=$request->stocid)->increment('stocks.quantity',$qntnt );
        DB::table('orders')->where('orders.id','=',$id)->update(array('orders.status'=>'0'));
        return response()->json(['Message'=>'Declined','Status'=>200],200);
    }else{
        return response()->json(['Message'=>'This order with id '.$id.' Not available','Status'=>400],400);
    }
    }
    /**
     * function which will use to confirm that order have been delireved
     */
    public function markAsDelivered(Request $request){
        $id=new order();
        $id=$request->orderid;
        $select=DB::table('orders')->where('id',$id)->where('orders.status','=','2')->get();
        if($select->count()>0){
        DB::table('orders')->where('orders.id','=',$id)->where('orders.status','=','2')->update(array('orders.status'=>'3'));
        return response()->json(['Message'=>'Delivered','Status'=>200],200);
    }
    else{
        return response()->json(['Message'=>'You cant mark as delivered before ','Status'=>400],400);
    }
}

    /*
    function which will help farmer to restore orders cancelled
    */
    public function restore(Request $request){
        $id=new order();
        $id=$request->orderid;

        $select=DB::table('orders')->select('quantity')->where('orders.id','=',$id)
        ->get();
        if($select->count()>0){
        foreach($select as $selc){
            $qntnt = $selc->quantity;
        }
        $stock3=new stock();
        $select2=DB::table('stocks')->select('quantity')->where('stocks.id','=',$stock3->id=$request->stocid)
        ->get();
        foreach($select2 as $selc2){
            $qntnt2 = $selc2->quantity;
        }
        if($qntnt2>=$qntnt){
        $stock2=new stock();
        $stock=$this->user
        ->stocks()
        ->join('users','stocks.user_id','=','users.id')
        ->join('orders','stocks.id','=','orders.stockID')
        ->where('stocks.id','=',$stock2->id=$request->id)->decrement('stocks.quantity',$qntnt );
            DB::table('orders')->where('orders.id','=',$id)->where('orders.status','=','0')->update(array('orders.status'=>'1'));
            return response()->json(['Message'=>'restored','Status'=>200],200);
        }
        else{
            return response()->json(['Message'=>'you cant restore this order because you no longer have enough harvest','Status'=>200],200);
        }
    }else{
        return response()->json(['Message'=>'this order id '.$id.' or stock id not available','Status'=>400],400);
    }
    }
}
