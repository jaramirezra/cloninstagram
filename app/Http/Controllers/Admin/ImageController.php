<?php

namespace App\Http\Controllers\Admin;

use App\Models\Like;
use App\Models\Image;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('image.add');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $image = Image::find($id);
        if ($user && $image && $image->user->id == $user->id) {
            return view('image.add', [
                'image' => $image
            ]);
        } else {
            return redirect()->route('home');
        }
    }

    public function show($id)
    {
        $image = Image::findOrFail($id);
        return view('image.show', [
            'image' => $image
        ]);
    }

    public function store(Request $request)
    {
        if ($request->has('image_id')) {

            $validate = $this->validate($request, [
                'image_path' => 'image',
                'description' => 'required|string|max:255'
            ]);
            if ($validate) {
                $image = Image::find($request['image_id']);

                if (!empty($request['image_path'])) {
                    Storage::disk('images')->delete($image->image_path);
                    $image_path = $request->file('image_path');
                    if ($image_path) {
                        $image_path_name = time().$image_path->getClientOriginalName();
                        Storage::disk('images')->put($image_path_name, File::get($image_path));
                    }
                } else {
                    $image_path_name = $image->image_path;
                }
                
                $image->update([
                    'image_path' => $image_path_name,
                    'description' => $request['description']
                ]);
                return redirect()->route('home')->with('message', 'success');
            } else {
                return redirect()->route('home')->with('message', 'error');
            }
            
        } else {
            $validate = $this->validate($request, [
                'image_path' => 'required|image',
                'description' => 'required|string|max:255'
            ]);
            if ($validate) {
                $image_path = $request->file('image_path');
                if ($image_path) {
                    $image_path_name = time().$image_path->getClientOriginalName();
                    Storage::disk('images')->put($image_path_name, File::get($image_path));
                }
                
                Image::create([
                    'user_id' => Auth::user()->id,
                    'image_path' => $image_path_name,
                    'description' => $request->description
                ]);

                return redirect()->route('home')->with('message', 'success');
            } else {
                return redirect()->route('home')->with('message', 'error');
            }
        }
    }

    public function getImage($filename)
    {
        $file = Storage::disk('images')->get($filename);
        return new Response($file, 200, ['Content-Type' => 'image/' . \Illuminate\Support\Facades\File::extension($filename)]);
    }

    public function delete($id)
    {
        $user = Auth::user();
        $image = Image::find($id);
        $comments = Comment::where('image_id', $id)->get();
        $likes = Like::where('image_id', $id)->get();
        if ($user && $image && $image->user->id == $user->id) {

            if ($comments && count($comments) >= 1) {
                foreach ($comments as $comment) {
                    $comment->delete();
                }
            }

            if ($likes && count($likes) >= 1) {
                foreach ($likes as $like) {
                    $like->delete();
                }
            }

            Storage::disk('images')->delete($image->image_path);

            $image->delete();
            $messaje = array('message' => 'Imagen eliminada correctamente');

        } else {
            $messaje = array('message' => 'La imagen no se pudo eliminar');
        }

        return redirect()->route('home')->with($messaje);
    }
}
