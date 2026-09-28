<?php

require_once("cube_utils.php");
require_once("debug_time.php");
require_once("wavelengths_to_colors.php");

class SpectrumCollator {
    private $raiseErrorIfOutOfDomain = array(
        "blue_A" =>  2000,
        "red_A"  => 20000
    );

    const COLORSPACE_MONO = 0;
    const COLORSPACE_NUV_VIS_NIR = 1;
    const COLORSPACE_FORCED = 2;
    const COLORSPACE_PINK_INFRA_MAGENTA_UV = 3;

    public const OUTFILE_GD_IMAGE = NULL;

    private $use_colors = false;
    public function setUseColors($v){        
        $this->use_colors = $this::COLORSPACE_MONO;
        if ($this::COLORSPACE_NUV_VIS_NIR == $v){
            $this->use_colors = $this::COLORSPACE_NUV_VIS_NIR;
        }
        if ($this::COLORSPACE_FORCED == $v){
            $this->use_colors = $this::COLORSPACE_FORCED;
        }
        if ($this::COLORSPACE_PINK_INFRA_MAGENTA_UV == $v){
            $this->use_colors = $this::COLORSPACE_PINK_INFRA_MAGENTA_UV;
        }
        return $this;
    }

    private function colorSpaceToString(){
        if ($this->use_colors == $this::COLORSPACE_MONO){
            return 'mono';
        };
        if ($this->use_colors == $this::COLORSPACE_NUV_VIS_NIR){
            return 'vcol';
        };
        if ($this->use_colors == $this::COLORSPACE_FORCED){
            return 'fcol';
        };
        if ($this->use_colors == $this::COLORSPACE_PINK_INFRA_MAGENTA_UV){
            return 'pimu';
        }
        return 'unknown';
    }

    public function setMono(){
        $this->use_colors = $this::COLORSPACE_MONO;
        return $this;
    }
    public function setNuvVisNir(){
        $this->use_colors = $this::COLORSPACE_NUV_VIS_NIR;
        return $this;
    }
    public function setForcedColor(){
        $this->use_colors = $this::COLORSPACE_FORCED;
        return $this;
    }
    public function setForcedColorPinkIRMagentaUV(){
        $this->use_colors = $this::COLORSPACE_PINK_INFRA_MAGENTA_UV;
        return $this;
    }


    private function isLambdaInDomain($lambda_A){
        $lambda_A = floatval($lambda_A);
        $b = ($lambda_A >= $this->raiseErrorIfOutOfDomain["blue_A"]);
        $r = ($lambda_A <= $this->raiseErrorIfOutOfDomain["red_A"]);
        return $b && $r;
    }

    private function dieIfLambdaIsOutOfDomain($lambda_A){
        if ($this->isLambdaInDomain($lambda_A)){
            // all is fine
            return ;
        }else{
            $err = array(                
                "msg" => "FATAL: Lambda is out of domain",
                "lambda" => $lambda_A,
                "domain" => $this->raiseErrorIfOutOfDomain,                
            );
            var_dump($err);
            die();
        }
    }
    private $considerContinuumAboveLuminanceForGradientCorrection = 256/4;
    private $useGradientCorrection1 = true;
    private $useGradientCorrection2 = 1;
    private $cubes;
    private $monoify = true;
    private $blacklist = array(
        // '0833_1-Sun-3578',// for some reason creates bugs
    );    
    private $_panel_stripe_height = false;

    public function setPanelStripeHeight($h){
        $this->_panel_stripe_height = $h;
        return $this;
    }
    public function panelToFitIntoHeight($h){
        $this->setPanelStripeHeight("fit_into:".$h);
        return $this;
    }
    



    public function __construct(){
        $this->cubes = array();
        foreach (getSimpleParsedCubeFiles() as $cube){
            $allowed = true;
            foreach ($this->blacklist as $b){
                if (strpos($cube["realpath_of_average_file"], $b)!==false){
                    $allowed =false;
                }
            }
            if ($allowed){
                $this->cubes[] = $cube;
            }
        }
        
    }

    private $openImages = array();

    private function isColored($rgb){
        $t = 1.1;
        $g = max(1, $rgb["green"]);
        $r = max(1, $rgb["red"]);
        $b = max(1, $rgb["blue"]);

        $v = array(
            $g / $r,
            $g / $b,
            $r / $b
        );
        foreach ($v as $vi){
            if ($vi > $t){
                return true;
            }
            if ($vi < 1 / $t){
                return true;
            }
        }
        return false;
    }

    private function isLime($rgb){
        return $this->isColored($rgb);

        $g = max(1, $rgb["green"]);
        $r = max(1, $rgb["red"]);
        $b = max(1, $rgb["blue"]);
        $w = 1.2;
        if ((($g / $b) > $w)&&(($g / $r) > $w)){
            return true;
        }
        return false;
    }

    private function rangeIntoU8($a){
        $a = round($a);
        $a = max(0, $a);
        $a = min(255, $a);
        return $a;
    }

