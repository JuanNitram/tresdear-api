<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Admin\Base\BaseController as BaseController;
use Validator;

use App\Models\Clients;

class ClientsController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Clients::class, 'Clients');
    }

    public function clients()
    {
        $clients = Clients::orderBy('pos', 'asc')->get();

        if(count($clients) > 0){
            $success['clients'] = $clients;
            return $this->sendResponse($success, 'Clients');
        }

        return $this->sendError('No registered clients.', [], 200);
    }

    public function search($id)
    {
        $client = Clients::where('id', $id)->first();
        if($client){
            $success['client'] = $client;
            return $this->sendResponse($success, 'Client');
        }
        return $this->sendError('Client not found.', [], 200);
    }

    public function remove($id)
    {
        $client = Clients::where('id', $id)->first();
        if($client){
            foreach($client->media as $media)
                $media->delete();
            $client->delete();
            return $this->sendResponse([], 'Success');
        }
        return $this->sendError('Client not found.', [], 200);
    }

    public function save(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 200);
        }

        $client = Clients::create($data);

        if(isset($data['medias']) && is_array($data['medias'])){
            $this->store_medias($client, $data['medias']);
        }

        $success['client'] = $client;

        return $this->sendResponse($success, 'Client register successfully.');
    }

    public function update($id, Request $request)
    {
        $client = Clients::where('id', $id)->first();

        $data = $request->all();

//        dd(json_decode($data['medias']));

        $validator = Validator::make($data, [
            'name' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 200);
        }

        if($client){
            $client->update($data);

            if(isset($data['medias']) && is_array($data['medias'])){
                $this->store_medias($client, $data['medias']);
            }

            $success['client'] = $client;

            return $this->sendResponse($success, 'Client updated successfully.');
        }
        return $this->sendError('The client doesnt exists.', 200);
    }
}
