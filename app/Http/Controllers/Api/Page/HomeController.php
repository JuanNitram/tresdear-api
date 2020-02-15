<?php

namespace App\Http\Controllers\Api\Page;

use Illuminate\Http\Request;

use App\Models\Sliders;
use App\Models\Projects;
use App\Models\Clients;
use App\Models\Prices;

use App\Http\Controllers\Api\Page\Base\BaseController as BaseController;

class HomeController extends BaseController
{
    public function sliders(){
        $sliders = Sliders::orderBy('pos', 'asc')->get();

        if(count($sliders) > 0){
            $success['sliders'] = $sliders;
            return $this->sendResponse($success, 'Sliders');
        }

        return $this->sendError('No registered sliders.', [], 200);
    }

    public function projects(){
        $projects = Projects::orderBy('pos', 'asc')->get();

        if(count($projects) > 0){
            $success['projects'] = $projects;
            return $this->sendResponse($success, 'Projects');
        }

        return $this->sendError('No registered projects.', [], 200);
    }

    public function clients(){
        $clients = Clients::orderBy('pos', 'asc')->get();

        if(count($clients) > 0){
            $success['clients'] = $clients;
            return $this->sendResponse($success, 'Clients');
        }

        return $this->sendError('No registered clients.', [], 200);
    }

    public function prices(){
        $prices = Prices::orderBy('pos', 'asc')->get();

        if(count($prices) > 0){
            $success['prices'] = $prices;
            return $this->sendResponse($success, 'Prices');
        }

        return $this->sendError('No registered prices.', [], 200);
    }
}