    private function fixIfTooDark(&$oi){
        if (count($oi["avg"]) > 400){
            $lum_below = 170;
            $lum_max = 230;
            if ($oi["maxRedBlue"] < $lum_below){
                // long strips should not be dark                
                $f = $lum_max / max(1, $oi["maxRedBlue"]);
                for ($x = 0; $x<count($oi["avg"]); $x++){
                    foreach (array('red', 'green', 'blue') as $ch){
                        $oi["avg"][$x][$ch] = min(255, floor($f * $oi["avg"][$x][$ch]));
                    }
                }
            }
        }
    }

    private function fixLimeLine(&$oi){
        $debug = false;
        $multi = 0.97;
        foreach ($oi["limes"] as $lx){
            
            $oi["avg"][$lx]["red"]   = $this->rangeIntoU8($multi * $oi["minRedBlue"]);
            $oi["avg"][$lx]["green"] = $this->rangeIntoU8($multi * $oi["minRedBlue"]);
            $oi["avg"][$lx]["blue"]  = $this->rangeIntoU8($multi * $oi["minRedBlue"]);
        }
        return ;

        // check for lime
        $oi_orig_avg = $oi["avg"];
        for ($iters = 0; $iters<3; $iters++){
            for ($x=0; $x<count($oi["avg"]); $x++){
                $delimefy = 0.75;
                if (in_array($x, $oi["limes"])){                    
                    if ($debug){
                        //var_dump($oi);
                        echo "lime ";
                        var_dump($oi["avg"][$x]);
                    }
                    $rgb_right = $oi["avg"][$x];
                    $rgb_left = $oi["avg"][$x];
                    for ($left = 1; $left < 10; $left++){
                        if ($x-$left >= 0){
                            $rgb_left = $oi["avg"][$x-$left];
                            if (!$this->isLime($rgb_left)){
                                break;
                            }
                            $delimefy *= $delimefy;
                        }else{
                            $rgb_left = $oi["avg"][$x];
                        }
                    }
                    for ($right = 1; $right < 10; $right++){
                        if ($x+$right < count($oi["avg"])){
                            $rgb_right = $oi["avg"][$x+$right];
                            if (!$this->isLime($rgb_right)){
                                break;
                            }
                            $delimefy *= $delimefy;
                        }else{
                            $rgb_right = $oi["avg"][$x];
                        }
                    }

                    foreach (array_keys($oi["avg"][$x]) as $ch){
                        $p1 = round($delimefy*($rgb_right[$ch]+$rgb_left[$ch])/2);
                        $p2 = $oi["minRedBlue"]*$delimefy;
                        $oi["avg"][$x][$ch] = min($p2, $p1);
                    }
                    //$oi["avg"][$x]['green'] /= 2;
                    foreach (array_keys($oi["avg"][$x]) as $ch){
                        // $oi["avg"][$x][$ch] = ;
                    }
                    
                    if ($debug){
                        var_dump($rgb_left);
                        var_dump($rgb_right);
                        echo "lime-replaced ";
                        var_dump($oi["avg"][$x]);
                        //die();
                    }                
                }
            }
        }
        $oi["limes"] = array_unique($oi["limes"]);
        if ($debug){
            
            //var_dump($limes);
            //var_dump($oi["filename"]);
            if (strpos($oi["filename"], 'Sun-Ha')!==false){

                foreach ($oi["limes"] as $lime){
                    var_dump($oi_orig_avg[$lime]);
                    var_dump($oi["avg"][$lime]);
                }
                //die();
            }
            //var_dump($oi["avg"]);
            //die();
        }

    }

    private function openImage($filename){        
        
        foreach ($this->openImages as &$oi){
            if ($oi["filename"] == $filename){
                $oi["lastUsedAt"] = date("U");
                return $oi;
            }
        }
        if (count($this->openImages) > 10){
            usort($this->openImages, function ($a, $b){
                return ($a["lastUsedAt"] < $b["lastUsedAt"]) ? -1 : 1;
            });
            $e = array_shift($this->openImages);
            @imagedestroy($e["imageResource"]);
            unset($e["imageResource"]);
            unset($e);
        }
        $oi = array();
        $oi["filename"] = $filename;
        $img = imagecreatefromstring(file_get_contents($filename));
        $oi["imageResource"] = $img; 
        $oi["lastUsedAt"] = date("U");
        $oi["avg"] = array();
        $oi["minRedBlue"] = 256;
        $oi["maxRedBlue"] = 0;
        $oi["limes"] = array();
        $margin = 5;        
        for ($x=0; $x<imagesx($img); $x++){
            $r = 0;
            $g = 0;
            $b = 0;
            $k = 0;
            $had_lime = false;
            for ($y=$margin; $y<imagesy($img)-$margin; $y++){
                $c = $this->getRGB($img, imagecolorat($img, $x, $y));
                $had_lime = $had_lime || $this->isLime($c);
                $r += $c["red"];
                $g += $c["green"];
                $b += $c["blue"];
                $k++;
            }
            $item = array(
                "red" => round($r / $k),
                "green" => round($g / $k),
                "blue" => round($b / $k),
            );
            $had_lime = $had_lime || $this->isLime($item);
            if (!$had_lime){
                $rb2 = round((round($r / $k) + round($b / $k)) / 2);
                if ($oi["minRedBlue"] > $rb2){
                    $oi["minRedBlue"] = $rb2;
                }
                if ($oi["maxRedBlue"] < $rb2){
                    $oi["maxRedBlue"] = $rb2;
                }
            }else{
                $oi["limes"][] = $x;
            }
            $oi["avg"][] = $item;
        }        

        $this->fixLimeLine($oi);

        $this->fixIfTooDark($oi);

        $this->openImages[] = $oi;
        return $oi;
    }

