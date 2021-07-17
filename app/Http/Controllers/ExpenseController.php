<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Http\Requests\RegisterAuthRequest;
use App\User;
use Illuminate\Http\Request;
use App\Expense;
use App\Farm;
use App\Farmer;
use App\Season;
use App\Cropfarm;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use Validator;
use Notifiable;

class ExpenseController extends Controller
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
    public function index(Request $request)
    {
       
        $farms=new farm();
        $id=$request->farmid;
        $user=$this->user
        ->cropfarms()
        ->join('users','users.id','=','cropfarms.user_id')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->join('expenses','farms.id','=','expenses.farmID')
        ->join('seasons','seasons.id','=','cropfarms.seasonID')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        ->select('expenses.farmID','farms.id','crops.crops','farms.UPI',DB::raw('SUM(expenses.moneySpent) as total_expenses'))
        ->distinct('expenses.farmID')
        ->groupBy('expenses.farmID','farms.id','crops.crops','farms.UPI')
        ->where('cropfarms.status','=','1')
        ->where('farms.id','=',$id)->where('expenses.status','=','1')
        ->get();
       
$count=$user->count();
if($count>0){
    
return response()->json(['Message'=>'Success','Data'=>$user,'Status'=>200,'Returned_data'=>$count]); //
}else{
return response()->json(['Message'=>'Success','Data'=>$user,'Status'=>200]);
}
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view()
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
            'farmID' => 'required|integer',
            //'cropfarmID'=>'required|integer',
            'titles'=>'required',
            'description' => 'required',
            'moneySpent' =>'required|integer'
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 400,
                'data' => 'Validation Error.',
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $id=$request->farmID;
        $select=DB::table('farms')->where('id',$id)->where('farms.status','0')->get();
        if($select->count()>0){
        $expense = new Expense();
        $expense->farmID = $request->farmID;
        $expense->cropfarmID=$request->cropfarmID;
        $expense->titles=$request->titles;
        $expense->description = $request->description;
        $expense->moneySpent = $request->moneySpent;
     
        if ($this->user->expenses()->save($expense))
            return response()->json([
                'Message' => 'Success Expenses added',
                'Status' =>200
            ],200);
        else
            return response()->json([
                'message' => 'Sorry, expenses could not be added',
                'Status' => 400,
            ], 400);
        }else{
            return response()->json([
                'Message' => 'You are not allowed to add expenses',
                'Status' =>200
            ],200);
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
        $exp=new expense();
        $id=$request->farmid;
        $user=$this->user
        ->cropfarms()
        ->join('users','users.id','=','cropfarms.user_id')
        ->join('farms','farms.id','=','cropfarms.farmID')
        ->join('expenses','farms.id','=','expenses.farmID')
        ->join('seasons','seasons.id','=','cropfarms.seasonID')
        ->join('crops','crops.id','=','cropfarms.cropsID')
        
        ->select('expenses.id','crops.crops','farms.UPI','expenses.titles','expenses.description','expenses.moneySpent','expenses.created_at','expenses.updated_at')
        ->where('farms.id','=',$id)->where('cropfarms.status','=','1')->where('expenses.status','=','1')
        ->orderBy('expenses.id')
        ->get();
        if (!$user) {
            return response()->json([
                'Status' => 400,
                'message' => 'Sorry, Expense with id ' . $id . ' cannot be found'
            ], 400);
        }
      $count=$user->count();
      if($count>0){
     return response(['Message'=>'Success','Data'=>$user,'Status'=>200,'Returned_data'=>$count]); //
   }
   else{
   
    return response(['Message'=>'Success','Data'=>$user,'Status'=>200]);
   }
        //
    }

    public function newCrop(Request $request){

        $validator=Validator::make($request->all(), [
            'cropsID' => 'required',
            'farmID' => 'required',
            'seasonID'=>'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 400,
                'data' => 'Validation Error.',
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $id=new farm();
        $id=$request->farmID;
        $select=DB::table('farms')->where('farms.id','=',$id)->where('farms.status','=','1')->get();
        $count=$select->count();
        if($count>0){
        $id=DB::table('farms')->where('id','=',$id)->update(array('status'=>'0'));
        $add = new Cropfarm();
        $add->cropsID = $request->cropsID;
        $add->farmID=$request->farmID;
        $add->seasonID=$request->seasonID;
        if ($this->user->cropfarms()->save($add))
        return response()->json(['Message' =>'New crops registered','Status' => 200],200);
    else
        return response()->json([
            'message' => 'Sorry, new crops not added',
            'Status' => 400
        ], 400);
       
    }else{
        return response()->json(['Message' =>'This farms currently are used','Status' => 200],200);
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

}
