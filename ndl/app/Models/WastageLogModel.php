<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class WastageLogModel extends Model
{
    protected $table        = 'wastage_log';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'vehicle_id',
        'driver_id',
        'apartment_id',
        'route_master_id',        
        'wastage_id',
        'wastage_count',
        'photo',
        'date',
        'remarks',   
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