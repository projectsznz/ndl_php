<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    //
      /**
     * success response method.
     *
     * @param $result
     * @param $message
     *
     * @return JsonResponse
     */
    public function sendResponse($result, $message,$pagination=null)
    {
        $response = [
            'success'       => true,
            'status'        => 'Success',
            'status_code'    => 200,
            'data'          => $result,
            'message'       => $message,
            'pagination'    => $pagination,
            'serverTimezone'=> config('app.timezone'),
            'serverDateTime'=> date('Y-m-d H:i:sa')
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @param $error
     * @param  array  $errorMessages
     * @param  int  $code
     *
     * @return JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 200)
    {
        $response = [
            'success'       => false,
            'message'       => $error,
            'status'        => 'failure',
            'status_code'    => 400,
            'serverTimezone'=> config('app.timezone'),
            'serverDateTime'=> date('Y-m-d H:i:sa')
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
    public function sendEmptyResponse($errorMessages, $code = 200)
    {
        
    	$response = [
            'success'       => true,
            'message'       => $errorMessages,
            'status_code'   => $code,
            'status'        => 'failure',
            'data'          => (object) [],
            'serverTimezone'=> config('app.timezone'),
            'serverDateTime'=> date('Y-m-d H:i:sa')
        ];

        return response()->json($response, $code);
    }

    public function ExceptionError( $e ) 
    {
    
        $response = [
            'success' => false,
            'message' => 'Something went wrong',
            'status_code' => 404,
            'data' =>htmlentities($e), // (object) [],
            'serverTimezone'=> config('app.timezone'),
            'serverDateTime'=> date('Y-m-d H:i:sa')
        ];

        

        return response()->json( $response, 500 );
    }
}
