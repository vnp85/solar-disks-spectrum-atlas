<?php
  foreach ($parsedCubes as $cube){
    $cube["display"] = "none";
    $cube["container-class"] = "cube-spectrum-container";
    $cube['parent-id'] = 'main_spectrum';
    $cube["data-operates-on-the-cube-slices"] = 1;
    $cube["interval-player-display-value"] = "";
    echo_template("template_spectrum_bar.html", $cube);    
  }  
