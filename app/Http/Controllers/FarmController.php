<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Http\Request;
use App\Farm;
use App\User;
use App\Cropfarm;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use App\Farmer;
use Validator;
use App\Crops;
use Notifiable;

class FarmController extends Controller
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
        $result = array();
        $user=$this->user = JWTAuth::parseToken()->authenticate();
        $userid=$user->id;
        $farm = Farm::where('user_id',$userid)->get();

        foreach($farm as $farms){
            $farmid = $farms->id;
            $farmcrop =array();
            $frm =array();
            //$farmcrops = Cropfarm::where('farmID',$farmid)->get();
            $farmcrops=DB::table('cropfarms')
            ->leftjoin('farms','farms.id','=','cropfarms.id')
            ->leftjoin('crops','crops.id','=','cropfarms.cropsID')
            ->select('farms.id as farmID','cropfarms.id as cropfarmID','crops.photo','crops.crops as cropname','farms.status','farms.UPI','farms.location','farms.user_id','farms.plotsize','farms.created_at','farms.updated_at')
            ->where('farmID',$farmid)->where('cropfarms.status','=','1')
            ->get();
            foreach($farmcrops as $frmcrop){
                $farmcrop['cropfarmID']=$frmcrop->cropfarmID;
                $farmcrop['cropimage'] = $frmcrop->photo;
                $farmcrop['cropname'] = $frmcrop->cropname;
               
                //return $farmcrops;
            }
            $frm['farmID']=$farms->id;
            $frm['UPI']=$farms->UPI;
            $frm['location'] = $farms->location;
             $frm['plotsize']=$farms->plotsize;
            $frm['Status'] = $farms->status;
            $frm['farmcrop']=$farmcrop;
            $result[]=$frm;
        }
      $count= $farm->count();
      if($count>0){
      return response()->json(['Message'=>'Success','Data'=>$result,'Status'=>200,'Returned_data'=>$count]); //
     } else{
     return response()->json(['Message'=>'Success','Data'=>$result,'Status'=>200,'Returned_data'=>$count]);
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
        $user=$this->user = JWTAuth::parseToken()->authenticate();
        $userid=$user->id;
        $validator=Validator::make($request->all(), [
            'UPI' => 'unique:farms',
            'location' => 'required',
            'plotsize'=>'required',
            
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 400,
                'data' => 'Validation Error.',
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $farmers= DB::table('farmers')->select('user_id')->where('user_id','=',$userid)->get()->count();
        if($farmers>0){
        $farms = new Farm();
        $farms->UPI = $request->UPI;
        $farms->location = $request->location;
        $farms->plotsize=$request->plotsize;
        if ($this->user->farms()->save($farms))
            return response()->json(['Message' =>'Farm Registered','farm' => $farms],200);
        else
            return response()->json([
                'Message' => 'Sorry, new farm not added',
                'Status'=>400,
            ], 400);
        }
        else{
            return response()->json(['Message' =>'Before you register your farms complete your profile','Status' => 200],200);
        }
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
   
        $result = array();
        $id=$request->farmid;
        $farm = Farm::where('id',$id)->get();

        foreach($farm as $farms){
            $farmid = $farms->id;
            $farmcrop =array();
            $frm =array();
            //$farmcrops = Cropfarm::where('farmID',$farmid)->get();
            $farmcrops=DB::table('cropfarms')
            ->leftjoin('farms','farms.id','=','cropfarms.farmID')
            ->leftjoin('seasons','seasons.id','=','cropfarms.seasonID')
            ->leftjoin('crops','crops.id','=','cropfarms.cropsID')
            ->select('farms.id as farmID','cropfarms.id as cropfarmID','seasons.seasonLenght','crops.photo','crops.crops as cropname','farms.status','farms.UPI','farms.location','farms.user_id','farms.plotsize','farms.created_at','farms.updated_at')
            ->where('farmID',$farmid)->where('cropfarms.status','=','1')
            ->get();
            foreach($farmcrops as $frmcrop){
                $farmcrop['cropfarmID']=$frmcrop->cropfarmID;
                $farmcrop['cropimage'] = $frmcrop->photo;
                $farmcrop['cropname'] = $frmcrop->cropname;
                $farmcrop['seasons']=$frmcrop->seasonLenght;
               
                //return $farmcrops;
            }
            $frm['farmID']=$farms->id;
            $frm['UPI']=$farms->UPI;
            $frm['location'] = $farms->location;
            $frm['plotsize']=$farms->plotsize;
            $frm['Status'] = $farms->status;
            $frm['farmcrop']=$farmcrop;
            $result[]=$frm;
        }
      $count= $farm->count();
      if($count>0){
      return response()->json(['Message'=>'Success','Data'=>$result,'Status'=>200,'Returned_data'=>$count]); //
     } else{
     return response()->json(['Message'=>'Success','Data'=>$result,'Status'=>200,'Returned_data'=>$count]);
     }
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
        $id=$this->user = JWTAuth::parseToken()->authenticate();
        $userid=$id->id;
        $farmid=new Farm();
        $id=$request->farmid;
        $farms = $this->user->farms()->find($id);
        

    $farms = $this->user->farms()->find($id);
    if (!$farms) {
        return response()->json([
            'Status' => 400,
            'message' => 'Sorry, farm with id ' . $id . ' cannot be found'
        ], 400);
    }
       
        $farms->UPI = $request->UPI;
        $farms->location = $request->location;
        $farms->plotsize=$request->plotsize;
        $farms->save();
    return response()->json(['Message' =>'farms Updated','Status'=>200],200);
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * function for storing new type of crops
     */
       /**
        * function for selecting current crops your working on
        */
       public function viewCrops(){
           //$crops=$this->user
           $crops=DB::table('crops')
           ->select('crops.id','crops.photo','crops.crops','crops.created_at','crops.updated_at')
           ->get();
           $count=$crops->count();
           if($count>0){
               return response()->json(['Message'=>'Success','Data'=>$crops,'Data_returned'=>$count,'Status'=>200],200);
           }else{
            return response()->json(['Message'=>'Success','Data'=>'There is no any kind of crops you have','Status'=>200],200);
           }
       }

       /**
        * function for updating crops once there some mistake occured during regigister it
        */
       public function updateCrops(Request $request){

        $validator = Validator::make($request->all(), [
            'cropid' => 'required|integer',
            ]);
            if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
            }
            $id = $request->get('cropid');
            $validfarmer = Crops::where('id','=',$id)->count();
            if ($validfarmer == 0) {
                $retunerror = array('Message'=>'this crops is currently unvailable','status'=>400);
            return response()->json($retunerror);
            }
            else{
               
                $imageurls = array();
                $existedrecords =Crops::where('id','=',$request->get('cropid'))->get();
            
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
    
    $crops =crops::find($id);
    if (!$crops) {
        return response()->json([
            
            'message' => 'Sorry, crops with id ' . $id . ' cannot be found',
            'Status' => 400
        ], 400);
    }
       
        $crops->photo = $image_url;
        $crops->crops=$request->crops;
        $crops->save();
        return response()->json(['Message' =>'crops Updated','Status'=>200],200);
        
    }
}
public function farmsUsed(Request $request){
    
    $result=array();
    $both=array();
    $id=$request->cropid;
    //$fm=DB::table('crops')->where('crops.id','=',$id)->get();
   
    $farms=$this->user
    ->farms()
    ->join('cropfarms','cropfarms.farmID','=','farms.id')
    ->join('crops','cropfarms.cropsID','=','crops.id')
    ->join('stocks','stocks.cropfarmID','=','cropfarms.id')
    ->select('farms.UPI','crops.crops',
    DB::raw('SUM(stocks.quantity) as quantity2') )
    ->where('crops.id','=',$id)
    ->groupBy('farms.UPI','crops.crops')
    ->get();
    if($farms->count()>0){
    foreach($farms as $value){
        $exp=array();
        $exp=$this->user
        ->stocks()
        ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
        ->join('crops','cropfarms.cropsID','=','crops.id')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->join('expenses','expenses.cropfarmID','=','cropfarms.id')
        ->select(DB::raw('SUM(expenses.moneySpent) as moneySpent'))
        ->where('expenses.status','=','0')
        ->where('crops.id','=',$id)
        ->get();
        foreach($exp as $expenses){
            $exp['wholeExpenses']=$expenses->moneySpent;
            //return $exp;
        }
       
        $hav=$this->user
            ->stocks()
            ->join('orders','orders.stockID','=','stocks.id')
            ->join('cropfarms','stocks.cropfarmID','=','cropfarms.id')
            ->join('crops','crops.id','=','cropfarms.cropsID')
            ->join('farms','farms.id','=','cropfarms.farmID')
            ->select(DB::raw('SUM(stocks.price*orders.quantity) as sales'),
            DB::raw('SUM(orders.quantity) as sold_unity'),DB::raw('SUM(stocks.quantity) as quantity3'))
            ->where('orders.status','=','3')->where('crops.id','=',$id)
        ->get();
        foreach($hav as $harvest){
            //$hav['harverst']=$harvest->quantity3;
            //$hav['sold_unit']=$harvest->sold_unity;
            $hav['sales']=$harvest->sales;
            //return $hav;
        }
        $expns=$this->user
        ->farms()
        ->join('cropfarms','cropfarms.farmID','=','farms.id')
        ->join('crops','cropfarms.cropsID','=','crops.id')
        ->join('stocks','stocks.cropfarmID','=','cropfarms.id')
        ->leftjoin('expenses','expenses.cropfarmID','=','cropfarms.id')
        ->select('farms.UPI','crops.crops',
        DB::raw('SUM(stocks.quantity) as quantity3')
        ,DB::raw('SUM(expenses.moneySpent) as expens2') )
        ->where('crops.id','=',$id)
        ->groupBy('farms.UPI','crops.crops')
        ->get();
        foreach($expns as $val){
        }
        $farm['cropname']=$value->crops;
        $farm['UPI']=$value->UPI;
        $farm['expenses']=$val->expens2;
        $farm['quantity']=$value->quantity2;
        $result[]=$farm;
    }
    $both['farms']=$result;
    $both['Total_expenses']=$expenses->moneySpent;
    $both['haverst_quantity_kg']=$harvest->quantity3;
    $both['Sold_unity_kg']=$harvest->sold_unity;
    $both['Soles_money']=$harvest->sales;
    $both['profit']=$harvest->sales-$expenses->moneySpent;
    $count=$farms->count();
    if($count>0){
        return response()->json(['Message'=>'Success','Data'=>$both,'Returned_Data'=>$count,'Status'=>200]);
    }else{
        return response()->json(['Message'=>'Success','Data'=>$both,'Returned_Data'=>$count,'Status'=>200]);
    }}else{
        return response()->json(['Message'=>'No data','Status'=>200]);
    }
}


public function insideProfile(){
    $sum=array();
    $result=array();
    $farms=$this->user
      ->stocks() 
      ->join('orders','orders.stockID','=','stocks.id')
      ->join('cropfarms','cropfarms.id','=','stocks.cropfarmID')
      ->lefjoin('expenses','expenses.cropfarmID','=','cropfarms.id')
      ->join('crops','crops.id','=','cropfarms.cropsID')
      ->select(DB::raw('SUM(orders.quantity*stocks.price) as Total_amount_of_harvest,crops.id as cropid,crops.photo,crops.crops as cropname'))
        ->where('orders.status','=','3')
        ->where('expenses.status','=',0)
        ->groupBy('crops.id','crops.photo','crops.crops')
    ->get();
     $count=$farms->count();
    if($count>0){
        return response()->json(['Message'=>'Success','Data'=>$farms,'Returned_Data'=>$count,'Status'=>200]);
    }else{
        return response()->json(['Message'=>'Success','Data'=>$farms,'Returned_Data'=>$count,'Status'=>200]);
    }
}
}
