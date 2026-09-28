<?php
require_once("cube_mirror_server.php");
require_once("wavelengths_info.php");
require_once("hypercube.php");
function getPixelShiftFromCubeFilename($f){
    $f = basename($f);
    $f = str_ireplace('.png', '', $f);
    $f = str_ireplace('.jpg', '', $f);
    $f = explode('_', $f);
    $f = array_pop($f);
    $f = str_replace('P', '+', $f); 
    $f = str_replace('M', '-', $f); 
    $fn = floatval($f);
    if (strlen($f) > 4){
        //var_dump($fn);
        //var_dump($f);
        //die();
    }
    return $fn;
}  

function cube_getAngstromPerPixel($parsedCube){
    $pairs = $parsedCube['pixelWavelengthPairs'];
    $angstrom_per_pixel = 
      ($pairs[0]["lambda_A"] - $pairs[1]["lambda_A"]) / 
        ($pairs[0]["px"] - $pairs[1]["px"]); 
    return  $angstrom_per_pixel;   
}

function json_decode_with_custom_fields($s){
        $parsed = json_decode(trim($s), true);
        if (!isset($parsed['display-notes'])){
            $parsed['display-notes'] = '';            
        }
        for ($q =0; $q<15; $q++){
            $k = 'display-notes-'.$q;
            if (!empty($parsed[$k])){
                $parsed['display-notes'] .= ' '.$parsed[$k];
            }            
        }
        $parsed['display-notes'] = trim($parsed['display-notes']);        

        if (empty($parsed["trim_M"])){
            $parsed["trim_M"] = 0;
        };
        if (empty($parsed["trim_P"])){
            $parsed["trim_P"] = 0;
        };
        if (empty($parsed["instrument-id"])){
            $parsed["instrument-id"] = 1;
        }
        return $parsed;
}

