<?php


  $main_id = "main_spectrum";
  $main_parsed = cube_parseJsonFile($main_id.'.json');
  $main_argo = array(
    "id" => $main_id,
    "display" => "",
    "container-class" => "",
    "datetime" => "xx",
    "onSpectrumCursorMove" => "MainSpectrum_onCursorMoved",
    "data-operates-on-the-cube-slices" => 0,
    "interval-player-display-value" => "none",
    'img_src_spectrum_twin' => 'main_spectrum_collated.jpg'
  );
  foreach ($main_parsed as $mkey=>$mvalue){
    $main_argo[$mkey] = $mvalue;
  }
  
  echo_template("template_spectrum_bar.html", $main_argo);
?>
<script>
    (function (){
       var mainId = '<?php echo $main_id; ?>';
       var e = document.getElementById(mainId);
       e.style.opacity = 0.5;
    })();
</script>
