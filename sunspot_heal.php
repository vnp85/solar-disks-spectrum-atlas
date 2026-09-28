<?php

require_once("sunspot_heal_impl.php");

$argo = array(
    'input'       => "d:/20260506_rot_linS_img_C0505_M0125.jpg",
    'spot-mask'   => "d:/20260506_rot_linS_img_C0505_M0125_masked.jpg",
    'output'      => "d:/20260506_rot_linS_img_C0505_M0125_healed.jpg",
    "sunspot-heal-size" => 1,
    "sunspot-heal-extra-rotations" => 0,
);

$prev = '';
foreach ($argv as $a){
    echo $a;
    foreach (array_keys($argo) as $k){
        if ($prev == '--'.$k){
            $argo[$k] = $a;
        }
    }
    $prev = $a;    
}



healsunspots($argo);