function cube_basicParseJsonFile($filename){    
    $parsed = array();
    try {
        $parsed = json_decode_with_custom_fields(file_get_contents($filename));
        if (empty($parsed["disk-resize-factor"])){
            $parsed["disk-resize-factor"] = 1;
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

function getOptimizedLocalCubeFiles($parsed){
    $foldername = $parsed;
    if (!is_string($parsed)){
        $foldername = $parsed["cubeLocation"];
    }
    $proxify_cubeslices = false;
    if (is_array($parsed)){
        if (isset($parsed["proxify_if_hypercube"])){
            if ($parsed["proxify_if_hypercube"]){
                $proxify_cubeslices = true;
            }
        }
    }
    

    $foldername = str_replace("\\", '/', $foldername);
    $cacheFile = str_replace('cubes/', 'cached-cube-folders/', $foldername);
    $cacheFile .= '.txt';
    $cacheFile = str_replace('/.txt', '.txt', $cacheFile);
    $cacheFolder = dirname($cacheFile);
    
    try{ 
       @mkdir($cacheFolder);
    }catch (Exception $e){
       //die("gyurma");
    }
    $ret = false;

    if (false === $ret){
        if (file_exists($cacheFile)){
            $ret = false;
            try{ 
                $ret = unserialize(file_get_contents($cacheFile));
            }catch (Exception $e){
                $ret = false;
            }        
        }
    }


    if (false === $ret){
        $hc_file = $foldername.'/hyper.cube';
        if (file_exists($hc_file)){
            $hc = new HyperCube();
            $hc_files = $hc->openPack($hc_file)->listContents();
            $ret = array();
            foreach ($hc_files as $hi){
                $ret[] = $foldername.'/'.$hi["basename"];
            }

            if ($proxify_cubeslices){
                foreach ($ret as &$reti){
                    $reti = 'index.php?imgproxy='.$reti;
                }
            }            

            try{ 
              @file_put_contents($cacheFile, serialize($ret));
            }catch (Exception $e){
              //die("gyurma");
            }
        }
    }

    if (false === $ret){
        $ret = array_merge(glob($foldername."/*.png"), glob($foldername."/*.jpg"));
        try{ 
          @file_put_contents($cacheFile, serialize($ret));
        }catch (Exception $e){
          //die("gyurma");
        }
        
    }
    return $ret; 
}

function cube_getFileListOnLocation($parsed){    
    $local_files = getOptimizedLocalCubeFiles($parsed);
    $x = array();
    if (count($local_files) < 10){
        Debug_logMoment('few local files');    
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
    $id = basename($filename);
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
        "display-notes" => '',
        "disk-resize-factor" => 1
    );

    try {
        $parsed = cube_basicParseJsonFile($filename);
        if (isset($parsed['display-notes'])){
            $ret['display-notes'] = $parsed['display-notes'];
        }
        if (isset($parsed["disk-resize-factor"])){
            $ret["disk-resize-factor"] = $parsed["disk-resize-factor"];
        }
        $ret["trim_M"] = $parsed["trim_M"];
        $ret["trim_P"] = $parsed["trim_P"];
        
        //var_dump($parsed);die();
        if (!empty($parsed["cubeLocation"])){
            $started_at = microtime(true);
            $parsed["proxify_if_hypercube"] = true;
            $found_files = cube_getFileListOnLocation($parsed);
            $done_at = microtime(true);

            $duration_millis = ($done_at - $started_at)*1000;

            Debug_logMoment('globbed in '.$duration_millis);    

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

function normalizeWavelengthForSorting($w){
    return round(floatval($w)*1000);
};

function guessWavelengthForSorting($a){
    $ret = array();
    $ret[] = 0;
    if (isset($a["cwl_A"])){
        $ret[] = $a["cwl_A"];
    }

    if (isset($a["cwl_A_declared"])){
        $ret[] = $a["cwl_A_declared"];
    };
    if (isset($a["wavelength-extremes-blue-rounded"])){
        if (isset($a["wavelength-extremes-red-rounded"])){
            $r = floatval($a["wavelength-extremes-red-rounded"]);
            $b = floatval($a["wavelength-extremes-blue-rounded"]);
            $ret[] = $b + ($r - $b)/2;
        }
    }
    if (isset($a["cwl_A_forSorting"])){
        $ret[] = $a["cwl_A_forSorting"];
    }
    foreach ($ret as &$reti){
        $reti = normalizeWavelengthForSorting($reti);
    }
    //var_dump("domingo");
    //var_dump($a);    die();
    //var_dump($ret);
    $ret = array_pop($ret);
    return $ret;
}

function cube_sortParsedCubesByWavelength($a, $b){
    $a = guessWavelengthForSorting($a);
    $b = guessWavelengthForSorting($b);
    return $a - $b;
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
    Debug_logMoment('basic and additional wavelengths loaded');    
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
    Debug_logMoment('parsed cube wavelengths loaded');    
    
    
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

function getListOfCubeJsonFiles(){
    $json_files = glob(dirname(__FILE__).'/cubes-info/cube_*.json');
    return $json_files;
}

function getParsedCubeFiles(){
    $parsedCubes = array();
    foreach (getListOfCubeJsonFiles() as $cubeJson){
        $parsedCubes[] = cube_parseJsonFile($cubeJson);
    };
    return $parsedCubes;
}

function getSimpleParsedCubeFiles(){
    $parsedCubes = array();
    $homedir = dirname(__FILE__);
    $errors = array();
    foreach (getListOfCubeJsonFiles() as $cubeJson){
        $j = json_decode_with_custom_fields(file_get_contents($cubeJson));        
        if (is_array($j)){
            $avg_realpath = realpath($homedir.'/'.$j["cubeLocation"].'/'.$j["averageFilename"]);            
            usort($j["pixelWavelengthPairs"], function ($a, $b){
                return $a["px"] - $b["px"];
            });
            $should_cache_size = false;
            $should_overwrite = false;
            if ((!isset($j["averageFileImageSX"])) || (!isset($j["averageFileImageSY"]))){
                $j["averageFileImageSX"] = 0;
                $j["averageFileImageSY"] = 0;
                $should_cache_size = true;
            };    
            if (($j["averageFileImageSX"] < 10) || ($j["averageFileImageSY"] < 10)){
                $should_cache_size = true;
            }
            if ($should_cache_size){
                $img = imagecreatefromstring(file_get_contents($avg_realpath));
                if (!$img){
                    echo "ISSUE AT FILE: ".$avg_realpath."\r\n";
                }
                $j["averageFileImageSX"] = imagesx($img);
                $j["averageFileImageSY"] = imagesy($img);
                // but rearrange the keys, maybe...
                $should_overwrite = true;                
            }            
            if ($should_overwrite){
                $s = json_encode($j, JSON_PRETTY_PRINT);
                $s = str_replace('\\/', '/', $s);
                file_put_contents($cubeJson, $s);
            }

            $j["realpath_of_average_file"] = $avg_realpath;
            $j["lambda_A_at_pixel"] = array();
            for ($px = 0; $px < $j["averageFileImageSX"]; $px++){                                
                // find the two closest pixel-lambda pairs
                $pp = $j["pixelWavelengthPairs"];
                // this should be copy by value
                usort($pp, function ($a, $b) use ($px){
                    $da = abs($a["px"] - $px);
                    $db = abs($b["px"] - $px);
                    return $da-$db;
                });
                $scale = ($pp[0]["lambda_A"] - $pp[1]["lambda_A"]) / ($pp[0]["px"] - $pp[1]["px"]);
                $lambda = $pp[0]["lambda_A"] - ($pp[0]["px"] - $px) * $scale; 
                $j["lambda_A_at_pixel"][] = $lambda;
                if ($pp[0]["px"] == $px){
                    $bag = $pp[0];
                    $bag["calculated_lambda"] = $lambda;
                    $delta = abs($lambda - $pp[0]["lambda_A"]);
                    if ($delta > 0.001){
                        var_dump($j);
                        var_dump($bag);
                        die("oh boy");
                    }
                }
            }
            $parsedCubes[] = $j;    
        }else{
            $errors[] = $cubeJson;
        }
    };
    return $parsedCubes;
};    

function getSpectroheliographDescriptions(){
    $ret = array();
    $ret[] = array(
        "id" => 1,
        "hint"=> "Stock SolEx"
    );
    $ret[] = array(
        "id" => 2,
        "hint"=> "ML Astro SHG 700"
    );
    $ret[] = array(
        "id" => 3,
        "hint" => "Modified JamesR SolEx with 1200 grating and custom lenses"
    );
    $ret[] = array(
        "id" => 4,
        "hint" => "SolEx with 1800 grating"
    );
    $ret[] = array(
        "id" => 5,
        "hint" => "Modified ML Astro SHG 700 for IR"
    );
    $ret[] = array(
        "id" => 6,
        "hint" => "Modified SolEx with 3600 ln/mm grating"
    );
    return $ret;
}

function decorateWithInstrumentId(&$k, $id){
    $k["instrument-id"] = $id;
    $k["instrument-hint"] = '';
    foreach (getSpectroheliographDescriptions() as $device){
        if ($device["id"] == $id){
            $k["instrument-hint"] = $device["hint"];
        }
    }
    return $k;
};    

function getCanonizedInstrumentIdFromHint($hint, $defaultTo = 1){
    foreach (getSpectroheliographDescriptions() as $dev){
        if ($hint.'' === $dev["id"].''){
            return $dev["id"];
        }
    }
    $hint = ' '.trim(strtolower($hint)).' ';
    foreach (getSpectroheliographDescriptions() as $dev){
        $hinti = strtolower(' '.$dev["hint"].' ');        
        if (strpos($hinti, $hint)!==false){
            return $dev["id"];
        }
    }
    
    return $defaultTo;
}


function getObservedDispersions(){
    $ret = array();
    $ret[] = array(
        "instrument" => "solex 2400",
        "hint" => "helium-iron",
        "px" => 142.8,
        "lambda_A" => abs(5875.6 - 5883.8)
    );
    $ret[] = array(
        "instrument" => "ml astro shg 700",
        "hint" => "helium-sodium",
        "px" => 142.8,
        "lambda_A" => abs(5875.6 - 5889.98)
    );
    $ret[] = array(
        "instrument" => "solex 1800",
        "hint" => "iron-oxygen",
        "px" => 112,
        "lambda_A" => abs(7771.963 - 7780.574)
    );

    return $ret;
}
