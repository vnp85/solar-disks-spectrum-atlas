<?php

// from 

/*

THE ASTROPHYSICAL JOURNAL
AN INTERNATIONAL REVIEW OF SPECTROSCOPY AND
ASTRONOMICAL PHYSICS
volume Lxxi JANUARY 1930 number i


THE SPECTRUM OF THE CHROMOSPHERE
By S. A. MITCHELL

found on 

https://articles.adsabs.harvard.edu/pdf/1930ApJ....71....1M

*/

function ChromosphereFlashSpectrum_getMemoizeKeyword(){
    return 'ChromosphereFlashSpectrum_get_memoized';
}

function ChromosphereFlashSpectrum_get(){
    $magic = ChromosphereFlashSpectrum_getMemoizeKeyword();
    if (isset($GLOBALS[$magic])){
        // already prepared
    }else{
        $GLOBALS[$magic] = ChromosphereFlashSpectrum_get_raw();
    }
    return $GLOBALS[$magic];
}

function ChromosphereFlashSpectrum_memozie(){
    $magic = ChromosphereFlashSpectrum_getMemoizeKeyword();
    if (isset($GLOBALS[$magic])){
        // already prepared
    }else{
        ChromosphereFlashSpectrum_get();
    };
    return $magic;
}


function ChromosphereFlashSpectrum_get_raw(){
    $s = "
    // page 11
    lambda A,  chem,  disk, flash, height km
    3088.02,  Ti+,     7,    12, 2000
    3118.62,  Cr+,     2,    12, 1500
    3120.36,  Cr+,     3,    10, 1500
    3125.00,  Cr+,     4,    15, 1500 
    3132.05,  Cr+,     4,    20, 1500
    3162.56,  Ti+,     4,    10, 1000
    3168.49,  Ti+,     4,    12, 1000
    3183.67,  Cr+-Cr,  4,    10, 1000
    3187.71,  He-V+,   2,    10, 1200
    3190.82,  Ti+-V+,  7,    12, 1200
    3202.50,  Ti+-Fe,  2,    10, 1000     
    3213.27,  Fe+-Ti+, 4,    12, 1000
    // page 12
    3222.89,  Ti+,     4,    10, 1500
    3227.74,  Fe+-Fe,  4,    12, 1000
    3234.49,  Ti+,     3,    30, 2000   
    3236.59,  Ti+,     7,    25, 2000
    3239.03,  Ti+,     7,    20, 2000
    3242.00,  Ti+,     8,    20, 2000
    3251.96,  Ti+,     7,    10, 1500
    3252.94,  Ti+-Mn,  9,    12, 1500
    3261.57,  Ti+,     7,    10, 1500
    3277.36,  Fe+-Co,  7,    12, 1200
    3287.65,  Ti+-Fe,  5,    10, 1000
    3322.91,  Ti+,     8,    20, 1500
    // page 13
    3329.42,  Ti+-Co,  8,    18, 1500
    3332.08,  Ti+,     3,    12, 1200
    3335.18,  Ti+,     6,    15, 1500
    3340.33,  Ti+,     5,    15, 1500
    3341.88,  Ti+-Fe,  8,    25, 2000
    3346.73,  Ti+-Cr,  5,    10, 1000
    3349.00,  Ti+-Cr,  9,    20, 1500
    3349.41,  Ti+,     9,    35, 2500
    3358.50,  Cr+,     4,    12, 1200
    3363.35,  Cr+,     2,    10, 1000
    3361.24, Ti+-Sc+,  5,    25, 2500
    3368.07, Cr+,      5,    18, 1500 
    3372.81, Ti+,     10,    30, 2500
    3380.25, Ti+-Fe,   9,    15, 1500
    3382.72, Cr+,      4,    12, 1500 
    3383.84, Ti+-Fe,   9,    25, 2500
    3387.88, Ti+-Zr+,  5,    18, 1500   
    3394.56, Ti+-Fe,   6,    15, 1500
    3403.35, Cr+-Ni,   6,    12, 1500
    3408.81, Cr+,      3,    15, 1500
    3421.25, Cr+,      4,    12, 1500
    3422.71, Cr+-Fe,   7,    15, 1500
    //page 14
    3433.34, Cr+,      3,    18, 1500
    3440.62, Fe,      20,    12, 1500
    3441.01, Fe,      15,    10, 1500
    3441.97, Mn+,      6,    20, 1500
    3444.30, Ti+,      4,    12, 1200
    3456.44, Ti+,      3,    10,  800
    3460.32, Mn+,      4,    18, 1500
    3461.46, Ti+,      5,    12, 1200
    3465.79, Ti+-Co,  12,    10, 1000
    3474.11, Mn+,      4,    15, 1500
    3477.17, Ti+,      5,    12, 1200
    3482.95, Mn+,      5,    15, 1500
    3488.68, Mn+,      4,    12, 1200
    3491.03, Ti+,      5,    10, 1200
    3496.18, Zr+-Y+,   3,    10, 1200
    3504.88, Ti+-Fe,   5,    18, 1500
    3510.87, Ti+,      5,    15, 1500
    // page 15
    3520.23, Ti+,      2,    10, 1000
    3524.50, Ni,      20,    12,  800
    3535.41, Ti+,      4,    20, 1200
    3545.23, V+,       4,    10, 1000
    3556.71, Zr+-V+,  11,    15, 1000
    3558.53, Sc+-Fe,   8,    10, 1000
    3565.37, Fe-Ti+,  15,    12, 1200
    3570.14, Fe-Mn,   24,    15, 1200
    3572.52, Sc+-Zr+, 10,    18, 1200
    3576.38, Sc+,      7,    15, 1200
    3578.73, Cr,      10,    12, 1200
    3580.96, Sc+,      5,    10, 1200
    3581.22, Fe,      30,    20, 1500
    3585.4,  ~Fe,     10,    15, 1200
    3589.72, V+-Sc+,  10,    10,  800 
    3593.41, V+-Cr,   12,    12, 1000
    3596.03, Ti+,      4,    10, 1200
    // page 16
    3600.77, Y+,       3,    12, 1200
    3608.86, Fe,      20,    12, 1200
    3613.82, Sc+,      7,    25, 1500
    3618.77, Fe,      20,    12, 1200
    3624.84, Ti+-Fe,   5,    12, 1200
    3630.73, Sc+-Ca,   7,    20, 1500
    3631.48, Fe,      15,    12, 1200
    3641.35, Ti+,      4,    15, 1200
    3642.74, Sc+-Ti,  12,    18, 1200
    3645.34, Sc+-La+,  6,    12, 1200
    3647.81, Fe,      12,    12, 1500
    3651.77, Sc+,      4,    10, 1200
    3659.70, Ti+-Fe,  10,    12, 1000
    3662.23, Ti+-H30,  5,    15, 1200
    3664.66, Y+-H28,   2,    15, 1500
    3666.09, H27,      0,    12, 1500
    3667.74, H26,      0,    12, 1800
    3669.46, H25,      0,    18, 1800
    3671.33, Zr+-H24,  0,    18, 2000
    // page 17
    3673.82, H23,      0,    18, 2000
    3674.74, Zr+-Ti+,  3,    10,  600
    3676.34, Fe-Cr-H22, 6,   20, 2200
    3677.79, Cr+-Fe,  13,    15, 1000
    3682.82, H20,      0,    25, 2500
    3685.25, Ti+,     10,    80, 6000
    3686.83, H19,      0,    30, 3000
    3691.62, H18,      0,    35, 3000
    3694.13, Fe-Ni,   10,    12,  800
    3697.21, H17,      0,    40, 3500
    3703.89, H16,      0,    45, 4000
    3706.11, Ca+-Ti+,  9,    20, 1200
    3710.34, Y+,       3,    20,  800
    3712.06, H15,      0,    50, 5000
    3712.89, Cr+-Mn,   5,    10,  800
    3719.94, Fe,      40,    35, 2000
    3722.00, H14,      0,    55, 5600 
    3734.45, ~H13-Fe,  0,    70, 5600
    3737.00, ~Ca+-Ni-Fe, 30, 40, 2000
    3741.64, Ti+,      4,    25, 2000
    3743.49, Fe-Cr,   12,    10, 1000
    3745.78, Fe,      14,    30, 2000
    3747.8, ~Y+-Ti+Fe,10,    20, 1500
    3749.50, Fe,      20,    15, 1000
    3750.25, H12,      0,    70, 6000
    3757.66, Ti+-Cr,   4,    10, 1000
    3758.25, Fe,      15,    10, 1000
    3759.33, Ti+,     12,    70, 6000
    // page 18
    3761.5, ~Ti+,      7,    70, 6000
    3763.80, Fe,      10,    15, 1000
    3767.15, Fe,       8,    10, 1000
    3770.72, H11,      0,    80, 6000
    3774.38, Y+,       3,    20,  800
    3788.66, Y+,       2,    18,  800
    3798.02, H10,      0,    90, 6000
    3814.53, Ti+-Fe,   7,    10,  700
    3815.82, Fe,      15,    15, 1500
    3819.63, He,       0,    10, 5000
    3820.43, Fe,      25,    20, 1500
    3824.46, Fe,       6,    15, 1500
    3825.86, Fe,      20,    20, 1500
    3827.79, Fe,       8,    10, 1200
    3829.35, Mg,      10,    40, 6000
    3832.34, Mg,      15,    50, 6000
    3835.54, H9,       0,   100, 7000
    3838.30, Mg,      25,    60, 7000
    3840.44, Fe-CN,    8,    10, 1200

    // page 19
    3841.07, Fe-Mn,     10,  10, 1200
    3843.13, Zr+-Fe,    10,  10,  800
    3856.26, Fe-Si+,     9,  20, 1500
    3859.87, Fe,        20,  35, 2500
    3878.65, Fe-V+,     11,  20, 1500
    3886.32, Fe-La+,    15,  25, 1500
    3889.20, H dzeta,     0, 120, 8500
    3895.68, Fe,         5,  15, 1200
    3899.70, Fe,         8,  10, 1200
    3900.54, Ti+,        5,  40, 2000
    3905.53, Si,        12,  12,  800
    3906.48, Fe,        10,  10,  800
    // page 20
    3913.55, Ti+-Fe,     9,  40, 2500
    3914.45, Fe-Ti,      8,  10,  750
    3920.25, Fe,        10,  15, 1200
    3922.92, Fe,        12,  20, 1500
    3927.96, Fe,        10,  20, 1500
    3933.90, Ca+,     1000, 200, 14000
    3944.03, Al,        15,  25, 2000
    3950.33, Y+,         2,  10,  800
    3958.23, Zr+-Ti,     5,  20,  800
    3961.51, Al,        20,  35, 2000    
    3964.72, He,         0,   8, 1200
    3968.70, Ca+,      700, 175, 14000
    3970.25, H epsilon,  5, 120, 8500
    3981.81, Fe+-Ti+,    6,  10,  700
    3982.55, Y+-Ti,      5,  12,  900
    3988.47, La+,        0,  10,  600
    3991.16, Zr+-Cr,     3,  10,  700 
    // page 21
    3998.74, Zr+-Ti,     5,  10,  800
    3999.21, Ce+,        1,  10,  800
    4012.41, Ti+-Ce+,    4,  20, 1500
    4014.53, Sc+-Fe,     5,  10,  800
    4025.14, Ti+,        3,  10,  750
    4026.28, He,         0,  30, 5000
    4028.36, Ti+,        4,  12,  800
    4030.79, Mn,         9,  20, 1000
    4033.07, Mn,         7,  18, 1000
    4034.47, Mn,         6,  15, 1000
    4045.84, Fe,        30,  30, 1800
    // page 22
    4063.62, Fe,        20,  20, 1500
    4071.75, Fe,        15,  15, 1500
    4077.83, Sr+,        8,  80, 6000
    4101.85, H delta,   40, 140, 8000
    4118.70, Fe-Co,     11,  10,  600
    4129.73, Eu+,        1,  10,  600
    4143.90, ~He-Fe,    15,  14, 2000
    4149.24, ~Zr+-Fe,    6,  15,  900 
    4156.15, Zr+-Nd+,    5,  12,  500
    4163.66, Ti+-Cr,     4,  15, 1000
    4167.24, Mg,         8,   4,  800
    4171.95, Ti+-Fe,     2,  15, 1000
    4173.48, Ti+-Fe+,    8,  15, 1000
    4177.54, Y+-Fe,      6,  20, 1000
    4178.87, Fe+,        3,  15, 1000
    4202.11, Fe,         8,  10,  900
    4215.70, Sr+-CN,     5,  60, 6000
    4226.74, Ca,        20,  40, 5000
    4233.22, Fe+,        4,  30, 2200
    4235.92, Fe-Y+,     10,  10,  900
    4250.85, Fe,         9,  10,  900
    4254.36, Cr,         8,  25, 1500
    4260.51, Fe,        10,  10, 1000
    // page 25
    4271.77, Fe,        15,  15, 1500
    4274.77, Cr-Ti,      9,  20, 1500
    4289.5, ~Ca-Cr,      5,  18, 1500
    4290.18, Ti+,        2,  18, 2000
    4294.07, Ti+-Fe,     7,  18, 2000
    4300.05, Ti+,        3,  25, 2000
    4307.86, ~Ca-Ti+-Fe, 8,  25, 2000
    4312.82, Ti+,        3,  12, 1200
    4314.08, Sc+,        3,  12, 1200
    4314.97, Ti+-Fe,     8,  15, 1200
    4320.77, Sc+-Ti+,    5,  25, 1500
    4325.79, Fe-Ni,     10,  15, 1500
    4333.72, La+,        1,  10,  600
    4340.63, H gamma,   20, 160, 8000
    // page 26
    4351.84, Fe+-Cr,    10,  15, 1200
    4374.49, Sc+-Fe,     3,  10, 1000
    4375.00, Y+-Mn,      2,  12, 1000
    4383.54, Fe,        15,  15, 1600
    4395.13, Ti+-V,      5,  40, 2500
    4399.79, Ti+,        3,  10,  800
    4404.79, Fe,        10,  12, 1200
    44I7.7I, Ti+,        3,  12, 1200
    // page 27
    4443.85, Ti+, 5, 30, 2500
    4468.48, Ti+, 5, 40, 2500
    4471.54, He, 0, 80, 7500
    // page 28
    4501.28, Ti+, 5, 25, 2500
    4508.32, Fe+, 4, 12, 900
    45I5.32, Fe+, 3, 10, 800
    4520.22, Fe+, 3, 12, 800
    4522.67, Fe+-Ti, 5, 18, 1000
    4549.63, Ti+-Fe+, 8, 50, 2500
    4554.11, Ba+, 8, 50, 2000
    4555.89, Fe+, 3, 20, 1000
    4558.57, Cr+-La+, 3, 15, 1500
    4563.76, Ti+, 4, 30, 2500 
    // page 29
    4571.08, Mg,  5,  6,  700
    4572.00, Ti+, 6, 35, 2500
    4583.86, Fe+, 4, 25, 1500
    4589.93, Ti+, 3, 10, 1000
    4629.42, Fe+-Ti, 6, 20, 1000
    // page 30
    4685.83, He+, 0, 2, 3500
    4702.99, Mg, 10, 6, 500
    // page 31
    
    4779.97, Ti+-Co, 2, 10, 500
    4786.50, Y+-Ni, 3, 10, 450
    4805.15, Ti+, 3, 15, 800
    4823.46, Mn-Y+, 5, 10, 750
    4824.09, Cr+-Fe, 3, 12, 750
    4861.50, H beta, 30, 200, 8500
    // page 32
    4883.75, Y+, 2, 15, 600
    4891.54, Fe, 8, 10, 600
    4900.14, Y+, 2, 18, 600
    4920.50, Fe, 10, 10, 500,
    4923.96, Fe+, 5, 30, 2000
    4934.08, Ba+, 7, 25, 1200
    // page 33
    5015.68, He, 0, 2, 2500
    5018.44, Fe+, 4, 25, 2000
    // page 34
    // page 35
    5167.35, ~Mg-Fe, 15, 18, 1500
    5168.99, Fe+-Fe, 7, 25, 1500
    5172.65, Mg, 20, 30, 2000
    5183.58, Mg, 30, 40, 2500
    5188.69, Ti+, 2, 12, 700,
    // page 36
    5275.99, Fe+-Cr, 6, 20, 500
    5316.67, Fe+, 6, 30, 850
    5328.03, Fe, 8, 10, 500
    5362.86, Fe+, 3, 15, 500
    5371.51, Fe-Ni, 7, 12, 500
    // page 37
    5405.84, Fe, 6, 10, 600
    5429.73, Fe, 6, 15, 600
    5446.86, Fe-Ti, 8, 12, 500
    5455.59, Fe, 6, 12, 500
    // page 38
    5526.90, Sc+, 3, 15, 600
    5534.86, Fe+, 2, 15, 600
    5615.65, Fe, 6, 10, 600
    // page 39
    5657.84, Sc+, 2, 10, 600
    5853.69, Ba+, 5, 12, 600
    5857.51, Ca-Ni, 11, 10, 400
    // page 40
    5875.64, He, 0, 80, 7500
    5889.98, Na, 30, 25, 1500
    5895.99, Na, 20, 20, 1500
    6122.28, Ca, 10, 10, 800
    6136.78, Fe, 11, 10, 800
    6162.19, Ca, 15, 15, 1000,
    // page 41
    6230.76, Fe-V, 8, 10, 600
    6247.56, Fe+,  2, 10, 600
    6393.68, Fe, 7, 10, 600
    6400.02, Fe, 10, 12, 600
    6456.44, Fe+, 3, 15, 600
    6462.58, Ca-Fe, 8, 10, 600
    6494.95, Fe, 8, 10, 600
    6496.88, Ba+, 4, 20, 1500
    6516.16, Fe+, 2, 12, 800
    6562.80, H alpha, 40, 200, 12000
    6678.10, He-Fe, 5, 20, 2200
    // page 42
    7065.20, He, 0, 6, 1000

    ";
    $s = str_replace("\r\n", "\r", $s);
    $s = str_replace("\r", "\n", $s);
    $s = explode("\n", $s);
    $ret = array();
    foreach ($s as $line){
        $line = explode('//', $line);
        $line = trim($line[0]);
        if ($line == ''){
            continue;
        }
        $line = explode(',', $line);
        $col_i = 0;
        foreach ($line as &$col){
            $col = str_replace('-', ', ', $col);            
            if (0 == $col_i){
                $col = str_replace('I', '1', $col);
                $col = str_replace('Z', '2', $col);
                $col = str_replace('S', '5', $col);
            }
            if (1 == $col_i){
                $col = explode(',', $col);
                foreach ($col as &$elem){
                    if (trim($elem)!=''){
                        $gets_ion_mark = true;
                        $elem1 = str_replace('~', '', $elem);
                        if (strpos($elem1, 'H') === 0){
                            // hydrogen balmer
                            $gets_ion_mark = false;
                        }
                        if (strpos($elem1, 'CN') === 0){
                            // molecule
                            $gets_ion_mark = false;
                        }
                        if (strpos($elem1, 'CH') === 0){
                            // molecule
                            $gets_ion_mark = false;
                        }
                        
                        if ($gets_ion_mark){
                            if (strpos($elem, '+') === false){
                                $elem.= ' I';
                            }
                        }
                    }
                }
                $col = implode(',', $col);
                $col = str_replace('+', ' II', $col);
            }
            $col = trim($col);            
            $col_i++;
        }
        if (count($line) < 5){
            var_dump($line);
            die();
        }
        $ret[] = array(
            "lambda_A" => floatval($line[0]),
            "chemical" => $line[1],
            "disk_intensity" => $line[2],
            "flash_intensity" => $line[3],
            "formation_height_km" => $line[4]
        );
    }
    return $ret;
}

