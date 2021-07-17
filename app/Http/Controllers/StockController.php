<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stock;
use App\User;
use App\Crops;
use App\Farm;
use Carbon\Carbon;
use JWTAuth;
use App\Expense;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use Validator;
use Notifiable;
class StockController extends Controller
{
    protected $user;
    
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        //$this->status='Unpublished';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result=array();
        $both = array();
        $user=$this->user= JWTAuth::parseToken()->authenticate();
        $id=$user->id;
        $stock=DB::table('stocks')
        ->join('users','stocks.user_id','=','users.id')
        ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->join('seasons','seasons.id','=','cropfarms.seasonID')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->select('stocks.id as stockid','farms.id as farmid','crops.photo','stocks.cropfarmID','crops.crops','farms.UPI','seasons.seasonLenght','stocks.quantity','stocks.price as current_price','stocks.status','stocks.created_at','stocks.updated_at')
        ->where('stocks.quantity','>',0)
        ->where('stocks.user_id','=',$id)
        ->orderBy('stocks.created_at','DESC')
        ->get();
        $count2=$stock->count();
       if($count2>0){
        foreach($stock as $value){
            $worth=array();
            $unpu=array();
            $publish=array();
        $worth=DB::table('stocks')
        ->select(DB::raw('SUM(stocks.quantity*stocks.price) as worth'))
        ->where('stocks.quantity','>','0')
        ->where('stocks.user_id','=',$id)
        ->get();
        foreach($worth as $worth2){
                        $worth['worth']=$worth2->worth;
                        
        }
        $unpu=array();
         $unpu=DB::table('stocks')
        ->select(DB::raw('COUNT(stocks.status) as unpublished'))
        ->where('stocks.status','=','0')->where('stocks.quantity','>','0')->where('stocks.user_id','=',$id)
         ->get();
         foreach($unpu as $unpublished){
            $unpu['unpublished']=$unpublished->unpublished;
         }
         $publish=array();
        $publish=DB::table('stocks')
        ->select(DB::raw('COUNT(stocks.status) as published'))
        ->where('stocks.status','=','1')->where('stocks.quantity','>','0')->where('stocks.user_id','=',$id)
         ->get();
         foreach($publish as $published){
            $publish['published']=$published->published;
            
          }
            $res['stockid']=$value->stockid;
            $res['farmid']=$value->farmid;
            $res['cropfarmID']=$value->cropfarmID;
            $res['crops']=$value->crops;
            $res['photo'] = $value->photo;
            $res['UPI']=$value->UPI;
            $res['price']=$value->current_price;
            $res['status']=$value->status;
            $res['quantity'] = $value->quantity;
            $res['created_at']=$value->created_at;
            $res['updated_at']=$value->updated_at;
            $result[]=$res;
    }
    $both ["stock"]= $result;
    $both["worth"] = $worth2->worth;
    $both["unpublished"] = $unpublished->unpublished;
    $both["published"] = $published->published;
        $count=$stock->count();
      if($count>0){
     return response()->json(['Message'=>'Success','Data'=>$both,'Status'=>200,'Returned_data'=>$count]); //
   }}
   else{
       return response()->json(['Message'=>'No Data','Status'=>200,'Returned_data'=>$count2]);
   }
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
            'cropfarmID' => 'required|integer',
            'quantity' => 'required',
            'price'=>'required|integer',
            'status'=>'required|integer'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'data' => 'Validation Error.',
                'message' => $validator->errors()
            ];
            return response()->json($response, 404);
        }
        $id=new farm();
        $id=$request->farmID;
        $id2=$request->cropfarmID;
        $select=DB::table('farms')->where('farms.id','=',$id)->where('farms.status','=','0')->get();
        $count=$select->count();
        if($count>0){
        $farms=new farm();
        $farms=DB::table('farms')->where('id','=',$id)->update(array('status'=>'1'));
        $ex=new expense;
        $ex=DB::table('expenses')->where('farmID','=',$id)->update(array('status'=>'0'));
        $ex=DB::table('cropfarms')->where('id','=',$id2)->update(array('status'=>'0'));
        $stock = new stock();
        $stock->cropfarmID = $request->cropfarmID;
        $stock->quantity = $request->quantity;
        $stock->price = $request->price;
        $stock->status=$request->status;
     
        if ($this->user->stocks()->save($stock)){
            return response()->json(['Message' =>'Kept well in stock','Status'=>200],200);
        }
    }else{
            return response()->json(['Message' =>'You dont have harvest','Status' => 200],200);
        }
     
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $stk=new stock();
        $id=$request->stocid;
        $stock=$this->user
        ->stocks()
        ->join('users','stocks.user_id','=','users.id')
        ->join('cropfarms','cropfarms.id','stocks.cropfarmID')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->join('seasons','seasons.id','=','cropfarms.seasonID')
        ->select('stocks.id as stockid','farms.id as farmid','crops.photo','stocks.cropfarmID','crops.crops','farms.UPI','seasons.seasonLenght','stocks.quantity','stocks.price as current_price','stocks.status')
        ->where('stocks.id','=',$id)
        ->get();
        $count=$stock->count();
      if($count>0){
     return response(['Message'=>'Success','Data'=>$stock,'Status'=>200,'Returned_data'=>$count]); //
   }else{
   return response(['Message'=>'Success','Data'=>$stock,'Status'=>200,'Returned_data'=>$count]);
  }

        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * function that will use to check profit you may get in single haverst
     */
    public function Profit(Request $request)
    {
    $result=array();
    $both=array();
    $stock=$this->user
      ->stocks() 
      ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
      ->join('crops','crops.id','=','cropfarms.cropsID')
      ->join('orders','orders.stockID','=','stocks.id')
      ->join('expenses','expenses.cropfarmID','=','cropfarms.id')
      ->select(DB::raw('SUM(orders.quantity*stocks.price) as Total_amount_of_harvest,crops.id as cropid,crops.photo,crops.crops as cropname,orders.status'))
        ->where('orders.status','=','3')
        ->groupBy('crops.id','crops.photo','crops.crops','orders.status')
        ->where('cropfarms.status','=','0')
        //->where('expenses.status','=','0')
        ->get();
         $count2=$stock->count();
       if($count2>0){
        foreach($stock  as $value){
            //deal with expenses
            
            $sales=array();
            
           
            $sales=$this->user
            ->stocks()
            ->join('orders','orders.stockID','=','stocks.id')
            ->join('cropfarms','stocks.cropfarmID','=','cropfarms.id')
            ->join('crops','crops.id','=','cropfarms.cropsID')
            ->join('farms','farms.id','=','cropfarms.farmID')
            ->select(DB::raw('SUM(stocks.price*orders.quantity) as sales'))
            ->where('orders.status','=','3')
            ->get();
            foreach($sales as $sales2){
                $sales=$sales2->sales;
            }
            $ex22=array();
            $ex22=$this->user
            ->expenses()
            ->join('cropfarms','cropfarms.id','=','expenses.cropfarmID')
            ->select(DB::raw('SUM(expenses.moneySpent) as moneySpent2'))
            ->where('expenses.status','=','0')
            ->get();
            foreach($ex22 as $val23){
                $ex22['res']=$val23->moneySpent2; 
            } 
            $exp=array();
            $exp=$this->user
            ->stocks()
            ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
            ->join('crops','crops.id','=','cropfarms.cropsID')
            ->join('expenses','expenses.cropfarmID','=','cropfarms.id')
            ->select( DB::raw('SUM(expenses.moneySpent) as total_expenses'))
              ->where('expenses.status','=','0')
             ->groupBy('crops.id')
            ->get();
            foreach($exp as $expense){
              $exp[]=$expense->total_expenses;
         //return $exp;
            }
            $res['cropid']=$value->cropid;
            $res['photo']=$value->photo;
            $res['cropname']=$value->cropname;
            $res['total_expenses']=$expense->total_expenses;
            $res['Total_amount_of_harvest']=$value->Total_amount_of_harvest;
            $res['profit']=$value->Total_amount_of_harvest-$expense->total_expenses;
            $result[]=$res;
        } 
        $both['crops']=$result;
        $both['expenses']=$val23->moneySpent2;
        $both['sales']=$sales2->sales;
        $both['profit']=$sales2->sales-$val23->moneySpent2;
        
        $count=$stock->count();
        if($count>0){
        return response(['Message'=>'Success','Data'=>$both,'Status'=>200,'Returned_data'=>$count]); //
       }}
       else{
       return response(['Message'=>'Success','Data'=>'No Data','Status'=>200,'Returned_data'=>$count]);
      }
   
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
        $stk=new stock();
        $id=$request->stocid;
        $stock = $this->user->stocks()->find($id);
        if (!$stock) {
            return response()->json([
                'Status' => 400,
                'message' => 'Sorry, stock with id ' . $id . ' cannot be found'
            ], 400);
        }
        $stock->cropfarmID = $request->cropfarmID;
        $stock->quantity = $request->quantity;
        $stock->price=$request->price;
        $stock->status = $request->status;
        $stock->save();
        return response()->json(['Message' =>'stock Updated','Status' => 200],200);
    
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * function for publishing haverst
     */
    public function publish(Request $request){
       
        $stk=new stock();
        $id=$request->stocid;
        $status=$request->status;
        $publish= DB::table('stocks')->where('stocks.id','=',$id)->update(array('stocks.status'=>$status));
        if($status==0){
        return response()->json(['Message' =>'Unpublished','Status'=>200],200);
        }else{
            return response()->json(['Message' =>'Published','Status'=>200],200);
        }
    }
    /**
     * function will show all feeds available with anoth some info about those feeds
     */
    public function feeds(){
        $stock=$this->user
        ->stocks()
        ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->select('crops.id as cropid','crops.crops as cropname','crops.photo')
        ->groupBy('crops.id','crops.crops','crops.photo')
        ->get();
        $count=$stock->count();
        if($count>0){
            return response()->json(['Message'=>'Success','Data'=>$stock,'Returned_Data'=>$count,'Status'=>200]);
        } else{
            return response()->json(['Message'=>'Success','Data'=>$stock,'Returned_Data'=>$count,'Status'=>200]);
        }
    }

    public function insideFeed(Request $request){
        $id=$request->cropid;
        $result=array();
        $crop=array();
        $crop=$this->user
        ->stocks()
        ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
       //->join('orders','orders.stockID','=','stocks.id')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->select('crops.crops','crops.photo',DB::raw('SUM(stocks.quantity) as haverst'))
        ->where('crops.id','=',$id)
        ->groupBy('crops.crops','crops.photo')
        ->get();
        foreach($crop as $result2){
            $stock=array();
            $stock=$this->user
            ->stocks()
            ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
            ->join('orders','orders.stockID','=','stocks.id')
            ->join('crops','crops.id','=','cropfarms.cropsID')
            ->select(DB::raw('SUM(orders.quantity) as quantity'))
            ->where('crops.id','=',$id)
            ->get();
            foreach($stock as $value){
                $stock=$value->quantity;
            }
            
            $data['cropname']=$result2->crops;
            $data['photo']=$result2->photo;
            $data['harvest']=$result2->haverst+$value->quantity;
            $result[]=$data;
        }
        
        $count=$crop->count();
        if($count>0){
            return response()->json(['Message'=>'Success','Data'=>$result,'Data_returned'=>$count,'Status'=>200]);
        }else{
            return response()->json(['Message'=>'Success','Data'=>$crop,'Data_returned'=>$count,'Status'=>200]);
        }
    }
}
