<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;

class MainUserController extends Controller
{
    public function userLogout(){
        Auth::logout();
        return redirect()->route('login');
    }

    public function userProfile(){
        $id = Auth::User()->id;
        $user = User::find($id);
        return view('user.profile.view_profile',compact('user'));
    }

    public function editProfile(){
        $id = Auth::User()->id;
        $editData = User::find($id);
        return view('user.profile.edit_profile',compact('editData'));
    }

    public function storeProfile(Request $request){
        $data = User::find(Auth::user()->id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->profile_photo_path = $request->profile_photo_path;
        if ($request->file('profile_photo_path')) {
            $file = $request->file('profile_photo_path');
            @unlink(public_path('upload/userimages/'.$data->profile_photo_path));
            $filename =date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('/upload/userimages'),$filename);
            $data['profile_photo_path']= $filename;
        }
        $data->save();

        //toastr
        $notification = array(
            'message' => 'Profile update successfully!',
            'alert-type' => 'success'
        );
        return redirect()->route('user-profile')->with($notification);
    }
}
