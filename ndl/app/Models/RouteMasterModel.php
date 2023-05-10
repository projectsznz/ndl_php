<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class RouteMasterModel extends Model
{
    protected $table        = 'route_master';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'name',
        'wastage_id',
        'unload_point',
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