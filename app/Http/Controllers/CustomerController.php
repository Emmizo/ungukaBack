<?php

namespace App\Http\Controllers;
use App\Http\Requests\RegisterAuthRequest;
use App\User;
use JD\Cloudder\Facades\Cloudder;
use App\Stock;
use Illuminate\Http\Request;
use App\Customer;
use App\Order;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Notifiable;
class CustomerController extends Controller
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
        $farmers= $this->user
        ->customers()
        ->get(['id','photo','fname','lname','phone','identity','created_at','updated_at'])
        ->toArray();
    if (!$farmers) {
        return response()->json([
            'Status' => 400,
            'message' => 'Sorry, you cannot be found customer' 
        ], 400);
    }
    return response()->json(['Message'=>'Success','Data'=>$farmers,'Status'=>200],200);
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
        $validator=Validator::make($request->all(), [
            'photo' => 'required|image',
            'fname' => 'required',
            'lname' => 'required',
            'phone' => 'required|max:10|min:10',
            'user_id'=>'unique:farmers',
            'identity'=>'required|min:16',
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 404,
                'data' => 'Validation Error.',
                'message' => $validator->errors()
            ];
            return response()->json($response, 404);
        }
        $user=$this->user = JWTAuth::parseToken()->authenticate();
        $userid=$user->id;

        $image = $request->file('photo');

       $name = $request->file('photo')->getClientOriginalName();

       $image_name = $request->file('photo')->getRealPath();;

       Cloudder::upload($image_name, null);

       list($width, $height) = getimagesize($image_name);

       $image_url= Cloudder::show(Cloudder::getPublicId(), ["width" => $width, "height"=>$height]);

       //save to uploads directory
       $image->move(public_path("public/images"), $name);
      
        $check = customer::all()->where('user_id','=',$userid);
        if($check->count()<=0){
        $customers = new customer();
        $customers->fname = $request->fname;
        $customers->lname = $request->lname;
        $customers->phone = $request->phone;
        $customers->identity=$request->identity;
        $customers->photo =   $image_url;

        if ($this->user->customers()->save($customers))
            return response()->json(['Message' =>'Customer profile Completed','Status' => 200],200);
        else
            return response()->json([
                'message' => 'Sorry, complete profile could not be accomplished',
                'Status' => 400
            ], 400);
        }
        else{
            return response()->json(['Message' =>'you already completed your profile, you may update instead ','Status'=>200],200);
        }
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
/**
 * function for retrieving all harverst available to customers
 */
    public function ViewHarvest(){
        $harvest=DB::table('stocks')
                
                 ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
                ->join('seasons','cropfarms.seasonID','=','seasons.id')
                ->join('crops','crops.id','=','cropfarms.cropsID')
                ->join('users','stocks.user_id','=','users.id')
                ->join('farmers','farmers.user_id','=','users.id')
                ->join('farms','farms.id','=','cropfarms.farmID')
                ->select('stocks.id as stockID','crops.photo','crops.crops','stocks.quantity as quantity_of_harvest','farmers.fname','farmers.lname','farms.location','stocks.price as price_per_kg')
                ->where('stocks.status','=',1)
                ->where('quantity','>',0)
                ->orderBy('stocks.id','DESC')
                ->get();
        $count=$harvest->count();
        if($count>0){
        return response(['Message'=>'Success','Data'=>$harvest,'Status'=>200,'Returned_data'=>$count]); //
    }else{
        return response(['Message'=>'Success','Data'=>$harvest,'Status'=>200]);
        }

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
     * function which all customer to order harverst
     */
public function makeOrder(Request $request){
     $validator=Validator::make($request->all(), [
        'quantity' => 'required',
    ]);
    if ($validator->fails()) {
        $response = [
            'Status' => 404,
            'data' => 'Validation Error.',
            'message' => $validator->errors()
        ];
        return response()->json($response, 404);
    }
    $id=new stock();
    $id=$request->stockID;
    $customers2= $this->user
        ->customers()
        ->get(['id'])->count();
        if($customers2>0){
     $select=DB::table('stocks')->select('stocks.quantity')->where('id','=',$id)->get();
     foreach($select as $selc){
        $qntnt = $selc->quantity;
    }
    $order2= new stock();
    if($qntnt >= $order2->quantity = $request->quantity){
     DB::table('stocks')->where('id',$id)->decrement('quantity', $order2->quantity = $request->quantity);
     $order = new order();
    $order->stockID = $request->stockID;
    $order->quantity = $request->quantity;
    $order->status = $request->status;
        if ($this->user->customers()->save($order))
            return response()->json(['Message' =>'You made order ','Status'=>200],200);
        else
            return response()->json([
                
                'message' => 'Sorry, new order not added please try again',
                'Status' => 401
            ], 401);
        }else{
            return response()->json(['Message'=>'Try again','Isues'=>'You choose too much quantity than available','Status'=>400],400);
        }
        }
    else{
        return response()->json(['Message'=>'Complete profile before you make orders','Status'=>400],400);
    }
    }
        //
public function updateStock(){

}
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * function for updating customer personal profile
     */
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'customerid' => 'required|integer',
            ]);
            if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
            }
            $id = $request->get('customerid');
            $validcustomer = customer::where('id','=',$id)->count();
           // return $validcustomer;
            if ($validcustomer == 0) {
                $retunerror = array('message'=>'this customer is currently unvailable','status'=>400);
            return response()->json($retunerror);
            }
            else{
               
                $imageurls = array();
                $existedrecords =customer::where('id','=',$request->get('customerid'))->get();
            
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
                $customers = $this->user->customers()->find($id);
        if (!$customers) {
            return response()->json([
                'Status' => 400,
                'message' => 'Sorry, customer with id ' . $id . ' cannot be found'
            ], 400);
        }
                $customers->fname = $request->fname;
                $customers->lname = $request->lname;
                $customers->phone = $request->phone;
                $customers->identity=$request->identity;
                $customers->photo = $image_url;
                $customers->save();
           return response()->json(['Message' =>'customer Updated','Status'=>200],200);
            }
     }
    /**
     * function which wil display orders which are pending to be accepted or denied
     */
    public function myOrder(){
        $myorder= $this->user
        ->orders()
        ->join('users','orders.user_id','=','users.id')
        ->join('stocks','stocks.id','=','orders.stockID')
        ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        ->join('crops','crops.id','cropfarms.cropsID')
        ->join('farms','cropfarms.farmID','=','farms.id')
        ->join('farmers','farmers.user_id','farms.user_id')
        ->select('stocks.id as stockID','orders.id as orderID','crops.photo','crops.crops','farms.location','orders.quantity','orders.status as orderStatus','stocks.price as frw_per_kg',DB::raw('stocks.price*orders.quantity as Money_you_have_to_pay'),'farmers.fname','farmers.lname','farmers.phone','orders.created_at')
        ->where('orders.status','=','1')
        ->get();
        $count=$myorder->count();
       if($count>0){
       return response(['Message'=>'Success','Data'=>$myorder,'Status'=>200,'Returned_data'=>$count]); //
      }else{
      return response(['Message'=>'Success','Data'=>$myorder,'Status'=>200,'Returned_data'=>$count]);
     }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
 * function which will use to display delivered orders
 */
    public function delivered(){
        $myorder= $this->user
        ->orders()
        ->join('users','orders.user_id','=','users.id')
        ->join('stocks','stocks.id','=','orders.stockID')
        ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->join('farms','cropfarms.farmID','=','farms.id')
        ->join('farmers','farmers.user_id','farms.user_id')
        ->select('stocks.id as stockID','orders.id as orderID','crops.photo','crops.crops','farms.location','orders.quantity','orders.status','stocks.price as frw_per_kg',DB::raw('stocks.price*orders.quantity as Money_you_supposed_to_be_paid'),'farmers.fname','farmers.lname','farmers.phone','orders.updated_at')
        ->where('orders.status','=','3')
        ->get();
        $count=$myorder->count();
       if($count>0){
       return response(['Message'=>'Order delivered','Data'=>$myorder,'Status'=>200,'Returned_data'=>$count]); //
      }else{
      return response(['Message'=>'Order delivered','Data'=>$myorder,'Status'=>200,'Returned_data'=>$count]);
     }
    }
    public function cancelled(){
        $myorder= $this->user
        ->orders()
        ->join('users','orders.user_id','=','users.id')
        ->join('stocks','stocks.id','=','orders.stockID')
        ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->join('farms','cropfarms.farmID','=','farms.id')
        ->join('farmers','farmers.user_id','farms.user_id')
        ->select('stocks.id as stockID','orders.id as orderID','crops.photo','crops.crops','farms.location','orders.quantity','orders.status','stocks.price as frw_per_kg',DB::raw('stocks.price*orders.quantity as Money_you_supposed_to_be_paid'),'farmers.fname','farmers.lname','farmers.phone','orders.updated_at')
        ->where('orders.status','=','0')
        ->get();
        $count=$myorder->count();
       if($count>0){
       return response(['Message'=>'Success','Data'=>$myorder,'Status'=>200,'Returned_data'=>$count]); //
      }else{
      return response(['Message'=>'Success','Data'=>$myorder,'Status'=>200,'Returned_data'=>$count]);
     }
    }
/**
 * reorder function which will help to reoder
 */
    public function reorder(Request $request){
       
        $id=new order();
        $id=$request->orderid;
        $id2=$request->stocid;
        $select=DB::table('orders')->select('quantity')->where('orders.id','=',$id)
        ->get();
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
        DB::table('stocks')->where('id',$id2)->decrement('quantity',$qntnt);
        DB::table('orders')->where('orders.id','=',$id)->where('orders.status','=','0')->update(array('orders.status'=>'1'));
            return response()->json(['Message'=>'You reordered well','Status'=>200],200);
        }
        else{
            return response()->json(['Message'=>'you cant restore this order because the harvest remain not enough','Status'=>200],200);
        }
    }

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
        DB::table('stocks')
        ->join('orders','stocks.id','=','orders.stockID')
        ->join('users','stocks.user_id','=','users.id')
        ->where('stocks.id','=',$stock2->id=$request->stocid)->increment('stocks.quantity',$qntnt );
        $stock=$this->user
        ->orders()
        ->where('orders.id','=',$id)->update(array('orders.status'=>'0'));
        if (!$stock) {
            return response()->json([
                'Status' => 400,
                'message' => 'Sorry, customer with id ' . $id . ' order not assigned to you'
            ], 400);
        }
        else{
        return response()->json(['Message'=>'order Canceled','Status'=>200],200);
        }}else{
            return response()->json(['Message'=>'This order not available','Status'=>400],400);
        }
    }
    public function process(){
        $myorder=$this->user
        ->orders()
        ->join('stocks','stocks.id','=','orders.stockID')
        ->join('users','users.id','=','orders.user_id')
        ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->join('farmers','farmers.user_id','=','farms.user_id')
        ->select('orders.id','crops.photo','stocks.id as StocksID','crops.crops','farms.location','orders.quantity','orders.status','farmers.fname','farmers.lname','farmers.phone as contact_for_farmers','stocks.price as frw_per_kg',DB::raw('stocks.price*orders.quantity as Money_you_have_to_pay'),'orders.updated_at')
        ->where('orders.status','=','2')
        ->get();
        $count=$myorder->count();
       if($count>0){
       return response(['Message'=>'Success','Processin_Orders'=>$myorder,'Status'=>200,'Returned_data'=>$count]); //
      }else{
      return response(['Message'=>'Success','Data'=>$myorder,'Status'=>200,'Returned_data'=>$count]);
     }
    }
    /**
     * function which will be have responsiblities of to display all harverst bought
     */
