<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\ApartmentModel;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class ApartmentController extends BaseController
{
    //
    public function list(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');
 
            $page   = isset($request->page) ? $request->page : "1" ;
         
            $apartment =  ApartmentModel::where([["status","=","0"]]);
           
            $response       =  $apartment->paginate($paginatePerPage) ;;
          
         
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
         
            $apartment =  ApartmentModel::where([["status","=","0"]]);
           
            $response       =  $apartment->get() ;;
          
         
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
                'address'           =>  'required|string',
                'area'              =>  'required|string',
                'email'             =>  'required|email',
                'contact'           =>  'required|max:10|min:10',
                'lat'               =>  'required|string',
                'lng'               =>  'required|string',
                'qrcode'         =>  'required|string',
                'status'            =>  'required|integer|min:0|max:1',
                
            ];

            $message =  [                
                'name.required'         => 'Please enter name',   
                'address.required'      => 'Please enter address',   
                'area.required'         => 'Please enter area',   
                'email.required'        => 'Please enter email',   
                'contact.required'      => 'Please enter contact',   
                'lat.required'          => 'Please enter latitude',  
                'lng.required'          => 'Please enter longitude',  
                'qrcode.required'          => 'Please enter qrcode',    
                'status.required'       => 'Please select status',   
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
                $result         =  ApartmentModel::create($requestData);
                return $this->sendResponse($result,"New Apartment Created Successfully !");
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
            $apartment =   ApartmentModel::find($id);//

            if(isset($apartment))
            {     
                $rules = [
                    'id'            => 'required',                   
                    'name'              =>  'required|string',
                    'address'           =>  'required|string',
                    'area'              =>  'required|string',
                    'email'             =>  'required|email',
                    'contact'           =>  'required|max:10|min:10',
                    'lat'               =>  'required|string',
                    'lng'               =>  'required|string',
                    'qrcode'         =>  'required|string',
                    'status.required'   => 'Please select status',          
                    
                ];

                $message =  [
                    'id.required'   => 'Please enter id',                     
                    'name.required'     => 'Please enter name',   
                    'address.required'  => 'Please enter address',   
                    'area.required'     => 'Please enter area',   
                    'email.required'    => 'Please enter email',   
                    'contact.required'  => 'Please enter contact',   
                    'lat.required'      => 'Please enter latitude',  
                    'lng.required'      => 'Please enter longitude',  
                    'qrcode.required'          => 'Please enter qrcode',      
                    'status.required'   => 'Please select status',  
                    
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
                    $result         =  $apartment->update($requestData);
                    $Details    =  ApartmentModel::find($id);
                    return $this->sendResponse($Details,"Apartment Details Updated !");
                    
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
            $apartment   =   ApartmentModel::find($id);
            if(isset($apartment))
            {
                $apartment->delete();
                return $this->sendResponse([],"Apartment Deleted Successfully !");
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

