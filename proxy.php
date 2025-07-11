<?php
require_once("helpers.php");

// this file
//  - is used mainly in development with localhost/atlas
//  - to get files from the actual git repo's location
//  - see the atlas_index.php

if (!empty($_GET['imgproxy'])){
    $p = sanitize_file_path($_GET['imgproxy']);
    header('Content-Type: image/jpeg');     
    echo file_get_contents($p); 
    die();
}

if (!empty($_GET['mapproxy'])){    
    $s = file_get_contents(sanitize_file_path($_GET['mapproxy']));
    $s = str_replace("\r", ' ', $s);
    $s = str_replace("\n", ' ', $s);
    $s = str_replace("\t", ' ', $s);
    if (!empty($_GET['marked-wavelengths-a'])){
        $_GET['marked-wavelengths-a'] = explode(',', $_GET['marked-wavelengths-a']);
        for ($i=0; $i<count($_GET['marked-wavelengths-a']); $i++){
            $_GET['marked-wavelengths-a'][$i] = floatval($_GET['marked-wavelengths-a'][$i]);
        }
    }else{
        $_GET['marked-wavelengths-a'] = array();
    }
    echo '(function (){ 
      var a = '.$s.'; 
      var timeoutLen = 100;
      var t = document.getElementById("'.$_GET['target'].'");
      t.setAttribute("data-mapping", JSON.stringify(a));
      if (!t.getAttribute("src")){
        console.log("image has no meaningful source declared");
        timeoutLen *= 3;
        t.src = "?imgproxy="+encodeURIComponent(a.cubeLocation + "/" + a.averageFilename);
      }
      if (a["lambda-precision"]){
        t.setAttribute("data-lambda-precision", a["lambda-precision"]);
      }          
      setTimeout(function (){
        a.cwl_px = OnImage_wavelengthAToPixel(t, a.cwl_A);
        if (typeof a.putMarkerToCwl === "undefined"){
            a.putMarkerToCwl = true;
        }else{
            a.putMarkerToCwl = a.putMarkerToCwl ? true : false;
        }
        t.setAttribute("last-click-on-real-pixel", a.cwl_px);
        t.setAttribute("last-click-on-real-wavelength", a.cwl_A);
        if (a.extremeWavelengths_A){
            a.extremeWavelengths_A_source = "source-json";
            var ex = [];
            ex.push(OnImage_wavelengthAToPixel(t, a.extremeWavelengths_A[0]));
            ex.push(OnImage_wavelengthAToPixel(t, a.extremeWavelengths_A[1]));
            ex = ex.map(function (e){ return Math.round(e - a.cwl_px); }).join(",");
            t.setAttribute("data-extreme-pixel-shifts", ex);           
        }else{
            // it may have come from the image
            a.extremeWavelengths_A_source = "cube-enumeration";
            var ex = t.getAttribute("data-extreme-pixel-shifts", ex).split(","); 
            ex = ex.map(function (e){
               e = parseFloat(e);
               return Math.round(OnImage_pixelToWavelength_A(t, a.cwl_px+e));
            });
            a.extremeWavelengths_A = ex;
        }
        t.setAttribute("data-mapping", JSON.stringify(a));
        t.addEventListener("mousemove", SpectrumMouseMoveListener);
        t.addEventListener("mouseleave", SpectrumMouseLeaveListener);
        t.addEventListener("click", SpectrumMouseClickListener);
        OnImage_getFirstParentWithTagname(t, "a").addEventListener("keydown", SpectrumKeydownListener);
        if (a.putMarkerToCwl){
           OnImage_setMarkerToWavelength(t, a.cwl_A);
        }   
        var cwl_shower = OnImage_getFirstParentWithTagname(t, "table").getElementsByClassName("cwl_angstrom")[0];
        cwl_shower.innerHTML = a.cwl_A;
        ['.implode(',', $_GET['marked-wavelengths-a']).'].forEach(function (lambda_A){ OnImage_setMarkerToWavelength(t, lambda_A, WavelengthToColor(lambda_A)); });
        if (a.markersRelativeToCwl){
           a.markersRelativeToCwl = a.markersRelativeToCwl.map(function (m){
              var weKnowThePixelShift = false;
              var weKnowTheWavelengthShift = false;
              if (parseFloat(m.shift_A) === parseFloat(m.shift_A)){
                 weKnowTheWavelengthShift = true;
              }
              if (parseFloat(m.shift_px) === parseFloat(m.shift_px)){
                 weKnowThePixelShift = true;
              }
              
              if ((weKnowThePixelShift)&&(!weKnowTheWavelengthShift)){
                 m.shift_A = OnImage_pixelToWavelength_A(t, m.shift_px);
              }
              if ((!weKnowThePixelShift)&&(weKnowTheWavelengthShift)){
                 m.shift_px = OnImage_wavelengthAToPixel(t, m.shift_A);
                 console.log(m);
              }
              return m;
           });
           a.markersRelativeToCwl.forEach(function (e){
            var lambda_A = a.cwl_A + e.shift_A;
            OnImage_setMarkerToWavelength(t, lambda_A, WavelengthToColor(lambda_A));
           });
        }
        if (a.markers){
           a.markers = a.markers.map(function (m){
              var weKnowThePixel = false;
              var weKnowTheWavelength = false;
              if (parseFloat(m.lambda_A) === parseFloat(m.lambda_A)){
                 weKnowTheWavelength = true;
              }
              if (parseFloat(m.px) === parseFloat(m.px)){
                 weKnowThePixel = true;
              }
              
              if ((weKnowThePixel)&&(!weKnowTheWavelength)){
                 m.lambda_A = OnImage_pixelToWavelength_A(t, m.px);
              }
              if ((!weKnowThePixel)&&(weKnowTheWavelength)){
                 m.px = OnImage_wavelengthAToPixel(t, m.lambda_A);
                 console.log(m);
              }
              return m;
           });
           a.markers.forEach(function (e){
            var lambda_A = e.lambda_A;
            OnImage_setMarkerToWavelength(t, lambda_A, WavelengthToColor(lambda_A));
           });
        }           
        OnImage_getTheLambdaEditor(t).addEventListener("keyup", function (e){
           if (e.key === "Enter" || e.keyCode === 13) {
               SpectrumLambdaEditorEnterListener(t);
           };
        });
        OnImage_getThePixelShiftEditor(t).addEventListener("keyup", function (e){
           if (e.key === "Enter" || e.keyCode === 13) {
               SpectrumPixelShiftEditorEnterListener(t);
           };
        });

        OnImage_enqueueAddExtremeBlurs(t, a);
      }, 200); 
    })();
    '; 
    die();
}

