<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\UserFollower;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FollowController extends Controller
{
    public function all(Request $request)
    {
        $user_id = $request->input('user_id');
        $follower_id = $request->input('follower_id');

        $follow = UserFollower::with('user');

        if ($user_id) {
            $follow->where('user_id', $user_id);
        }

        if ($follower_id) {
            $follow->where('follower_id', $follower_id);
        }

        return ResponseFormatter::success(
            $follow->get(),
            'Data successfully retrieved'
        );
    }

    public function follower(Request $request){
        $user_id = $request->input('user_id');
            $follower = UserFollower::where('user_id', $user_id)->with('follower')->get();
            if ($follower) {
                return ResponseFormatter::success(
                    $follower,
                    'User data successfully retrieved'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'User data no available',
                    404
                );
            }
    }

    public function following(Request $request)
    {
        $follower_id = $request->input('follower_id');
        $follower = UserFollower::where('follower_id', $follower_id)->with('user')->get();
        if ($follower) {
            return ResponseFormatter::success(
                $follower,
                'User data successfully retrieved'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'User data no available',
                404
            );
        }
    }

    public function store(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => ['required'],
                'follower_id' => ['required'],
            ]);

            if ($validation->fails()) {
                $error = $validation->errors()->all()[0];
                return ResponseFormatter::error([
                    'message' => 'Failed to add data',
                    'error' => $error
                ], 'Failed to add data', 422);
            } else {
                $follower = UserFollower::updateOrCreate([
                    'user_id' => $request->user_id,
                    'follower_id' => $request->follower_id,
                ]);
                $following = User::where('id', $request->input('follower_id'))->value('following');
                $following += 1;

                User::where('id', $request->input('follower_id'))->update([
                    'following' => $following
                ]);
                return ResponseFormatter::success($follower, 'Data added successfully');
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
        $user_id = $request->input('user_id');
        $follower_id = $request->input('follower_id');

            $follow = UserFollower::where([
                ['user_id', $user_id],
                ['follower_id', $follower_id]
            ])->delete();
                $following = User::where('id', $request->input('follower_id'))->value('following');
                $following -= 1;

                User::where('id', $request->input('follower_id'))->update([
                    'following' => $following
                ]);
            if ($follow) {
                return ResponseFormatter::success(
                    $follow,
                    'Data deleted successfully'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data no available',
                    404
                );
            }
    }

}
