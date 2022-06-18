<?php

namespace App\Http\Controllers\Front;

use App\Models\Booking;
use App\Models\BookingTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\FrontBaseController;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontController extends FrontBaseController
{
    public function __construct()
    {
        parent::__construct();

    }

    public function bookingSlots(Request $request)
    {
        $userId = $request->userId;
        $bookingDate = Carbon::createFromFormat('Y-m-d', $request->bookingDate);
        $day = $bookingDate->format('l');
        $bookingTime = BookingTime::withoutGlobalScope('company')->where('user_id', $userId)->where('day', strtolower($day))->first();
        //check if multiple booking allowed
        $bookings = Booking::withoutGlobalScope('company')->where('user_id', $userId)->select('id', 'date_time')->where(DB::raw('DATE(date_time)'), $bookingDate->format('Y-m-d'));
        if ($bookingTime->per_day_max_booking !=(0||'') && $bookingTime->per_day_max_booking <= $bookings->count())
        {
            $msg = __('messages.reachMaxBookingPerDay') . Carbon::createFromFormat('Y-m-d', $request->bookingDate)->format('Y-m-d');
            return ['status' => 'fail', 'msg' => $msg];
        }

        if ($bookingTime->multiple_booking == 'no') {
            $bookings = $bookings->get();
        } else {
            $bookings = $bookings->whereRaw('DAYOFWEEK(date_time) = ' . ($bookingDate->dayOfWeek + 1))->get();
        }

        $variables = compact('bookingTime', 'bookings');

        if ($bookingTime->status == 'enabled') {
            if ($bookingDate->day == Carbon::today()->day) {
                $startTime = Carbon::createFromFormat('H:i:s', $bookingTime->utc_start_time);
                while ($startTime->lessThanOrEqualTo(Carbon::now())) {
                    $startTime = $startTime->addMinutes($bookingTime->slot_duration);
                }
            } else {
                $startTime = Carbon::createFromFormat('H:i:s', $bookingTime->utc_start_time);
            }
            $endTime = Carbon::createFromFormat('H:i:s', $bookingTime->utc_end_time);

            $startTime->setTimezone('UTC');
            $endTime->setTimezone('UTC');

            $startTime->setDate($bookingDate->year, $bookingDate->month, $bookingDate->day);
            $endTime->setDate($bookingDate->year, $bookingDate->month, $bookingDate->day);

            $variables = compact('startTime', 'endTime', 'bookingTime', 'bookings');
        }
        return $variables;
    }

    public function saveBooking($request)
    {

        
        $user = User::firstOrNew(['email' => $request->email]);
        $user->name = $request->first_name . ' ' . $request->last_name;
        $user->email = $request->email;
        $user->mobile = $request->phone;
        $user->calling_code = $request->calling_code;
        $user->password = '123456';
        $user->save();

        $user->attachRole(Role::where('name', 'customer')->first()->id);

        Auth::loginUsingId($user->id);
        $this->user = $user;
        

        $products = (array) json_decode(request()->cookie('products', true));
        $keys = array_keys($products);
        $type = $products[$keys[0]]->type ? $products[$keys[0]]->type : 'booking';

        // get products and bookingDetails
        $products       = json_decode($request->cookie('products'), true);

        /* booking details having bookingDate, bookingTime, selected_user, emp_name */
        $bookingDetails = json_decode($request->cookie('bookingDetails'), true);

        if (is_null($products) && ($type !='booking' || is_null($bookingDetails))) {
            return ['status' => 'fail', 'msg' => ''];;
        }

        if($type == 'booking')
        {
            // get bookings and bookingTime as per bookingDetails date
            $bookingDate = Carbon::createFromFormat('Y-m-d', $bookingDetails['bookingDate']);
            $day = $bookingDate->format('l');
            $bookingTime = BookingTime::where('day', strtolower($day))->first();

            $bookings = Booking::select('id', 'date_time')->where(DB::raw('DATE(date_time)'), $bookingDate->format('Y-m-d'))->whereRaw('DAYOFWEEK(date_time) = ' . ($bookingDate->dayOfWeek + 1))->get();

            if ($bookingTime->max_booking != 0 && $bookings->count() > $bookingTime->max_booking) {
                return ['status' => 'fail', 'msg' => ''];;
            }
        }

        $originalAmount = $taxAmount = $amountToPay = $discountAmount = $couponDiscountAmount = 0;

        $bookingItems = array();

        $companyId = 0;

        foreach ($products as $key => $product) {

            $amount = ($product['quantity'] * $product['price']);

            $deal_id = ($product['type'] == 'deal') ? $product['id'] : null;

            $business_service_id = ($product['type'] == 'service') ? $product['id'] : null;

            $bookingItems[] = [
                "business_service_id" => $business_service_id,
                "quantity" => $product['quantity'],
                "unit_price" => $product['price'],
                "amount" => $amount,
                "deal_id" => $deal_id,
            ];

            $originalAmount = ($originalAmount + $amount);

            $companyId = $product['companyId'];
        }

        $amountToPay = ($originalAmount + $taxAmount);

        $amountToPay = round($amountToPay, 2);

        $dateTime = $type !== 'deal' ? Carbon::createFromFormat('Y-m-d', $bookingDetails['bookingDate'])->format('Y-m-d') . ' ' . Carbon::createFromFormat('H:i:s', $bookingDetails['bookingTime'])->format('H:i:s') : '';


        $booking = new Booking();
        $booking->company_id = $companyId;
        $booking->user_id = $user->id;
        $booking->date_time = $dateTime;
        $booking->status = 'pending';
        $booking->payment_gateway = 'cash';
        $booking->original_amount = $originalAmount;
        $booking->discount = $discountAmount;
        $booking->discount_percent = '0';
        $booking->payment_status = 'pending';
        $booking->additional_notes = $request->additional_notes;
        $booking->location_id = $request->location;
        $booking->source = 'online';
        $booking->amount_to_pay = $amountToPay;
        $booking->save();

        foreach ($bookingItems as $key => $bookingItem) {
            $bookingItems[$key]['booking_id'] = $booking->id;
            $bookingItems[$key]['company_id'] = $companyId;
        }

        DB::table('booking_items')->insert($bookingItems);

        return ['status' => 'sucess', 'msg' => 'Order placed successfully.'];

    }


} /* End of class */
