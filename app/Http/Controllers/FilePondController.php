<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class FilePondController extends Controller
{
    public function process(Request $request)
    { 
        $locationID = date('Y-m-d_H_i_s').'_'.uniqid().'.'.$request->file('filepond')->extension();
        
        $request->file('filepond')
                ->storeAs('filepond/tmp/', $locationID, 'public');

        return $locationID; // it is stored as "serverId" on filepond file in frontend
    }

    public function revert(Request $request)
    { 
       Storage::disk('public')->delete('filepond/tmp/'.$request->getContent());
    }

    public function load(Request $request)
    {
        switch ($request->model) {            
            case 'User':
                $path = User::find($request->id)->profile_picture_path;
                break;
        }

        /** @var \Illuminate\Filesystem\Filesystem */
        $storagePublicDisk = Storage::disk('public');
        $storagePath = $storagePublicDisk->path($path);

        return response()->file(
            $storagePath, [
                'Content-Disposition' => 'inline', 
                'filename' => $storagePath
            ]
        );
    }

    public function remove(Request $request)
    {
        switch ($request->model) {            
            case 'User':
                $model = User::find($request->id);
                $path = $model->profile_picture_path;
                $abortIfCheck = $model->is_this_user_the_auth_user;
                $deleteOperation = 'update';
                $deleteParams = ['profile_picture_path' => $model->getDefaultProfilePicture()];
                break;
        }

        abort_if(!$abortIfCheck, 403);

        if(Storage::disk('public')->delete($path)) return $model->{$deleteOperation}($deleteParams);
    }
}
