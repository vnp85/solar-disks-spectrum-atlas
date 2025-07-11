<?php
require_once("cube_mirror_server.php");
require_once("wavelengths_info.php");
function getPixelShiftFromCubeFilename($f){
    $f = basename($f);
    $f = str_ireplace('.png', '', $f);
    $f = str_ireplace('.jpg', '', $f);
    $f = explode('_', $f);
    $f = array_pop($f);
    $f = str_replace('P', '+', $f); 
    $f = str_replace('M', '-', $f); 
    $f = floatval($f);
    return $f;
}  

function cube_getAngstromPerPixel($parsedCube){
    $pairs = $parsedCube['pixelWavelengthPairs'];
    $angstrom_per_pixel = 
      ($pairs[0]["lambda_A"] - $pairs[1]["lambda_A"]) / 
        ($pairs[0]["px"] - $pairs[1]["px"]); 
    return  $angstrom_per_pixel;   
}

function cube_basicParseJsonFile($filename){
    $parsed = array();
    try {
        $parsed = json_decode(file_get_contents($filename), true);
        if (empty($parsed["trim_M"])){
            $parsed["trim_M"] = 0;
        };
        if (empty($parsed["trim_P"])){
            $parsed["trim_P"] = 0;
        };
        if (empty($parsed["instrument-index"])){
            $parsed["instrument-index"] = 1;
        }
        if (!empty($parsed["wavelengths-of-interest"])){
            if(is_string($parsed["wavelengths-of-interest"])){
                if ("pixelWavelengthPairs" == $parsed["wavelengths-of-interest"]){
                    $parsed["wavelengths-of-interest"] = $parsed["pixelWavelengthPairs"];
                }
            }
            
            for ($q=0; $q<count($parsed["wavelengths-of-interest"]); $q++){
                $woi = $parsed["wavelengths-of-interest"][$q];
                if (is_string($woi)){
                    if (stripos($woi, "pixelWavelengthPairs") === 0){
                        $woi = str_replace("pixelWavelengthPairs", "", $woi);
                        $woi = str_replace("[", "", $woi);
                        $woi = str_replace("]", "", $woi);
                        $woi = intval(trim($woi));
                        $woi = $parsed["pixelWavelengthPairs"][$woi];                        
                    }    
                }
                if (!isset($woi["lambda_A"])){
                    if (isset($woi["px"])){
                        $woi["lambda_A"] = $parsed["cwl_A"] + cube_getAngstromPerPixel($parsed)*$woi["px"];
                    }
                }
                $parsed["wavelengths-of-interest"][$q] = $woi;
                if (is_array($parsed["wavelengths-of-interest"][$q])){                    
                    // mekka
                }
            }
            //var_dump($parsed);echo '<br>';
        }        
    }catch(Exception $exception){

    }    
    return $parsed;
}

function cube_getFileListOnLocation($parsed){
    $local_files = array_merge(glob($parsed["cubeLocation"]."/*.png"), glob($parsed["cubeLocation"]."/*.jpg"));
    $x = array();
    if (count($local_files) < 10){
        // probably a placeholder folder,
        //    the bulk of the data may be on a mirror server
        $mirror_files = cube_mirrorServer_getCubeFolderContents($parsed["cubeLocation"]);
        foreach ($mirror_files as $mf){
            $b = basename($mf);
            $found_locally = false;
            foreach ($local_files as $lf){
                if (basename($lf) == $b){
                    $found_locally = true;
                }
            }
            if (!$found_locally){
                $x[] = $mf;
            }
        }
    }else{
        // probably a normally populated folder
    }

    $local_files = array_merge($local_files, $x);
    sort($local_files);

    return $local_files;
}

