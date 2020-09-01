<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp;

class TherapistLocations extends Model
{
    protected $table='therapist_locations';

    protected $fillable=['lat','lang', 'therapist_id'];



    public static function getTherapistDistancesandTimes($lat, $lang, $nearby){

        $destinations="";
        foreach($nearby as $n){
            $destinations=$destinations.$n['lat_lang'].'|';
        }

        $url="https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=$lat,$lang&destinations=$destinations&key=AIzaSyA38xR5NkHe1OsEAcC1aELO47qNOE3BL-k";

        try{
            //die('dsd');
            $client = new GuzzleHttp\Client();

            $response = $client->get($url);

            $body=$response->getBody()->getContents();

            $jsonobject=json_decode($body, true);
//print_r($jsonobject);die;
            if($jsonobject){
                $distances=$jsonobject['rows'][0]['elements']??[];
            }
            //print_r($distances);die;
            $gradeswisedistances=[];
            $i=0;
            foreach($nearby as $n){
                //echo 'a--b--';
                if(!isset($gradeswisedistances['grade_'.$n['grade']])){
                    $gradeswisedistances['grade_'.$n['grade']]=[];
                }

                if(!isset($gradeswisedistances['grade_'.$n['grade']]['distance'])) {
                    if ($distances[$i]['status'] == 'OK') {
                        //echo "aklads--";
                        $gradeswisedistances['grade_' . $n['grade']]['distance'] = $distances[$i]['distance'];
                        $gradeswisedistances['grade_' . $n['grade']]['duration'] = $distances[$i]['duration'];
                    }
                }
                else{
                    //echo $distances[$i]['status'].'status';
                    if($distances[$i]['status']=='OK')
                        //echo "comparing: ".$gradeswisedistances['grade_'.$n['grade']]['distance']['value'].' with '.$distances[$i]['distance']['value'];
                        if($gradeswisedistances['grade_'.$n['grade']]['distance']['value'] > $distances[$i]['distance']['value']){

                            $gradeswisedistances['grade_'.$n['grade']]['distance']=$distances[$i]['distance'];
                            $gradeswisedistances['grade_'.$n['grade']]['duration']=$distances[$i]['duration'];
                        }

                }


//                if(!isset($gradeswisedistances['grade_'.$n['grade']]['duration']))
//                    if($distances[$i]['status']=='OK')
//                        $gradeswisedistances['grade_'.$n['grade']]['duration']=$distances[$i]['duration'];
//                else{
//                    if($gradeswisedistances['grade_'.$n['grade']]['duration']['value'] > $distances[$i]['duration']['value'])
//                        if($distances[$i]['status']=='OK')
//                            $gradeswisedistances['grade_'.$n['grade']]['duration']=$distances[$i]['duration'];
//                }

                $i++;
            }

            //print_r($gradeswisedistances);die;

            return $gradeswisedistances;


        }catch(GuzzleHttp\Exception\TransferException $e){
            $body=$e->getResponse()->getBody()->getContents();
        }

        return $body;


    }

}
