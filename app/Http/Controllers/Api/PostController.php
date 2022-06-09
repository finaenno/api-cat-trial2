<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Google\Cloud\Storage\StorageClient;

class PostController extends Controller
{

    public function all(Request $request)
    {
        $id = $request->input('id');
        $user_id = $request->input('user_id');

        if ($id) {
            $post = Post::withCount(['user', 'loves', 'comments'])->with(['user', 'loves', 'comments'])->find($id);
            if ($post) {
                return ResponseFormatter::success(
                    $post,
                    'Post data successfully retrieved'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Post data no available',
                    404
                );
            }
        }

        $post = Post::withCount(['user','loves','comments'])->with(['user', 'loves', 'comments']);



        if ($user_id) {
            $post->where('user_id', $user_id);
        }

        return ResponseFormatter::success(
            $post->get(),
            'Post data successfully retrieved'
        );
    }

    public function store(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,svg|max:2048'],
                'title' => ['required', 'string'],
                'description' => ['required', 'string'],
            ]);

            // $storage = new StorageClient([
            //     'keyFilePath' => getcwd(). '/../flowing-silo-350506-e0fbc96d1dcf.json',
            // ]);

            // $bucketName = 'cat-pedigree-posts';
            // $bucket = $storage->bucket($bucketName);

            if ($validation->fails()) {
                $error = $validation->errors()->all()[0];
                return ResponseFormatter::error([
                    'message' => 'Failed to add data',
                    'error' => $error
                ], 'Failed to add data', 422);
            } else {
                if ($request->photo && $request->photo->isValid()) {
                    $slug = Str::slug($request->user()->username);
                    $fileName = 'photo-' . $slug . '-' . time() . '.' . $request->photo->extension();
                    $request->photo->storeAs('public/posts', $fileName);
                    $path = "posts/$fileName";
                    // $request = $bucket->upload(
                    //     fopen($fileName, 'r'),
                    //     [
                    //         'predefinedAcl' => 'publicRead'
                    //     ]
                    // );
                    // echo "File uploaded successfully. File path is: https://storage.googleapis.com/$bucketName/$fileName";
                }
                $post = Post::create([
                    'user_id' => $request->user()->id,
                    'photo' => $path,
                    'title' => $request->title,
                    'description' => $request->description,
                ]);
                return ResponseFormatter::success($post, 'Data added successfully');
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');

        if ($id) {
            $post = Post::destroy($id);
            if ($post) {
                return ResponseFormatter::success(
                    $post,
                    'data deleted successfully'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Post data no available',
                    404
                );
            }
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            if ($id) {
                $post = Post::find($id);
                $validation = Validator::make($request->all(), [
                    'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,svg|max:2048'],
                    'title' => ['required', 'string'],
                    'description' => ['required', 'string'],
                ]);
                if ($validation->fails()) {
                    $error = $validation->errors()->all()[0];
                    return ResponseFormatter::error([
                        'message' => 'Cat Failed to change',
                        'error' => $error
                    ], 'Cat Failed to change', 422);
                } else {
                    if ($post) {
                        $post->photo = $request->photo;
                        $post->title = $request->title;
                        $post->description = $request->description;
                        if ($request->photo && $request->photo->isValid()) {
                            $slug = Str::slug($request->user()->username);
                            $fileName = 'photo-' . $slug . '-' . time() . '.' . $request->photo->extension();
                            $request->photo->storeAs('public/photos', $fileName);
                            $path = "photos/$fileName";
                            $post->photo = $path;
                        }
                        $post->update();
                        return ResponseFormatter::success(
                            $post,
                            'Post updated'
                        );
                    } else {
                        return ResponseFormatter::error(
                            null,
                            'Post data no available',
                            404
                        );
                    }
                }
            } else {
                return ResponseFormatter::error(
                    null,
                    'Post data no available',
                    404
                );
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }
}