function polyfill_item_with_chromosphere_info(&$item){
    if (is_array($item)){
        if (!isset($item["lambda_A"])){
            if (isset($item[0]["lambda_A"])){
                for ($i = 0;$i<count($item); $i++){
                    polyfill_item_with_chromosphere_info($item[$i]);
                }
                return ;
            }
        }
    }
    $magic = ChromosphereFlashSpectrum_memozie();

    $keys = array(
        "chromosphere_disk_intensity", 
        "chromosphere_flash_intensity", 
        "chromosphere_formation_height_km", 
        "chromosphere_chemical"
    );
    foreach ($keys as $ki){
        if (!isset($item[$ki])){
            $item[$ki] = -1;
        }
    }
    
    foreach ($GLOBALS[$magic] as $cl){
        $delta = abs($cl["lambda_A"] - $item["lambda_A"]);
        if ($delta < 0.5){
            // should also have common chemicals     
            $match_count = 0;
            $cl_chemicals = explode(',', str_replace('~', '', $cl["chemical"]));
            $it_chemicals = '';
            if (isset($item["caption"])){
                $it_chemicals .= ','.$item["caption"];
            }
            $it_chemicals = str_replace('~', ' ', $it_chemicals);
            $it_chemicals = str_replace('(', ' ', $it_chemicals);
            $it_chemicals = explode(',', $it_chemicals);
            foreach ($cl_chemicals as $cc){
                $cc = trim($cc);
                $cc = substr($cc, 0, 1);
                foreach ($it_chemicals as $it){
                    $it = trim($it);
                    $it = substr($it, 0, 1);
                    if ($cc == $it){
                        $match_count++;
                    }
                }
            }
            
            if ($match_count > 0){
                $item["chromosphere_flash_intensity"] = $cl["flash_intensity"];
                $item["chromosphere_disk_intensity"] = $cl["disk_intensity"];
                $item["chromosphere_formation_height_km"] = $cl["formation_height_km"];
                $item["chromosphere_chemical"] = $cl["chemical"];            
            }
        }
    }

}
