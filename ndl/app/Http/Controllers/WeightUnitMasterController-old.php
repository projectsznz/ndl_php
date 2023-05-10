<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\ApartmentModel;
use App\Models\WeightUnitMasterModel;
use App\Models\WastageMasterModel;
use Exception;
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
         
            $weightunit =  WeightUnitMasterModel::where([["status","=","0"]]);
           
            $response       =  $weightunit->paginate($paginatePerPage) ;;
          
         
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
         
            $weightunit =  WeightUnitMasterModel::where([["status","=","0"]]);
           
            $response       =  $weightunit->get() ;;
          
         
            return $this->sendResponse($response,"Data retrieved Successfully");

        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }
    }
}
