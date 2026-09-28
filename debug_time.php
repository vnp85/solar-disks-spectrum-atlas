<?php

function Debug_logMoment($s = ''){
   if (!isset($GLOBALS["debug_microtimes"])){
      $GLOBALS["debug_microtimes"] = array();
   };
   $GLOBALS["debug_microtimes"][] = array(microtime(true), $s);
}

function Debug_printMoments(){
    echo "print moments\r\n";    
    Debug_logMoment('print moments');
    //var_dump($GLOBALS["debug_microtimes"]);
    if (count($GLOBALS["debug_microtimes"]) > 0){
        foreach ($GLOBALS["debug_microtimes"] as $m){
            $delta_micros = $m[0] - $GLOBALS["debug_microtimes"][0][0];
            $delta_millis = round($delta_micros * 1000);

            echo $delta_millis.'ms '.$m[1]."\r\n";
        }
    }   
}

Debug_logMoment("BigBang");