public function allOrdered(){
    $total=$this->totalAmount();
    $user=$this->user
->orders()
    ->join('stocks','stocks.id','=','orders.stockID')
    ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
    ->join('crops','crops.id','=','cropfarms.cropsID')
    ->select('crops.id as cropid','orders.user_id','stocks.price','crops.crops','crops.photo',DB::raw('SUM(stocks.price)*SUM(orders.quantity) as Amount_Spent,SUM(orders.quantity) as quantity'))
    ->groupBy('crops.id','orders.user_id','stocks.price','crops.photo','crops.crops','stocks.price')
    ->where('orders.status','=','3') ->groupby('orders.user_id')->groupby('crops.crops')
    ->get();
    $count=$user->count();
    if($count>0){
        return response()->json(['Message'=>'success','Data'=>$user,$total,'Returned_Data'=>$count,'Status'=>200]);
    }else{
        return response()->json(['Message'=>'success','Data'=>$user,$total,'Returned_Data'=>$count,'Status'=>200]);
    }
}
public function totalAmount(){
    $user=$this->user
    ->orders()
    ->join('stocks','stocks.id','=','orders.stockID')
    ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
    ->join('crops','crops.id','=','cropfarms.cropsID')
    ->select(DB::raw('SUM(stocks.price*orders.quantity) as Amount_Spent'),DB::raw('SUM(orders.quantity) as all_quantity'),DB::raw('COUNT(distinct(crops.crops)) as crops'))
    ->where('orders.status','=','3')
    ->get();
    $count=$user->count();
    if($count>0){
        return response()->json(['Message'=>'success','Data'=>$user,'Returned_Data'=>$count,'Status'=>200]);
    }else{
        return response()->json(['Message'=>'success','Data'=>$user,'Returned_Data'=>$count,'Status'=>200]);
    }
}
/**
 * function will help customers to view the names of farmers and price before he/she place order
 */
