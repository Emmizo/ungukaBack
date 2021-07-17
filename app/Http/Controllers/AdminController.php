<?php

namespace App\Http\Controllers;
use App\Http\Requests\RegisterAuthRequest;
use App\User;
use JD\Cloudder\Facades\Cloudder;
use App\stock;
use Illuminate\Http\Request;
use App\customer;
use App\order;
use App\Crops;
use App\farmer;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use App\expense;
use Illuminate\Support\Facades\Hash;
use Validator;
use Notifiable;

class AdminController extends Controller
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
        $farmers=new farmer();
        $farmers=DB::table('farmers')
        ->leftjoin('users','users.id','=','farmers.user_id')
        ->select('users.id','farmers.photo','farmers.fname','farmers.lname','farmers.phone','farmers.identity','users.status','farmers.created_at','farmers.updated_at')
        ->where('users.status','=','1')->orWHere('users.status','=','2')
        ->get();
        $count=$farmers->count();
        if($count>0){
        return response()->json(['Message'=>'Success','Data'=>$farmers,'Status'=>200,'Data_returned'=>$count]);
        }
        else{
            return response()->json(['Message'=>'Success','Data'=>$farmers,'Status'=>200,'Data_returned'=>$count]); 
        }

        //
    }
    /**
     * function will use to retrieve all customers available
     */
public function allCustomers(){
    $customers=new customer();
    $customers=DB::table('customers')
    ->leftjoin('users','users.id','=','customers.user_id')
    ->select('users.id','customers.photo','customers.fname','customers.lname','customers.phone','customers.identity','users.status','customers.created_at','customers.updated_at')
    ->where('users.status','=','1')
    ->orwhere('users.status','2')
    ->get();
    $count=$customers->count();
    if($count>0){
    return response()->json(['Message'=>'Success','Data'=>$customers,'Status'=>200,'Data_returned'=>$count]);
    }
    else{
        return response()->json(['Message'=>'Success','Data'=>$customers,'Status'=>200,'Data_returned'=>$count]); 
    }

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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function farmerWithFarms(Request $request){
        $data=new farmer();
        $id=$request->farmerid;
        $data=DB::table('farms')
        ->join('users','users.id','=','farms.user_id')
        ->join('farmers','users.id','=','farmers.user_id')
        ->select('farms.id','farmers.fname','farmers.lname','farmers.phone','farms.UPI','farms.plotsize','farms.location')
        ->where('farms.user_id','=',$id)
        ->get();
        $count=$data->count();
        if($count>0){
        return response()->json(['Message'=>'Success','Data'=>$data,'Status'=>200,'Returned_data'=>$count]);
    }else{
        return response()->json(['Message'=>'Success','Data'=>$data,'Status'=>200,'Returned_data'=>$count]);
    }
}
/**
 * retrieve crops
 */
public function showCrops(Request $request){
    $data=new crops();
    $data=DB::table('crops')
    ->select('crops.id','crops.photo','crops.crops')
    ->get();
    $count=$data->count();
    if($count>0){
    return response()->json(['Message'=>'Success','Data'=>$data,'Status'=>200,'Returned_data'=>$count]);
}
else{
    return response()->json(['Message'=>'Success','Data'=>$data,'Status'=>200,'Returned_data'=>$count]);
    }
}
/**
 * store types of crops
 */
public function storeCrops(Request $request)
{
    $validator=Validator::make($request->all(), [
        'photo' => 'required|image',
        'crops' => 'required',
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
   
    $crops = new crops();
    $crops->photo = $image_url;
    $crops->crops=$request->crops;
    $crops->save();
    return response()->json(['Message' =>'New crops registered','Status' => 200],200);
    }
/**
 * suspended farmers which are blocked but you may give again access
 */
    public function farmerSuspended(){
        $suspend=DB::table('farmers')
        ->join('users','users.id','=','farmers.user_id')
        ->select('users.id as userid','farmers.photo','farmers.fname','farmers.lname','farmers.identity','farmers.phone','users.status')
        ->where('users.status','=','2')
        ->get();
        $count=$suspend->count();
        if($count>0){
            return response()->json(['Message'=>'Success','Data'=>$suspend,'Returned_data'=>$count,'Status'=>200]);
        }else{
            return response()->json(['Message'=>'Success','Data'=>$suspend,'Returned_data'=>$count,'Status'=>200]);
        }
    }
    /**
     * allow farmer access after to be blocked
     */
    public function allowAccess(Request $request){
        $id=$request->userid;
        $status=$request->status;
        $sel=DB::table('users')->where('users.id','=',$id)->get();
        $count=$sel->count();
        if($count>0 && $status==1 || $status==2 || $status==99){
            $access= DB::table('users')->where('users.id','=',$id)->update(array('users.status'=>$status));
            if($status==1)
            return response ()->json(['Message'=>'Unlocked','Status'=>200]);
        }if($status==2){
            return response()->json(['Message'=>'suspended','Status'=>200]);
        }if($status==99){
            return response()->json(['Message'=>'OFF','Status'=>200]);
        }
        else{
            return response()->json(['Message'=>'Unknown number you used','Status'=>400]);
        }
    }
    /**
     * update season
     */
public function updateSeason(Request $request){
    $id=$request->seasonid;
    $seasonname=$request->name;
    $status=$request->status;
    $update=DB::table('seasons')
    ->where('seasons.id','=',$id)
    ->update(array('seasons.seasonLenght'=>$seasonname,'status'=>$status));
    return response()->json(['Message'=>'season Updated','Status'=>200]);
}
}
