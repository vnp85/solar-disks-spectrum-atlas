<?php

require_once("cube_utils.php");

foreach (getSimpleParsedCubeFiles() as $c){
    $pairs = $c["pixelWavelengthPairs"];
    $cwl_A = $c["cwl_A"];
    $pair_contains_cwl = false;    
    $min_dist = 99999;
    foreach ($pairs as $p){
        $min_dist = min(abs($p["lambda_A"] - $cwl_A), $min_dist);
        if ($min_dist < 0.0150001){
            $pair_contains_cwl = true;
        }
    }
    if (!$pair_contains_cwl){
        $bag = array(
            "cubeLocation" => $c["cubeLocation"],
            "minDist" => $min_dist,
            "cwl_A" => $c["cwl_A"]
        );
        var_dump($bag);        
    }
}