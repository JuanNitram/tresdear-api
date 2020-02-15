<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Admin\Base\BaseController as BaseController;
use Validator;

use App\Models\Projects;

class ProjectsController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Projects::class, 'Projects');
    }

    public function projects(){
        $projects = Projects::orderBy('pos', 'asc')->get();

        if(count($projects) > 0){
            $success['projects'] = $projects;
            return $this->sendResponse($success, 'Projects');
        }

        return $this->sendError('No registered projects.', [], 200);
    }

    public function search($id)
    {
        $project = Projects::where('id', $id)->first();

        if($project){
            $success['project'] = $project;
            return $this->sendResponse($success, 'Project');
        }
        return $this->sendError('Project not found.', [], 200);
    }

    public function remove($id)
    {
        $project = Projects::where('id', $id)->first();
        if($project){
            foreach($project->media as $media)
                $media->delete();
            $project->delete();
            return $this->sendResponse([], 'Success');
        }
        return $this->sendError('Project not found.', [], 200);
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

        $project = Projects::create($data);

        if(isset($data['thumbnail']) && is_array($data['thumbnail'])){
            $this->store_medias($project, $data['thumbnail'], 'thumbnail');
        }

        if(isset($data['breadcrumb']) && is_array($data['breadcrumb'])){
            $this->store_medias($project, $data['breadcrumb'], 'breadcrumb');
        }

        $success['project'] = $project;

        return $this->sendResponse($success, 'Project register successfully.');
    }

    public function update($id, Request $request)
    {
        $project = Projects::where('id', $id)->first();
        if($project){
            $data = $request->all();

            $validator = Validator::make($data, [
                'name' => 'required',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors(), 200);
            }

            if(isset($data['thumbnail']) && is_array($data['thumbnail'])){
                $this->store_medias($project, $data['thumbnail'], 'thumbnail');
            }

            if(isset($data['breadcrumb']) && is_array($data['breadcrumb'])){
                $this->store_medias($project, $data['breadcrumb'], 'breadcrumb');
            }

            $project->update($data);
            $success['project'] = $project;

            return $this->sendResponse($success, 'Project updated successfully.');
        }
    }

}
