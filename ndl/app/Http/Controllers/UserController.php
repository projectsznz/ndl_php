<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
class UserController extends BaseController
{
    //
    public function list(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');

            $filter = isset($request->filter) ? $request->filter : "all" ;
            $page   = isset($request->page) ? $request->page : "1" ;
            if("all" == strtolower($filter))
            {
                $users =  User::where([["type","<>","99"]]);
            }
            else if(("admin" == strtolower($filter)))
            {
                      $users =  User::where([["type","<>",2]]) ;
            }
            else if(("driver" == strtolower($filter)))
            {
                $users =  User::where([["type","=",2]]) ;
            }
            $response       =  $users->paginate($paginatePerPage) ;;
            // $response       =  $users->paginate(2);
            
            // $collection     = $response->toArray();

            //  $pagination = [
            //     'total'             => $response->total(),
            //     'per_page'          => $response->perPage(),
            //     'current_page'      => $response->currentPage(),
            //     'last_page'         => $response->lastPage(),
            //     'from'              => $response->firstItem(),
            //     'to'                => $response->lastItem(),
            //     'first_page_url'    => $collection['first_page_url'],
            //     'last_page_url'     => $collection['last_page_url'],
            //     'next_page_url'     => $response->nextPageUrl(),
            //     'path'              => $collection['path'],
            //     'prev_page_url'     => $response->previousPageUrl(),
            // ];

        

            return $this->sendResponse($response,"Data retrieved Successfully");

        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }

    }
    public function listNoPagination(Request $request)
    {
        try{

            $paginatePerPage = Config('settings.paginate_per_page');

            $filter = isset($request->filter) ? $request->filter : "all" ;
            $page   = isset($request->page) ? $request->page : "1" ;
            if("all" == strtolower($filter))
            {
                $users =  User::where([["type","<>","99"]]);
            }
            else if(("admin" == strtolower($filter)))
            {
                      $users =  User::where([["type","<>",2]]) ;
            }
            else if(("driver" == strtolower($filter)))
            {
                $users =  User::where([["type","=",2]]) ;
            }
            $response       =  $users->get();
        
            return $this->sendResponse($response,"Data retrieved Successfully");

        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }

    }
    public function create(Request $request)
    {
        try 
        {
            $rules = [
            
                'type'              => 'required|integer',
                'name'              =>  'required|string',
                'phone'             =>  'required|numeric|unique:users',
                'email'             =>   'required|email|unique:users',
                'password'          =>  'required',      
                'status'            =>  'required|integer|min:0|max:1',             
                
            ];

            $message =  [
                'type.required'             => 'Please select type',
                'name.required'             => 'Please enter name',
                'phone.required'            => 'Please enter phone number',
                'email.required'            => 'Please enter email',
                'password.required'         => 'Please enter password',
                'status.required'           => 'Please select status',
            ];

            if(isset($request->type) && $request->type==2)
            {
                $rules = [
            
                    'photo'         => 'required|image|mimes:jpeg,png,jpg|max:5120',
                    'license_no'    =>  'required|string',
                    'license_copy'  =>  'required|image|mimes:jpeg,png,jpg|max:5120',
                              
                    
                ];
                $message =  [
                    'photo.required'            => 'Please select photo',
                    'license_no.required'       => 'Please enter license no',
                    'license_copy.required'     => 'Please select licence copy file',
                    
                    
                ];

            }

            $validator = Validator::make($request->all(), $rules, $message);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first());
            
            
            }
            else
            {   
                if($request->hasFile('photo') && $request->hasFile('photo')==1)
                {
                $image = $request->file('photo');
                $nameValidate = $image->getClientOriginalName();
                $pattern ='/[\'^£$%&*()!}{@#~?><>,|=+¬]/';
    
                if (preg_match($pattern,$nameValidate) != 0) {
                    return $this->sendError("Image name contains special characters are not allowed.");
                }
    
                $image = $request->file('photo');
                $extension = strtolower($image->getClientOriginalExtension());
                if (!in_array($extension, ['jpeg', 'png', 'jpg']) || !in_array(mime_content_type($_FILES['photo']['tmp_name']), ['image/jpeg', 'image/png', 'image/jpg'])) {
                     return $this->sendError('The upload image must be an image.');
                }
    
                // never assume the upload succeeded
                if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                     return $this->sendError('The upload image must be an image.');
                }
    
                $info = getimagesize($_FILES['photo']['tmp_name']);
    
                if ($info === FALSE) {
                     return $this->sendError('The upload image must be an image.');
                }
    
                if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
                     return $this->sendError('The upload image must be an image.');
                }

                 
            
                    $extension = $request->file('photo')->getClientOriginalExtension(); 
        
                    $fileName = "uploads_".date('YmdHi') ."_". rand(11111, 99999) . '.' . $extension; 
        
        
                    $filePath = $request->file('photo')->storeAs('uploads', $fileName, 'public');
                 
                    $requestData['photo']    =  $filePath;
         
         
                }
                else
                {
                    $request->request->remove('photo');
                }
                
                if($request->type==2)
                {
                    $imageLC = $request->file('license_copy');
                    $nameValidate = $imageLC->getClientOriginalName();
                    $pattern ='/[\'^£$%&*()!}{@#~?><>,|=+¬]/';
        
                    if (preg_match($pattern,$nameValidate) != 0) {
                        return $this->sendError("Image name contains special characters are not allowed.");
                    }
        
                    $imageLC = $request->file('license_copy');
                    $extension = strtolower($imageLC->getClientOriginalExtension());
                    if (!in_array($extension, ['jpeg', 'png', 'jpg']) || !in_array(mime_content_type($_FILES['license_copy']['tmp_name']), ['image/jpeg', 'image/png', 'image/jpg'])) {
                         return $this->sendError('The upload image must be an image.');
                    }
        
                    // never assume the upload succeeded
                    if ($_FILES['license_copy']['error'] !== UPLOAD_ERR_OK) {
                         return $this->sendError('The upload image must be an image.');
                    }
        
                    $info = getimagesize($_FILES['license_copy']['tmp_name']);
        
                    if ($info === FALSE) {
                         return $this->sendError('The upload image must be an image.');
                    }
        
                    if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
                         return $this->sendError('The upload image must be an image.');
                    }
    
                    if($request->hasFile('license_copy') && $request->hasFile('license_copy')==1){
                
                        $extension = $request->file('license_copy')->getClientOriginalExtension(); 
            
                        $fileName = "uploads_license_".date('YmdHi') ."_". rand(11111, 99999) . '.' . $extension; 
            
            
                        $filePath = $request->file('license_copy')->storeAs('uploads', $fileName, 'public');
                     
                        $requestData['license_copy']    =  $filePath;
             
             
                    }
                    else
                    {
                        $request->request->remove('license_copy');
                    }
                }
                

                $requestData = $request->all();
                $requestData['password'] =  Hash::make($request->password);

                if($request->type==0 || $request->type==1  )
                {
                    $requestData['license_no'] = ""; 
                    $requestData['license_copy'] = ""; 
                }
                $requestData['created_by']   =  auth('sanctum')->user()->id;   

                $result =  User::create($requestData);

                return $this->sendResponse($result,"New User Created Successfully !");
            }
        }
        catch (\Exception $e) {

            return $this->ExceptionError( $e );
        }
    }
    public function edit(Request $request)
    {
        try 
        {
            // $enc_id     =  $request->enc_id;
            // $deenc_id   = 0;

       
            // try {
            //     if($enc_id !=null)
            //     {
            //         $deenc_id = \Crypt::decryptString($enc_id) ;
            //     }
    
            // } catch (DecryptException $e) {
            //     //
            //     return $this->sendError("Not a valid enc_id.Please enter valid enc_id ");
            // }
            $id = $request->id;
            $user =   User::find($id);//

            if(isset($user))
            {     
                $rules = [
                    'id'                => 'required',
                    'type'              => 'required|integer',
                    'name'              =>  'required|string',
                    'phone'             =>  'required|numeric|unique:users,phone,'.$id,
                    'email'             =>   'required|email|unique:users,email,'.$id,
                    'status'            =>  'required|integer|min:0|max:1',    
                //  'password'          =>  'required',            
                    
                ];

                $message =  [
                    'id.required'             => 'Please enter id',
                    'type.required'             => 'Please select type',
                    'name.required'             => 'Please enter name',
                    'phone.required'            => 'Please enter phone number',
                    'email.required'            => 'Please enter email',
                    'status.required'           => 'Please select status',
                //  'password.required'         => 'Please enter password',
                    
                ];

                if(isset($request->type) && $request->type==2)
                {
                    $rules = [
                
                        'photo'         => 'required|image|mimes:jpeg,png,jpg|max:5120',
                        'license_no'    =>  'required|string',
                        'license_copy'  =>  'required|image|mimes:jpeg,png,jpg|max:5120',
                                
                        
                    ];
                    $message =  [
                        'photo.required'            => 'Please select photo',
                        'license_no.required'       => 'Please enter license no',
                        'license_copy.required'     => 'Please select licence copy file',
                        
                        
                    ];

                }

                $validator = Validator::make($request->all(), $rules, $message);
                if($validator->fails())
                {
                    return $this->sendError($validator->errors()->first());
                
                
                }
                else
                {   
                    $requestData = $request->all();
                    if($request->hasFile('photo') && $request->hasFile('photo')==1)
                    {
                    $image = $request->file('photo');
                    $nameValidate = $image->getClientOriginalName();
                    $pattern ='/[\'^£$%&*()!}{@#~?><>,|=+¬]/';
        
                    if (preg_match($pattern,$nameValidate) != 0) {
                        return $this->sendError("Image name contains special characters are not allowed.");
                    }
        
                    $image = $request->file('photo');
                    $extension = strtolower($image->getClientOriginalExtension());
                    if (!in_array($extension, ['jpeg', 'png', 'jpg']) || !in_array(mime_content_type($_FILES['photo']['tmp_name']), ['image/jpeg', 'image/png', 'image/jpg'])) {
                        return $this->sendError('The upload image must be an image.');
                    }
        
                    // never assume the upload succeeded
                    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                        return $this->sendError('The upload image must be an image.');
                    }
        
                    $info = getimagesize($_FILES['photo']['tmp_name']);
        
                    if ($info === FALSE) {
                        return $this->sendError('The upload image must be an image.');
                    }
        
                    if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
                        return $this->sendError('The upload image must be an image.');
                    }

                    
                
                        $extension = $request->file('photo')->getClientOriginalExtension(); 
            
                        $fileName = "uploads_".date('YmdHi') ."_". rand(11111, 99999) . '.' . $extension; 
            
            
                        $filePath = $request->file('photo')->storeAs('uploads', $fileName, 'public');
                        
                        $requestData['photo']    =  $filePath;
            
            
                    }
                    else
                    {
                        $request->request->remove('photo');
                    }
                    
                    if($request->type==2)
                    {
                        $imageLC = $request->file('license_copy');
                        $nameValidate = $imageLC->getClientOriginalName();
                        $pattern ='/[\'^£$%&*()!}{@#~?><>,|=+¬]/';
            
                        if (preg_match($pattern,$nameValidate) != 0) {
                            return $this->sendError("Image name contains special characters are not allowed.");
                        }
            
                        $imageLC = $request->file('license_copy');
                        $extension = strtolower($imageLC->getClientOriginalExtension());
                        if (!in_array($extension, ['jpeg', 'png', 'jpg']) || !in_array(mime_content_type($_FILES['license_copy']['tmp_name']), ['image/jpeg', 'image/png', 'image/jpg'])) {
                            return $this->sendError('The upload image must be an image.');
                        }
            
                        // never assume the upload succeeded
                        if ($_FILES['license_copy']['error'] !== UPLOAD_ERR_OK) {
                            return $this->sendError('The upload image must be an image.');
                        }
            
                        $info = getimagesize($_FILES['license_copy']['tmp_name']);
            
                        if ($info === FALSE) {
                            return $this->sendError('The upload image must be an image.');
                        }
            
                        if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
                            return $this->sendError('The upload image must be an image.');
                        }
        
                        if($request->hasFile('license_copy') && $request->hasFile('license_copy')==1){
                    
                            $extension = $request->file('license_copy')->getClientOriginalExtension(); 
                
                            $fileName = "uploads_license_".date('YmdHi') ."_". rand(11111, 99999) . '.' . $extension; 
                
                
                            $filePath = $request->file('license_copy')->storeAs('uploads', $fileName, 'public');
                        
                            $requestData['license_copy']    =  $filePath;
                
                
                        }
                        else
                        {
                            $request->request->remove('license_copy');
                        }
                    }
                    

                    
                    if(isset($request->password) && ($request->password!=""||$request->password!=null))
                    {
                        $requestData['password'] =  Hash::make($request->password);
                    }else
                    {
                        $request->request->remove('password');
                    }
                
                    
                    if($request->type==0 || $request->type==1  )
                    {
                        $requestData['license_no'] = ""; 
                        $requestData['license_copy'] = ""; 
                    }
                    $request->request->remove('id');
                    
                    $requestData['modified_by']   =  auth('sanctum')->user()->id; 
                    $result =  $user->update($requestData);
                    $userDetails  =  User::find($id);
                    return $this->sendResponse($userDetails,"User Details Updated !");
                    
                }
            }
            else
            {
                return $this->sendError('No data available for the id passed');
            }
        }
        catch (\Exception $e) {
            
            \Log::info($e);
            return $this->ExceptionError( $e );
        }
    }
    public function delete(Request $request)
    {
        try{
            // $enc_id     =  $request->enc_id;
            // $deenc_id   = 0;

       
            // try {
            //     if($enc_id !=null)
            //     {
            //         $deenc_id = \Crypt::decryptString($enc_id) ;
            //     }
    
            // } catch (DecryptException $e) {
            //     //
            //     return $this->sendError("Not a valid enc_id to be deleted.Please enter valid enc_id");
            // }
            $id     =  $request->id;
            $user=User::find($id);
            if(isset($user))
            {
          
                $requestData['status']=1;
                $result =  $user->update($requestData);
                return $this->sendResponse([],"User Deleted Successfully !");
            }
            else
            {
                return $this->sendError('No data available for the id passed');  
            }

         
          
        }
        catch (\Exception $e) {
            \Log::info($e);
            return $this->ExceptionError( $e );
        }
    }
}
