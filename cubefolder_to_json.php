<?php

$homedir = str_replace('\\', '/', dirname(__FILE__));

$cubeFolders = glob($homedir.'/cubes/*', GLOB_ONLYDIR);
$cubeFiles = glob($homedir.'/cube_*.json');
$cubeFilesReferenceFolders = array();
foreach ($cubeFiles as $c){
    $c = json_decode(file_get_contents($c), true);
    $l = str_replace("\\", "/", $c["cubeLocation"]);
    $cubeFilesReferenceFolders[] = $l;
}


$cubeFolders = array_filter($cubeFolders, function ($e) use (&$cubeFilesReferenceFolders){
    $e = str_replace("\\", "/", $e).'/';
    foreach ($cubeFilesReferenceFolders as $c){
       if (strpos($e, $c)!==false){
          return false;
       }  
    }
    return true;
});

foreach ($cubeFolders  as $cubeFolder){
    echo $cubeFolder."\r\n";
    $average = '';
    $marked_average = '';
    $index_php = '';
    foreach (glob($cubeFolder.'/*.*') as $filename){
       if (strpos(basename($filename), 'average') !== false){
          $average = $filename;
       }
       if (strpos(basename($filename), 'marked_avg') !== false){
          $marked_average = $filename;
       }
       if (strpos(basename($filename), 'index.php') !== false){
          $index_php = $filename;
       }
    }
    if ('' == $index_php){
        file_put_contents($cubeFolder.'/index.php', '<?php //silence');        
    }
    if ('' == $marked_average){
        if ('' != $average){
            // let us create a marked average
            $i = imagecreatefromstring(file_get_contents($average));
            $o = imagerotate($i, -90, 0);            
            // find the cwl
            $y = floor(imagesy($i) / 2);
            $cwl = -1;
            for ($x = 0; $x < imagesx($i); $x++){
                $rgb = imagecolorat($i, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $ti = 80; // target intensity
                $tid = 1.2;//target intensity diff

                //echo $r.' '.$g.' '.$b.''."\r\n";

                if ((($g > $ti) && ($g > $r*$tid)) 
                   || 
                   (($r > $ti) && ($r > $b*$tid))){
                       // highlighted color
                       $cwl = $x;
                   }                
            }
            if ($cwl > -1){
                echo 'has CWL '.$cwl;
                $d = 0.2;
                $blueline = floor(imagesx($i)*(1 - $d));
                if ($cwl > imagesx($i) / 2){
                    $blueline = floor(imagesx($i)*($d));
                }
                imageline($o, 0, $blueline, imagesx($o)-1, $blueline, 0x0000FF);
            }else{
                echo 'no cwl here';
            }
            echo "\r\n";

            if ($cwl > -1){
                $ma = str_replace('average', 'marked_avg', $average);
                imagejpeg($o, $ma, 95);

                $ma = explode('.', $ma);
                array_pop($ma);
                $ma[] = 'json';
                $ma = implode('.', $ma);
                echo 'json file='.$ma."\r\n";
                if (!file_exists($ma)){
                    file_put_contents($ma, '{ "file_kind": "dev,debug,not-data", "blue_px": '.$blueline.', "blue_angstrom": 99999.9, "green_px": '.$cwl.', "green_angstrom": 99999.9 }');
                }
            }
            imagedestroy($i);
            imagedestroy($o);            
        }
    }
};

$renames_executed = 0;                

foreach ($cubeFolders  as $cubeFolder){
    $tails_to_replace = array('betablue', 'unknown', '99999', '9999');
    $cube_hints = false;
    foreach (glob($cubeFolder.'/*.*') as $filename){
       if (strpos(basename($filename), 'marked_avg') !== false){
        if (strpos(basename($filename), '.json') !== false){
          $cube_hints = json_decode(file_get_contents($filename), true);
        }
       } 
    }
    if ($cube_hints){
        foreach ($tails_to_replace as $t){
            $cf = $cubeFolder;
            $cf = str_replace('\\', '/', $cf);
            $cf = explode('/', $cf);
            $cf[count($cf)-1] = str_replace($t, floor($cube_hints["green_angstrom"]), $cf[count($cf)-1]);
            $cf = implode('/', $cf);
            if ($cf != $cubeFolder){
                rename($cubeFolder, $cf);
                $renames_executed++;                
            }
        }
    }
};


if ($renames_executed > 0){
    die("folders have been renamed");
}else{
    echo "nothing renamed :)\r\n";
}


foreach ($cubeFolders  as $cubeFolder){
    $cubeFolder = str_replace("\\", "/", $cubeFolder).'/';
    $jpg_files = glob($cubeFolder.'/*.jpg');
    $json_files = glob($cubeFolder.'/*.json');
    $avg = array_filter($jpg_files, function ($e){
       if (strpos($e, 'average')!==false){
          return true;
       }
       return false;
    });
    $marked_avg = array_filter($json_files, function ($e){
       if (strpos($e, 'marked_avg.json')!==false){
          return true;
       }
       return false;
    });

    if (count($marked_avg) == 1){
       $marked_avg = json_decode(file_get_contents($marked_avg[0]), true);
       $marked_avg["cwl_A"] = $marked_avg["green_angstrom"];
       $marked_avg["pixelWavelengthPairs"]= array(
        array( "px" => $marked_avg["green_px"], "lambda_A" => $marked_avg["green_angstrom"] ),
        array( "px" => $marked_avg["blue_px"], "lambda_A" => $marked_avg["blue_angstrom"] ),
       );        
    }else{
       $marked_avg = array(); 
       $marked_avg["cwl_A"] = 99999.99;
       $marked_avg["pixelWavelengthPairs"]= array(
        array( "px" => 9999, "lambda_A" => 9999.99 ),
        array( "px" => 9999, "lambda_A" => 9999.99 ),
       );        
    }
    
    $k = array();
    $k["cubeLocation"] = str_replace('*'.$homedir.'/', '', '*'.$cubeFolder);
    $k["averageFilename"] =  basename($avg[0]);
    $k["datetime"] = "from-path";
    $k["cwl_A"] = $marked_avg["cwl_A"];
    $k["putMarkerToCwl"] = false;
    $k["lambda-precision"] = 2;
    $k["pixelWavelengthPairs"] = $marked_avg["pixelWavelengthPairs"];
    $k["_wavelengths-of-interest"] = "pixelWavelengthPairs";
    $k["title"] = "not used atm";
    $k["trim_M"] = 0;
    $k["trim_P"] = 0;
    var_dump($k);
    $candidate_filename = explode('/', $k["cubeLocation"]);
    $n = array_pop($candidate_filename);
    if (!$n){
        $n = array_pop($candidate_filename);
    }
    $candidate_filename = explode('-', $n);
    $last = array_pop($candidate_filename);
    $candidate_filename = $homedir.'/cube_'.$candidate_filename[0].$candidate_filename[1].$candidate_filename[2].'_'.$candidate_filename[3].'_'.$last.'.json';    
    if (!file_exists($candidate_filename)){
        $j = json_encode($k, JSON_PRETTY_PRINT);
        $j = str_replace('\\', '/', $j);
        $j = str_replace('//', '/', $j);
        file_put_contents($candidate_filename, $j);
    }
};



