<?php

require_once("spectrum-collator.php");

$start = 'd:/output_';
$collatedSpectrum = new SpectrumCollator();


//$collatedSpectrum->setForcedColorPinkIRMagentaUV()->synthetizeRange('d:/?.jpg', 3400, 4100, 0.1);
//$collatedSpectrum->setMono()->synthetizeRange('d:/?.jpg', 6900, 7100, 0.1);


$collatedSpectrum->
  setForcedColorPinkIRMagentaUV()->
  //setMono()->
  panelToFitIntoHeight(4000)->
  setPanelRowStepA(200)->
  synthetizePanel('d:/?.jpg', '330 nm', '1140 nm', '0.1 A');
  //synthetizePanel('d:/?.jpg', 11000, 11400, 0.1);
  //synthetizeRange('d:/?.jpg', '900 nm', '920 nm', 0.1);
  //synthetizeRange('d:/?.jpg', 3050, 11600, 0.5);
  //synthetizeRange('d:/?.jpg', 3050, 3700, 0.5);
  //synthetizeRange('d:/?.jpg', 4500, 5500, 0.5);
//$collatedSpectrum->synthetizeAroundCwl('d:/spectHuge.jpg', 3600 + (6600-3600) / 2, (6600-3600) / 2, 0.3);
/* */ 

/*
$k2 = new SpectrumCollator();

$k2->synthetizeRuledColumn('d:/hydrogen?.jpg', '340 nm', '400 nm', 0.05);
//$k2->synthetizeAtlasMasterSpectrum('d:/?.jpg');

/* */