public function fillQuantity(Request $request){
     $id=$request->stockID;
    $data=DB::table('stocks')
    ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
    ->join('farms','farms.id','=','cropfarms.farmID')
    ->join('farmers','farmers.user_id','stocks.user_id')
    ->join('crops','crops.id','=','cropfarms.cropsID')
    ->select('stocks.id','farms.location','stocks.quantity','farmers.fname','farmers.lname','crops.photo','crops.crops','stocks.price')
    ->where('stocks.id','=',$id) 
    ->get();
    $count=$data->count();
    if($count>0){
        return response()->json(['Message'=>'Success','Data'=>$data,'Returned_data'=>$count,'Status'=>200]);
    }else{
        return response()->json(['Message'=>'Success','Data'=>$data,'Returned_data'=>$count,'Status'=>200]);
    }
}
public function supplier(Request $request){
$id=$request->cropid;
$supplier=$this->user
->orders()
->join('stocks','stocks.id','=','orders.stockID')
->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
->join('farms','farms.id','=','cropfarms.farmID')
->join('users','users.id','=','stocks.user_id')
->join('crops','crops.id','=','cropfarms.cropsID')
->join('farmers','farmers.user_id','=','users.id')
->select('crops.crops','farmers.fname','farmers.lname',DB::raw('(stocks.price)*SUM(orders.quantity) as Amount_Spent,SUM(orders.quantity) as quantity'),'orders.updated_at')
->groupBy('crops.crops','farmers.fname','farmers.lname','stocks.price','orders.quantity','orders.updated_at')
->orderBY('orders.updated_at','DESC')
->where('crops.id','=',$id) ->where('orders.status','=','3')
->get();
$count=$supplier->count();
if($count>0){
    return response()->json(['Message'=>'Success','Data'=>$supplier,'Returned_data'=>$count,'Status'=>200]);
}else{
    return response()->json(['Message'=>'Success','Data'=>$supplier,'Returned_data'=>$count,'Status'=>200]);
}
    
}
}
