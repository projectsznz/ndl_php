<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class RouteMappingModel extends Model
{
    protected $table        = 'route_mapping';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'route_id',
        'apartment_id',
        'priority',
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
    // public function AparatementValue()
    // {
    //     return $this->belongsTo(\App\Models\ApartmentModel::class, 'apartment_id', 'id');
    // }
}