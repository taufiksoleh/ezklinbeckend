<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends ApiController
{
    public function index() {
        $user = Auth::guard('api')->user();
        $data = Customer::find($user->customer_id);
        if(!$data){
            return $this->setStatusCode(401)->makeResponse(null, 'Failed To Retrieve Data', [], 'error');
        }

        return $this->setStatusCode(200)->makeResponse($data, 'Success Retrieve User');
    }

    public function edit_profile(Request $request)
    {
    	# code...
    	$this->validate($request, [
    		'id' => 'required',
            'email' => 'email|nullable',
            'username' => 'required',
            'phone' => 'required|numeric'
        ]);

        DB::beginTransaction();
        try {
        	$data = array(
        		'name' => $request->username,
            	'phone' => $request->phone,
            	'email' => $request->email
        	);
        	Customer::where('customer_id',$request->id)->update($data);
        }catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 422,
                'status' => 'error',
                'message' => 'Unknown Error',
                'data' => $e->getMessage()
            ], 422);
        }

        DB::commit();

        return response()->json([
            'status_code' => 200,
            'status' => 'success',
            'message' => 'Successfully Edit Profile',
            'data' => []
        ], 200);
    }
}
