<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\BaseController as BaseController;

class SettingsController extends BaseController
{
    //
    public function index()
    {
        $settingsArray = [
            "app_name"          => env('APP_NAME'),
            "base_url"          => env('APP_URL'),
            "media_base_url"    => env('APP_MEDIA_URL'),
            "timezone"          => Config('app.timezone'),
            "records_per_page"  => Config('settings.paginate_per_page'),
            "authentication_type"  => "Bearer Token"
        ];

        
        

        return $this->sendResponse($settingsArray,"Settings");
    }
}
