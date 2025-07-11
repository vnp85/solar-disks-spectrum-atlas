<?php

require_once("helpers.php");


function generate_spectrum_jpeg(){
    // with spectrum lines of interest    
    $target = "main_spectrum";
    $margin = 400;
    $lambda_blue = 3450-$margin;
    $lambda_red = 9000;    
    $h = 600;
    $grad = $h/5;
    $dark_lines = array(
        array("cwl" => 3820, "width" => 12), // iron
        array("cwl" => 3934, "width" => 22), // CaK
        array("cwl" => 3969, "width" => 18), // CaH
        array("cwl" => 4308, "width" => 22), // G-band
        array("cwl" => 4861, "width" => 10), // H beta
        array("cwl" => 4957.61, "width" => 8), // Fe (c)
        array("cwl" => 5183.62, "width" => 7),// magnezium triplet
        array("cwl" => 5172.70, "width" => 7),// magnezium triplet
        array("cwl" => 5168.91, "width" => 5),// magnezium triplet
        array("cwl" => 5889.95, "width" => 5),// sodium dublet
        array("cwl" => 5895.92, "width" => 5),// sodium dublet
        array("cwl" => 6563, "width" => 15),// hydrogen alpha
        array("cwl" => 8540, "width" => 15),// calcium
        array("cwl" => 6867, "width" => 15),// telluric O2
        array("cwl" => 7300, "width" => 15),// telluric H2O
        array("cwl" => 7594, "width" => 35),// telluric O2
        array("cwl" => 7000, "width" => -10),// telluric O2
        array("cwl" => 4000, "width" => -10),// telluric O2
    );
    $i = imagecreatetruecolor($lambda_red - $lambda_blue, $h);
    imagefilledrectangle($i, 0, 0, imagesx($i), imagesy($i), 0xFFFFFF);
    for ($lambda_a = $lambda_blue; $lambda_a < $lambda_red; $lambda_a++){
        $x = $lambda_a - $lambda_blue;
        $color = wavelength_nm_to_RGB($lambda_a / 10, $lambda_blue / 10, $lambda_red  / 10);
        foreach ($dark_lines as $dark_line){
            $delta = abs($lambda_a - $dark_line["cwl"]);
            $width = 0.4*($dark_line["width"])/2;
            $special_color = 0;
            if ($width < 0){
                $special_color = 0xFFFFFF;
            }
            if ($delta < abs($width)){
                $color = $special_color;
            }
        }        
        imageline($i, $x, 0, $x, imagesy($i)-$grad, $color); 

        $yh = 0;
        $mod = 4;
        $i_lambda_a = $lambda_a+round($mod/2);
        if ($i_lambda_a % 100 <$mod){
            $yh = $h / 40;            
        }
        if ($i_lambda_a % 500 <$mod){
            $yh = $h / 20;
        }
        if ($i_lambda_a % 1000 <$mod*2){
            $yh *= 2;       
            if ($i_lambda_a % 1000 == 0){                
                $i2 = imagecreatetruecolor(200, 20);
                $faktor = 3.5;
                imagefilledrectangle($i2, 0, 0, imagesx($i2), imagesy($i2), 0xFFFFFF);
                imagestring($i2, 4, 0, 0, $i_lambda_a, 0x00);   
                $dest_y = imagesy($i) - $grad + $yh;
                imagecopyresampled($i, $i2, $x, $dest_y, 0, 0, imagesx($i2)*$faktor, imagesy($i2)*$faktor, imagesx($i2)-1, imagesy($i2)-1);
                imagedestroy($i2);
            }  
        }
        if ($yh > 0){            
            imageline($i, $x, imagesy($i) - $grad, $x, imagesy($i) - $grad + $yh, 0);
        }
    }    

    $main_spectrum = array();
    $main_spectrum['cwl_A'] = 5400;
    $main_spectrum['pixelWavelengthPairs'] = array(
        array("px" => 0, "lambda_A" => $lambda_blue),
        array("px" => imagesx($i)-1, "lambda_A" => $lambda_red)
    );
    $main_spectrum["putMarkerToCwl"] = false;
    $main_spectrum["lambda-precision"] = 2;
    $main_spectrum["extremeWavelengths_A"] = array($lambda_blue+$margin, $lambda_red-$margin);

    imagejpeg($i, $target.'.jpg', 95);
    file_put_contents($target.'.json', json_encode($main_spectrum));
}


generate_spectrum_jpeg();