    public function lambdaToPixelInFile($lambda_A){
        $candidates = array();
        $cube_index = 0;         
        foreach ($this->cubes as $cube){
            $x = 0;
            $cube_added = false;            
            foreach ($cube["lambda_A_at_pixel"] as $lambda_A_at_pixel){
                $delta = abs($lambda_A_at_pixel - $lambda_A);                
                if ($delta < 1){
                    if (!$cube_added){
                        $candidates[] = $cube_index;
                        $cube_added = true;
                    }
                }
            }
            $cube_index++;
        }
        $candidates_2 = array();
        foreach ($candidates as $c){
            $smallest_delta = 9999999;
            $x = 0;
            $closest_x = 0;
            $closest_w = 0;
            foreach ($this->cubes[$c]["lambda_A_at_pixel"] as $w){
                $delta = abs($lambda_A - $w);
                if ($delta < $smallest_delta){
                    $smallest_delta = $delta;
                    $closest_x = $x;
                    $closest_w = $w;
                }
                $x++;
            }
            $candidates_2[] = array(
                "cubeIndex" => $c,
                "cubeName" => $this->cubes[$c]["cubeLocation"],
                "closestX" => $closest_x,
                "lambda_A_atClosestX" => $closest_w,
                "pixelWavelengthPairs" => $this->cubes[$c]["pixelWavelengthPairs"],                
            );
        }
        return $candidates_2;
    }

    private function getRGB($i, $c){
        //$ret  = imagecolorsforindex($i, $c);
        //imagecolorsforindex
        $ret = array();
        $ret["red"]   = ($c >> 16) & 0xFF;
        $ret["green"] = ($c >>  8) & 0xFF;
        $ret["blue"]  = ($c >>  0) & 0xFF;
        return $ret;
    }

    private function solutionsToColorForWavelength(&$solutions, $lambda_A){
        $debug = false;
        $r = 0;
        $g = 0;
        $b = 0;
        $kounter = 0;
        if ($debug){
            if (abs(6562.8 - $lambda_A) < 0.2){
                foreach ($solutions as &$solution){
                    $sx = $this->cubes[$solution["cubeIndex"]]["averageFileImageSX"];
                    $cx = $solution["closestX"];
                    $rgb = $solution["avg"][$cx];                
                    echo "mobra ";
                    var_dump($rgb);
                    var_dump($solution["cubeName"]);
                    echo '-----------------';
                };    
                //var_dump($solutions);
                die();
            }

        }

        foreach ($solutions as &$solution){
            $sx = $this->cubes[$solution["cubeIndex"]]["averageFileImageSX"];
            $cx = $solution["closestX"];
            $rgb = $solution["avg"][$cx];                

            $weight = 1;

            $x_edge1 = abs($solution["closestX"] - $sx);
            $x_edge2 = abs($solution["closestX"]);
            $x_edge = ($x_edge2 < $x_edge1) ? $x_edge2 : $x_edge1; 

            $max_start_weight = 255;
            if ($x_edge > $max_start_weight){
                $x_edge = $max_start_weight;
            }
            $weight = $x_edge;
            
            for ($w = 0; $w<$weight; $w++){
                $r += $rgb["red"];
                $g += $rgb["green"];
                $b += $rgb["blue"];
                $kounter++;
            }
        }
        
        $ret = 0;
        if ($kounter > 0){
            $r = min(round($r / $kounter), 255);
            $g = min(round($g / $kounter), 255);
            $b = min(round($b / $kounter), 255);
            
            $lumi = $r + $g + $g + $b;
            //$lumi = 2*$r + 2*$b;
            $lumi = min(round($lumi / 4), 255);
            if ($this->monoify){
                $r = $lumi;
                $g = $lumi;
                $b = $lumi;
            }
            

            $ret = $r*256*256 + $g*256 + $b;
        }
        return $ret;
    }

    public function synthetizeAroundCwl($outfile, $cwl_A, $wings_A, $step_A = 0.5, $gradientCorrectionIterations = false){                
        $cwl_A = $this->somethingToAngstrom($cwl_A);
        $wings_A = $this->somethingToAngstrom($wings_A);
        $step_A = $this->somethingToAngstrom($step_A);
        return $this->synthetizeRange($outfile, $cwl_A - $wings_A, $cwl_A + $wings_A, $step_A, $gradientCorrectionIterations);  
    }     

