<?php
//--------------------------
function analyzePixel($i, $x, $y){
    $ret = array(
        "i" => true,  // image-valid
        "c" => false, // colored        
        "h" => false  // bright
    );
    if ($x < 0){
        $ret["i"] = false;
        return $ret;
    }
    if ($x >= imagesx($i)){
        $ret["i"] = false;
        return $ret;
    }
    if ($y < 0){
        $ret["i"] = false;
        return $ret;
    }
    if ($y >= imagesy($i)){
        $ret["i"] = false;
        return $ret;
    }
    $pxm = imagecolorat($i, $x, $y);
    $ret = array(
        "p" => $pxm,
        "r" => ($pxm >> 16) & 0xFF,
        "g" => ($pxm >> 8) & 0xFF,
        "b" => ($pxm >> 0) & 0xFF,
        "c" => false,
        "i" => true,        
        "h" => false,
    );    
    $considerColoredAbove = 15;
    if (abs($ret["r"] - $ret["g"]) > $considerColoredAbove){
        $ret["c"] = true;
    }
    if (abs($ret["b"] - $ret["g"]) > $considerColoredAbove){
        $ret["c"] = true;
    }
    if (abs($ret["b"] - $ret["r"]) > $considerColoredAbove){
        $ret["c"] = true;
    }
    if ($ret["g"] > 80){
        $ret["h"] = true;
    }
    return $ret;
}

function buildAverageFor($i, $m, $x, $y){
    $max_r = imagesx($i) / 20;
    $acc = 0;
    $minimumPixelCount = 10;
    $r = 2;
    $acc_r = 0;
    $acc_g = 0;
    $acc_b = 0;
    $acc_c = 0;

    while (($acc < $minimumPixelCount)&&($r < $max_r)){
        $circum = 2*M_PI*$r;
        $angle_step = floor(360/($circum*1.2));
        $angle_step = max($angle_step, 0.5);
        for ($angle = 0; $angle < 360; $angle+= $angle_step){
            $px = $x + (cos(deg2rad($angle))*($r));
            $py = $y + (sin(deg2rad($angle))*($r));
            $mpx = analyzePixel($m, $px, $py);
            if ($mpx["i"]){
                if ($mpx["c"]){
                    // colored, ignore
                }else{
                    $weight = $max_r - $r;
                    $ipx = analyzePixel($i, $px, $py);
                    if ($ipx["h"]){
                        // this is a bright enough pixel
                        for ($w = 0; $w < $weight; $w++){
                            $acc_r += $ipx["r"];
                            $acc_g += $ipx["g"];
                            $acc_b += $ipx["b"];
                            $acc_c++;
                        }
                    }
                }
            }
        }
        $r++;
    }

    $acc_r /= $acc_c;
    $acc_g /= $acc_c;
    $acc_b /= $acc_c;

    $acc_r = floor(min(255, $acc_r));
    $acc_g = floor(min(255, $acc_g));
    $acc_b = floor(min(255, $acc_b));

    $ret = $acc_r*256*256 + $acc_g*256 + $acc_b;
    
    return $ret;
}

