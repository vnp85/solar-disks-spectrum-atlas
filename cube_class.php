<?php

require_once("debug_time.php");
require_once("cube_utils.php");
require_once("hypercube.php");
require_once("wavelengths_to_colors.php");

class AtlasCubes {
    private $cubes = array();
    public function __construct(){
        $old_folder = getcwd();
        chdir(dirname(__FILE__));
        $simple_parsed = getSimpleParsedCubeFiles();
        $this->cubes = array();
        $ki = 0;
        foreach (getListOfCubeJsonFiles() as $cube){            
            $parsed = cube_parseJsonFile($cube);    
            $parsed["lambda_A_at_pixel"] = $simple_parsed[$ki]["lambda_A_at_pixel"];
            $parsed['lambda_A_of_files'] = array();
            $pixel_shift_of_line_zero = 0;
            $delta = 999999;
            for ($px = 0; $px < count($parsed["lambda_A_at_pixel"]); $px++){
                $local_delta = abs($parsed["lambda_A_at_pixel"][$px] - $parsed["cwl_A_declared"]);
                if ($local_delta < $delta){
                    $delta = $local_delta;
                    $pixel_shift_of_line_zero = $px;
                }
            }

            foreach ($parsed["cube_slices"] as $i){
                $item = array();
                $item["filename"] = $i;
                $px = getPixelShiftFromCubeFilename($i);
                $pxi = $pixel_shift_of_line_zero + $px;
                if ($pxi < 0){
                    // maybe an overflow with the generator math script
                }else{
                    $item["lambda_A"] = $parsed["lambda_A_at_pixel"][$pxi];
                    $parsed['lambda_A_of_files'][] = $item;
                }
            }
            $ki++;

            $this->cubes[] = $parsed;
        };  
        chdir($old_folder);                
    }

    public function getCubeSliceContents($filename){        
        // TODO: implement for hypercube
        if (is_array($filename)){
            $filename = $filename['filename'];
        }
        $filename = dirname(__FILE__).'/'.$filename;
        return file_get_contents($filename);
    }


    public function getImageFilenamesAroundLambda($lambda_A, $max_distance_A = 50, $max_filecount = 20){
        if ($max_filecount < 1){
            $max_filecount = 99999999;
        }
        if ($max_distance_A <= 0){
            $max_filecount = 1;
            $max_distance_A = 0.1;
        }
        $ret = array();
        foreach ($this->cubes as $k){
            foreach ($k['lambda_A_of_files'] as $i){
                $local_delta = abs(abs($i['lambda_A'] - $lambda_A));
                if ($local_delta <= $max_distance_A ){
                    $i["local_delta"] = $local_delta;
                    $ret[] = $i;             
                }
            }
        }
        usort($ret, function ($a, $b){
            $d = $a["local_delta"] - $b["local_delta"];
            if ($d < 0){
                $d = -1;
            }
            if ($d > 0){
                $d = 1;
            }
            return $d;
        });

        while (count($ret) > $max_filecount){
            array_pop($ret);
        }
        return $ret;
    }

    function rgbToLumina($rgb){
       $r = ($rgb >> 16) & 0xFF;
       $g = ($rgb >> 8) & 0xFF;
       $b = $rgb & 0xFF;
       return floor(($r + $g + $g + $b)/4);
    }



    public function getLimbDarkeningFromFile($filename){
        $ret = array();
        $i = imagecreatefromstring($this->getCubeSliceContents($filename));
        $cx = imagesx($i) / 2;
        $cy = imagesy($i) / 2;
        // assume all suns have the same radius inside the same sized jpeg                
        $angles = array(0, 10, 20, 30, 40);
        $all_angles = array();
        foreach ($angles as $a){
            $all_angles[] = $a;
            $all_angles[] = $a+180;
            $all_angles[] = ($a/2);
            $all_angles[] = ($a/2)+180;
        }
        for ($r = 0; $r < $cx; $r++){
            $ret[] = 0;                        
            foreach ($all_angles as $angle){
                $px = $cx + (cos(deg2rad($angle))*($r));
                $py = $cy + (sin(deg2rad($angle))*($r));                
                $ret[count($ret)-1] += $this->rgbToLumina(imagecolorat($i, $px, $py));
            };    
            $ret[count($ret)-1] = $ret[count($ret)-1] / count($all_angles);
        }
        @imagedestroy($i);
        unset($i);
        // ret is pixel value at radius
        return $ret;
    }


    public function getLimbDarkeningAroundLambda($lambda_A, $distance_A = 10){
        $files = $this->getImageFilenamesAroundLambda($lambda_A, $distance_A, -1);
        $limbs = array();
        $k = count($files);
        foreach ($files as $fileItem){
            $limb = $this->getLimbDarkeningFromFile($fileItem);
            while (count($limbs) < count($limb)){
                $limbs[] = 0;
            }
            for ($i=0; $i<count($limb); $i++){
                $limbs[$i] += $limb[$i];
            }
        }
        $max_val = 0;
        for ($fi=0; $fi<count($limbs); $fi++){
            $limbs[$fi] /= $k;
            $max_val = max($limbs[$fi], $max_val);
        }       
        if ($max_val > 0){
            for ($fi=0; $fi<count($limbs); $fi++){
                $limbs[$fi] *= (255 / $max_val);
            };
        } 
        return $limbs;
    }

    public function wavelengthsToLimbDarkening($lambda_As, $output_images = false){
        if (!is_array($lambda_As)){
            $lambda_As = array($lambda_As);
        }
        $ret = array();
        foreach ($lambda_As as $angstrom){
            $inti = $this->getLimbDarkeningAroundLambda($angstrom, 30);            
            if ($output_images){
                $a = $angstrom;
                while (strlen($a) < 6){
                    $a = '0'.$a;
                }
                $im = $output_images.$a.'.jpg';
                $x = (floor(count($inti) / 100)+2)*100;
                $y = 300;
                $i = imagecreatetruecolor($x, $y);
                for ($r = 0; $r < count($inti); $r++){
                    imagesetpixel($i, $r+50, imagesy($i) - 25 - round($inti[$r]), 0xFFFFFF);
                }
                imagejpeg($i, $im, 90);
            }
            $ret[] = array(
                "lambda_A" => $angstrom,
                "px-intensity-center-to-margin" => $inti
            );    
        }
        return $ret;
    }

  
};