    private $lastHeight = 0;
    private $lastWidth = 0;

    public function getLastHeight(){
        return $this->lastHeight;
    }
    public function getLastWidth(){
        return $this->lastWidth;
    }
    private function autoFilename($outfile, $bag){
        if ($this::OUTFILE_GD_IMAGE === $outfile){
            // sentinel value for having not a file but a GD object
            return $outfile;
        }
        if (strpos($outfile, '?')!==false){
            $name_components = array();
            if (isset($bag["filename-head"])){
                $name_components[] = $bag["filename-head"];
            }else{
                $name_components[] = 'collated-spectrum';
            };
            if (isset($bag['blue_A'])){
                $blue = round($bag['blue_A']);
                $red = round($bag['red_A']);
            }else{
                $blue = round($bag['cwl_A'] - $bag['wings_A']);
                $red = round($bag['cwl_A'] + $bag['wings_A']);
            }
            $step_mA = round(1000*$bag['step_A']);

            $name_components[] = $this->addLeadingZerosToWavelength($blue);
            $name_components[] = $this->addLeadingZerosToWavelength($red);
            $name_components[] = $this->addLeadingZerosToWavelength($step_mA);
            $name_components[] = $this->colorSpaceToString();
            $bn = implode('_', $name_components);
            foreach (array('.png', '.jpg', '') as $ext){
                $outfile = str_replace('?'.$ext, $bn.$ext, $outfile);
            }
        }
        return $outfile;
    }

    private function addLeadingZerosToWavelength($n){
        $n .= '';
        while (strlen($n) < 5){
            $n = '0'.$n;
        }
        return $n;
    }

    private function somethingToAngstrom($a){
        $units = array(
            array(
                "label" => "mA",
                "multi" => 0.001
            ),
            array(
                "label" => "A",
                "multi" => 1
            ),
            array(
                "label" => "um",
                "multi" => 10*1000
            ),
            array(
                "label" => "nm",
                "multi" => 10
            ),
        );
        foreach ($units as $u){
            if (strpos($a.'', $u["label"])!==false){
                $a = trim(str_replace($u["label"], '', $a));
                $a = floatval($a)*$u["multi"];
            }
        }
        return $a;
    }

    private function ingressRangeValues(&$outfile, &$lambda_A_blue, &$lambda_A_red, &$step_A, $fnHead = false){
        $lambda_A_blue = $this->somethingToAngstrom($lambda_A_blue);
        $lambda_A_red = $this->somethingToAngstrom($lambda_A_red);
        $step_A = $this->somethingToAngstrom($step_A);
        if ($lambda_A_red < $lambda_A_blue){
            $dummy = $lambda_A_red;
            $lambda_A_red = $lambda_A_blue;
            $lambda_A_blue = $dummy;
        }
        $bag = array(
            'blue_A' => $lambda_A_blue,
            'red_A'  => $lambda_A_red,
            'step_A' => $step_A
        );
        if ($fnHead){
            $bag['filename-head'] = $fnHead;
        }
        $outfile = $this->autoFilename($outfile, $bag);

        $this->dieIfLambdaIsOutOfDomain($lambda_A_blue);
        $this->dieIfLambdaIsOutOfDomain($lambda_A_red);
        return $this;
    }

    private const MINIMUM_RANGE_HEIGHT_PX = 300;
    private const ABSOLUTE_MAXIMUM_RANGE_HEIGHT_PX = 1000;
    private const ABSOLUTE_MINIMUM_RANGE_HEIGHT_PX = 100;
    private $_minimumRangeHeight_px = false;

    public function setMinimumRangeHeightPx($v_px){
        $v_px = floatval($v_px);
        if ($v_px){
            //
        }else{
            $v_px = $this::MINIMUM_RANGE_HEIGHT_PX;
        }
        $this->_minimumRangeHeight_px = min(
            $this::ABSOLUTE_MAXIMUM_RANGE_HEIGHT_PX, 
            max(
                $this::ABSOLUTE_MINIMUM_RANGE_HEIGHT_PX, 
                round($v_px)
            )
        );

        return $this;
    }

    public function getMinimumRangeHeightPx(){
        if (false == $this->_minimumRangeHeight_px){
            $this->_minimumRangeHeight_px = $this::MINIMUM_RANGE_HEIGHT_PX;
        }
        return $this->_minimumRangeHeight_px;
    }

    public function getRangeHeightFromProposal($px){
        $ret = $px;
        $ret = max($ret, $this->getMinimumRangeHeightPx());
        $ret = min(
            $this::ABSOLUTE_MAXIMUM_RANGE_HEIGHT_PX, 
            max(
                $this::ABSOLUTE_MINIMUM_RANGE_HEIGHT_PX, 
                $ret
            )
        );
        return round($ret);
    }