function cube_parseJsonFile($filename){
    $id = $filename;
    $id = str_replace('.json', '', $id);
    $id = str_replace('/', '_', $id);
    $id = str_replace('.', '_', $id);
    $id = str_replace('?', '_', $id);    

    $ret = array(
        "bluest" => -99999,
        "reddest" => 99999,
        "cube_slices" => array(),
        "mapfile" => $filename,
        "id" => $id,        
        "datetime" => "",
        "trim_P" => 0,
        "trim_M" => 0,
        "instrument-id" => 1,
    );

    try {
        $parsed = cube_basicParseJsonFile($filename);
        $ret["trim_M"] = $parsed["trim_M"];
        $ret["trim_P"] = $parsed["trim_P"];
        
        //var_dump($parsed);die();
        if (!empty($parsed["cubeLocation"])){
            $found_files = cube_getFileListOnLocation($parsed);
            foreach ($found_files as $filename){
                if (strpos($filename, 'img_C')!==false){
                   $ret['cube_slices'][] = $filename;
                }
             };    
             sort($ret['cube_slices']);     
             $m = $ret["trim_M"];
             $p = $ret["trim_P"];

             if (strpos($filename, '20250607')!==false){
                $m = 5;
             }
             while ($m > 0){
                $m--;
                array_shift($ret['cube_slices']);
             }
             while ($p > 0){
                $p--;
                array_pop($ret['cube_slices']);
             }
             $ret["img_src"] = $parsed["cubeLocation"] .'/'. $parsed["averageFilename"];
        }        
    

        if (empty($parsed["datetime"])){
            if (empty($ret["img_src"])){
                $parsed["datetime"] = "";
            }else{
                $parsed["datetime"] = "from-path";
            }
        }
        if ($parsed["datetime"] == 'from-path'){
            $p = $ret["img_src"];
            $p = str_replace('\\', '/', $p);
            $p = explode('/', $p);
            for ($minlen = 3; $minlen < 10; $minlen++){
                foreach ($p as $word){
                    $word = str_replace('_', '-', $word);
                    $word = explode('-', $word);                    
                    if (is_numeric($word[0])){
                        if (strlen($word[0]) === 8){
                            $word[0] = substr($word[0], 0, 4).'-'.substr($word[0], 4, 2).'-'.substr($word[0], 6, 2);
                            $word = implode('-', $word);
                            $word = explode('-', $word);
                        }
                    }
                    if (count($word) >= $minlen){
                        $candidate = true;
                        for ($i=0; $i<$minlen; $i++){
                            if (!is_numeric($word[$i])){
                                $candidate = false;
                            }
                        }
                        if ($candidate){
                            $ret["datetime"] = implode("-", array_slice($word, 0, $minlen));
                            $sharpcap_naming_tail = explode('-', $ret["datetime"]);
                            $sharpcap_naming_tail = array_pop($sharpcap_naming_tail);
                            if ((strlen($sharpcap_naming_tail) == 1)&&(is_numeric($sharpcap_naming_tail))){
                                $ret["datetime"] = explode('-', $ret["datetime"]);
                                array_pop($ret["datetime"]);
                                $ret["datetime"] = implode('-', $ret["datetime"]);
                            }
                        }        
                    }
                }    
            }
        }
                

    
        if (count($ret['cube_slices']) > 1){
            $ret['bluest'] = getPixelShiftFromCubeFilename($ret['cube_slices'][0]);
            $ret['reddest'] = getPixelShiftFromCubeFilename($ret['cube_slices'][count($ret['cube_slices'])-1]);
        }else{
            // no files?
            $ret['bluest'] = 0;
            $ret['reddest'] = 0;
        }

        $prek = 0;
        if (!empty($parsed["lambda-precision"])){
            $prek = $parsed["lambda-precision"];
        };
        $prek = floatval($prek) || 0;
        $multi = pow(10, $prek);


        $ret["wavelength-extremes-red"] = ceil($multi*($parsed["cwl_A"] + cube_getAngstromPerPixel($parsed)*$ret['reddest']))/$multi;
        $ret["wavelength-extremes-blue"] = floor($multi*($parsed["cwl_A"] + cube_getAngstromPerPixel($parsed)*$ret['bluest']))/$multi;
        $ret["wavelength-extremes-datasource"] = "globbed,".$ret['bluest'].','.$ret['reddest'];
    }catch(Exception $e){
        //
    }
    $ret["extremePixelShifts"] = $ret['bluest'].','.$ret['reddest'];
    $ret["cube-slices-list"] = "\r\n".implode(",\r\n", $ret['cube_slices'])."\r\n";
    $ret["cwl_A_declared"] = $parsed["cwl_A"];
    if (!empty($parsed["extremeWavelengths_A"])){
        $ret["wavelength-extremes-red"] = max($parsed["extremeWavelengths_A"]);
        $ret["wavelength-extremes-blue"] = min($parsed["extremeWavelengths_A"]);
        $ret["wavelength-extremes-datasource"] = "parsed";
    }

    $ret["wavelengths-of-interest"] = array();
    if (isset($parsed["wavelengths-of-interest"])){
        if (is_array($parsed["wavelengths-of-interest"])){
            $ret["wavelengths-of-interest"] = $parsed["wavelengths-of-interest"];
        }
    }

    if (isset($parsed["instrument-id"])){
        $ret["instrument-id"] = $parsed["instrument-id"];
    }
       
    
    $ret["wavelength-extremes-blue-rounded"] = floor($ret["wavelength-extremes-blue"]);
    $ret["wavelength-extremes-red-rounded"] = ceil($ret["wavelength-extremes-red"]);

    $ret["img_src_diagram_twin"] = cube_generateDiagramTwin($ret);
    return $ret;
}

function cube_sortParsedCubesByWavelength($a, $b){
    return floatval($a["cwl_A_declared"])*100 - floatval($b["cwl_A_declared"])*100;
}

