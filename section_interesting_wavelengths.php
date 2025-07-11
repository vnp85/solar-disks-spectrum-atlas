<?php


    $parsedCubes = array();
    foreach (glob('cubes-info/cube_*.json') as $cube){
        $parsedCubes[] = cube_parseJsonFile($cube);
    };  
    usort($parsedCubes, "cube_sortParsedCubesByWavelength");

    $wavelengthsOfInteres = getTheWavelengthsOfInterest($parsedCubes);

    echo '<script>
    function Spectrum_getWavelengthList(){
        var woi = [];
    ';   
    foreach ($wavelengthsOfInteres as $woi){         
        echo '        woi.push('.json_encode($woi).');'."\r\n";
    }; 
    foreach (getFurtherWavelengthWorthyToLabelOnScreenButNotWithDedicatedButtons() as $woi){         
        echo '        woi.push('.json_encode($woi).');'."\r\n";
    };    
    echo '
        return woi;
    } 
    function Spectrum_getWavelengthClosestEnoughTo(lambda_A){
        // also find by: closeenough closetoenough

        var woi = Spectrum_getWavelengthList();
        woi.sort(function (a, b){
           return Math.abs(a.lambda_A - lambda_A) - Math.abs(b.lambda_A - lambda_A);
        });
        var width_A = (woi[0].width_mA || 50) / 1000;
        width_A = Math.max(0.3, width_A);

        if (Math.abs(woi[0].lambda_A - lambda_A) < width_A*0.8){
           return woi[0];
        }else{
           return null;
        }
    }    
    function Spectrum_interpretInCaseItIsAString(n){
        if (parseFloat(n) === parseFloat(n)){
           return n;
        }           
        var ret = n;
    ';
    echo '  
      var woi = Spectrum_getWavelengthList();
      woi.forEach(function (w){ if (w.caption.indexOf(n) > -1){ ret = w.lambda_A; }; });
      woi.forEach(function (w){ if (w.caption == n){ ret = w.lambda_A; }; });
      return ret; 
    };


    </script>';
    
    foreach ($wavelengthsOfInteres as $woi){        
        echo '<button class="wavelength-button" '.
          ' data-display-importance="'.$woi["displayImportance"].'" '.
          ' data-is-ionized="'.($woi["ionized"] ? "true" : "false").'" '.
          ' data-wavelength-uid="'.$woi["uid"].'"'.
          ' style="display:none; cursor:pointer" data-wavelength-angstrom="'.$woi["lambda_A"].'"'.
          ' onclick="WavelengthOfInterestClicked(this)"'.
          '>'.
          $woi["caption"].
          '</button>'."\r\n";
    }

    $woiClustered = wavelengthInfo_getClustersByClassEnumerations($wavelengthsOfInteres);
    echo '<script>'."\r\n";
    echo 'function wavelengthButtons_showSelectedBy(fieldName, fieldValue){ '."\r\n";
    echo ' var wb = document.getElementsByClassName("wavelength-class-selector-button");'."\r\n";
    echo ' for (var i=0; i<wb.length; i++){ var lev = wb[i].getAttribute(fieldName); var found = (lev == fieldValue); wb[i].style.backgroundColor = found ? "yellow" : "silver"; };'."\r\n";
    echo '};'."\r\n";

    echo 'function wavelengthButtons_showUpTillClass(c){'."\r\n";
    echo ' var origC = c; c--; // c should be 1..5 or something'."\r\n";
    echo ' var clusters = []; '."\r\n";
    foreach ($woiClustered as $woic){        
        $reti = array();
        foreach ($woic as $woici){
            $reti[] = $woici["uid"];
        }
        echo 'clusters.push('.json_encode($reti, JSON_PRETTY_PRINT).');'."\r\n";        
    };
    echo ' var l = document.getElementsByClassName("wavelength-button");'."\r\n";
    echo ' for (var i=0; i<l.length; i++){ var uid = l[i].getAttribute("data-wavelength-uid"); var found = clusters[c].indexOf(uid) > -1; l[i].style.display = found ? "" : "none"; };'."\r\n";
    echo ' wavelengthButtons_showSelectedBy("data-woi-class-level", origC);';
    echo '};';
    echo '</script>'."\r\n";



    $dispi = array();
    foreach ($wavelengthsOfInteres as $woi){        
        $dispi[] = $woi["displayImportance"].' '.$woi["caption"].'<br>';
    };    

    $woicK = 0;
    $woicButtons_html = '&nbsp;&nbsp;';
    $woicButtons_html .= '<button onclick="Spectrum_toggleWoiTableVisibility(\'woi_table_wrapper\')">##</button><span id="woi_table_wrapper"></span> ';
    $woicButtons_html .= ' &#128065; ';
    foreach ($woiClustered as $woic){                
        $woicK++;
        $label = '1..'.$woicK;
        $label = str_replace('1..1', '1', $label);
        $woicButtons_html .= '<button class="wavelength-class-selector-button" data-woi-class-level="'.$woicK.'" onclick="wavelengthButtons_showUpTillClass('.$woicK.')">#'.$label.'</button>';
    };    

    //$woicButtons_html .= ' <button class="wavelength-class-selector-button" data-should-be-ionized="false">Elem I</button>';
    //$woicButtons_html .= ' <button class="wavelength-class-selector-button" data-should-be-ionized="any">I, II...</button>';
    //$woicButtons_html .= ' <button class="wavelength-class-selector-button" data-should-be-ionized="true">II...</button>';

    $woicButtons_html = str_replace('"', '"+String.fromCharCode(34)+"', $woicButtons_html);
    echo '<script>document.getElementById("wavelength-selector-wrapper").innerHTML = "'.$woicButtons_html.'";</script>';
    echo '<script>wavelengthButtons_showUpTillClass(1);</script>'."\r\n";


    
