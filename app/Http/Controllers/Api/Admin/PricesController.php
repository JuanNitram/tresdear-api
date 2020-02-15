<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Admin\Base\BaseController as BaseController;
use Illuminate\Support\Facades\Storage;

use Validator;

use App\Models\Prices;

class PricesController extends BaseController
{
    /**
    * Register api
    *
    * @return \Illuminate\Http\Response
    */

    public function prices(){
        $file_preferences = config('file_preferences');
        $prices = Prices::orderBy('pos', 'asc')->get();

        if(count($prices) > 0){
            $success['prices'] = $prices;
            return $this->sendResponse($success, 'Prices');
        }

        return $this->sendError('No registered prices.', [], 200);
    }

    public function search($id){
        $price = Prices::where('id', $id)->first();
        if($price){
            $success['price'] = $price;
            return $this->sendResponse($success, 'Price');
        }
        return $this->sendError('Price not found.', [], 200);
    }

    public function remove($id){
        $price = Prices::where('id', $id)->first();
        if($price){
            $price->delete();
            return $this->sendResponse([], 'Success');
        }
        return $this->sendError('Price not found.', [], 200);
    }

    public function save(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required',
            'subtitle' => 'required',
            'price' => 'required',
            'details' => 'required',
            'description' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 200);
        }

        $price = Prices::create($data);

        $success['price'] = $price;

        return $this->sendResponse($success, 'Price register successfully.');
    }

    public function update($id, Request $request){
        $price = Prices::where('id', $id)->first();
        if($price){
            $data = $request->all();

            $validator = Validator::make($data, [
                'title' => 'required',
                'subtitle' => 'required',
                'price' => 'required',
                'details' => 'required',
                'description' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors(), 200);
            }

            $price->update($data);

            $success['price'] = $price;

            return $this->sendResponse($success, 'Price updated successfully.');
        }
        return $this->sendError('The price doesnt exists.', $validator->errors(), 200);
    }

    public function saveOrder(Request $request){
        $success = [];
        $index = 0;
        $pricesPos = $request->items;

        foreach($pricesPos as $id){
            $price = Prices::where('id', $id)->first();
            $price->update([
                'pos' => $index,
            ]);
            $index++;
        }

        return $this->sendResponse([], 'Prices order saved successfully');
    }

    public function activeMany(Request $request){
        $pricesId = $request->items;
        $active = $request->active;

        foreach($pricesId as $id){
            $price = Prices::where('id', $id)->first();
            if($price){
                $price->update([
                    'active' => $active,
                ]);
            }
        }
        return $this->sendResponse([], 'Prices activated succesfully.');
    }

    public function removeMany(Request $request){
        $pricesId = $request->items;
        $active = $request->active;

        foreach($pricesId as $id){
            $price = Prices::where('id', $id)->first();
        }
        return $this->sendResponse([], 'Prices removed succesfully.');
    }

}