function cube_generateDiagramTwin($item){
    $ret = '';
    if (isset($item["img_src"])){
        $average_filename = $item["img_src"];
    }else{
        return $ret;
    }    

    $can_cache = true;

    if (!empty($_GET['purge_cache'])){
        if (1 == $_GET['purge_cache']){
            $can_cache = false;
        }
    }

    if (@file_exists($average_filename)){
        $outfilename = dirname($average_filename).'/avg_twin_'.md5_file($average_filename).'.jpg';
        if (file_exists($outfilename) && $can_cache){
            return $outfilename;
        }
        try {
            $i = imagecreatefromstring(file_get_contents($average_filename)); 
            $o = imagecreatetruecolor(imagesx($i), imagesy($i));
            $y = floor(imagesy($i)/2);
            $prev_lum = 0;
            $scanline = array();
            $min_l = 99999;
            $max_l = 0;

            for ($x=0; $x<imagesx($i); $x++){
                $rgb = imagecolorat($i, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = ($rgb) & 0xFF;

                $l = $g;
                $green_pixel = false;
                if ($g >= ($r+$b)*0.6){
                    // the green line
                    $l = $prev_lum;
                    $green_pixel = true;
                }
                $prev_lum = $l;
                $min_l = min($l, $min_l);
                $max_l = max($l, $max_l);
                if ($green_pixel){
                    $l = -1;
                }
                $scanline[] = $l;
            }            

            // linear stretch            
            for ($x=0; $x<count($scanline); $x++){
                $scanline[$x] -= $min_l;
            }
            $source_span = $max_l - $min_l;

            $dest_margin = 10;
            $dest_span = imagesy($i) - ($dest_margin*2);

            imagefilledrectangle($o, 0, 0, imagesx($o), imagesy($o), 0xFFFFFF);
            $prev_point = false;
            for ($x=0; $x<count($scanline); $x++){
                if ($scanline[$x] >= 0){
                    $y = round(imagesy($o) - $dest_margin - $dest_span * ($scanline[$x] / $source_span));
                    if (false === $prev_point){
                        imagesetpixel($o, $x, $y, 0x00);
                    }else{
                        imageline($o, $prev_point[0], $prev_point[1], $x, $y, 0x00);
                    }
                    $prev_point = array($x, $y);    
                }
            }        
            imagejpeg($o, $outfilename, 99);
            imagedestroy($o);            
            imagedestroy($i);            
        } catch (Exception $e) {
            //echo 'Caught exception: ',  $e->getMessage(), "\n";
        }   
        if (file_exists($outfilename)){
            return $outfilename;
        }             
    }else{
        //; 
    }
    return $ret;
}


function getTheWavelengthsOfInterest($parsedCubes = false){
    $wavelengths_of_interest = get_basic_and_additional_wavelengths();
    if (is_array($parsedCubes)){
        foreach ($parsedCubes as $pc){
            if (isset($pc["wavelengths-of-interest"])){
                if (is_array($pc["wavelengths-of-interest"])){
                    foreach ($pc["wavelengths-of-interest"] as $pci){
                        $pci["loadedFrom"] = "cube_json";
                        $pci = wavelengthInfo_getPolyfilledItem($pci);
                        $wavelengths_of_interest[] = $pci;
                    }
                }    
            }
        }    
    }
    
    
    foreach ($wavelengths_of_interest as &$woi){
        if (is_numeric($woi)){
            $woi = array("lambda_A" => $woi);
        }
        if (empty($woi["caption"])){
            $woi["caption"] = $woi["lambda_A"].'&Aring;';
        }
    };
    usort($wavelengths_of_interest, function ($a, $b){
        return $a["lambda_A"]*100 - $b["lambda_A"]*100;
    });

    $last_wavelength = -1;
    $ret = array();
    foreach ($wavelengths_of_interest as $w){
        $w["caption"] = str_replace('%wavelength%', $w["lambda_A"].'&Aring;', $w["caption"]);
        $separated = (abs($w["lambda_A"] - $last_wavelength) > 0.1);
        if ($separated || ($w["must_include"])){
            $ret[] = $w;
        }else{
            // could concat the caption?
        }  
        $last_wavelength = $w["lambda_A"];
    }

    return $ret;
}

function parsedCubes_getCoveredWavelengthIntervals($parsedCubes){
    $intervals = array();
    foreach ($parsedCubes as $cube){
        $intervals[] = array(
            $cube["wavelength-extremes-blue-rounded"], 
            $cube["wavelength-extremes-red-rounded"], 
            $cube["wavelength-extremes-datasource"],
            $cube["wavelength-extremes-blue"],
            $cube["wavelength-extremes-red"],
        );    
    }

    usort($intervals, function ($a, $b){
        return $a[0]*10000 - $b[0]*10000;
    });

    $new_intervals = array();
    foreach ($intervals as $i){
        if (0 == count($new_intervals)){
            $new_intervals[] = $i;
        }else{           
            if ($new_intervals[count($new_intervals)-1][1] >= $i[0]){
                //echo "overlap at:".implode(", ", $new_intervals[count($new_intervals)-1]).' with '.implode(', ', $i)."\r\n";
                $new_intervals[count($new_intervals)-1][1] = max($i[1], $new_intervals[count($new_intervals)-1][1]);
            }else{
                $new_intervals[] = $i;
            }
        }
    }
    return $new_intervals;
}

function coveredWavelengthIntervals_isWavelengthCovered($ci, $lambda_A){
    foreach ($ci as $i){
        if (($lambda_A <= $i[1])&&($lambda_A >= $i[0])){
            return true;
        }
    }
    return false;
}


