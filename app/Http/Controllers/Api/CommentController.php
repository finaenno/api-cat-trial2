<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class CommentController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $post_id = $request->input('post_id');
        $user_id = $request->input('user_id');

        if ($id) {
            $comment = Comment::with('post', 'user')->find($id);
            if ($comment) {
                return ResponseFormatter::success(
                    $comment,
                    'Comment data successfully retrieved'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Comment data no available',
                    404
                );
            }
        }

        $comment = Comment::with('post', 'user');

        if ($post_id) {
            $comment->where('post_id', $post_id);
        }

        if ($user_id) {
            $comment->where('user_id', $user_id);
        }

        return ResponseFormatter::success(
            $comment->get(),
            'Comment data successfully retrieved'
        );
    }

    public function store(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'post_id' => ['required'],
                'description' => ['required'],
            ]);

            if($validation->fails()){
                $error = $validation->errors()->all()[0];
                return ResponseFormatter::error([
                    'message' => 'Failed to add data',
                    'error' => $error
                ], 'Failed to add data', 422);
            }else{
                $comment = Comment::create([
                    'post_id' => $request->post_id,
                    'user_id' => $request->user()->id,
                    'description' => $request->description
                ]);
                return ResponseFormatter::success($comment, 'Data added successfully');
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
            $comment = Comment::destroy($id);
            if ($comment) {
                return ResponseFormatter::success(
                    $comment,
                    'data deleted successfully'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Comment data no available',
                    404
                );
            }
        }
    }
}
