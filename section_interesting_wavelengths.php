<?php
    echo '<script>';
    require_once("section_interesting_lambdas_common.php");
    
    Debug_logMoment('nist list generated');    
    echo '
    function Spectrum_getWavelengthClosestEnoughTo(lambda_A){
        // also find by: closeenough closetoenough        
        var woi;
        if (window.memoizedWoiForClosestWavelength){
            // carry on
        }else{
            var woi1 = Spectrum_getWavelengthList();
            var woi2 = Spectrum_getNistWavelengthList();
            window.memoizedWoiForClosestWavelength = woi1.concat(woi2);
        };    
        woi = window.memoizedWoiForClosestWavelength;


        woi.sort(function (a, b){
           return Math.abs(a.lambda_A - lambda_A) - Math.abs(b.lambda_A - lambda_A);
        });
        var width_A = (parseFloat(woi[0].width_mA) || 50) / 1000;
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
        $shortened_caption = $woi["caption"];
        $shortened_caption = explode(' ', $shortened_caption);
        $last_item = array_pop($shortened_caption);
        if (strpos($last_item, '&Aring;')!==false){
            $last_item = round(floatval($last_item)).'';
        }
        $shortened_caption[] = $last_item;
        $shortened_caption = implode(' ', $shortened_caption);
        echo '<button class="wavelength-button" '.
          ' data-display-importance="'.$woi["displayImportance"].'" '.
          ' data-is-ionized="'.($woi["ionized"] ? "true" : "false").'" '.
          ' data-wavelength-uid="'.$woi["uid"].'"'.
          ' style="display:none; cursor:pointer" data-wavelength-angstrom="'.$woi["lambda_A"].'"'.
          ' onclick="WavelengthOfInterestClicked(this)"'.
          '>'.
          $shortened_caption.
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

    $woi_table_template = file_get_contents('template_woi_table.html');    
    $woicK = 0;
    $woicButtons_html = '&nbsp;&nbsp;';
    $woicButtons_html .= '<button class="extend-more-button" onclick="Spectrum_toggleWoiTableVisibility(\'woi_table_wrapper\')">[...]</button>'.$woi_table_template.' ';
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
    $woicButtons_html = str_replace("\n", '"+String.fromCharCode(13)+"', $woicButtons_html);
    $woicButtons_html = str_replace("\r", '"+String.fromCharCode(10)+"', $woicButtons_html);
    $woicButtons_html .= '&nbsp;<small><button onclick=\"toggleGalleryView()\">gallery</button></small>';
    echo '<script>document.getElementById("wavelength-selector-wrapper").innerHTML = "'.$woicButtons_html.'";</script>';
    echo '<script>wavelengthButtons_showUpTillClass(1);</script>'."\r\n";
    echo '<script>
    function isGalleryViewOpen(){
      var t = document.getElementById("artistic-gist-gallery-wrapper"); 
      var v = t.style.display; 
      if (v == "none"){
        return false;
      };
      return true;
    }  
    function toggleGalleryView(){ 
      var t = document.getElementById("artistic-gist-gallery-wrapper"); 
      var v = t.style.display; 
      if (v == "none"){
        t.style.display = "";
      }else{
        t.style.display = "none";
      }
      URLmanager.pushPartialState({ "open-gallery-view": isGalleryViewOpen() ? "1" : "0" });
    }
    function openGalleryView(){
      if (isGalleryViewOpen()){
        // carry on
      }else{
        toggleGalleryView();
        document.location.hash = "gallery-view-top";
      }
    }
      '."\r\n";
    echo '</script>'."\r\n";

    echo '<div style="display:none; width: 100%; margin: 0px; background-color:black; color: silver" id="artistic-gist-gallery-wrapper">';
    echo '<div>&nbsp;</div>';
    require_once("section_gallery.php");
    echo '<div>&nbsp;</div>';
    echo '</div>';
    
