<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class WeightUnitMasterModel extends Model
{
    protected $table        = 'weight_units_master';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'name',        
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