    public function synthetizeRange($outfile, $lambda_A_blue, $lambda_A_red, $step_A = 0.5, $gradientCorrectionIterations = false){        
        $this->ingressRangeValues($outfile, $lambda_A_blue, $lambda_A_red, $step_A, false);        
        $imageSx = round(abs($lambda_A_blue - $lambda_A_red) / abs($step_A));
        $imageSy = $this->getRangeHeightFromProposal($imageSx/10);
        $i = imagecreatetruecolor($imageSx, $imageSy);        
        $this->lastHeight = $imageSy;
        $this->lastWidth  = $imageSx;
        $x = 0;
        $colors = array();
        $wavelengths_A = array();
        $solutionsCount = array();
        for ($lambda_A = $lambda_A_blue; $lambda_A < $lambda_A_red; $lambda_A += $step_A){
            $solutions = $this->lambdaToPixelInFile($lambda_A);            
            foreach ($solutions as &$solution){
                $oi = $this->openImage($this->cubes[$solution["cubeIndex"]]["realpath_of_average_file"]);
                $solution["imageresource"] = $oi["imageResource"];
                $solution["avg"] = $oi["avg"];
                
            }
            $colors[] = $this->solutionsToColorForWavelength($solutions, $lambda_A);
            $wavelengths_A[] = $lambda_A;
            $solutionsCount[] = count($solutions);
            $x++;
        }
        // gradient removal
        $use_gradient_correction = $this->useGradientCorrection1;
        $limit = $this->useGradientCorrection2;
        if (true === $this->useGradientCorrection2){
            $limit = 1;
        };
        if (false === $this->useGradientCorrection2){
            $limit = 1; // 0?
        };
        if (is_numeric($gradientCorrectionIterations)){
            $limit = $gradientCorrectionIterations;
            $use_gradient_correction = true;
        }

        for ($gradientIteratrions = 0; $gradientIteratrions < $limit; $gradientIteratrions++){

            if ($use_gradient_correction){
                $gradient_multi = array();
                $gradient_multi_minval = 256;
                $gradient_multi_maxval = 0;
                $x = 0;
                $minimum_range_to_correct_gradient_from__px = 100;
                $maximum_range_to_correct_gradient_from__px = 1000;
                $gradient_range = max($imageSx/10, $minimum_range_to_correct_gradient_from__px);
                $gradient_range = min($gradient_range, $maximum_range_to_correct_gradient_from__px);
                $grads = array($gradient_range, $gradient_range/2);
                $grads = array($gradient_range);
                $konti = $this->considerContinuumAboveLuminanceForGradientCorrection;
                if ($lambda_A < 3700){
                    //$konti /= 4;
                }
                foreach ($colors as $color){            
                    $gradient_multi[] = 0;
                    $k = 0;
                    if ($solutionsCount[$x] > 0){
                        foreach ($grads as $gr){
                            for ($r = 0; $r < $gr; $r++){
                                $xi = $x+$r-$gr/2;
                                $xi = round($xi); 
                                $in_image_range = ($xi >= 0)&&($xi < $imageSx);                                                                
                                if ($in_image_range){                                    
                                    $lumi = $colors[$xi] & 0xFF;
                                    if ($lumi > $konti){
                                        $gradient_multi[$x] += $lumi;
                                        $k++;
                                    }
                                }
                            }
                        }
                    }else{
                        // pixel-wavelength is out of domain
                    }
                    if (0 == $k){
                        $gradient_multi[$x] = 1;
                    }else{
                        $gradient_multi[$x] /= $k;
                        if ($gradient_multi_minval > $gradient_multi[$x]){
                            $gradient_multi_minval = $gradient_multi[$x];
                        }
                        if ($gradient_multi_maxval < $gradient_multi[$x]){
                            $gradient_multi_maxval = $gradient_multi[$x];
                        };         
                    }

                    $x++;
                }
                

                foreach ($gradient_multi as &$gmi){
                    if (($gradient_multi_maxval - $gradient_multi_minval) < 1){
                        $gmi = 1;
                        $gradient_multi_maxval = 1;
                    }else{
                        /*
                        $gmi = $gmi - $gradient_multi_minval;
                        $gmi =  1 - ($gmi / ($gradient_multi_maxval - $gradient_multi_minval)); 
                        $gmi *= 2.5;
                        */
                    }
                }

            }else{
                // no gradient correction
            }
//var_dump($gradient_multi);
//var_dump($gradient_multi_maxval);
//die();

            $x = 0;
            $maxlum = 0;
            foreach ($colors as &$color){
                if ($use_gradient_correction){                    
                    $closest_nosolution = 999;
                    for ($nixi=$x; $nixi>=0; $nixi--){
                        if ($solutionsCount[$nixi] == 0){
                            $dist = abs($nixi - $x);
                            if ($dist < $closest_nosolution){
                                $closest_nosolution = $dist;
                            }
                        }
                    }
                    for ($nixi=$x; $nixi<count($solutionsCount); $nixi++){
                        if ($solutionsCount[$nixi] == 0){
                            $dist = abs($nixi - $x);
                            if ($dist < $closest_nosolution){
                                $closest_nosolution = $dist;
                            }
                        }
                    }
                    $k = $color;
                    $r = ($k >> 16) & 0xFF;
                    $g = ($k >> 8)  & 0xFF;
                    $b = ($k >> 0)  & 0xFF;

                    $add = $gradient_multi[$x];
                    $nearness = $closest_nosolution / ($gradient_range);
                    if ($nearness >= 1){
                        // no job here
                    }else{
                        $add = $add * ($nearness) + ($gradient_multi_maxval*(1-$nearness));
                    }
                    


                    $r = ($r / $add) * $gradient_multi_maxval;
                    $g = ($g / $add) * $gradient_multi_maxval;
                    $b = ($b / $add) * $gradient_multi_maxval;

                    $r = min(255, round($r));
                    $g = min(255, round($g));
                    $b = min(255, round($b));
                    $color = $r*256*256 + $g*256 + $b;
                }else{
                    //no gradient correction
                }
                $lumi = $color & 0xFF;
                $maxlum = max($lumi, $maxlum);
                $x++;
            };    
        };        
        $x = 0;    
        $lambdaIndex = 0;    
        foreach ($colors as $k){
            if (($maxlum >= 230)){
                $r = ($k >> 16) & 0xFF;
                $g = ($k >> 8)  & 0xFF;
                $b = ($k >> 0)  & 0xFF;

                $r = min(255, round($r * 0.9));
                $g = min(255, round($g * 0.9));
                $b = min(255, round($b * 0.9));

                $k = $r*256*256 + $g*256 + $b;
            }
            if (($maxlum < 230)&&($maxlum > 0)){
                // don't stretch to saturation
                $m = $maxlum + 10;
                $r = ($k >> 16) & 0xFF;
                $g = ($k >> 8)  & 0xFF;
                $b = ($k >> 0)  & 0xFF;

                $r = min(255, round($r / ($m / 255)));
                $g = min(255, round($g / ($m / 255)));
                $b = min(255, round($b / ($m / 255)));

                $k = $r*256*256 + $g*256 + $b;
            }
            if ($this->use_colors != $this::COLORSPACE_MONO){
                $current_wavelength = $wavelengths_A[$lambdaIndex];
                $rgb_at_wavelength = wavelengthToColor($current_wavelength, $this->use_colors);
 
                $r = ($k >> 16) & 0xFF;
                $g = ($k >> 8)  & 0xFF;
                $b = ($k >> 0)  & 0xFF;

                $r = min(255, round($r * $rgb_at_wavelength[0] / 255));
                $g = min(255, round($g * $rgb_at_wavelength[1] / 255));
                $b = min(255, round($b * $rgb_at_wavelength[2] / 255));

                $k = $r*256*256 + $g*256 + $b;

            }
            imageline($i, $x, 0, $x, $imageSy-1, $k);
            $lambdaIndex++;
            $x++;
        }  
        if ($this::OUTFILE_GD_IMAGE === $outfile){
            return $i;
        }else{
            imagejpeg($i, $outfile, 95);
        }    
        return $outfile;
    }

