<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\RouteAssignModel;
use App\Models\RouteMasterModel;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Models\WeightUnitMasterModel;

class AuthController extends BaseController
{
    //
    public function login(Request $request)
    {
        $login_type = filter_var($request->username, FILTER_VALIDATE_EMAIL ) 
        ? 'email' 
        : 'phone'; 
        $platform =  isset($request->platform) ? $request->platform : '';
        if(Auth::attempt([$login_type => $request->username, 'password' =>  $request->password])){ 
            $user = Auth::user(); 
            $success['token']   =  $user->createToken('NDL')->plainTextToken; 
            $success['name']    =  $user->name;
            $success['type']    =  $user->type ;
            $success['id']      =  $user->id ;
            if(isset($user->type) && $user->type==0)
            {
                $success['usertype'] = "Super Admin" ;
            }
            if(isset($user->type) && $user->type==1)
            {
                $success['usertype'] = "Admin" ;
            }
            if(strtolower($platform) =='web' && ($user->type =='0' || $user->type =='1'))
            {

            }
            elseif(($user->type =='2') && strtolower($platform) =='app')
            {
               
            }  
            else
            {
                return $this->sendError('Authorization Failed - Not allowed to login via this platform');
            }

            if(isset($user->type) && $user->type==2)
            {    $success['driver_id']      =  $user->id ;
                $success['license_copy']    =  $user->license_copy ;
                $success['license_no']      =  $user->license_no ;
                $success['usertype']        = "Driver" ;
            }
            $success['phone']               =  $user->phone ;
            $success['email ']              =  $user->email  ;
            $success['email_verified_at']   =  $user->email_verified_at ;
            $success['photo']               =  $user->photo ;
           
            $success['last_access_at']      =  $user->last_access_at ;
            $success['status']              =  $user->status ;
            $success['created_at']          =  $user->created_at ;
            $success['weight_units']        =  WeightUnitMasterModel::select('name')->where([['status','=','0']])->pluck('name') ;
            $driverId          =  auth('sanctum')->user()->id; 
             
           $route =  RouteAssignModel::select("route_assign.route_master_id" 
                    )
             
            ->where([
                ["route_assign.completed_status","=","0"],
                ["route_assign.status","=","0"],
                ["route_assign.date","=",date('Y-m-d')],
                ['route_assign.driver_id','=',$driverId]
                ])->get()->pluck('route_master_id');

            $success['route_id']    =  0 ;
            $success['wastage_id']  =  0 ;
            if(count($route)>0){
                $success['route_id']          =  $route[0] ;
                $wastageId = RouteMasterModel::select("wastage_id")->where([["route_master.status","=","0"],["route_master.id","=",$route[0]]])->get()->pluck('wastage_id');
                $success['wastage_id']          =  $wastageId[0] ;
            }
           
            
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Authorization Failed - Username and Password Invalid');
        } 

    }
  
}

