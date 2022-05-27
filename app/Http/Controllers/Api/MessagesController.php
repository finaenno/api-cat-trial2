<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Messages;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $receiver_user_id = $request->input('receiver_user_id');
        $messages = $request->input('messages');

        if ($id) {
            $messagess = Messages::with('user')->find($id);
            if ($messagess) {
                return ResponseFormatter::success(
                    $messagess,
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

        $messagess = Messages::with('user');

        if ($receiver_user_id) {
            $messagess->where('receiver_user_id', $receiver_user_id);
        }

        if ($messages) {
            $messagess->where('messages', $messages);
        }

        return ResponseFormatter::success(
            $messagess->paginate($limit),
            'Comment data successfully retrieved'
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'receiver_user_id' => ['required'],
                'messages' => ['required'],
                
            ]);

            if($validation->fails()){
                $error = $validation->errors()->all()[0];
                return ResponseFormatter::error([
                    'message' => 'Failed to add data',
                    'error' => $error
                ], 'Failed to add data', 422);
            }else{
                $messages = messages::updateOrCreate([
                    'receiver_user_id' => $request->receiver_user_id
                ],[
                    'receiver_user_id' => $request->receiver_user_id,
                    'sender_user_id' => $request->user()->id,
                    'messages' => $request->messages                   
                ]);
                return ResponseFormatter::success($messages, 'Data added successfully');
            }                                                           

        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