    public function synthetizeColumn($outfile, $lambda_A_blue, $lambda_A_red, $step_A = 0.5, $gradientCorrectionIterations = false){        
        $this->ingressRangeValues($outfile, $lambda_A_blue, $lambda_A_red, $step_A, 'column');                
        $i = $this->synthetizeRange(
            $this::OUTFILE_GD_IMAGE, 
            $lambda_A_blue, $lambda_A_red, 
            $step_A, $gradientCorrectionIterations
        );        
        $angle_deg___blue_left_to_blue_top = 270;
        $j = imagerotate($i, $angle_deg___blue_left_to_blue_top, 0x000000);
        @imagedestroy($i);
        unset($i);
        if ($this::OUTFILE_GD_IMAGE === $outfile){
            return $j;
        }else{
            imagejpeg($j, $outfile, 95);
        }    
        return $outfile;
    }

    public function synthetizeRuledColumn($outfile, $lambda_A_blue, $lambda_A_red, $step_A = 0.5, $gradientCorrectionIterations = false){        
        $this->ingressRangeValues($outfile, $lambda_A_blue, $lambda_A_red, $step_A, 'ruledColumn');
        $i = $this->synthetizeColumn(
            $this::OUTFILE_GD_IMAGE, 
            $lambda_A_blue, $lambda_A_red, 
            $step_A, $gradientCorrectionIterations
        );

        $left = 100;
        $top = 100;
        $imagesy_i = imagesy($i);
        $j = imagecreatetruecolor(imagesx($i) + $left, imagesy($i)+$top*2);
        imagecopy($j, $i, $left, $top, 0, 0, imagesx($i), imagesy($i));        
        @imagedestroy($i);        
        unset($i);


        for ($y = 0; $y <= $imagesy_i; $y += 50){
            $p = 100;
            $lambda_text = round(($lambda_A_blue + $step_A*$y) * $p) / $p;
            $lambda_text = number_format($lambda_text, 2, '.', '');
            $iy = $top + $y;
            imageline($j, $left - 10, $iy, $left, $iy, 0xFFFFFF);
            imagestring($j, 4, $left/10, $iy-8, $lambda_text, 0xFFFFFF);
        }
        if ($this::OUTFILE_GD_IMAGE === $outfile){
            return $j;
        }else{
            imagejpeg($j, $outfile, 95);
        }    
        return $outfile;
    }

    
    

