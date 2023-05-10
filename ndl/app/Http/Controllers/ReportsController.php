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
use Illuminate\Support\Facades\DB as FacadesDB;

class ReportsController extends BaseController
{
    //
    public function dateWiseList(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
            
            if(isset($request->fromdate) && $request->fromdate !=null)
            {
                $fromdate = date('Y-m-d',strtotime($request->fromdate));
            }
            else
            {
                $fromdate = date('Y-m-d', strtotime('-10 days', strtotime(date('Y-m-d'))));
            }

            if(isset($request->todate) && $request->todate !=null)
            {
                $todate = date('Y-m-d',strtotime($request->todate));
            }
            else
            {
                $todate = date('Y-m-d');
            }

            $jl =  JourneyLogModel::select(
                "journey_log.created_by",
                "journey_log.modified_by",
                "journey_log.created_at",
                "journey_log.updated_at",
                
                "journey_log.date",
                "wastage_master.name as wastage_type_name" ,
                "journey_log.journey_startdate as start_journey_app",
                "journey_log.journey_enddate as end_journey_app",
                "journey_log.wastage_weight",
                "journey_log.wastage_measurement"
                
            
        
        )
        ->leftjoin("route_master","route_master.id","journey_log.route_id")
        ->leftjoin("wastage_master","wastage_master.id","route_master.wastage_id")
        ->whereBetween('date',[$fromdate,$todate]);
        $response       =  $jl->paginate($paginatePerPage) ;;
      
            return $this->sendResponse($response,"Data retrieved Successfully");

        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }

    }
   
    public function wastageTypeList(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;

            $rules = [  
                      
                'wastage_type'            =>  'required|integer|min:0',
                
            ];

            $message =  [  
          
                'wastage_type.required'           => 'Please enter wastage_type (numeric)',   
            ];

          
            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }

            
            if(isset($request->wastage_type) && $request->wastage_type !=null)
            {
                $wastage_type = $request->wastage_type;
            }
            else
            {
                $wastage_type = 0;

            }

          

            $jl =  JourneyLogModel::select(
                "journey_log.created_by",
                "journey_log.modified_by",
                "journey_log.created_at",
                "journey_log.updated_at",
                
                "journey_log.date",
                "wastage_master.name as wastage_type_name" ,
                "journey_log.journey_startdate as start_journey_app",
                "journey_log.journey_enddate as end_journey_app",
                "journey_log.wastage_weight",
                "journey_log.wastage_measurement"
                
            
        
        )
        ->leftjoin("route_master","route_master.id","journey_log.route_id")
        ->leftjoin("wastage_master","wastage_master.id","route_master.wastage_id");
            if( $wastage_type ==0)
            {
                $response       =  $jl->paginate($paginatePerPage) ;;
            }
            else
            {
                $response       =  $jl->where([["wastage_master.id","=",$wastage_type]])->paginate($paginatePerPage) ;;
            }
            
      
            return $this->sendResponse($response,"Data retrieved Successfully");

        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }

    }

    public function apartmentWise(Request $request)
    {
        try{
            $paginatePerPage = Config('settings.paginate_per_page');

            $rules = [  
                      
                'apartment_id'            =>  'required|integer|min:0',
                
            ];

            $message =  [  
          
                'apartment_id.required'           => 'Please enter apartment_id (numeric)',   
            ];

            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }
            if(isset($request->apartment_id) && $request->apartment_id !=null)
            {
                $apartment_id = $request->apartment_id;
            }
            else
            {
                $apartment_id = 0;

            }

            $response  = WastageLogModel::select("apratments.id as apartment_id","apratments.name as apartment_name","apratments.address as apartment_address","apratments.area as apartment_area","apratments.whatsapp as whatsapp","apratments.qrcode as apartment_qrcode",
            DB::raw( "CASE WHEN apratments.status = 0 then 'Active' 
        
          else 'In Active'
          end  as apartment_status") ,
            "wastage_log.wastage_count","wastage_log.date as wastage_log_date")
            ->leftjoin("apratments","apratments.id","wastage_log.apartment_id") ;

                if( $apartment_id ==0)
                {
                    $response       =  $response->paginate($paginatePerPage) ;;
                }
                else
                {
                    $response       =  $response->where([      ["wastage_log.apartment_id","=",$request->apartment_id],])->paginate($paginatePerPage) ;;
                }
                
                return $this->sendResponse($response,"Data retrieved Successfully");

        }
        catch(\Exception $e)
        {
            return $this->ExceptionError($e);
        }
    }
     public function dateWiseList_Report(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
            
            if(isset($request->fromdate) && $request->fromdate !=null)
            {
                $fromdate = date('Y-m-d',strtotime($request->fromdate));
            }
            else
            {
                $fromdate = date('Y-m-d', strtotime('-10 days', strtotime(date('Y-m-d'))));
            }

            if(isset($request->todate) && $request->todate !=null)
            {
                $todate = date('Y-m-d',strtotime($request->todate));
            }
            else
            {
                $todate = date('Y-m-d');
            }

            $jl =  JourneyLogModel::select(
                "apratments.name as Apratments Name",
                "route_master.name as Route Master Name",
                "users.name as Driver Name",
                "vehicles.vehicle_name as Vehicle Name",
                "wastage_master.name as Wastage Type" ,
                "journey_log.wastage_count as Wastage Count",
                "journey_log.wastage_measurement as Wastage Measurement",
                "journey_log.date as Date"
                
            
        
        )
        ->leftjoin("route_master","route_master.id","journey_log.route_id")
        ->leftjoin("wastage_master","wastage_master.id","route_master.wastage_id")
        ->leftjoin("apratments","apratments.id","journey_log.start_apartment_id")
        ->leftjoin("users","users.id","journey_log.driver_id")
        ->leftjoin("vehicles","vehicles.id","journey_log.vehicle_id")
        ->whereBetween('date',[$fromdate,$todate]);
        $response       =  $jl->paginate($paginatePerPage) ;;
      
            return $this->sendResponse($response,"Data retrieved Successfully");

        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }

    }
    
}
