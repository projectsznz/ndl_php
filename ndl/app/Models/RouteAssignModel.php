<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class RouteAssignModel extends Model
{
    protected $table        = 'route_assign';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'mode',
        'route_master_id',
        'driver_id',
        'vehicle_id',
        'date',
        'created_by',
        'modified_by',
        'status',
        'created_at',
        'updated_at','completed_status'
    ];
    //  protected $hidden = [
  //      'id',
       
   // ];
   // protected $appends = ['enc_id'];
   // public function getEncIdAttribute()
   // {
     //   return Crypt::encryptString( $this->id );
   // }
}