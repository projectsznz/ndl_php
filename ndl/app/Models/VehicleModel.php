<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class VehicleModel extends Model
{
    protected $table        = 'vehicles';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'vehicle_name',        
        'vehicle_number',
        'fuel_type',
        'vehicle_color',
        'vehicle_type',
        'device_id',
        'imei',
        'gsm',
        'created_by',
        'modified_by',
        'status',
        'created_at',
        'updated_at',
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