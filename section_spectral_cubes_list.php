<?php

 foreach ($parsedCubes as $cube){
    // list the cubes info, to select which cube to show
    $cube["spectral-color"] = wavelength_nm_to_webColor($cube["cwl_A_declared"]/10);
    echo_template("template_cube_list_item.html", $cube);    
  };  

  $wavelengthIntervals = parsedCubes_getCoveredWavelengthIntervals($parsedCubes);
echo '<script>'.
'function isWavelengthCoveredByCubes(lambda_A){';
  echo 'var ret = false; var wavelength_intervals=[';
  foreach ($wavelengthIntervals as $wli){
    echo '['.$wli[0].','.$wli[1].'],';
  }
  echo '];';
  echo 'wavelength_intervals.forEach(function (wi){'."\r\n".' if ((lambda_A >= wi[0]) && (lambda_A <= wi[1])){ ret = true; };});';
  echo "\r\n";
  echo 'return ret;';
  echo "\r\n";
echo '}'.
'</script>';  

?>
<script>
  setTimeout(function (){
    var img = document.getElementById("main_spectrum");
    for (var lambda_A = 3000; lambda_A < 10000; lambda_A += 5){      
      if (isWavelengthCoveredByCubes(lambda_A)){
        // carry on
      }else{
        OnImage_setPartialMarkerToWavelength(img, lambda_A, 'grey', "25%");
      }
    }
  }, 500);  
</script>  

