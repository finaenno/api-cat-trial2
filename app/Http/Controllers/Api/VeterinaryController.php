<?php

namespace App\Http\Controllers\Api;

use App\Models\Veterinary;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class VeterinaryController extends Controller
{
    public function all()
    {
        $vete = Veterinary::orderBy('name','asc')->get();
        if($vete){
            return ResponseFormatter::success(
                $vete,
                'Veterinary data successfully retrieved'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Veterinary data no available',
                404
            );
        }
    }

}
