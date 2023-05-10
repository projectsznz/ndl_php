<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class VehicleTrackModel extends Model
{
    protected $table        = 'vehicles_track';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'vehicle_link',        
        'lat',
        'lng',
        'gsm_signal',
        'gsm_signal',
        'datetime',         
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