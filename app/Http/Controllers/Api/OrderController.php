<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class OrderController extends ApiController
{
	public function orders(Request $request)
	{
		$model = new Order();
		$where = array(
					'status'=>$request->status,
					'customer_id'=>$request->id
				);
		$data = $model->getOrder($where);
		return response()->json([
            'status_code' => 200,
            'status' => 'success',
            'message' => 'Successfully Load Data',
            'data' => $data
        ], 200);
	}

    public function order_detail(Request $request)
    {
        # code...
        $model = new Order();
        $where = array(
                    'order_id'=>$request->order_id
                );
        $data = $model->getOrderDetail($where)->first();
        var_dump($data);exit;
        return response()->json([
            'status_code' => 200,
            'status' => 'success',
            'message' => 'Successfully Load Data',
            'data' => $data
        ], 200);
    }

    public function layanan_laundry(Request $request)
    {
        # code...
        $model = new Order();
        $where = array(
                    'type'=>'Laundry'
                );
        $data = $model->getLayanan($where);
        return response()->json([
            'status_code' => 200,
            'status' => 'success',
            'message' => 'Successfully Load Data',
            'data' => $data
        ], 200);
    }

    public function layanan_service(Request $request)
    {
        # code...
        $model = new Order();
        $where = array(
                    'type'=>'Service'
                );
        $data = $model->getLayanan($where);
        return response()->json([
            'status_code' => 200,
            'status' => 'success',
            'message' => 'Successfully Load Data',
            'data' => $data
        ], 200);
    }

	public function save_bookservice(Request $request)
	{
		# code...
		$this->validate($request, [
    		'id' => 'required',
            'partner' => 'required',
            'layanan' => 'required',
            'type' => 'required',
            'tanggal' => 'required',
            'qty' => 'required',
            'jam' => 'required',
            'alamat' => 'required'
        ]);
        $layanan = explode("#", $request->layanan);
        $unit = "";
        if($request->type=='Cleaning'){
        	$unit = "Jam";
        }elseif ($request->type=='Laundry') {
        	# code...
        	$unit = "Kg";
        }
        $shecdule = date('Y-m-d H:i:s',strtotime(date('Y-m-d',strtotime($request->tanggal)).' '.date('H:i:s',strtotime($request->jam))));

        DB::beginTransaction();
        try {
        	$data = array(
        		'order_id' => Uuid::uuid4(),
        		'order_number' => mt_rand(1000, 9999),
            	'service_type' => $request->type,
            	'customer_id' => $request->id,
            	'partner_id' => $request->partner,
                'layanan_id' => $layanan[0],
            	'schedule_datetime' => $shecdule,
            	'qty' => $request->qty,
            	'unit' => $unit,
            	'amount' => $request->qty*$layanan[1],
            	'address' => $request->alamat,
                'kabupaten_id'=>0,
                'kecamatan_id'=>0,
                'latitude'=> $request->lat,
                'longitude'=> $request->lng,
            	'address_note' => $request->note,
            	'status' => 'Pending',
            	'created_at'=> date('Y-m-d H:i:s')
        	);
        	Order::create($data);
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
        //$order_id = Order::orderBy('created_at', 'DESC')->limit(1)->value('order_id');
        return response()->json([
            'status_code' => 200,
            'status' => 'success',
            'message' => 'Successfully Bookservice'
            //'order_id' => $order_id
        ], 200);
	}
}