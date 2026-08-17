<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($searh = null)
    {   
        if (!empty($searh)) {
            return view('user.index', [
                'users' => User::where('nick', 'LIKE', '%'.$searh.'%')
                              ->orWhere('name', 'LIKE', '%'.$searh.'%')
                              ->orWhere('last_name', 'LIKE', '%'.$searh.'%')  
                              ->orderBy('id', 'desc')->paginate(5)
            ]); 
        } else {
            return view('user.index', [
                'users' => User::orderBy('id', 'desc')->paginate(5)
            ]); 
        }
        
    }
    
    public function config()
    {
        return view('user.config');
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);

        $validate = $this->validate($request, [
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'nick' => 'required|string|max:255|',
            'email' => 'required|string|email|max:255'
            //  'nick' => 'required|string|max:255|unique:users, nick,'.$id,
            //  'email' => 'required|string|email|max:255|unique:users, email,'.$id
        ]);

        if ($validate) {

            $image_path_name = null;
            $image_path = $request->file('image_path');
            if ($image_path) {
                $image_path_name = time().$image_path->getClientOriginalName();
                Storage::disk('users')->put($image_path_name, File::get($image_path));
            }

            $data = [
                'name' => $request['name'],
                'last_name' => $request['lastname'],
                'nick' => $request['nick'],
                'email' => $request['email'],
            ];

            if ($image_path_name) {
                $data['image'] = $image_path_name;
            }

            $user->update($data);

            return redirect()->route('user.config')->with('message', 'update');

        } else {
            return redirect()->route('user.config')->with('message', 'error');
        }
    }

    public function getImage($filename)
    {
        $file = Storage::disk('users')->get($filename);
        return new Response($file, 200, ['Content-Type' => 'image/' . \Illuminate\Support\Facades\File::extension($filename)]);
    }

    public function profile($id)
    {
        return view('user.profile', [
            'user' => User::findOrFail($id)
        ]);
    }
    
}
