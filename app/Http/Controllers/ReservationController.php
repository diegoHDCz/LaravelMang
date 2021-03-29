<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\AreaDisabledDay;
use App\Models\Reservation;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;


class ReservationController extends Controller
{
    public function getReservations() {
        $array = ['error'=>'', 'list'=>[]];
        $daysHelper = ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'];

        $areas = Area::where('allowed', 1)->get();

        foreach($areas as $area) {
            $dayList = explode(',', $area['days']);

            $daysGroup = [];
            $lastDay = intval(current($dayList));
            $daysGroup[] = $daysHelper[$lastDay];
            array_shift($dayList);

            foreach($dayList as $day){
                if(intval($day)!= $lastDay + 1){
                    $daysGroup[] = $daysHelper[$lastDay];
                    $daysGroup[] = $daysHelper[$day];
                }

                $lastDay = intval($day);
            }
            $daysGroup[] = $daysHelper[end($dayList)];

            //Juntando as datas
            $dates = '';
            $close = 0;
            foreach($daysGroup as $group){
                if($close ===0){
                    $dates.=$group;
                }else{
                    $dates.='-'.$group.',';
                }

                $close = 1- $close;
            }

            $dates = explode(',', $dates);
            array_pop($dates);

            $start = date('H:i', strtotime($area['start_time']));
            $end = date('H:i', strtotime($area['end_time']));

            foreach($dates as $dayKey=>$dayValue) {
                $dates[$dayKey].=' '.$start.' às '.$end;
            }

            $array['list'][] = [
                'id'=>$area['id'],
                'cover'=>asset('storage/'.$area['cover']),
                'title'=>$area['title'],
                'dates'=>$dates
            ];
        }


        return $array;
    }

    public function getMyReservations($id, Request $request) {
        $array =['error'=>''];
        $validator = Validator::make($request->all(), [
            'date'=>'required|date_format:Y-m-d',
            'time'=> 'required|date_format:H:i:s',
            'property'=>'required'

        ]);

        if(!$validator->fails()) {
            $date = $request->input('date');
            $time = $request->input('time');
            $property = $request->input('property');

            $unit = Unit::find($property);
            $area = Area::find($id);

            if($unit && $area) {
                $can = true;
                $weekday = date('w',strtotime($date));
                
                $allowedDays = explode(',', $area['days']);

                if(!in_array($weekday, $allowedDays)){
                    $can = false;
                }else{
                    $start = strtotime($area['start_time']);
                    $end = strtotime('-1 hour',strtotime($area['end_time']));
                    $revservationtime = strtotime($time);

                    if($revservationtime <$start || $revservationtime >$end){
                        $can = false;
                    }

                }

                $existingDisabledDays = AreaDisabledDay::where('id_area', $id)
                ->where('day', $date)
                ->count();

                if($existingDisabledDays > 0) {
                    $can = false;
                }

                $existingReservations = Reservation::where('id_area', $id)
                ->where('reservation_date',$date. ' '.$time)
                ->count();
                if($existingReservations > 0) {
                    $can = false;
                }

                if($can){
                    $newReservation = new Reservation();
                    $newReservation->id_unit = $property;
                    $newReservation->id_area = $id;
                    $newReservation->reservation_date = $date.' '.$time;
                    $newReservation->save();

                }else{
                    $array['error'] = 'Reserva não permitida neste dia/horário';
                }

            }else{
                $array['error'] = 'Dados Incorretos';
                return $array;
            }

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }
}
