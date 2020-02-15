<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Admin\Base\BaseController as BaseController;
use Validator;

use App\Models\Sliders;

class SlidersController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Sliders::class, 'Sliders');
    }

    public function sliders()
    {
        $sliders = Sliders::orderBy('pos', 'asc')->get();

        if(count($sliders) > 0){
            $success['sliders'] = $sliders;
            return $this->sendResponse($success, 'Sliders');
        }

        return $this->sendError('No registered sliders.', [], 200);
    }

    public function search($id)
    {
        $slider = Sliders::where('id', $id)->first();
        if($slider){
            $success['slider'] = $slider;
            return $this->sendResponse($success, 'Slider');
        }
        return $this->sendError('Slider not found.', [], 200);
    }

    public function remove($id)
    {
        $slider = Sliders::where('id', $id)->first();
        if($slider){
            foreach($slider->media as $media)
                $media->delete();
            $slider->delete();
            return $this->sendResponse([], 'Success');
        }
        return $this->sendError('Slider not found.', [], 200);
    }

    public function save(Request $request)
    {
        $data = $request->all();

        foreach($data as $key => $value){
            if($value == 'null')
                $data[$key] = null;
        }

        $slider = Sliders::create($data);

        if(isset($data['medias']) && is_array($data['medias'])){
            $this->store_medias($slider, $data['medias']);
        }

        $success['slider'] = $slider;

        return $this->sendResponse($success, 'Slider register successfully.');
    }

    public function update($id, Request $request)
    {
        $slider = Sliders::where('id', $id)->first();

        $data = $request->all();

        foreach($data as $key => $value){
            if($value == 'null')
                $data[$key] = null;
        }

        if($slider){
            $slider->update($data);

            if(isset($data['medias']) && is_array($data['medias'])){
                $this->store_medias($slider, $data['medias']);
            }

            $success['slider'] = $slider;

            return $this->sendResponse($success, 'Slider register successfully.');
        }
        return $this->sendError('The slider doesnt exists.',200);
    }

}