function healsunspots($argo){
    $i = imagecreatefromstring(file_get_contents($argo['input']));
    $m = imagecreatefromstring(file_get_contents($argo['spot-mask']));

    //repair the circumference first
    $def_ratio = (732 / 1100);
    $sun_diameter = imagesx($i) * $def_ratio;
    $sun_radius = $sun_diameter / 2;
    $rot_deg_array = array();
    $rot_deg_array[] = 2;
    if ($argo["sunspot-heal-extra-rotations"] > 0){
        $rot_deg_array[] = 3;
    }
    if ($argo["sunspot-heal-extra-rotations"] > 1){
        $rot_deg_array[] = 4;
    }
    $rot_deg_array[] = 5;
    if ($argo["sunspot-heal-extra-rotations"] > 2){
        $rot_deg_array[] = 6;
    }
    if ($argo["sunspot-heal-extra-rotations"] > 3){
        $rot_deg_array[] = 7;
    }
    $rot_deg_array[] = 8;
    $rot_deg_array[] = 10;
    $rot_deg_array[] = 15;

    $rot_deg_array_pairs = array();
    foreach ($rot_deg_array as $rdi){
        $rot_deg_array_pairs[] = $rdi;
        $rot_deg_array_pairs[] = 0 - $rdi;
    }

    foreach ($rot_deg_array_pairs as $rot_deg){

        $ri = imagerotate($i, $rot_deg, 0xFF0000);
        $rm = imagerotate($m, $rot_deg, 0xFF0000);    
        $cx = imagesx($ri)/2;
        $cy = imagesy($ri)/2;

        foreach (array(
            array(
                "start_at" => $sun_radius*0.9,
                "angle_step" => 0.25,
                "radius_step" => 1
            ),
            array(
                "start_at" => $sun_radius*0.95,
                "angle_step" => 0.125,
                "radius_step" => 1
            ),
            array(
                "start_at" => $sun_radius*0.9,
                "angle_step" => 0.3,
                "radius_step" => 2
            ),
            array(
                "start_at" => $sun_radius*0.8,
                "angle_step" => 0.125,
                "radius_step" => 2
            ),
            array(
                "start_at" => $sun_radius*0.6,
                "angle_step" => 0.5,
                "radius_step" => 2
            ),
            array(
                "start_at" => $sun_radius*0.4,
                "angle_step" => 1,
                "radius_step" => 3
            ),
            array(
                "start_at" => 0,
                "angle_step" => 2,
                "radius_step" => 2
            ),
        ) as $item){
            for ($r = max(2, $item["start_at"]); $r<$sun_radius; $r += $item["radius_step"]){
                // $item["angle_step"]
                $angle_step = min($item["angle_step"], max(0.1, ($r * 2 * M_PI) / 360));
                
                for ($angle = 0; $angle < 360; $angle += $angle_step){
                    $rpx = round($cx + (cos(deg2rad($angle))*($r)));
                    $rpy = round($cy + (sin(deg2rad($angle))*($r)));

                    $mpx = round(imagesx($m)/2 + (cos(deg2rad($angle))*($r)));
                    $mpy = round(imagesy($m)/2 + (sin(deg2rad($angle))*($r)));
                    $original_mask_px = analyzePixel($m, $mpx, $mpy);
                    if ($original_mask_px['c']){
                        $rotated_mask_px = analyzePixel($rm, $rpx, $rpy);
                        if ($rotated_mask_px['c']){
                            // the rotated mask is also marked
                        }else{
                            // the rotated mask is not marked, hence we could steal some pixels
                            $rotated_image_pixel = imagecolorat($ri, $rpx, $rpy);
                            imagesetpixel($i, $mpx, $mpy, $rotated_image_pixel);
                            imagesetpixel($m, $mpx, $mpy, $rotated_image_pixel);
                        }
                    }
                }
            }

        }
    }
/*
    imagejpeg($ri, 'd:/ri.jpg', 90);
    imagejpeg($rm, 'd:/mi.jpg', 90);
    imagejpeg($m, 'd:/mi2.jpg', 90);
    die();
/* */

    $drop_size = max($argo["sunspot-heal-size"], 3);

    foreach (array(16, 12, 8, 6, 4, 2, 1) as $d){
        for ($y = 0; $y < imagesy($i); $y+= $d) {
            $has = false;
            echo $d.':'.$y."\r\n";
            for ($x = 0; $x < imagesx($i); $x+= $d){
                $mask_px = analyzePixel($m, $x, $y);
                if ($mask_px["c"]){
                    $has = true;
                    $apx = buildAverageFor($i, $m, $x, $y);
                                    
                    $dm = $drop_size - 1;
                    if ($d > 10){
                        $dm++;
                    }
                    if ($dm > 0){
                        imagefilledrectangle($i, $x-$dm, $y-$dm, $x+$dm, $y+$dm, $apx);
                        imagefilledrectangle($m, $x-$dm, $y-$dm, $x+$dm, $y+$dm, $apx);
                        /*
                        for ($dx = -$dm; $dx<=$dm; $dx++){
                            for ($dy = -$dm; $dy<=$dm; $dy++){
                                imagesetpixel($i, $x+$dx, $y+$dy, $apx);                    
                                imagesetpixel($m, $x+$dx, $y+$dy, $apx);
                            }
                        };
                        */
                    }else{
                        imagesetpixel($i, $x, $y, $apx);                    
                        imagesetpixel($m, $x, $y, $apx);
                    }
                }
            }    
            if (!$has){
                // this scanline had no events
            }
        }
        //imagejpeg($m, "d:/mask_".$d.'.jpg', 90);
    } 


    imagejpeg($i, $argo["output"], 90);
}

