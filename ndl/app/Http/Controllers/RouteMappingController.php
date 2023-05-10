<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\ApartmentModel;
use App\Models\RouteMappingModel;
use App\Models\RouteMasterModel;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use DB;
class RouteMappingController extends BaseController
{
    //
    
    public function list(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
         
            $route =  RouteMappingModel::select("route_mapping.*",
                    
            "apratments.name as apratments_name",
            "apratments.address as apratments_address",
            "apratments.area as apratments_area",
            "apratments.email as apratments_email",
            "apratments.lat as apratments_lat",
            "apratments.lng as apratments_lng",                     
            "apratments.contact as apratments_contact",
            "apratments.whatsapp as apratments_whatsapp",
            "wastage_master.name as wastage_name" ,
            "wastage_master.id as wastage_id" ,
            
            )
            ->leftjoin("route_master","route_mapping.route_id","route_master.id")
            ->leftjoin("apratments","apratments.id","route_mapping.apartment_id")
            ->leftjoin("wastage_master","wastage_master.id","route_master.wastage_id") ;
           
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
            $route =  RouteMappingModel::select("route_mapping.*",
                    
                    "apratments.name as apratments_name",
                    "apratments.address as apratments_address",
                    "apratments.area as apratments_area",
                    "apratments.email as apratments_email",
                    "apratments.lat as apratments_lat",
                    "apratments.lng as apratments_lng",                     
                    "apratments.contact as apratments_contact",
                    "apratments.whatsapp as apratments_whatsapp",
                    "wastage_master.name as wastage_name" ,
                    "wastage_master.id as wastage_id" ,
                    
                    )
            ->leftjoin("route_master","route_mapping.route_id","route_master.id")
            ->leftjoin("apratments","apratments.id","route_mapping.apartment_id")
            ->leftjoin("wastage_master","wastage_master.id","route_master.wastage_id") ;
       
            // $route =  DB::table("route_mapping")
            //             ->select("route_mapping.*",
            //                     "apratments.name as apratments_name",
            //                     "apratments.address as apratments_address",
            //                     "apratments.area as apratments_area",
            //                     "apratments.email as apratments_email",
            //                     "apratments.lat as apratments_lat",
            //                     "apratments.lng as apratments_lng",
            //                     "apratments.contact as apratments_contact",
            //                     "apratments.whatsapp as apratments_whatsapp",
            //                     "wastage_master.name as wastage_name" ,
            //                     "wastage_master.id as wastage_id" 
            //                     )
            //             ->leftjoin("route_master","route_mapping.route_id","route_master.id")
            //             ->leftjoin("apratments","apratments.id","route_mapping.apartment_id")
            //             ->leftjoin("wastage_master","wastage_master.id","route_master.wastage_id")->get() ;; ;
            // $routeArr = [];
            // $routeResponseArr = [];
            // foreach($route as $rr)
            // { 
            //     $routeArr['enc_id']         =  Crypt::encryptString($rr->id) ;    
            //     $routeArr['status']         = $rr->status;
            //     $routeArr['created_by']     = $rr->created_by;
            //     $routeArr['modified_by']    = $rr->modified_by;
            //     $routeArr['created_at']     = $rr->created_at;
            //     $routeArr['updated_at']     = $rr->updated_at;
            //     $routeArr['route_enc_id']   = Crypt::encryptString($rr->route_id) ;           
            //     $routeArr['remarks']                = $rr->remarks;
            //     $routeArr['apartment_enc_id']       = Crypt::encryptString($rr->apartment_id) ;
            //     $routeArr['apratments_name']        = $rr->apratments_name;
            //     $routeArr['apratments_address']     = $rr->apratments_address;
            //     $routeArr['apratments_area']        = $rr->apratments_area;
            //     $routeArr['apratments_email']       = $rr->apratments_email;
            //     $routeArr['apratments_lat']         = $rr->apratments_lat;
            //     $routeArr['apratments_lng']         = $rr->apratments_lng;
            //     $routeArr['apratments_contact']     = $rr->apratments_contact;
            //     $routeArr['apratments_whatsapp']    = $rr->apratments_whatsapp;
            //     $routeArr['wastage_enc_id']         = Crypt::encryptString($rr->wastage_id) ; 
            //     $routeArr['wastage_name']           = $rr->wastage_name;
            //     array_push($routeResponseArr,$routeArr);
            // }  
           
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
            
            $apartmentIds   = ($request->input('apartment_id'));
            $route_id       = $request->input('route_id') ;
            $priority       = $request->input('priority') ;
            // try {
            //     if($route_id !=null)
            //     {
            //         $deenc_id = \Crypt::decryptString($route_id) ;
            //     }
    
            // } catch (DecryptException $e) {
            //     //
            //     return $this->sendError("Not a valid enc_id.Please enter valid enc_id - route_id ");
            // }
            
            $route = RouteMasterModel::find($route_id);

            $errorMsg = [];
            if(isset($route))
            {
                $inputData['route_id'] = $route_id;
                $inputData['priority'] = $priority;

                $rules = [    
                    'route_id'          =>  'required|string',
                    'apartment_id'      =>  'required|string',              
                    'status'            =>  'required|integer|min:0|max:1',
                    'priority'            =>  'required|integer',
                    
                ];
    
                $message =  [                
                    'route_id.required'     => 'Please select route id',   
                    'apartment_id.required' => 'Please choose apartment id',                    
                    'status.required'       => 'Please select status',
                    'priority.required'       => 'Please select status',   
                ];

                $apartmentIdsArray = explode(',',$apartmentIds);
                $validator = Validator::make($request->all(), $rules, $message);
                if($validator->fails())
                {
                    return $this->sendError($validator->errors()->first());
                }
                else
                {     
                    $requestData = $request->all();    
                    foreach($apartmentIdsArray as $aid)
                    { 
                        $apartments =  ApartmentModel::find($aid);
                        if(isset($apartments))
                        {
                            $inputData['apartment_id'] = $aid ;
                            $inputData['created_by']   =  auth('sanctum')->user()->id; 

                            $wastageId = RouteMappingModel::select("*")->where([["route_id","=",$inputData['route_id']],["priority","=",$inputData['priority']]])->get();
                            $wastageId_json =json_decode(json_encode($wastageId), true);
                            if (empty($wastageId_json)) {
                                $result =  RouteMappingModel::create($inputData);
                                return $this->sendResponse($errorMsg,"New Route Mapping Created Successfully !");
                            }else{
                                return $this->sendError('This Priority is already added !!!'); 
                            }
                            
                        }
                        else
                        {
                            $errorMsg =["Not a valid id.Please enter valid id - apartment_id - ".$aid];
                        }

                        // try {
                        //     if($aid !=null)
                        //     {
                        //         $apart_id = \Crypt::decryptString($aid) ;
                        //     }
                            
                            
                
                        // } catch (DecryptException $e) {
                        //     //
                        //     $errorMsg =["Not a valid enc_id.Please enter valid enc_id - apartment_id "];
                       
                        // }
                        
                    }           
                     
                    
                }
                
            }
            else
            {
                return $this->sendError('No data available (route_master) for the id  passed');  
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
            $route =   RouteMappingModel::find($id);//

            if(isset($route))
            {     
                $rules = [    
                    'id'                => 'required',   
                    'route_id'          =>  'required|string',
                    'apartment_id'      =>  'required|string',              
                    'status'            =>  'required|integer|min:0|max:1',
                    
                ];
    
                $message =  [           
                    'id.required'           => 'Please enter id',         
                    'route_id.required'     => 'Please select route id',   
                    'apartment_id.required' => 'Please choose apartment id',                    
                    'status.required'       => 'Please select status',   
                ];
                
                $validator = Validator::make($request->all(), $rules, $message);
                if($validator->fails())
                {
                    return $this->sendError($validator->errors()->first()); 
                }
                else
                {    
                    $route_dec_id = $apart_dec_id = 0 ;
                    // try {
                    //     if($request->route_id !=null)
                    //     {
                    //         $route_dec_id = \Crypt::decryptString($request->route_id) ;
                    //     }
            
                    // } catch (DecryptException $e) {
                    //     //
                    //     return $this->sendError("Not a valid enc_id.Please enter valid route enc_id ");
                    // }

                    // try {
                    //     if($request->apartment_id !=null)
                    //     {
                    //         $apart_dec_id = \Crypt::decryptString($request->apartment_id) ;
                    //     }
            
                    // } catch (DecryptException $e) {
                    //     //
                    //     return $this->sendError("Not a valid enc_id.Please enter valid apartment enc_id ");
                    // }

                    $request->request->remove('id');
                    $requestData    =  $request->all();
                    $inputData      = [];
                    $inputData['route_id']      = $request->route_id;
                    $inputData['apartment_id']  = $request->apartment_id;
                    $inputData['modified_by']   =  auth('sanctum')->user()->id; 
                    $result     =  $route->update($inputData);
                    $Details    =  RouteMappingModel::find($id);
                    return $this->sendResponse($Details,"Route Mapping Updated !");
                    
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
            $route   =   RouteMappingModel::find($id);
            if(isset($route))
            {
                $route->delete();
                return $this->sendResponse([],"Route Mapping Deleted Successfully !");
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
