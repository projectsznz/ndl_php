<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\RouteAssignModel;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\ApartmentModel;
use App\Models\JourneyLogModel;
use App\Models\RouteMappingModel;
use App\Models\RouteMasterModel;
use App\Models\User;
use App\Models\VehicleModel;
use App\Models\WastageLogModel;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use DB;
use Exception;

class RouteAssigningController extends BaseController
{
    //
    public function list(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
         
            $route =  RouteAssignModel::select("route_assign.*",
            "route_master.name as route_master_name" ,
            "route_master.unload_point as route_master_unload_point" ,
            "users.type as usertype",
            "users.name as driver_name",
            "users.email as driver_email",
            "users.photo as driver_photo",
            "vehicles.vehicle_name as vehicle_name",
            "vehicles.vehicle_number as vehicle_number",
            "vehicles.fuel_type as fuel_type",
            "vehicles.vehicle_type as vehicle_type",
            "route_mapping.apartment_id"
            
        
        )
        ->leftjoin("route_master","route_assign.route_master_id","route_master.id")
        ->leftjoin("route_mapping","route_mapping.route_id","route_assign.route_master_id")
        ->leftjoin("users","route_assign.driver_id","users.id") 
        ->leftjoin("vehicles","route_assign.vehicle_id","vehicles.id") ;
           
            $response       =  $route->paginate($paginatePerPage) ;;
            $cnt = 0; 

            foreach($response as $res)
            {
                $response[$cnt]['apartment'] =   ApartmentModel::select('apratments.*','route_mapping.priority')->leftjoin("route_mapping","route_mapping.apartment_id","apratments.id")->where([['route_mapping.route_id','=',$res['route_master_id']],["route_mapping.status",'=','0']])->orderBy('route_mapping.priority', 'asc')->get(); 
                
                $cnt++;
            }
         
            return $this->sendResponse($response,"Data retrieved Successfully");

        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }

    }
    public function listNoPagination(Request $request)
    {
        try{
             

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
            $route =  RouteAssignModel::select("route_assign.*",
                        "route_master.name as route_master_name" ,
                        "route_master.unload_point as route_master_unload_point" ,
                        "users.type as usertype",
                        "users.name as driver_name",
                        "users.email as driver_email",
                        "users.photo as driver_photo",
                        "vehicles.vehicle_name as vehicle_name",
                        "vehicles.vehicle_number as vehicle_number",
                        "vehicles.fuel_type as fuel_type",
                        "vehicles.vehicle_type as vehicle_type",
             
                    
                    )
            ->leftjoin("route_master","route_assign.route_master_id","route_master.id")
            ->leftjoin("users","route_assign.driver_id","users.id") 
            ->leftjoin("vehicles","route_assign.vehicle_id","vehicles.id") ;
        
           
            $response  = $route->get();
            $cnt = 0; 

            foreach($response as $res)
            {
                $response[$cnt]['apartment'] =   ApartmentModel::select('apratments.*')->leftjoin("route_mapping","route_mapping.apartment_id","apratments.id")->where([['route_mapping.route_id','=',$res['route_master_id']],["route_mapping.status",'=','0']])->get(); 
                
                $cnt++;
            }
    
                return $this->sendResponse($response,"Data retrieved Successfully");

            }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }

    } 
    public function  getDriverAssignedRoute(Request $request)
    {
        try{
             $driverId          =  auth('sanctum')->user()->id; 
            $date =  $request->date;
             
            
            $rules = [                  
                'date'              =>  'required|date',  
                
            ];

            $message =  [                          
                'date.required'     => 'Please enter valid date',  
            ];

          
            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }

       //   return  $wastageLog =  WastageLogModel::where([['date','=',$date]])->get();
            $route =  RouteAssignModel::select("route_assign.*",
                        "route_master.name as route_master_name" ,
                        "route_master.unload_point as route_master_unload_point" ,
                        "users.type as usertype",
                        "users.name as driver_name",
                        "users.email as driver_email",
                        "users.photo as driver_photo",
                        "vehicles.vehicle_name as vehicle_name",
                        "vehicles.vehicle_number as vehicle_number",
                        "vehicles.fuel_type as fuel_type",
                        "vehicles.vehicle_type as vehicle_type",
             
                    
                    )
            ->leftjoin("route_master","route_assign.route_master_id","route_master.id")
            ->leftjoin("users","route_assign.driver_id","users.id") 
            ->leftjoin("vehicles","route_assign.vehicle_id","vehicles.id")
            ->where([["route_assign.completed_status","=","0"],["route_assign.status","=","0"],["route_assign.date","=",$date],['route_assign.driver_id','=',$driverId]]);
        
           
             $response  = $route->get();
            $cnt = 0; 

            foreach($response as $res)
            {
                $response[$cnt]['apartment'] =   ApartmentModel::select('apratments.*')->leftjoin("route_mapping","route_mapping.apartment_id","apratments.id")->where([['route_mapping.route_id','=',$res['route_master_id']],["route_mapping.status",'=','0']])->get(); 
                
                $cnt++;
            }
    
                return $this->sendResponse($response,"Data retrieved Successfully");

            }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }
    }
    
    public function  getDriverAssignedApartment(Request $request)
    {
        try{
             
            $date =  $request->date;
            $route_id =  $request->route_id;
            $driver_id =  $request->driver_id;
            $driverId          =  auth('sanctum')->user()->id;  

            
            $rules = [                  
                'date'          =>  'required|date', 
                'route_id'      =>  'required',  
             //   'driver_id'     =>  'required', 
                
            ];

            $message =  [                          
                'date.required'     => 'Please enter valid date',  
                'route_id.required'     => 'Please enter route_id',  
               // 'driver_id.required'     => 'Please enter driver_id',  
            ];

          
            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }

       
            $route =  RouteAssignModel::select("route_assign.*")
            ->leftjoin("route_master","route_assign.route_master_id","route_master.id")
            ->leftjoin("users","route_assign.driver_id","users.id") 
            ->leftjoin("vehicles","route_assign.vehicle_id","vehicles.id")
            ->where([
                ["route_assign.completed_status","=","0"],
                ["route_assign.status","=","0"],
                ["route_assign.driver_id","=",$driverId],
                ["route_assign.route_master_id","=",$route_id],
                ["route_assign.date","=",$date]
            ]);
        
           
            $response  = $route->get();
            $cnt = $totalAparmentMappedToRoute = $dataEnteredApartment= 0; 
            $responseApart = [];

            $wastageLogEntry = WastageLogModel::where([ ["wastage_log.driver_id","=",$driverId],
            ["wastage_log.route_master_id","=",$route_id],
            ["wastage_log.date","=",$date]])->get()->pluck('apartment_id');

        /*    $responseApart['apartment']  = WastageLogModel::select("apratments.*","wastage.log.wastage_count","wastage.log.date as wastage_log_date")
            ->leftjoin("apratments","apratments.id","wastage_log.apartment_id")
            ->where(
                [ 
                    ["wastage_log.driver_id","=",$driverId],
                    ["wastage_log.route_master_id","=",$route_id],
                    ["wastage_log.date","=",$date]
                ])->get() ;
*/
            $dataEnteredApartment = count($wastageLogEntry);
            $responseApart['wastage_log_added_apartment_count'] = $dataEnteredApartment;
            $totalAparmentMappedToRoute =    RouteMappingModel::where([
                ['route_mapping.route_id','=',$route_id],
                ["route_mapping.status",'=','0']
            ])->count();
            $responseApart['total_apartment_count'] = $totalAparmentMappedToRoute;
           
            if(sizeof($response))
            {
                foreach($response as $res)
                {
                    $responseApart['apartment'] =   ApartmentModel::select('apratments.*')
                    ->leftjoin("route_mapping","route_mapping.apartment_id","apratments.id")
                    ->where([
                        ['route_mapping.route_id','=',$res['route_master_id']],
                        ["route_mapping.status",'=','0']
                        ])
                        ->whereNotIn('route_mapping.apartment_id', $wastageLogEntry)
                        ->limit(1)
                    ->get(); 
                    
                    $cnt++;
                }
                

            }
            else
            {
                $responseApart['apartment'] = [];
            }

            
            
    
                return $this->sendResponse(array($responseApart),"Data retrieved Successfully");

            }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }
    }
    public function checkOpenJourney(Request $request)
    {
        $driverId          =  auth('sanctum')->user()->id;  
        $rules = [    
            'date'          =>  'required|date',
            'route_id'      =>  'required|integer',  
           // 'driver_id'     =>  'required|integer',    
        ];

        $message =  [                
            'date.required'         => 'Please enter date',   
            'route_id.required'     => 'Please select route id',   
           // 'driver_id.required'    => 'Please select driver id',   
          
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if($validator->fails())
        {
            return $this->sendError($validator->errors()->first());
        }

         $checkAlreadyEntry =  JourneyLogModel::where([
            ['journey_log.route_id','=',$request->route_id],
              ['journey_log.date','=',$request->date],
              ['journey_log.driver_id','=',$driverId],
            ['journey_log.journey_status','=','0'] 
        ])->limit(1)->get();
         $checkAlreadyEntryCount =  sizeof($checkAlreadyEntry);
         if($checkAlreadyEntryCount>0)
        {
            $message= "You have Open Journey - ".$request->date;
            $journey_count = "1";
            $journey_log_id= $checkAlreadyEntry[0]->id;
            $journey_log_vehicle_id= $checkAlreadyEntry[0]->vehicle_id;
        }
        else
        {
            $message= "No Open Journey for the date - ".$request->date;
            $journey_count = "0";
            $journey_log_id= "0";
            $journey_log_vehicle_id="0";
        }
        $array =array(
                    "is_open_journey"=>$journey_count,
                    "journey_log_id"=>$journey_log_id,
                    "message" => $message,
                     "vehicle_id" =>$journey_log_vehicle_id
                );

        return $this->sendResponse($array,"Data retrieved Successfully");
    }
    public function  getCompletedApartmentList(Request $request)
    {
        try{
             
            $date =  $request->date;
            $route_id =  $request->route_id;
           
            $driverId          =  auth('sanctum')->user()->id;  

            
            $rules = [                  
                'date'          =>  'required|date', 
                'route_id'      =>  'required',  
             //   'driver_id'     =>  'required', 
                
            ];

            $message =  [                          
                'date.required'     => 'Please enter valid date',  
                'route_id.required'     => 'Please enter route_id',  
               // 'driver_id.required'     => 'Please enter driver_id',  
            ];

          
            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }

        
            

            $wastageLogEntry = WastageLogModel::where([ ["wastage_log.driver_id","=",$driverId],
            ["wastage_log.route_master_id","=",$route_id],
            ["wastage_log.date","=",$date]])->get()->pluck('apartment_id');

            $responseApart  = WastageLogModel::select("apratments.*","wastage_log.wastage_count","wastage_log.date as wastage_log_date")
            ->leftjoin("apratments","apratments.id","wastage_log.apartment_id")
            ->where(
                [ 
                    ["wastage_log.driver_id","=",$driverId],
                    ["wastage_log.route_master_id","=",$route_id],
                    ["wastage_log.date","=",$date]
                ])->get() ;

            // $responseApart =   ApartmentModel::select('apratments.*') 
            // ->where([                
            //     ["apratments.status",'=','0']
            //     ])
            //     ->whereIn('apratments.id', $wastageLogEntry)
                 
            // ->get();
             
 
                return $this->sendResponse( ($responseApart),"Wastage Log added Apartment list - Data retrieved Successfully");

            }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }
    }
    public function create(Request $request)
    {
        try 
        {   
            $variable = ['MANUAL'];

            $rules = [  
                'mode'              => 'required|in:'.implode(",", $variable),  
                'route_master_id'   =>  'required|string',
                
                'vehicle_id'        =>  'required|string',       
                'driver_id'         =>  'required|string', 
                'date'              =>  'required|date',        
                'status'            =>  'required|integer|min:0|max:1',
                
            ];

            $message =  [  
                'mode.required'             => 'Please enter mode',  
                'mode.in'                  => 'Please enter mode as MANUAL',              
                'route_master_id.required'  => 'Please select route master',   
                'vehicle_id.required'       => 'Please select vehicle',    
                'driver_id.required'        => 'Please select driver',                    
                'date.required'             => 'Please enter valid date',    
                'status.required'           => 'Please select status',   
            ];

          
            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }
            $dec_route_master_id = $dec_driver_id  = $dec_vehicle_id = 0;

            $route_master_id       = $request->input('route_master_id') ;
            $routeMasterCheck = RouteMasterModel::find($route_master_id);
            if(!isset($routeMasterCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - route_master_id ");
            }

           

            $driver_id       = $request->input('driver_id') ;
            $driverCheck = User::find($driver_id)->where([["type","=","2"]]);
            if(!isset($driverCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - driver_id ");
            }

          

            $vehicle_id       = $request->input('vehicle_id') ;
            $vehicleCheck = VehicleModel::find($vehicle_id);
            if(!isset($vehicleCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - vehicle_id ");
            }
            
            

            $inputData      =  [];
            $inputData['mode']              = $request->mode  ;
            $inputData['route_master_id']   = $route_master_id ;
            $inputData['driver_id']         = $driver_id ;
            $inputData['vehicle_id']        = $vehicle_id  ;
            $inputData['date']              = $request->date  ;
            $inputData['created_by']        =  auth('sanctum')->user()->id; 

            $result         =  RouteAssignModel::create($inputData);
            return $this->sendResponse($result,"New Route Assigning Created Successfully !");

        }
        catch (DecryptException $e) 
        {
             
                return $this->sendError("Not a valid enc_id.Please enter valid enc_id - vehicle_id ");
        }

            
    }
    public function edit(Request $request)
    {
        try 
        {
            $variable = ['MANUAL'];
            $rules = [    
                'id'                => 'required',
                'mode'              => 'required|in:'.implode(",", $variable), 
                'route_master_id'   =>  'required|string',
                'vehicle_id'        =>  'required|string',       
                'driver_id'         =>  'required|string', 
                'date'              =>  'required|date',        
                'status'            =>  'required|integer|min:0|max:1',
                
            ];

            $message =  [   
                'id.required'              => 'Please enter id', 
                'mode.required'             => 'Please enter mode',  
                'mode.in'                  => 'Please enter mode as MANUAL',                       
                'route_master_id.required'  => 'Please select route master',   
                'vehicle_id.required'       => 'Please select vehicle',    
                'driver_id.required'        => 'Please select driver',                    
                'date.required'             => 'Please enter valid date',    
                'status.required'           => 'Please select status',   
            ];

            
          
            $validator = Validator::make($request->all(), $rules, $message);

            
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }

            $id     =  $request->id;
            $deenc_id   = 0;
       
         
            $routeAssign =   RouteAssignModel::find($id);//

              if(!isset($routeAssign))
            {
                return $this->sendError("Not a valid id.Please enter valid id - id ");
            }
            $dec_route_master_id = $dec_driver_id  = $dec_vehicle_id = 0;
            $route_master_id       = $request->input('route_master_id') ;

            $routeMasterCheck = RouteMasterModel::find($route_master_id);
            if(!isset($routeMasterCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - route_master_id ");
            }

            

            $driver_id       = $request->input('driver_id') ;
            $driverCheck = User::find($driver_id)->where([["type","=","2"]]);
            if(!isset($driverCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - driver_id ");
            }
        

            $vehicle_id       = $request->input('vehicle_id') ;
            $vehicleCheck = User::find($vehicle_id);
            if(!isset($vehicleCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - vehicle_id ");
            }
             
            

            $inputData      =  [];
            $inputData['mode']              = $request->mode  ;
            $inputData['route_master_id']   = $route_master_id ;
            $inputData['driver_id']         = $driver_id ;
            $inputData['vehicle_id']        = $vehicle_id  ;
            $inputData['date']              = $request->date  ;
            $inputData['modified_by']       =  auth('sanctum')->user()->id; 
            $request->request->remove('id');
 
            $result         =  $routeAssign->update($inputData);
            $Details        =  RouteAssignModel::find($id);
            return $this->sendResponse($Details,"Route Assigning Updated Successfully !");

        }
        catch (DecryptException $e) 
        {
             
                return $this->sendError("Not a valid id.Please enter valid id - vehicle_id ");
        }

            
    }
    public function delete(Request $request)
    {
        try{
            // $enc_id     =  $request->enc_id;
            // $deenc_id   = 0;

       
            // try {
            //     if($enc_id !=null)
            //     {
            //         $deenc_id = \Crypt::decryptString($enc_id) ;
            //     }
    
            // } catch (DecryptException $e) {
            //     //
            //     return $this->sendError("Not a valid enc_id to be deleted.Please enter valid enc_id");
            // }
            $id     =  $request->id;    
            $route   =   RouteAssignModel::find($id);
            if(isset($route))
            {
                $route->delete();
                return $this->sendResponse([],"Route Assignment Deleted Successfully !");
            }
            else
            {
                return $this->sendError('No data available for the id passed');  
            }

          
        }
        catch (\Exception $e) {
            \Log::info($e);
            return $this->ExceptionError( $e );
        }
    }
}
