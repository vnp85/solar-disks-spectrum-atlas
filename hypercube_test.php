<?php

require_once('hypercube.php');

$hc = new HyperCube();

$tf = 'd:/hyper.cube';

$file_list = $hc->createPack($tf)
   ->addToPackIfCandidateFromGlob('C:/Users/nagypali/Documents/kepernyo-guider/solar-disks-spectrum-atlas/cubes/2026-05-27-0719_1-Sun-10990/*.jpg')
   ->writePack()->getWrittenFiles();

$hc->openPack($tf);
var_dump($hc->selfTestOnFile($file_list[8]));   

