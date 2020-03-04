<?php

namespace App\Http\Controllers\Api\Page;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\ContactForm;
use GuzzleHttp\Client;
use Validator;

use App\Http\Controllers\Api\Page\Base\BaseController as BaseController;

class ContactController extends BaseController
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->data, [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required',
        ]);

        $reCaptcha = $request->recaptcha;

        $client = new Client();

        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['form_params'=>
                [
                    'secret'    => env('GOOGLE_RECAPTCHA_SECRET'),
                    'response'  => $reCaptcha
                 ]
            ]
        );

        $body = json_decode((string)$response->getBody());

        if(!$validator->fails() && $body->success){
            Mail::to('hola@tresdear.es')->send(new ContactForm($request->data));
            return $this->sendResponse([], 'Mail sended successfully.');
        }

        return $this->sendError('Validation Error.', $validator->errors(), 200);


    }
}
