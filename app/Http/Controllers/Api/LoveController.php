<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Love;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class LoveController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $post_id = $request->input('post_id');
        $user_id = $request->input('user_id');

        if ($id) {
            $love = Love::with('post','user')->find($id);
            if ($love) {
                return ResponseFormatter::success(
                    $love,
                    'Love data successfully retrieved'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Love data no available',
                    404
                );
            }
        }

        $love = love::with('post','user');

        if ($post_id) {
            $love->where('post_id', $post_id);
        }

        if ($user_id) {
            $love->where('user_id', $user_id);
        }

        return ResponseFormatter::success(
            $love->get(),
            'Love data successfully retrieved'
        );
    }

    public function store(Request $request)
    {
        try {
            $love = Love::firstOrCreate([
                'post_id' => $request->post_id,
                'user_id' => $request->user()->id,
            ]);
            return ResponseFormatter::success($love, 'Data added successfully');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function destroy(Request $request)
    {
        $loves = Love::where([
            ['post_id', $request->input('post_id')],
            ['user_id', $request->input('user_id')],
        ]);
        if ($loves) {
            $loves->delete();
            if ($loves) {
                return ResponseFormatter::success(
                    $loves,
                    'data deleted successfully'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'love data no available',
                    404
                );
            }
        }
    }
}
