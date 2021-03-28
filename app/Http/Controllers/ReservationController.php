<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;

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
}
