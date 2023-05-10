<?php

namespace App\Http\Controllers\Masters;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\WeightUnitMasterModel;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
class WeightUnitMasterController extends BaseController
{
    //
    public function list(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
         
            $weightUnitMaster =  WeightUnitMasterModel::where([["status","=","0"]]);
           
            $response       =  $weightUnitMaster->paginate($paginatePerPage) ;;
          
         
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
         
            $weightUnitMaster =  WeightUnitMasterModel::where([["status","=","0"]]);
           
            $response       =  $weightUnitMaster->get() ;
          
         
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
                'name'              =>  'required|string',
                'status'            =>  'required|integer|min:0|max:1',       
            ];

            $message =  [                
                'name.required'             => 'Please enter name',   
                'status.required'           => 'Please select status', 
            ];
         
            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            }
            else
            {     
                $requestData = $request->all();     
                $requestData['created_by']   =  auth('sanctum')->user()->id;            
                $result =  WeightUnitMasterModel::create($requestData);
                return $this->sendResponse($result,"New Weight Unit Master Created Successfully !");
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
            $weightUnitMaster =   WeightUnitMasterModel::find($id);//

            if(isset($weightUnitMaster))
            {     
                $rules = [
                    'id'                => 'required',                   
                    'name'              =>  'required|string',
                    'status'            =>  'required|integer|min:0|max:1',         
                    
                ];

                $message =  [
                    'id.required'               => 'Please enter id',                     
                    'name.required'             => 'Please enter name',
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
                    $requestData =  $request->all();
                    $requestData['modified_by']   =  auth('sanctum')->user()->id;  
                    $result         =  $weightUnitMaster->update($requestData);
                    $userDetails    =  WeightUnitMasterModel::find($id);
                    return $this->sendResponse($userDetails,"Weight Unit Master Updated !");
                    
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
            //     return $this->sendError("Not a valid enc_id.Please enter valid enc_id ");
            // }
            $id     =  $request->id;
            $weightUnitMaster   =   WeightUnitMasterModel::find($id);
            if(isset($weightUnitMaster))
            {
                $weightUnitMaster->delete();
                return $this->sendResponse([],"Weight Unit Master Deleted Successfully !");
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
