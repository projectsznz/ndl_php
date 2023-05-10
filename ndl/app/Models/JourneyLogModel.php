<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class JourneyLogModel extends Model
{
    protected $table        = 'journey_log';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'date',        
        'journey_startdate',
        'journey_enddate',
        'route_id',
        'driver_id',
        'start_apartment_id',
        'end_apartment_id',
        'vehicle_id',
        'lat',
        'lng',
        'end_lat',
        'end_lng',
        'journey_status',
        'photo',
        'remarks',
        'created_by',
        'modified_by',
        'status',
        'created_at',
        'updated_at',
        'wastage_count',
        'wastage_weight',
        'wastage_measurement'

    ];
   protected $hidden = [
       'wastage_count',
       
   ];
   // protected $appends = ['enc_id'];
   // public function getEncIdAttribute()
   // {
     //   return Crypt::encryptString( $this->id );
   // }
  
}