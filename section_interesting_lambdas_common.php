<?php

    $parsedCubes = array();
    foreach (glob('cubes-info/cube_*.json') as $cube){
        $parsedCubes[] = cube_parseJsonFile($cube);
        Debug_logMoment('done parsing cube '.basename($cube));    

    };  
    if (function_exists('cube_sortParsedCubesByWavelength')){
        usort($parsedCubes, "cube_sortParsedCubesByWavelength");
    }
    if (!function_exists('getTheWavelengthsOfInterest')){
        require_once('cube_utils.php');
    }
    if (!function_exists('Debug_logMoment')){
        function Debug_logMoment($a = 1, $b = 1, $c = 1){};
    }


    $wavelengthsOfInteres = getTheWavelengthsOfInterest($parsedCubes);

    echo '
    function Spectrum_getWavelengthList(){
        var woi = [];
    ';   
    foreach ($wavelengthsOfInteres as $woi){         
        echo '        woi.push('.json_encode($woi).');'."\r\n";
    }; 
    foreach (getFurtherWavelengthWorthyToLabelOnScreenButNotWithDedicatedButtons() as $woi){         
        echo '        woi.push('.json_encode($woi).');'."\r\n";
    };    
    Debug_logMoment('woi pushes done');

    $gotten_nist_wavelengths = array();
    // $gotten_nist_wavelengths = get_nist_wavelengths();

    echo '
        return woi;
    } 
    function Spectrum_nameToWavelengthA(name){
        if ("CaK" == name){
            name = "Ca K CaK";
        }
        if ("CaH" == name){
            name = "Ca H CaH";
        }
        var a = Spectrum_getWavelengthList();
        var n = (name+"").toLowerCase().split(" ").filter(function (e){ return e!== ""; });
        a = a.map(function (e){
          e.caption = " "+e.caption.toLowerCase().trim()+" ";
          e.score = 0;
          n.forEach(function (w){
             if (e.caption.indexOf(w) > -1){
               e.score++;
             }
             if (e.caption.indexOf(" "+w+" ") > -1){
               e.score+=2;
             }
          });
          return e;
        }).sort(function (a,b){
          var c = b.score - a.score;
          if (c < 0){
             return -1;
          }
          if (c > 0){
             return 1;
          }   
          return 0;
        }).filter(function (e,i){
          return i < 10;
        });
        console.log(a);
        return a[0].lambda_A;
    }    
    function Spectrum_getNistWavelengthList(){
        var woi = '; echo json_encode($gotten_nist_wavelengths); echo ';        
        return woi.filter(function (e){ return e.intensity > 200; }).map(function (e){
           e.caption = e.caption + "_"+e.intensity;
           return e;
        });
    }';