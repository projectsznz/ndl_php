<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\VehicleModel;
use App\Models\VehicleTrackModel;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class VehicleController   extends BaseController
{
    //
    public function list(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
         
            $vehicle        =  VehicleModel::where([["status","=","0"]]);
           
            $response       =  $vehicle->paginate($paginatePerPage) ;;
          
         
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
         
            $vehicle        =  VehicleModel::where([["status","=","0"]]);
           
            $response       =  $vehicle->get();
          
         
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
                'vehicle_name'      =>  'required|string',
                'vehicle_number'    =>  'required|string',
                'fuel_type'         =>  'required|string',
                'vehicle_type'      =>  'required|string',
                'status'            =>  'required|integer|min:0|max:1',
                
            ];

            $message =  [                
                'vehicle_name.required'     => 'Please enter vehicle name',   
                'vehicle_number.required'   => 'Please enter vehicle number',   
                'fuel_type.required'        => 'Please enter fuel type',   
                'vehicle_type.required'     => 'Please enter vehicle type',                      
                'status.required'           => 'Please select status',   
            ];
         
            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }
            else
            {     
                $requestData    = $request->all();   
                $requestData['created_by']   =  auth('sanctum')->user()->id;                
                $result         =  VehicleModel::create($requestData);
                
                return $this->sendResponse($result,"New Vehicle Created Successfully !");
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
            // $enc_id     =  $request->enc_id;
            // $deenc_id   = 0;
       
            // try {
            //     if($enc_id !=null)
            //     {
            //         $deenc_id = \Crypt::decryptString($enc_id) ;
            //     }
    
            // } catch (DecryptException $e) {
            //     //
            //     return $this->sendError("Not a valid enc_id.Please enter valid enc_id ");
            // }
            $id     =  $request->id;
            $vehicle =   VehicleModel::find($id);//

            if(isset($vehicle))
            {     
                $rules = [
                    'id'                => 'required',          
                    'vehicle_name'      =>  'required|string',
                    'vehicle_number'    =>  'required|string',
                    'fuel_type'         =>  'required|string',
                    'vehicle_type'      =>  'required|string',
                    'status'            =>  'required|integer|min:0|max:1',
                    
                ];
    
                $message =  [  
                    'id.required'               => 'Please enter id',                       
                    'vehicle_name.required'     => 'Please enter vehicle name',   
                    'vehicle_number.required'   => 'Please enter vehicle number',   
                    'fuel_type.required'        => 'Please enter fuel type',   
                    'vehicle_type.required'     => 'Please enter vehicle type',                      
                    'status.required'           => 'Please select status',   
                ];
                
                $validator = Validator::make($request->all(), $rules, $message);
                if($validator->fails())
                {
                    return $this->sendError($validator->errors()->first()); 
                }
                else
                {    
                    $request->request->remove('id');
                    $requestData    =  $request->all();
                    $requestData['modified_by']   =  auth('sanctum')->user()->id;   
                    $result         =  $vehicle->update($requestData);
                    $Details        =  VehicleModel::find($id);
                    return $this->sendResponse($Details,"Vehicle Details Updated !");
                    
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
            $vehicle   =   VehicleModel::find($id);
            if(isset($vehicle))
            {
                $vehicle->delete();
                return $this->sendResponse([],"Vehicle Deleted Successfully !");
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
    public function saveTracking(Request $request)
    {
        try 
        {
            // $enc_id     =  $request->vehicle_link;
            // $deenc_id   = 0;
       
            // try {
            //     if($enc_id !=null)
            //     {
            //         $deenc_id = \Crypt::decryptString($enc_id) ;
            //     }
    
            // } catch (DecryptException $e) {
            //     //
            //     return $this->sendError("Not a valid enc_id.Please enter valid enc_id ");
            // }
            $vehicle_id     =  $request->vehicle_link;
            $vehicle =   VehicleModel::find($vehicle_id);//

            $rules = [    
             //   'vehicle_link'      =>  'required',
                'lat'               =>  'required|string',
                'lng'               =>  'required|string',
                'status'            =>  'required|integer|min:0|max:1', 
                
                
            ];

            $message =  [                
               // 'vehicle_link.required'     => 'Please select vehicle id',   
                'lat.required'              => 'Please enter latitude',  
                'lng.required'              => 'Please enter longitude',     
                'status.required'           => 'Please select status',
                   
            ];
         
            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }
            else
            {    
                $request->request->remove('id');
                $requestData    = $request->all();        
                $requestData['vehicle_link'] = $vehicle_id ;   
                $requestData['created_by']          =  auth('sanctum')->user()->id;     
                $result         =  VehicleTrackModel::create($requestData);
                return $this->sendResponse($result,"Vehicle Track Record Created Successfully !");
            }
        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }
    }
    public function listTracking(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
          
            $vehicle =  VehicleTrackModel::select(
                "vehicles_track.*",
                "vehicles.id as vehicle_id",
                "vehicles.vehicle_name as vehicle_name",
                "vehicles.vehicle_number as vehicle_number",
                "vehicles.fuel_type as fuel_type",
                "vehicles.vehicle_type as vehicle_type"
                )
                ->leftjoin("vehicles","vehicles.id","vehicles_track.vehicle_link")
                ->where([["vehicles_track.status","=","0"]]);
            $response       =  $vehicle->paginate($paginatePerPage) ;;     
            return $this->sendResponse($response,"Data retrieved Successfully");

        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }
    }
}

