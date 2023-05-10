<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\ApartmentModel;
use App\Models\JourneyLogModel;
use App\Models\RouteMappingModel;
use App\Models\RouteMasterModel;
use App\Models\User;
use App\Models\WastageLogModel;
use App\Models\WastageMasterModel;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends BaseController
{
    //
    public function index(Request $request)
    {
        try{

            $dashboard = [];
            $dashboard['apartment']['total'] =  ApartmentModel::count();
            $dashboard['apartment']['active'] =  ApartmentModel::where([['status','=','0']])->count();
            $dashboard['apartment']['inactive'] =  ApartmentModel::where([['status','=','1']])->count();
             
            $dashboard['drivers']['total'] =  User::where([ ['type','=','2']])->count();
            $dashboard['drivers']['active'] =  User::where([['status','=','0'],['type','=','2']])->count();
            $dashboard['drivers']['inactive'] =  User::where([['status','=','1'],['type','=','2']])->count();
            $dashboard['routes']['total'] =  RouteMasterModel::count();
            $dashboard['routes']['active'] =  RouteMasterModel::where([['status','=','0']])->count();
            $dashboard['routes']['inactive'] =  RouteMasterModel::where([['status','=','1']])->count();
            $dashboard['wastage']['total'] =   WastageLogModel::sum('wastage_count'); ;

            $wastageMaster =  WastageMasterModel::where([['status','=','0']])->get();
            $cnt=0;
            $responseWastage=[];
            foreach($wastageMaster as $wastage)
            {
              
                $routeMaster = RouteMasterModel::where([['status','=','0'],['wastage_id','=',$wastage['id']]])->pluck('id');
                $totalVehicles =  RouteMappingModel::whereIn('route_id',$routeMaster)->count();
                $responseWastage[$cnt]['wastage_type']=$wastage['name'];
                $responseWastage[$cnt]['vehicle_count'] =$totalVehicles;
                $cnt++;
            }
            $dashboard['vehicle'] = $responseWastage;
            $dateNow10Days =date('Y-m-d', strtotime('-10 days', strtotime(date('Y-m-d'))));
            $dateNow= date('Y-m-d');
            $dashboard['wastage_log_count_by_date'] = WastageLogModel::select("wastage_log.date","wastage_master.name",DB::RAW("SUM(wastage_log.wastage_count) AS count"))
            ->leftjoin("wastage_master","wastage_master.id","wastage_log.wastage_id"
            )->whereBetween('date',[$dateNow10Days,$dateNow])
            ->groupBy("wastage_log.date","wastage_master.name"
            )->get();

            
            $dashboard['wastage_log_weight_bar_chart'] = DB::SELECT("SELECT DATE_FORMAT(a.`journey_startdate`,'%Y-%m-%d') Date,  SUM(a.wastage_weight) AS weight , a.wastage_measurement ,  c.name FROM `journey_log` a 
            left join route_master b on a.route_id = b.id 
            left join wastage_master c on b.wastage_id = c.id 
            where   DATE_FORMAT(a.`journey_startdate`,'%Y-%m-%d') BETWEEN '$dateNow10Days' and '$dateNow'
            GROUP BY a.route_id ,DATE_FORMAT(a.`journey_startdate`,'%Y-%m-%d') ,a.wastage_measurement");

            return $this->sendResponse($dashboard,"Data retrieved Successfully");

        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }
    }
     
}