    private function setLastWidthHeightFromFile($filename){
        if (file_exists($filename)){
            $info = array();
            $h = getimagesize($filename, $info);
            $this->lastWidth = $h[0];
            $this->lastHeight = $h[1];
        }
        
    }

    private $default_panel_lineStep_A = 200;
    private $default_panel_lambdaStep_A = 0.1;
    private $defaul_panel_lineWidth_factor = 1.2;

    public function setPanelRowStepA($v){
        $v = floatval($v);
        $v = min(5000, $v);
        $v = max(100, $v);        
        $this->default_panel_lineStep_A = $v;
        return $this;
    }

    public function setPanelLambdaStepA($v){
        $v = floatval($v);
        $v = min(10, $v);
        $v = max(0.001, $v);        
        $this->default_panel_lambdaStep_A = $v;
        return $this;
    }

    public function setPanelLineWidthFactor($v){
        $v = floatval($v);
        $v = max($v, 1.0);
        $v = min($v, 1.5);
        $this->defaul_panel_lineWidth_factor = $v;
        return $this;
    }


    public function synthetizePanel($outfile, $lambdaBlue_A, $lambdaRed_A, $lambdaStep_A = false, $lineStep_A = false, $lineWidth_factor = false){
        if (false === $lambdaStep_A){
            $lambdaStep_A = $this->default_panel_lambdaStep_A;
        }
        if (false === $lineStep_A){
            $lineStep_A = $this->default_panel_lineStep_A;
        }
        if (false === $lineWidth_factor){
            $lineWidth_factor = $this->defaul_panel_lineWidth_factor;                        
        }
        $lambdaBlue_A = $this->somethingToAngstrom($lambdaBlue_A);
        $lambdaRed_A = $this->somethingToAngstrom($lambdaRed_A);        
        $lambdaStep_A = $this->somethingToAngstrom($lambdaStep_A);        
        $lineStep_A = $this->somethingToAngstrom($lineStep_A);

        $this->dieIfLambdaIsOutOfDomain($lambdaBlue_A);
        $this->dieIfLambdaIsOutOfDomain($lambdaRed_A);
        if ($lambdaRed_A < $lambdaBlue_A){
            $dummy = $lambdaRed_A;
            $lambdaRed_A = $lambdaBlue_A;
            $lambdaBlue_A = $dummy;
        }

        $infobag = array(
            "filename-head" => 'spectrum-combo',
            'blue_A' => $lambdaBlue_A,
            'red_A'  => $lambdaRed_A,
            'step_A' => $lambdaStep_A,
        );
        
        $outfile = $this->autoFilename($outfile, $infobag);
        $lineWidth_factor = max($lineWidth_factor, 1.0);
        $lineWidth_factor = min($lineWidth_factor, 1.5);
        $lineStep_A = max(50, $lineStep_A);
        $lineStep_A = min($lineStep_A, 1500);
        $lineStep_A = min($lineStep_A, abs($lambdaBlue_A -  $lambdaRed_A));

        $files_to_collate = array();
        $halfWidth = ($lineStep_A/2)*$lineWidth_factor;

        $start_A = $lambdaBlue_A + $halfWidth;
        $end_A = $lambdaRed_A - $halfWidth;
        $end_A = max($end_A, $start_A);

        for ($lambda_A = $start_A; $lambda_A <= $end_A; $lambda_A += $lineStep_A){
            $dest_file = dirname($outfile).'/?.jpg';
            
            $local_bag = array(
               "filename-head" => 'spectrum-combo-piece',

               'cwl_A' => $lambda_A,
               'wings_A'  => $halfWidth,
               'step_A' => $lambdaStep_A,

               "lambda_left" => round($lambda_A - $halfWidth),
               "lambda_right" => round($lambda_A + $halfWidth),
               "lambda_center" => round($lambda_A),                  
            ); 
            $dest_file = $this->autoFilename($dest_file, $local_bag);            
            if (!file_exists($dest_file)){
                $this->synthetizeAroundCwl($dest_file, $lambda_A, $halfWidth, $lambdaStep_A);
            }else{
                $this->setLastWidthHeightFromFile($dest_file);
            }
            $local_bag["filename"] = realpath($dest_file);

            $files_to_collate[] = $local_bag;
        }

        // TODO: make font-aware
        $half_label_width = 16;
        $label_height = 16;

        $fit_into_h = 100*1000;
        $h = $this->getLastHeight();
        if (is_numeric($this->_panel_stripe_height)){
            $h = round(floatval($this->_panel_stripe_height));
        }
        if (strpos($this->_panel_stripe_height, 'fit_into:') === 0){
            $fit_into_h = explode(':', $this->_panel_stripe_height);
            $fit_into_h = floatval($fit_into_h[1]);
        }

        $w = $this->getLastWidth();

        $master_h = $fit_into_h + 1;        
        $h++;
        do {
            $margin_y = max(50, round($h/2));
            $margin_x = min(100, round($w/20));
            $margin_x = max($half_label_width*4, 40);
            $margin_x = round($margin_x);

            $top = round(max(100, $margin_y/10));

            $master_h = (count($files_to_collate))*($h+$margin_y)+$top;

            $h--;
        } while (($master_h > $fit_into_h)&&($h > 10));


        $master_w = $w + $margin_x*2;
        $i = imagecreatetruecolor($master_w, $master_h);
        imagefilledrectangle($i, 0, 0, imagesx($i), imagesy($i), 0x000000);
        $y = $top;
        $mA_per_pixel = 1;
        foreach ($files_to_collate as $f){
            $mA_per_pixel = $f['step_A']*1000;
            $j = imagecreatefromstring(file_get_contents($f["filename"]));
            //imagecopy($i, $j, $margin_x, $y, 0, 0, $w, $h);
            imagecopyresampled($i, $j, $margin_x, $y, 0, 0, $w, $h, imagesx($j), imagesy($j));
            $text_y = round($y + $h + $label_height/2);
            $y += ($h+$margin_y);
            $font = 4;            


            imagestring($i, $font, imagesx($i)/2, $text_y, $f["lambda_center"], 0xFFFFFF);    
            imagestring($i, $font, $margin_x-$half_label_width, $text_y, $f["lambda_left"], 0xFFFFFF);    
            imagestring($i, $font, imagesx($i)-$margin_x-$half_label_width, $text_y, $f["lambda_right"], 0xFFFFFF);    
            @imagedestroy($j);
            unset($j);
        }

        $angstrom = 'A'; chr(197); //'A';        
        $written_mA = 10000;
        $written_scale_label = $written_mA;
        $unit = 'm'.$angstrom;
        $scale_width = round($written_mA / $mA_per_pixel);
        $scale_x1 = imagesx($i) - $scale_width - $margin_x;
        $scale_x2 = $scale_x1 + $scale_width;
        $scale_y =  $text_y + $label_height*3;
        if ($written_mA > 5000){
            $written_scale_label /= 1000;
            $unit = $angstrom;        
        }        
        $scale_wing_px = 2;
        imageline($i, $scale_x1, $scale_y, $scale_x2, $scale_y, 0xFFFFFF);
        imageline($i, $scale_x1, $scale_y-$scale_wing_px, $scale_x1, $scale_y+$scale_wing_px, 0xFFFFFF);
        imageline($i, $scale_x2, $scale_y-$scale_wing_px, $scale_x2, $scale_y+$scale_wing_px, 0xFFFFFF);
        $text_x = round($scale_x1 + $scale_width/2);
        $text_y = round($scale_y + $label_height/5);
        imagestring($i, $font, $text_x, $text_y, $written_scale_label.$unit, 0xFFFFFF);
        

        imagejpeg($i, $outfile, 90);
        return $outfile;
    }

