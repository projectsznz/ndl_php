<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\ApartmentModel;
use App\Models\RouteMappingModel;
use App\Models\RouteMasterModel;
use App\Models\JourneyLogModel;
use App\Models\RouteAssignModel;
use App\Models\User;
use App\Models\VehicleModel;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use DB;
class JourneyLogController extends BaseController
{
    //
    
    public function list(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
         
            $route =  JourneyLogModel::select("journey_log.*",
                        "users.type as usertype",
                        "users.name as driver_name",
                        "users.email as driver_email",
                        "users.photo as driver_photo",
                        "vehicles.vehicle_name as vehicle_name",
                        "vehicles.vehicle_number as vehicle_number",
                        "vehicles.fuel_type as fuel_type",
                        "vehicles.vehicle_type as vehicle_type",
                        DB::raw( "CASE WHEN journey_log.journey_status = 0 then 'Started' 
                          WHEN journey_log.journey_status = 1 then 'Completed' 
                         WHEN journey_log.journey_status = 2 then 'Paused' 
                         WHEN journey_log.journey_status = 3 then 'Not Completed Due to Reasons' 
                        else ''
                        end  as journey_status_name") ,
                         
                    )
                    ->leftjoin("route_master","route_master.id","journey_log.route_id")
                    ->leftjoin("vehicles","vehicles.id","journey_log.vehicle_id")
                    ->leftjoin("users","users.id","journey_log.driver_id") ;
           
            $response       =  $route->paginate($paginatePerPage) ;;
          
         
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
            $route =  JourneyLogModel::select("journey_log.*",
            "users.type as usertype",
            "users.name as driver_name",
            "users.email as driver_email",
            "users.photo as driver_photo",
            "vehicles.vehicle_name as vehicle_name",
            "vehicles.vehicle_number as vehicle_number",
            "vehicles.fuel_type as fuel_type",
            "vehicles.vehicle_type as vehicle_type",
            DB::raw( "CASE WHEN journey_log.journey_status = 0 then 'Started' 
              WHEN journey_log.journey_status = 1 then 'Completed' 
             WHEN journey_log.journey_status = 2 then 'Paused' 
             WHEN journey_log.journey_status = 3 then 'Not Completed Due to Reasons' 
            else ''
            end  as journey_status_name") ,
             
        )
        ->leftjoin("route_master","route_master.id","journey_log.route_id")
        ->leftjoin("vehicles","vehicles.id","journey_log.vehicle_id")
        ->leftjoin("users","users.id","journey_log.driver_id") ;
        
            $response       =  $route->get() ;;
          
         
            return $this->sendResponse($response,"Data retrieved Successfully");
         

        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }

    }
    public function create(Request $request)
    {
        try 
        {
            
            $rules = [    
                'date'          =>  'required|date',
                'route_id'      =>  'required',  
                'driver_id'     =>  'required',              
              //  'start_apartment_id'  =>  'required',  
          
                'lat'  =>  'required',
                'lng'  =>  'required',
                
           
            //    'status'            =>  'required|integer|min:0|max:1',
                
            ];

            $message =  [                
                'date.required'         => 'Please enter date',   
                'route_id.required'     => 'Please select route id',   
                'driver_id.required'    => 'Please select driver id',   
                //'start_apartment_id.required' => 'Please select start apartment id',   
                'lat.required'          => 'Please enter latitude',   
                'lng.required'          => 'Please select longitude',
                                
              //  'status.required'       => 'Please select status',   
            ];


            $route_id       = $request->input('route_id') ;
            $routeMasterCheck = RouteMasterModel::find($route_id);
            if(!isset($routeMasterCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - route_id ");
            }

            $driver_id       = $request->input('driver_id') ;
            $driverCheck = User::find($driver_id)->where([["type","=","2"]]);
            if(!isset($driverCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - driver_id ");
            }
            $apartment_id       = $request->input('start_apartment_id') ;
            $apartmentCheck   = ApartmentModel::find($apartment_id);
            if(!isset($apartmentCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - start_apartment_id ");
            }
            $vehicle_id     = $request->input('vehicle_id') ;             
            $vehicleCheck   = VehicleModel::find($vehicle_id);
            if(!isset($vehicleCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - vehicle_id ");
            }
            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }

             $checkAlreadyEntry =  JourneyLogModel::where([
                ['journey_log.route_id','=',$route_id],
                ['journey_log.date','=',$request->date],
                ['journey_log.driver_id','=',$driver_id],
                ['journey_log.journey_status','=','0'] 
            ])->count();
            if($checkAlreadyEntry==0)
            {
                $requestData    = $request->all();
                $requestData['journeyStartDate']    =date('Y-m-d H:i:s');
                $requestData['created_by']          =  auth('sanctum')->user()->id; 
                $result         =  JourneyLogModel::create($requestData);
                return $this->sendResponse($result,"New JourneyLog Created Successfully !");
            }
            else
            {
                return $this->sendError("Already Journey Started for the route_id - ".$route_id ." , driver_id - ".$driver_id.", date - ".$request->date );
            }
            
             
            
        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }
    }
    public function edit(Request $request)
    {
        try 
        {
        
            $id             =  $request->id;   
            $journeylog     =   JourneyLogModel::find($id);//
            $date= $request->date; 
            if(isset($journeylog))
            {     
                
                $rules = [    
                    'date'          =>  'required|date',
                    'route_id'      =>  'required',  
                    'driver_id'     =>  'required', 
                    'route_assign_id'=> 'required',             
                  // 'end_apartment_id'  =>  'required',  
                    'journey_enddate'  =>  'required|date_format:Y-m-d H:i:s',
                    'end_lat'  =>  'required',
                    'end_lng'  =>  'required',
                    'journey_status'=>  'required|integer',
                    'wastage_weight'=>  'required',
                    'wastage_measurement'=>'required'
                //    'status'            =>  'required|integer|min:0|max:1',
                    
                ];

                $message =  [                
                    'date.required'         => 'Please enter date',   
                    'route_id.required'     => 'Please select route id',   
                    'driver_id.required'    => 'Please select driver id',   
                    'route_assign_id.required'    => 'Please enter route_assign_id',   
                  //  'end_apartment_id.required' => 'Please select end apartment id', 
                    'journey_enddate.required' => 'Please enter   journey_enddate (Y-m-d H:i:s)',  
                    'journey_status'        =>'Please enter journey completion status',  
                    'end_lat.required'          => 'Please enter latitude',   
                    'end_lng.required'          => 'Please select longitude',
                    'wastage_weight.required'=>   'Please enter wastage weight',  
                    'wastage_measurement'    =>'Please enter wastage measurement'        
                //  'status.required'       => 'Please select status',   
                ];


                $route_id       = $request->input('route_id') ;
                $routeMasterCheck = RouteMasterModel::find($route_id);
                if(!isset($routeMasterCheck))
                {
                    return $this->sendError("Not a valid id.Please enter valid id - route_id ");
                }

                $driver_id       =  auth('sanctum')->user()->id;
                $driverCheck = User::find($driver_id)->where([["type","=","2"]]);
                if(!isset($driverCheck))
                {
                    return $this->sendError("Not a valid id.Please enter valid id - driver_id ");
                }
                // $apartment_id       = $request->input('end_apartment_id') ;
                // $apartmentCheck   = ApartmentModel::find($apartment_id);
                // if(!isset($apartmentCheck))
                // {
                //     return $this->sendError("Not a valid id.Please enter valid id - end_apartment_id ");
                // }
                
                $vehicle_id     = $request->input('vehicle_id') ;             
                $vehicleCheck   = VehicleModel::find($vehicle_id);
                if(!isset($vehicleCheck))
                {
                    return $this->sendError("Not a valid id.Please enter valid id - vehicle_id ");
                }
                
                $validator = Validator::make($request->all(), $rules, $message);
                if($validator->fails())
                {
                    return $this->sendError($validator->errors()->first()); 
                }
                $checkAlreadyEntry =  JourneyLogModel::where([
                    ['journey_log.route_id','=',$route_id],
                    ['journey_log.date','=',$request->date],
                    ['journey_log.driver_id','=',$driver_id],
                    ['journey_log.journey_status','=','0'] 
                ])->count();
                if($checkAlreadyEntry>0)
                {
                    $requestData    = $request->all();

                    if($request->hasFile('photo') && $request->hasFile('photo')==1)
                    {
                        $image = $request->file('photo');
                        $nameValidate = $image->getClientOriginalName();
                        $pattern ='/[\'^£$%&*()!}{@#~?><>,|=+¬]/';
            
                        if (preg_match($pattern,$nameValidate) != 0) {
                            return $this->sendError("Image name contains special characters are not allowed.");
                        }
            
                        $image = $request->file('photo');
                        $extension = strtolower($image->getClientOriginalExtension());
                        if (!in_array($extension, ['jpeg', 'png', 'jpg']) || !in_array(mime_content_type($_FILES['photo']['tmp_name']), ['image/jpeg', 'image/png', 'image/jpg'])) {
                            return $this->sendError('The upload image must be an image.');
                        }
            
                        // never assume the upload succeeded
                        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                            return $this->sendError('The upload image must be an image.');
                        }
            
                        $info = getimagesize($_FILES['photo']['tmp_name']);
            
                        if ($info === FALSE) {
                            return $this->sendError('The upload image must be an image.');
                        }
            
                        if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
                            return $this->sendError('The upload image must be an image.');
                        }
        
                        
                    
                            $extension = $request->file('photo')->getClientOriginalExtension(); 
                
                            $fileName = "uploads_".date('YmdHi') ."_". rand(11111, 99999) . '.' . $extension; 
                
                
                            $filePath = $request->file('photo')->storeAs('uploads', $fileName, 'public');
                        
                            $requestData['photo']    =  $filePath;
                
             
                    }
                    else
                    {    
                        $request->request->remove('photo');
                    }
                   
                    $requestData['journeyEndDate']    =$request->journey_enddate;
                    $requestData['modified_by']   =  auth('sanctum')->user()->id; 
                    $result         =  $journeylog->update($requestData);

                  $ra =   RouteAssignModel::where([
                    ["route_assign.completed_status","=","0"],
                    ["route_assign.status","=","0"],
                    ["route_assign.date","=",$date],
                    ['route_assign.driver_id','=',$driver_id],
                    ['route_assign.route_master_id','=',$request->route_id],
                    ['route_assign.vehicle_id','=',$request->vehicle_id],
                    ['route_assign.id','=',$request->route_assign_id],
                    ])->update(array("completed_status" =>"1"));
                   
                    
                    return $this->sendResponse($result," JourneyLog Updated Successfully !");
                }
                else
                {
                    return $this->sendError("Journey Already Completed for the route_id - ".$route_id ." , driver_id - ".$driver_id.", date - ".$request->date );
                }
                 
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
    public function delete(Request $request)
    {
        try{
            return $this->sendError('Journey Log Cannot be Deleted !');
            
          
        }
        catch (\Exception $e) {
            \Log::info($e);
            return $this->ExceptionError( $e );
        }
    }
}
