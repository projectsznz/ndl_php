<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class UnloadPointMasterModel extends Model
{
    protected $table        = 'unload_point_master';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'name',
        'lat',
        'lng',
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