    public function synthetizeAtlasMasterSpectrum($outfile){
        $old_color = $this->use_colors;        
        $this->use_colors = $this::COLORSPACE_PINK_INFRA_MAGENTA_UV;

        $lambdaBlue_A = 3050;
        $lambdaRed_A = 11600;
        $lambdaStep_A = 0.5;

        $master_spectrum_width_px = 8550;
        $master_spectrum_height_px = 780;

        $infobag = array(
            "filename-head" => 'master-spectrum',
            'blue_A' => $lambdaBlue_A,
            'red_A'  => $lambdaRed_A,
            'step_A' => $lambdaStep_A,
        );
        
        $outfile = $this->autoFilename($outfile, $infobag);
        $ms = $this->synthetizeRange($this::OUTFILE_GD_IMAGE, $lambdaBlue_A, $lambdaRed_A, $lambdaStep_A);        
        $ms2 = imagecreatetruecolor($master_spectrum_width_px, $master_spectrum_height_px);
        imagecopyresampled($ms2, $ms, 0,0, 0,0, imagesx($ms2), imagesy($ms2), imagesx($ms), imagesy($ms));
        @imagedestroy($ms);
        unset($ms);

        if ($this::OUTFILE_GD_IMAGE === $outfile){
            return $ms2;
        }else{
            imagejpeg($ms2, $outfile, 95);
        }    
        $this->use_colors = $old_color;                
        return $outfile;       
    }

}