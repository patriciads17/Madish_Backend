<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{

    public function index()
    {
        $id_user = Auth::user()->id;
        $reservations = Reservation::all()->where('id_customer','=',$id_user); 

        if(count($reservations)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $reservations
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); 

    }

    public function show($id_reservation)
    {
        $id_user = Auth::user()->id;
        $reservation = Reservation::find($id_reservation)->where('id_customer','=',$id_user);

        if(!is_null($reservation)) {
            return response([
                'message' => 'Retrieve Reservation Success',
                'data' => $reservation
            ], 200);
        } 

        return response([
            'message' => 'Reservation Not Found',
            'data' => null
        ], 404); 
    }

    public function store(Request $request)
    {
        $storeData = $request->all(); 
        $validate = Validator::make($storeData, [
            'name' => 'required|max:60|regex:/^[a-zA-Z]+$/|alpha',
            'phone_num' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
            'email' => "required|email:rfc,dns|unique:App\Models\User,email,$id",
            'username' => 'required|min:8',
            'booking_date' => 'required|date_format:Y-m-d H:i:s|after:1 hours',
            'num_customer' => 'required|min:2',
            'status' => 'On Process...'        
        ]);

        $storeData['id_customer'] = Auth::user()->id;
        $storeData['id_reservation'] = $this.generateUniqueCode;

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
        
            $reservation = Reservation::create($storeData);
            return response([
                'message' => 'Add Reservation Success',
                'data' => $reservation
            ], 200); 
    }

    public function destroy($id_reservation)
    {
        
        $reservation = Reservation::find($id_reservation); 
        
        if (is_null($reservation)) {
            return response([
                'message' =>'Reservation Not Found',
                'data' => null
            ], 404);
        }

        if($reservation->delete()) {
            return response([
                'message' =>'Delete Reservation Success',
                'data' => $reservation
            ], 200); 
        } 

        return response([
            'message' => 'Delete Reservation Failed',
            'data' => null,
        ], 400); 

    }

    public function update(Request $request, $id_reservation)
    {
        $reservation = Reservation::find($id_reservation); 
        if (is_null($reservation)) {
            return response([
                'message' =>'Reservation Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all(); 

        if(is_null($updateData['room'])){

            $validate = Validator::make($updateData, [
                'name' => 'required|max:60|regex:/^[a-zA-Z]+$/|alpha',
                'phone_num' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
                'email' => "required|email:rfc,dns|unique:App\Models\User,email,$id",
                'username' => 'required|min:8',
                'booking_date' => 'required|date_format:Y-m-d H:i:s|after:1 hours',
                'num_customer' => 'required|min:2',
                'status' => 'On Process...'
            ]); 

            if($validate->fails())
                return response(['message' => $validate->errors()], 400);

            $reservation->name =$updateData['name'];
            $reservation->phone_num =$updateData['phone_num'];
            $reservation->email =$updateData['email'];
            $reservation->username =$updateData['username'];
            $reservation->booking_date =$updateData['booking_date'];
            $reservation->num_customer =$updateData['num_customer'];
            $reservation->status =$updateData['status'];
            
            if($reservation->save()) {
                return response([
                    'message' => 'Update Reservation Success',
                    'data' =>$reservation
                ], 200);
            } 

        }else{
            $validate = Validator::make($updateData, [
                'room' => 'required',
                'table_num' => 'required',
                'status' => 'required'
            ]);

            if($validate->fails())
                return response(['message' => $validate->errors()], 400);
            
            $reservation->room =$updateData['room'];
            $reservation->table_num =$updateData['table_num'];
            $reservation->status =$updateData['status'];

            if($reservation->save()) {
                return response([
                    'message' => 'Update Reservation Success',
                    'data' =>$reservation
                ], 200);
            } 
        }
        return response([
            'message' => 'Update Reservation Failed',
            'data' => null,
        ], 400); 
    }

    public function generateUniqueCode()
    {
        do {
            $id_reservation = random_int(100000, 999999);
        } while (Product::where("id_reservation", "=", $id_reservation)->first());
  
        return $id_reservation;
    }
}
