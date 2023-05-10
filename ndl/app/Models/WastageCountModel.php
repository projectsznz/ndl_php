<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class WastageCountModel extends Model
{
    protected $table        = 'wastage_count';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'apartment_id',        
        'wastage_id',        
        'wastage_count',        
        'measurement_type','measurement_type_id',        
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