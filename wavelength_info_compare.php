<?php

chdir(dirname(__FILE__));

require_once("wavelengths_info.php");

$narnia = array_merge(
    getFurtherWavelengthWorthyToLabelOnScreenButNotWithDedicatedButtons(),
    get_basic_and_additional_wavelengths()
);

$narnia[] = awl_notImportant(6646.966,171,'Fe I');
$narnia[] = awl_notImportant(5005.719,136,'Fc I');
$narnia[] = awl_notImportant(4340.475,2855,'H r');


$student_filename = "C:/Users/nagypali/Downloads/moore_binned_0p5A complete.csv";

$student = file($student_filename);
$q = 0;
foreach ($student as $line){
   $i = explode(',', trim($line));
   $i[] = '';
   $i[] = '';
   $lambda = floatval($i[0]);
   $eqwi = $i[1];
   if ($eqwi === ''){
     $eqwi = 0;
   }
   $eqwi = floatval($eqwi);


   $chemical = $i[2];
   $chemical = str_replace(' p', '', $chemical);
   $chemical = str_replace('|', '', $chemical);
   $chemical = str_replace('--', '', $chemical);
   $chemical = str_replace('?', '', $chemical);
   $chemical = str_replace("\t", ' ', $chemical);
   $chemical = str_replace("Halpha", "H alpha", $chemical);
   $chemical = str_replace("Ca II (K)", "CaK", $chemical);
   $chemical = str_replace("Ca II (H)", "CaH", $chemical);
   $chemical = trim($chemical);

   $narnia_items = array();   
   
   foreach ($narnia as $ni){
     $t = 1;
     if (strpos($ni["caption"], '~') !==false){
        $t = 2;
     }
     if (abs($lambda - $ni["lambda_A"]) < $t){
        if (strpos($ni["caption"], $chemical) !== false){
            $narnia_items[] = $ni;
        }
     }
   }
   $skip_chemicals = array('CH', 'CN', 'Fe I');
   if (count($narnia_items) == 0){
      if ($eqwi > 100){
        if (in_array($chemical, $skip_chemicals)){
            //
        }else{
                echo "(".$q.") found no analog for (".$chemical."): ".$line;
                $q++;
        }
      }
   }
}