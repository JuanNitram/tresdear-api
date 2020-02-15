<?php


namespace App\Http\Controllers\Api\Admin\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Junity\Hashids\Facades\Hashids;
use Facades\{ App\Facades\Media as MediaManager };
use App\Models\Media;

use MediaUploader;
use Validator;


class BaseController extends Controller
{
    protected $MODEL = null;
    protected $section = '';

    public function __construct($model = null, $section = '')
    {
        $this->MODEL = $model;
        $this->section = $section;
    }

    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function store_medias($model, $medias, $group = null)
    {
        $preferences = config('file_preferences');

        foreach ($medias as $media) {
            $media_type = current(explode('/', $media->getMimeType()));
            if ($media_type == 'image') {
                $media_filename = pathinfo($media->getClientOriginalName(), PATHINFO_FILENAME);

                $m_original_media = MediaUploader::fromSource($media)->toDestination('public', $preferences['images_folder'])
                    ->useFilename($media_filename . '-original')->upload();

                if ($group)
                    $model->attachMedia($m_original_media, [$group]);
                else
                    $model->attachMedia($m_original_media, ['medias']);

                foreach ($preferences['sizes'] as $key => $dimension) {
                    $resized_media = \Image::make($media)->resize($dimension[0], $dimension[1])->encode('jpg', $preferences['quality']);
                    $m_resized_media = MediaUploader::fromString($resized_media)->toDestination('public', $preferences['images_folder'])
                        ->useFilename($media_filename . '-' . $key)->upload();

                    if ($group)
                        $model->attachMedia($m_resized_media, [$group]);
                    else
                        $model->attachMedia($m_resized_media, ['medias']);
                }
            }
        }
    }

    public function removeMedia(Request $request)
    {
        $preferences = config('file_preferences');

        $data = $request->all();
        $validator = Validator::make($data, [
            'media_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 200);
        }

        try{
            $media = Media::where('id', $request->media_id)->first();

            if($media){
                $ex = explode('-', $media->filename);
                if(is_numeric($ex[count($ex) - 1])){
                    $aux = $ex;
                    $aux[count($aux) - 2] = 'original';
                    $filename = implode($aux, '-');

                    $media = Media::where('filename', $filename)->first();
                    if($media) $media->delete();

                    foreach($preferences['sizes'] as $key => $dimension){
                        $aux = $ex;
                        $aux[count($aux) - 2] = $key;
                        $filename = implode($aux,'-');

                        $media = Media::where('filename', $filename)->first();
                        if($media) $media->delete();
                    }
                } else {
                    $filename = implode(array_slice($ex,0,count($ex)-1), '-');
                    $media->delete();

                    $media = Media::where('filename', $filename . '-original')->first();

                    if($media)
                        $media->delete();

                    foreach($preferences['sizes'] as $key => $dimension){
                        $media = Media::where('filename', $filename . '-' . $key)->first();
                        if($media) $media->delete();
                    }
                }
            }
            return $this->sendResponse([], 'Media removed succesfully');
        } catch(Exception $e) {
            return $this->sendError([], 'Error, please remove the entire model!');
        }

    }

    public function saveOrder(Request $request)
    {
        $index = 0;
        $itemsPos = $request->items;

        foreach($itemsPos as $id){
            $item = $this->MODEL::where('id', $id)->first();
            $item->update([
                'pos' => $index,
            ]);
            $index++;
        }

        return $this->sendResponse([], $this->section . ' order saved successfully');
    }

    public function activeMany(Request $request)
    {
        $itemsId = $request->items;
        $active = $request->active;

        foreach($itemsId as $id){
            $item = $this->MODEL::where('id', $id)->first();
            if($item){
                $item->update([
                    'active' => $active,
                ]);
            }
        }

        return $this->sendResponse([], $this->section . ' activated succesfully.');
    }

    public function removeMany(Request $request)
    {
        $itemsId = $request->items;

        foreach($itemsId as $id){
            $item = $this->MODEL::where('id', $id)->first();

            if($item){
                foreach($item->media as $media)
                    $media->delete();
                $item->delete();
            }
        }

        return $this->sendResponse([], $this->section . ' removed succesfully.');
    }

}
