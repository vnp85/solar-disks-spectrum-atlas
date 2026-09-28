<?php

require_once("cube_class.php");

$a = new AtlasCubes();

file_put_contents('d:/limbi.txt', json_encode(
    $a->wavelengthsToLimbDarkening(
        array(3650, 4050, 5000, 6000, 7000, 8000, 9000, 10000, 11000),
        'd:/image_'
    )
));


//$a->getImageFilenamesAroundLambda(6562.8);
