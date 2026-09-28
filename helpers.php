<?php

require_once("debug_time.php");
require_once("cube_utils.php");
require_once("cube_mirror_server.php");

/* 

git hint, when copying files and line endings get messed up

EOL windows/linux style new-line enter \r\n

 git add --renormalize .     

*/

function echo_template($filename, $varbag){
    if (empty($varbag["extremePixelShifts"])){
        $varbag["extremePixelShifts"] = '-999999,999999';
    }
    if (empty($varbag['cwl_A'])){
        $varbag['cwl_A'] = '<span class="cwl_angstrom"></span>';
    }
    if (empty($varbag["mapfile"])){
        $varbag["mapfile"] = $varbag["id"].'.json';
    }
    if (empty($varbag["img_src"])){
        $varbag["img_src"] = $varbag["id"].'.jpg';
        $varbag["img_hash_postfix"] = '?hash='.md5_file($varbag["img_src"]);
    }
    if (empty($varbag["title"])){
        $varbag["title"] = '';
    }

    if ($varbag["img_src"] == '?'){
        $varbag["img_src"] = '';
    }else{
        $varbag["img_src"] = 'index.php?imgproxy='.str_replace('?', '&', $varbag["img_src"]);
    }    

    if (!empty($varbag["img_src_diagram_twin"])){
        $varbag["img_src_diagram_twin"] = 'index.php?imgproxy='.$varbag["img_src_diagram_twin"].'&ra='.md5(mt_rand());
    };

    if (!empty($varbag["img_src_spectrum_twin"])){
        $varbag["img_src_spectrum_twin"] = 'index.php?imgproxy='.$varbag["img_src_spectrum_twin"].'&ra='.md5(mt_rand());
        if (!isset($varbag["img_src_spectrum_twin_display"])){
            $varbag["img_src_spectrum_twin_display"] = '';
        }
    }
    if (!isset($varbag["img_src_spectrum_twin_display"])){
        $varbag["img_src_spectrum_twin_display"] = 'none';
    }
    if (empty($varbag["img_src_spectrum_twin"])){
        $varbag["img_src_spectrum_twin"] = '';
    }

    if (empty($varbag["img_hash_postfix"])){
        $varbag["img_hash_postfix"] = '';
    }

    if ($varbag["mapfile"] == '?'){
        $varbag["mapfile"] = '';
    }else{
        $varbag["mapfile"] = 'index.php?mapproxy='.$varbag["mapfile"].'&target='.$varbag["id"].'&random='.mt_rand();        
    }
    if (empty($varbag["lambda-precision"])){
        $varbag["lambda-precision"] = 0;
    }
    if (empty($varbag["rainbow-width"])){
        $varbag["rainbow-width"] = 800;
    }

    $varbag["random"] = mt_rand();

    $f = file_get_contents($filename);
    foreach ($varbag as $key=>$value){
        $what = '%'.$key.'%';
        if (strpos($f, $what)!==false){
            $f = str_replace($what, $value, $f);
        }
    }
    echo $f;
}
function wavelength_nm_to_RGB($wavelength_nm, $blue_extreme = 380, $red_extreme = 780) {
    return wavelength_nm_to_RGB_imp($wavelength_nm, $blue_extreme, $red_extreme, 0);
};      
function wavelength_nm_to_RGB_imp($wavelength_nm, $blue_extreme = 380, $red_extreme = 780, $output_index=0) {
    $gamma = 0.80;
    $intensityMax = 255;

    if(($wavelength_nm >= $blue_extreme) && ($wavelength_nm < 440)) {
        $red = -($wavelength_nm - 440) / (440 - $blue_extreme);
        $green = 0.0;
        $blue = 1.0;
    } else if(($wavelength_nm >= 440) && ($wavelength_nm < 490)) {
        $red = 0.0;
        $green = ($wavelength_nm - 440) / (490 - 440);
        $blue = 1.0;
    } else if(($wavelength_nm >= 490) && ($wavelength_nm < 510)) {
        $red = 0.0;
        $green = 1.0;
        $blue = -($wavelength_nm - 510) / (510 - 490);
    } else if(($wavelength_nm >= 510) && ($wavelength_nm < 580)) {
        $red = ($wavelength_nm - 510) / (580 - 510);
        $green = 1.0;
        $blue = 0.0;
    } else if(($wavelength_nm >= 580) && ($wavelength_nm < 645)) {
        $red = 1.0;
        $green = -($wavelength_nm - 645) / (645 - 580);
        $blue = 0.0;
    } else if(($wavelength_nm >= 645) && ($wavelength_nm <= $red_extreme)) {
        $red = 1.0;
        $green = 0.0;
        $blue = 0.0;
    } else {
        $red = 0.0;
        $green = 0.0;
        $blue = 0.0;
    }

    // Let the intensity fall off near the vision limits

    if(($wavelength_nm >= $blue_extreme) && ($wavelength_nm < 420)) {
        $factor = 0.3 + 0.7 * ($wavelength_nm - $blue_extreme) / (420 - $blue_extreme);
    } else if(($wavelength_nm >= 420) && ($wavelength_nm < 701)) {
        $factor = 1.0;
    } else if(($wavelength_nm >= 701) && ($wavelength_nm <= $red_extreme)) {
        $factor = 0.3 + 0.7 * ($red_extreme - $wavelength_nm) / ($red_extreme - 700);
    } else {
        $factor = 0.6;
        $red = 0.8; 
        $green = $red/4;
        $blue = $red;
        if ($wavelength_nm < 500){
           $red /= 2;
        }else{
            $blue /= 2;
        }
     } 
     
     if (($wavelength_nm > 700)||($wavelength_nm < 400)){
        $factor *= 0.7;  
     }
        


    $rgb = array(0,0,0);

    // Don't want 0^x = 1 for x <> 0
    $rgb[0] = $red == 0.0 ? 0 : round($intensityMax * pow($red * $factor, $gamma));
    $rgb[1] = $green == 0.0 ? 0 : round($intensityMax * pow($green * $factor, $gamma));
    $rgb[2] = $blue == 0.0 ? 0 : round($intensityMax * pow($blue * $factor, $gamma));

    $css_rgb = 'rgb('.implode(',', $rgb).')';

    $rgb_col = $rgb[0] * 256*256 + $rgb[1]*256 + $rgb[2];

    $outputs = array(
        $rgb_col,
        $rgb,
        $css_rgb
    );
    return $outputs[$output_index];
}


