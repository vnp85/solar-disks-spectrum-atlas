<?php

function vnp_hajonaplo_spectrum_disks(){
    $here = getcwd();
    ob_start();
    $b = 'generic.php';
    try {
        foreach (array(
            '../',
            '../../'
        ) as $folder){
            $candidate = $folder.$b;
            if (file_exists($candidate)){
                chdir($folder);
                require_once(basename($candidate));
                aladin_handle_hajonaplo('', true);
            }
        }
    } catch (Exception $e) {
        // silence echo 'Caught exception: ',  $e->getMessage(), "\n";
    } finally {
        // echo "First finally.\n";
    }   
    ob_end_clean();  
    chdir($here);  
}


vnp_hajonaplo_spectrum_disks();
