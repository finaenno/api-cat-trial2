<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $user_id = $request->input('user_id');

        if ($id) {
            $post = Post::with('user')->find($id);
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

        $post = Post::with('user');

        if ($user_id) {
            $post->where('user_id', $user_id);
        }

        return ResponseFormatter::success(
            $post->paginate($limit),
            'Post data successfully retrieved'
        );
    }

    public function store(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,svg|max:2048'],
                'description' => ['required', 'string'],
                'lat' => ['nullable'],
                'long' => ['nullable'],
            ]);

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
                }
                $post = Post::create([
                    'user_id' => $request->user()->id,
                    'photo' => $path,
                    'description' => $request->description,
                    'lat' => $request->lat,
                    'long' => $request->long,
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
                    'description' => ['required', 'string'],
                    'lat' => ['nullable'],
                    'long' => ['nullable'],
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
                        $post->description = $request->description;
                        $post->lat = $request->lat;
                        $post->long = $request->long;
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