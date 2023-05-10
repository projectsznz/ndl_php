<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class ApartmentModel extends Model
{
    protected $table        = 'apratments';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'name',        
        'address',
        'area',
        'email',
        'lat',
        'lng',
        'contact',
        'whatsapp',
        'created_by',
        'modified_by',
        'status',
        'created_at',
        'updated_at',
        'qrcode'
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