function wavelength_nm_to_webColor($wavelength_nm, $blue_extreme = 380, $red_extreme = 780){
    return wavelength_nm_to_RGB_imp($wavelength_nm, $blue_extreme, $red_extreme, 2);    
}
    


function sanitize_file_path($p){
    $p = $p.'';
    $sanipid = $p;
    $sanipid = str_replace('?hash=', '', $sanipid);
    $sanipid = str_replace('&hash=', '', $sanipid);
    foreach (array(
        '?',
        '*',
        '!',
        '://',
        '\\\\',
        '..',
        './',
        '.\\'
    ) as $blacklisted){
        if (strpos($sanipid, $blacklisted)!==false){
            die("illegal proxy path ".$blacklisted.' '.$p);
        }    
    }
    return cube_mirrorServer_replaceSlug($p);    
}

function getRequireStack(){
    $ret = array();
    foreach (debug_backtrace() as $a){
        if ($a["function"] == "require_once"){
            $ret[] = $a;
        }
    }
    return $ret;
}


function rgbScale($rgb, $f){
    $b = $rgb & 0xFF;
    $rgb = $rgb >> 8;
    $g = $rgb & 0xFF;
    $rgb = $rgb >> 8;
    $r = $rgb & 0xFF;

    $b = max(0, min(255, round($f*$b)));
    $g = max(0, min(255, round($f*$g)));
    $r = max(0, min(255, round($f*$r)));

    return $r*256*256 + $g*256 + $b;
}

function get_svg_gamma_items(){
    $html = '<svg height="0">';
    $mini = 40;
    $maxi = 400;
    $step = 10;
    for ($gamma = $mini; $gamma <= $maxi; $gamma+=$step){
        $expo = $gamma/100;
        $html .= '
    <filter id="gamma-'.$gamma.'">
        <feComponentTransfer>
        <feFuncR type="gamma" exponent="'.$expo.'" amplitude="1.0" offset="0" />
        <feFuncG type="gamma" exponent="'.$expo.'" amplitude="1.0" offset="0" />
        <feFuncB type="gamma" exponent="'.$expo.'" amplitude="1.0" offset="0" />
        </feComponentTransfer>
    </filter>
    ';
    }
    $html .= '</svg>';
    $html .= '<script>
    function getSvgGammaClosestToPercent(p, with_id = false){    
       p = parseFloat(p) || 100;       
       p = Math.max(p, '.$mini.');
       p = Math.min(p, '.$maxi.');
       p = Math.floor(p / '.$step.')*'.$step.';
       if (with_id){
         p = "gamma-"+p;
       }
       return p; 
    }
    </script>';
    return $html;
}




