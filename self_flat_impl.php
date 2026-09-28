<?php

$argo = array(
    'input'  => "J:/2026-04-25/Sun-darko0_filter-n99--Skywatcher-OtaR62Ap400F-SHG/2026-04-25-1233_6-Sun-darko0-trimmed/cube-png/20260425_rot_linS_img_C0634_P0034.png",
    'output' => "d:/output.jpg",
    'angle'  => "193,419",
    'angle2' => 0,
    'sunspot-mask' => '',
    'sunspot-healed-intermediary-should-be' => '',
    'debug-folder' => false,
    "sunspot-heal-size" => 1,
    "sunspot-heal-extra-rotations" => 0,
);

$prev = '';
foreach ($argv as $a){
    echo $a."\r\n";
    foreach (array_keys($argo) as $k){
        if ($prev == '--'.$k){
            $argo[$k] = $a;
        }
    }
    $prev = $a;
}



if (strpos($argo["angle"], ',')!==false){
    // x, y
    $argo["angle"] = explode(',', $argo["angle"]);
    $argo["angle"] = atan2(floatval($argo["angle"][1]), floatval($argo["angle"][0]));
    var_dump($argo["angle"]);
}

if (strpos($argo["angle"].'', 'deg')){
    // decide the unit perhaps...
}

$argo["angle"] += floatval(deg2rad($argo["angle2"]));


var_dump($argo);

function rotateAndCropImage($imagick, $angle_deg){
    $xy1 = $imagick->getImageGeometry();        
    $imagick->rotateImage(new ImagickPixel(), $angle_deg);
    $xy2 = $imagick->getImageGeometry();        
    $imagick->setImagePage($imagick->getImageWidth(), $imagick->getImageHeight(), 0, 0);
    $imagick->cropImage($xy1["width"], $xy1["height"], ($xy2["width"] - $xy1["width"])/2, ($xy2["height"] - $xy1["height"])/2);
}

function stretchForFlattening($clone, $ow, $oh){
    $clone->resizeImage($ow, 8, Imagick::FILTER_GAUSSIAN , 2);
    $clone->resizeImage($ow, $oh, Imagick::FILTER_GAUSSIAN , 2);
}

function newGrayscaleImage($filename){
    global $argo;
    $i = imagecreatefromstring(file_get_contents($filename));
    ob_start();
    imagepng($i, null, 0);
    $b = ob_get_contents();
    ob_end_clean();
    if ($argo['debug-folder']){
        imagepng($i, $argo['debug-folder']."/impi1.png", 0);        
    };
    $i = new Imagick();
    $i->readImageBlob($b);
    if ($argo['debug-folder']){
        $i->writeImage($argo['debug-folder']."/impi2.png");        
    };
    return $i;
}


    if ('' == $argo["sunspot-mask"]){
        // we don't have a sunspot mask
    }else{
        // we have a sunspot mask, lets heal the input
        if (!file_exists($argo["sunspot-healed-intermediary-should-be"])){
            require_once("sunspot_heal_impl.php");
            $heal_argo = array(
                'input'       => $argo["input"],
                'spot-mask'   => $argo["sunspot-mask"],
                'output'      => $argo["sunspot-healed-intermediary-should-be"],
                "sunspot-heal-size" => $argo["sunspot-heal-size"],
                "sunspot-heal-extra-rotations" => $argo["sunspot-heal-extra-rotations"],
            );
            healsunspots($heal_argo);                
        }
    }

    $imagick = new Imagick($argo["input"]);
    $xy = $imagick->getImageGeometry();        

    $draw = new \ImagickDraw();
    $draw->setStrokeColor(new \ImagickPixel('white'));
    $draw->setFillColor(new \ImagickPixel('black'));
    $draw->rectangle(0,0,$xy["width"], $xy["width"]);
    $draw->setFillColor(new \ImagickPixel('white'));
    $ratio = 2910/4400;
    $solar_diameter = $ratio * $xy["width"];
    $solar_left = ($xy["width"] - $solar_diameter)/2;
    $t = pow($solar_left, 2);
    $cx = $xy["width"] / 2;
    $cy = $xy["width"] / 2;
    $solar_diameter *= 0.99;
    $draw->ellipse($cx, $cy, $solar_diameter/2, $solar_diameter/2, 0, 360);    
    $sundisk = clone $imagick;
    $sundisk->drawImage($draw);
    $sundisk->blurImage($xy["width"]/220,$xy["width"]/220);
    if ($argo['debug-folder']){
        $sundisk->writeImage($argo['debug-folder']."/solardisk.jpg");
    }
    
    


    $to_horiz_angle = -rad2deg($argo["angle"]) + 90;
    rotateAndCropImage($imagick, $to_horiz_angle);

    if ('' == $argo["sunspot-mask"]){
        $clone = clone $imagick;
    }else{
        $clone = newGrayscaleImage($argo["sunspot-healed-intermediary-should-be"]);
        rotateAndCropImage($clone, $to_horiz_angle);
    }
    stretchForFlattening($clone, $xy["width"], $xy["height"]);
    if ($argo['debug-folder']){
        $clone->writeImage($argo['debug-folder']."/grid.jpg");
    };    

    if ('' == $argo["sunspot-mask"]){
        $avg = newGrayscaleImage($argo["input"]);
    }else{
        $avg = newGrayscaleImage($argo["sunspot-healed-intermediary-should-be"]);
    }
    for ($deg =0; $deg<360; $deg += 10){        
        $clone2 = clone $imagick;
        rotateAndCropImage($clone2, $deg);
        $avg->addImage($clone2);
    }
    $avg->resetIterator();
    $average = $avg->averageImages();
    stretchForFlattening($average, $xy["width"], $xy["height"]);
    if ($argo['debug-folder']){
        $average->writeImage($argo['debug-folder']."/avg.jpg");
    };



    $darker = clone $average;
    $darker->compositeImage($clone, Imagick::COMPOSITE_MINUSDST, 0, 0);
    $darker->compositeImage($sundisk, Imagick::COMPOSITE_MULTIPLY, 0, 0);
    if ($argo['debug-folder']){
        $darker->writeImage($argo['debug-folder']."/darko.jpg");
    };
    
    $lighter = clone $average;
    $lighter->compositeImage($clone, Imagick::COMPOSITE_MINUSSRC, 0, 0);
    $lighter->compositeImage($sundisk, Imagick::COMPOSITE_MULTIPLY, 0, 0);
    if ($argo['debug-folder']){
        $lighter->writeImage($argo['debug-folder']."/litto.jpg");
    };
    
    
    //$imagick->compositeImage($lighter, Imagick::COMPOSITE_MINUSSRC, 0, 0);
    //$imagick->compositeImage($darker, Imagick::COMPOSITE_PLUS, 0, 0);
    $imagick->compositeImage($lighter, Imagick::COMPOSITE_PLUS, 0, 0);
    $imagick->compositeImage($darker, Imagick::COMPOSITE_MINUSSRC, 0, 0);


    rotateAndCropImage($imagick, 0-$to_horiz_angle);
    $imagick->writeImage($argo["output"]);


/*    $clone->writeImage($argo["output"]);
    $clone2->writeImage($argo["output"]);
    $average->writeImage($argo["output"]);
*/    


