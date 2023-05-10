<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\ApartmentModel;
use App\Models\WastageCountModel;
use App\Models\WastageMasterModel;
use App\Models\WeightUnitMasterModel;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class WastageCountController extends BaseController
{
    //
    public function list(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
         
            $wastagecount =  WastageCountModel::select("wastage_count.*","apratments.name as apartment_name","weight_units_master.name as measurement_type")
            ->leftjoin("apratments","apratments.id","wastage_count.apartment_id")
            ->leftjoin("weight_units_master","weight_units_master.id","wastage_count.measurement_type_id")
            ->where([["wastage_count.status","=","0"],["weight_units_master.status","=","0"]])  ;
           
            $response       =  $wastagecount->paginate($paginatePerPage) ;;
          
         
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
         
            $wastagecount =  WastageCountModel::select("wastage_count.*","apratments.name as apartment_name","weight_units_master.name as measurement_type")->leftjoin("apratments","apratments.id","wastage_count.apartment_id")
            ->leftjoin("weight_units_master","weight_units_master.id","wastage_count.measurement_type_id")->where([["wastage_count.status","=","0"],["weight_units_master.status","=","0"]])  ;
           
            $response       =  $wastagecount->get() ;;
          
         
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
                'apartment_id'      =>  'required|string',
                'wastage_id'        =>  'required|string',
                'wastage_count'     =>  'required',
             //   'measurement_type'  =>  'required|string',
                'measurement_type_id'=>'required|integer',
               
                
            ];

            $message =  [                
                'apartment_id.required'     => 'Please select apartment',   
                'wastage_id.required'       => 'Please select wastage type',   
                'wastage_count.required'    => 'Please enter wastage count',   
                'measurement_type.required' => 'Please enter measurement type',   
                'measurement_type_id.required' => 'Please enter measurement type id',   
                   
            ];
            
            $apartment_id       = $request->input('apartment_id') ;         

            
            $apartmentCheck = ApartmentModel::find($apartment_id);
            if(!isset($apartmentCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - apartment_id ");
            }

            $wastage_id       = $request->input('wastage_id') ;
            $wastageCheck = WastageMasterModel::find($wastage_id);
            if(!isset($wastageCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - wastage_id ");
            }
            $measurement_type_id       = $request->input('measurement_type_id') ;
            $WeightUnitCheck = WeightUnitMasterModel::find($measurement_type_id);
            if(!isset($WeightUnitCheck))
            {
                return $this->sendError("Not a valid id.Please enter valid id - measurement_type_id ");
            }

            // try 
            // {
            //     if($apartment_id !=null)
            //     {  
            //           $dec_apartment_id = \Crypt::decryptString($apartment_id) ;

            //         try {
            //             $apartmentCheck = ApartmentModel::find($dec_apartment_id);
            //         }catch (Exception $e) {
            //             //
            //             return $this->sendError("Not a valid enc_id.Please enter valid enc_id - apartment_id ");
            //         }

            //         $apartmentCheck = ApartmentModel::find($dec_apartment_id);
            //         if(!isset($apartmentCheck))
            //         {
            //             return $this->sendError("Not a valid enc_id.Please enter valid enc_id - apartment_id ");
            //         }
            //     }
    
            // } catch (DecryptException $e) {
            //     //
            //     return $this->sendError("Not a valid enc_id.Please enter valid enc_id - apartment_id ");
            // }
            // try 
            // {
            //     if($wastage_id !=null)
            //     {  
            //           $dec_wastage_id = \Crypt::decryptString($wastage_id) ;

            //         try {
            //             $wastageCheck = WastageMasterModel::find($dec_wastage_id);
            //         }catch (Exception $e) {
            //             //
            //             return $this->sendError("Not a valid enc_id.Please enter valid enc_id - wastage_id ");
            //         }

            //         $wastageCheck = WastageMasterModel::find($dec_wastage_id);
            //         if(!isset($wastageCheck))
            //         {
            //             return $this->sendError("Not a valid enc_id.Please enter valid enc_id - wastage_id ");
            //         }
            //     }
    
            // } catch (DecryptException $e) {
            //     //
            //     return $this->sendError("Not a valid enc_id.Please enter valid enc_id - wastage_id ");
            // }

            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }
            else
            {     
                $requestData    = $request->all();
                
                $inputData = [];
                $inputData['wastage_id']        = $wastage_id;
                $inputData['apartment_id']      = $apartment_id;
                $inputData['wastage_count']     = $request->wastage_count;
                $inputData['measurement_type']  = $request->measurement_type;
                $inputData['measurement_type_id']  = $request->measurement_type_id;
                $inputData['status']            = $request->status;
                $inputData['created_by']        =  auth('sanctum')->user()->id;  
                $result         =  WastageCountModel::create($inputData);
                return $this->sendResponse($result,"New Wastage Count Created Successfully !");
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
            $wastagecount =   WastageCountModel::find($id);//

            if(isset($wastagecount))
            {     
                $rules = [    
                    'apartment_id'      =>  'required|string',
                    'wastage_id'        =>  'required|string',
                    'wastage_count'     =>  'required',
                //    'measurement_type'  =>  'required|string',
                    'measurement_type_id'=>'required|integer',
                    
                ];
    
                $message =  [                
                    'apartment_id.required'     => 'Please select apartment',   
                    'wastage_id.required'       => 'Please select wastage type',   
                    'wastage_count.required'    => 'Please enter wastage count',   
                    'measurement_type.required' => 'Please enter measurement type',   
                    'measurement_type_id.required' => 'Please enter measurement type id', 
                       
                ];
                
                $apartment_id       = $request->input('apartment_id') ;
                $apartmentCheck = ApartmentModel::find($apartment_id);
                if(!isset($apartmentCheck ))
                {
                    return $this->sendError("Not a valid id.Please enter valid id - apartment_id ");
                }

                $wastage_id       = $request->input('wastage_id') ;
                $wastageCheck = WastageMasterModel::find($wastage_id);
                if(!isset($wastageCheck))
                {
                    return $this->sendError("Not a valid id.Please enter valid id - wastage_id ");
                }

                $measurement_type_id       = $request->input('measurement_type_id') ;
                $WeightUnitCheck = WeightUnitMasterModel::find($measurement_type_id);
                if(!isset($WeightUnitCheck))
                {
                    return $this->sendError("Not a valid id.Please enter valid id - measurement_type_id ");
                }
                // try 
                // {
                //     if($apartment_id !=null)
                //     {  
                //           $dec_apartment_id = \Crypt::decryptString($apartment_id) ;
    
                //         try {
                //             $apartmentCheck = ApartmentModel::find($dec_apartment_id);
                //         }catch (Exception $e) {
                //             //
                //             return $this->sendError("Not a valid enc_id.Please enter valid enc_id - apartment_id ");
                //         }
    
                //         $apartmentCheck = ApartmentModel::find($dec_apartment_id);
                //         if(!isset($apartmentCheck ))
                //         {
                //             return $this->sendError("Not a valid enc_id.Please enter valid enc_id - apartment_id ");
                //         }
                //     }
        
                // } catch (DecryptException $e) {
                //     //
                //     return $this->sendError("Not a valid enc_id.Please enter valid enc_id - apartment_id ");
                // }
    
                
                // try 
                // {
                //     if($wastage_id !=null)
                //     {  
                //           $dec_wastage_id = \Crypt::decryptString($wastage_id) ;
    
                //         try {
                //             $wastageCheck = WastageMasterModel::find($dec_wastage_id);
                //         }catch (Exception $e) {
                //             //
                //             return $this->sendError("Not a valid enc_id.Please enter valid enc_id - wastage_id ");
                //         }
    
                //         $wastageCheck = WastageMasterModel::find($dec_wastage_id);
                //         if(!isset($wastageCheck))
                //         {
                //             return $this->sendError("Not a valid enc_id.Please enter valid enc_id - wastage_id ");
                //         }
                //     }
        
                // } catch (DecryptException $e) {
                //     //
                //     return $this->sendError("Not a valid enc_id.Please enter valid enc_id - wastage_id ");
                // }
    
                $validator = Validator::make($request->all(), $rules, $message);
                if($validator->fails())
                {
                    return $this->sendError($validator->errors()->first()); 
                }
                else
                {    
                    $request->request->remove('enc_id');
                  
                
                    $inputData = [];
                    $inputData['wastage_id']        = $wastage_id;
                    $inputData['apartment_id']      = $apartment_id;
                    $inputData['wastage_count']     = $request->wastage_count;
                    $inputData['measurement_type']  = $request->measurement_type;
                    $inputData['measurement_type_id']  = $request->measurement_type_id;
                    $inputData['status']            = $request->status;
                    $inputData['modified_by']   =  auth('sanctum')->user()->id;  
                    $result         =  $wastagecount->update($inputData);
                    $Details    =  WastageCountModel::find($id);
                    return $this->sendResponse($Details,"Wastage Count Details Updated !");
                    
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
            $wastagecount   =   WastageCountModel::find($id);
            if(isset($wastagecount))
            {
                $wastagecount->delete();
                return $this->sendResponse([],"Wastage Count Deleted Successfully !");
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

