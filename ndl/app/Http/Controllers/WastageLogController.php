<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\ApartmentModel;
use App\Models\RouteMasterModel;
use App\Models\User;
use App\Models\VehicleModel;
use App\Models\WastageLogModel;
use App\Models\WastageMasterModel;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class WastageLogController extends BaseController
{
    //
    public function list(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
         
            $wastagelog =  WastageLogModel::select("wastage_log.*",
                          
                            "vehicles.vehicle_name as vehicle_name",
                            "vehicles.vehicle_number as vehicle_number",
                            "vehicles.fuel_type as fuel_type",
                            "vehicles.vehicle_type as vehicle_type",
                            "users.type as usertype",
                            "users.name as driver_name",
                            "users.email as driver_email",
                            "users.photo as driver_photo",
                            
                            "apratments.name as apratments_name",
                            "apratments.address as apratments_address",
                            "apratments.area as apratments_area",
                            "apratments.email as apratments_email",
                            "apratments.lat as apratments_lat",
                            "apratments.lng as apratments_lng",                     
                            "apratments.contact as apratments_contact",
                            "apratments.whatsapp as apratments_whatsapp",
                            "route_master.name as route_master_name" ,
                            "route_master.unload_point as route_master_unload_point" ,
                            "wastage_master.name as wastage_name" ,
                            "wastage_master.id as wastage_id" ,
                            )->where([["wastage_log.status","=","0"]]) 
                         ->leftjoin("vehicles","wastage_log.vehicle_id","vehicles.id")
                         ->leftjoin("users","users.id","wastage_log.driver_id")
                         ->leftjoin("apratments","apratments.id","wastage_log.apartment_id")
                         ->leftjoin("route_master","route_master.id","wastage_log.route_master_id")
                         ->leftjoin("wastage_master","wastage_master.id","wastage_log.wastage_id");
                          
           
            $response       =  $wastagelog->paginate($paginatePerPage) ;;
          
         
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
         
            $wastagelog =  WastageLogModel::select("wastage_log.*",
                          
            "vehicles.vehicle_name as vehicle_name",
            "vehicles.vehicle_number as vehicle_number",
            "vehicles.fuel_type as fuel_type",
            "vehicles.vehicle_type as vehicle_type",
            "users.type as usertype",
            "users.name as driver_name",
            "users.email as driver_email",
            "users.photo as driver_photo",
            
            "apratments.name as apratments_name",
            "apratments.address as apratments_address",
            "apratments.area as apratments_area",
            "apratments.email as apratments_email",
            "apratments.lat as apratments_lat",
            "apratments.lng as apratments_lng",                     
            "apratments.contact as apratments_contact",
            "apratments.whatsapp as apratments_whatsapp",
            "route_master.name as route_master_name" ,
            "route_master.unload_point as route_master_unload_point" ,
            "wastage_master.name as wastage_name" ,
            "wastage_master.id as wastage_id" ,
            )->where([["wastage_log.status","=","0"]]) 
         ->leftjoin("vehicles","wastage_log.vehicle_id","vehicles.id")
         ->leftjoin("users","users.id","wastage_log.driver_id")
         ->leftjoin("apratments","apratments.id","wastage_log.apartment_id")
         ->leftjoin("route_master","route_master.id","wastage_log.route_master_id")
         ->leftjoin("wastage_master","wastage_master.id","wastage_log.wastage_id");
           
            $response       =  $wastagelog->get() ;;
          
         
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
                'vehicle_id'        =>  'required|string',
                'driver_id'         =>  'required|string',
                'apartment_id'      =>  'required|string', 
                'route_master_id'   =>  'required|string',
                'wastage_id'        =>  'required|string',
                'wastage_count'     =>  'required',
              //  'photo'             => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'date'              =>  'required|string',
               
                
            ];

            $message =  [                
                'vehicle_id.required'       => 'Please select vehicle',
                'driver_id.required'        => 'Please select driver',
                'apartment_id.required'     => 'Please select apartment',
                'route_master_id.required'  => 'Please select route',
                   
                'wastage_id.required'       => 'Please select wastage type',   
                'wastage_count.required'    => 'Please enter wastage count',   
              //  'photo.required'            => 'Please select photo',   
                'date.required'             => 'Please enter date',   
                   
            ];
            $vehicle_id     = $request->input('vehicle_id') ;             
            $vehicleCheck   = VehicleModel::find($vehicle_id);
            if(!isset($vehicleCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - vehicle_id ");
            }

            $driver_id       = $request->input('driver_id') ;
            $driverCheck = User::find($driver_id)->where([["type","=","2"]]);
            if(!isset($driverCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - driver_id ");
            }

            $apartment_id       = $request->input('apartment_id') ;             
            $apartmentCheck = ApartmentModel::find($apartment_id);
            if(!isset($apartmentCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - apartment_id ");
            }

            $wastage_id     = $request->input('wastage_id') ;
            $wastageCheck   = WastageMasterModel::find($wastage_id);
            if(!isset($wastageCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - wastage_id ");
            }

            $route_master_id       = $request->input('route_master_id') ;
            $routeMasterCheck = RouteMasterModel::find($route_master_id);
            if(!isset($routeMasterCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - route_master_id ");
            }


            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }
            else
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
                $requestData['created_by']   =  auth('sanctum')->user()->id;
                $result         =  WastageLogModel::create($requestData);
                return $this->sendResponse($result,"New Wastage Log Created Successfully !");
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

            $id     =  $request->id;
            $wastagecount =   WastageLogModel::find($id);//
            if(isset($wastagecount))
            {
                

                $rules = [   
                    'vehicle_id'        =>  'required|string',
                    'driver_id'         =>  'required|string',
                    'apartment_id'      =>  'required|string', 
                    'route_master_id'   =>  'required|string',
                    'wastage_id'        =>  'required|string',
                    'wastage_count'     =>  'required',
                 //   'photo'         => 'required|image|mimes:jpeg,png,jpg|max:5120',
                    'date'              =>  'required|string',
                
                    
                ];

                $message =  [                
                    'vehicle_id.required'       => 'Please select vehicle',
                    'driver_id.required'        => 'Please select driver',
                    'apartment_id.required'     => 'Please select apartment',
                    'route_master_id.required'  => 'Please select route',
                    
                    'wastage_id.required'       => 'Please select wastage type',   
                    'wastage_count.required'    => 'Please enter wastage count',   
                  //  'photo.required'            => 'Please select photo',   
                    'date.required'             => 'Please enter date',   
                    
                ];
                $vehicle_id     = $request->input('vehicle_id') ;             
                $vehicleCheck   = VehicleModel::find($vehicle_id);
                if(!isset($vehicleCheck))
                {
                    return $this->sendError("Not a valid id.Please enter valid id - vehicle_id ");
                }

                $driver_id       = $request->input('driver_id') ;
                $driverCheck = User::find($driver_id)->where([["type","=","2"]]);
                if(!isset($driverCheck))
                {
                    return $this->sendError("Not a valid id.Please enter valid id - driver_id ");
                }

                $apartment_id       = $request->input('apartment_id') ;             
                $apartmentCheck = ApartmentModel::find($apartment_id);
                if(!isset($apartmentCheck))
                {
                    return $this->sendError("Not a valid id.Please enter valid id - apartment_id ");
                }

                $wastage_id     = $request->input('wastage_id') ;
                $wastageCheck   = WastageMasterModel::find($wastage_id);
                if(!isset($wastageCheck))
                {
                    return $this->sendError("Not a valid id.Please enter valid id - wastage_id ");
                }

                $route_master_id       = $request->input('route_master_id') ;
                $routeMasterCheck = RouteMasterModel::find($route_master_id);
                if(!isset($routeMasterCheck))
                {
                    return $this->sendError("Not a valid id.Please enter valid id - route_master_id ");
                }


                $validator = Validator::make($request->all(), $rules, $message);
                if($validator->fails())
                {
                    return $this->sendError($validator->errors()->first());
                }
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
                    $requestData['modified_by']   =  auth('sanctum')->user()->id;  
                    $result         =  $wastagecount->update($requestData);
                    $Details        =  WastageLogModel::find($id);
                    return $this->sendResponse($Details,"Wastage Log Details Updated !");

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
             
            $id             =  $request->id;
            $wastagecount   =   WastageLogModel::find($id);
            if(isset($wastagecount))
            {
                $wastagecount->delete();
                return $this->sendResponse([],"Wastage Log Deleted Successfully !");
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

