<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Admin\Base\BaseController as BaseController;
use Illuminate\Support\Facades\Hash;
use Validator;

use App\Admin;
use App\Models\AdminsTypes;
use App\Models\Sections;

class AdminsController extends BaseController
{
    /**
    * Register api
    *
    * @return \Illuminate\Http\Response
    */

    public function admins(){
        $admins = Admin::with('sections')->get();
        if(count($admins) > 0){
            $success['admins'] = $admins;
            return $this->sendResponse($success, 'Admins');
        }
        return $this->sendError('No registered admins.', [], 200);
    }

    public function search($id){
        $admin = Admin::where('id', $id)->with('sections')->first();
        if($admin){
            $success['admin'] = $admin;
            return $this->sendResponse($success, 'Admin founded successfully.');
        }
        return $this->sendError('Admin not found.', [], 200);
    }

    public function remove($id){
        $admin = Admin::where('id', $id)->first();
        if($admin){
            $admin->delete();
            return $this->sendResponse([], 'Admin deleted successfully.');
        }
        return $this->sendError('Admin not found.', [], 200);
    }

    public function save(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'types_id' => 'required',
            'sections' => 'required',
            'parent' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 200);
        }

        $parent = Admin::where('email', $data['parent'])->first();
        $admin_type = AdminsTypes::where('id', $data['types_id'])->first();

        // 'Fuckin Hierachy'
        if($parent){
            if($admin_type){
                $sections_attach = [];
                $sections = Sections::all();

                if($parent->types_id == 1){ // The parent is SuperAdmin, then set all sections to new Admin

                    if($data['types_id'] > 1){
                        foreach($data['sections'] as $data_section){
                            foreach($sections as $section)
                                if($data_section == $section->id)
                                    array_push($sections_attach, $data_section);
                        }
                    } else {
                        foreach($sections as $section)
                            array_push($sections_attach, $section->id);
                    }

                } else { // The parent is not Admin, then set sections based on the sections of the parent

                    if($data['types_id'] == 1){
                        return $this->sendError('A Admin cant create new Superadmin .', [], 200);
                    } else{ // A Admin wants to create a admin based on his sections
                        $parent_sections = $parent->sections()->get();
                        if($parent->types_id <= $data['types_id']){
                            foreach($data['sections'] as $section){
                                foreach($parent_sections as $parent_section)
                                    if($section == $parent_section->id)
                                        array_push($sections_attach, $section);
                            }
                        } else {
                            return $this->sendError('Hierarchy not respected .', [], 200);
                        }
                    }

                }

                $data['password'] = Hash::make($data['password']);
                $admin = Admin::create($data);

                $admin->sections()->attach($sections_attach);

                $success['admin'] = $admin;

                return $this->sendResponse($success, 'Admin register successfully.');
            }
            return $this->sendError('Admin type doesnt exists.', [], 200);
        }

        return $this->sendError('Admin doesnt exists.', [], 200);

    }

    public function update($id, Request $request){
        $admin = Admin::where('id', $id)->first();
        if($admin){
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                // 'password' => 'required',
                // 'c_password' => 'required|same:password',
                'types_id' => 'required',
                'sections' => 'required',
                'parent' => 'required',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors(), 200);
            }

            $data = [];
            foreach($request->all() as $key => $value){
                if($key != 'email')
                    $data[$key] = $value;
            }

            if(isset($data['password']))
                $data['password'] = Hash::make($data['password']);

            $admin->update($data);

            $admin->sections()->detach();
            $admin->sections()->attach($data['sections']);

            $success = [];
            $success['admin'] = $admin;

            return $this->sendResponse($success, "Updated succesfully.");
        }
        return $this->sendError('Admin not found.', [], 200);
    }

}
