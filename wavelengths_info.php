<?php

require_once("wavelength_info_helpers.php");
require_once("wavelength_info_nist.php");
require_once("wavelength_info_chromosphere.php");

/* wavelength info are 
    [1] 
    mainly from the monograph 
    https://nvlpubs.nist.gov/nistpubs/Legacy/MONO/nbsmonograph61.pdf


    THE SOLAR SPECTRUM 2935A to 8770A
    
    Second Revision of Rowland's Preliminary Table
    of
    Solar Spectrum Wavelengths
    Charlotte E. Moore
    National Bureau of Standards
    M. G. J. MiNNAERT J. HOUTGAST
    Utrecht Observatory

    -------------------------------------------------------------------------

    [2] 
    some data are from the NIST database of spectral lines
    https://www.nist.gov/pml/atomic-spectra-database


    [3]
    magnetic lines are from
    https://articles.adsabs.harvard.edu/pdf/1973SoPh...28....9H


    [4]
    coronal wavelengths are from    
    https://iopscience.iop.org/article/10.3847/1538-4357/aa9edf

    [5]
    infrared wavelength info are from
    THE SOLAR SPECTRUM, 6600 ΤΟ 13495

    HAROLD D. BABCOCK
    Mount Wilson Observatory of the
    Carnegie Institution of Washington
    CHARLOTTE E. MOORE
    Princeton University Observatory ;
    National Bureau of Standards since November 1945
    https://babel.hathitrust.org/cgi/pt?id=uc1.32106002409180&seq=5
    -------------------------------------------------------------------------

    photogenyClass is my addition for the amateur astronomer
*/



function get_hydrogen_balmer_series(){
  $ret = array(
      array("lambda_A" => 6562.808, "caption" => "H alpha", "width_mA" => 4020, "photogenyClass" => 1, "displayImportanceFactor" => 3),
      array("lambda_A" => 4861.35, "caption" => "H beta", "width_mA" => 3680, "photogenyClass" => 1, "displayImportanceFactor" => 0.8),
      array("lambda_A" => 4340.47, "caption"=> "H gamma", "width_mA" => 2855, "photogenyClass" => 2),
      array("lambda_A" => 4101.75, "caption"=> "H delta", "width_mA" => 3133, "photogenyClass" => 2, "displayImportanceFactor" => 0.7),
      array("lambda_A" => 3970.0, "caption"=> "H epsilon", "width_mA" => 776, "photogenyClass" => 3, "displayClusterBoundaryMarker" => "3"),
      array("lambda_A" => 3889.064, "caption" => "H 8 (dzeta)", "photogenyClass" => 4, "width_mA" => 2346, "displayImportanceFactor" => 0.2),
      array("lambda_A" => 3835.397, "caption" => "H 9 (eta)", "photogenyClass" => 4, "width_mA" => 2362, "displayImportanceFactor" => 0.2),

      array("lambda_A" => 3797.90, "caption" => "H 10 (?theta?)", "width_mA" => 3463, "photogenyClass" => 5, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3770.63, "caption" => "H 11", "width_mA" => 1860, "photogenyClass" => 5, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3760.15, "caption" => "H 12", "photogenyClass" => 5, "width_mA" => 1388, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3734.37, "caption" => "H 13", "photogenyClass" => 5, "width_mA" => 1014, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3721.94, "caption" => "H 14", "width_mA" => 536, "photogenyClass" => 5, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3711.97, "caption" => "H 15", "width_mA" => 300, "photogenyClass" => 5, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3703.86, "caption" => "H 16", "width_mA" => 235, "photogenyClass" => 5, "displayImportanceFactor" => 0.1),    
      //array("lambda_A" => 3697.15, "caption" => "H 17", "photogenyClass" => 5, "displayImportanceFactor" => 0.1),          
  );

  $nist_hydrogens = explode("\n", NIST_getRawHydrogenText());
  $h_counter = 17;
  $multi = 1000;
  foreach ($nist_hydrogens as $line){
    $line = trim($line);
    $line = explode(' ', $line);
    if (is_numeric($line[0])){
      if (is_numeric($line[3])){
        $w_mA = round(hydrogen_NIST_intensity_to_lineSomething(floatval($line[4])));
        $item = array(
           "lambda_A" => round(floatval($line[0])*10 *$multi)/$multi, 
           "caption" => "H ".$h_counter, 
           "photogenyClass" => 5, 
           "width_mA" => $w_mA.'(?)',
           "widthForCalculations" => $w_mA,
           "displayImportanceFactor" => 0.01
        );      
        if ($item["caption"] == 'H 21'){
          $item["chromosphere_flash_intensity"] = 25;
          $item["chromosphere_chemical"] = 'H';
        }            
        $ret[] = $item; 
        $h_counter++;
      }
    }
  }


  for ($i=0; $i<count($ret); $i++){
    $ret[$i]["ionized"] = false;
  }





  $ret = wavelengthInfo_getPolyfilledItemArray($ret, array("must_include" => true));
  polyfill_item_with_chromosphere_info($ret);
  return $ret;
}

function hydrogen_NIST_intensity_to_lineSomething($nisti){
  $hydrogen_epsilon_in_nist = 30000;
  $hydrogen_epsilon_in_width = 776;

  return ($nisti*$hydrogen_epsilon_in_width)/$hydrogen_epsilon_in_nist;
}




function get_basic_wavelengths(){
    $a = get_hydrogen_balmer_series();
    $a = array_merge($a, array(
      array("lambda_A" => 3741.645, "caption" => "Ti II %wavelength%", "width_mA" => 133),
      array("lambda_A" => 3759.3, "caption" => "Ti II %wavelength%", "width_mA" => 334),

      array("lambda_A" => 3820.44, "caption"=> "L-band %wavelength%",  "photogenyClass" => 3, "width_mA" => 1712),

      array("lambda_A" => 3838.3, "caption"=> "Mg I %wavelength%",  "photogenyClass" => 3, "width_mA" => 1920),

      array("lambda_A" => 3913.47, "caption" => "Ti II %wavelength%", "width_mA" => 138),

      array("lambda_A" => 3933.6, "caption" => "CaK", "width_mA" => 2000, "photogenyClass" => 1, "displayImportanceFactor" => 1.5, "max_ionization_level" => 1),
      array("lambda_A" => 3968.47,  "caption" => "CaH", "width_mA" => 1500,  "photogenyClass" => 1, "displayImportanceFactor" => 1.5, "max_ionization_level" => 1),

      array("lambda_A" => 4307.9, "caption" => "G-band %wavelength%", "width_mA" => 1000, "photogenyClass" => 3, "displayImportanceFactor" => 0.5),

      array("lambda_A" => 5173, "caption" => "Mg triplet",  "photogenyClass" => 2, "displayImportanceFactor" => 1.5, "max_ionization_level" => 0),

      array("lambda_A" => 5892, "caption" => "Na doublet",  "photogenyClass" => 2, "ionized" => false),
  
    ));
    $a = wavelengthInfo_getPolyfilledItemArray($a, array("must_include" => true));
    return $a;
  }


  function getHeliumLines(){
    $ret = array();

    $faintBag = array(
      "displayImportanceFactor" => 0.5, 
      "ionized" => false,
      "photogenyClass" => 5
    );
    $faintBag2 = array(
      "displayImportanceFactor" => 50, 
      "ionized" => false,
      "photogenyClass" => 5
    );
    $brightBag = array(
      "displayImportanceFactor" => 0.8, 
      "ionized" => false,
      "photogenyClass" => 1
    );
        
    
    // where did this come from? $ret[] =  awl_notImportant__NIST_intensityNotWidth(6867, "caption" => "He I %wavelength%", "width_mA" => 500,"photogenyClass" => 5, "displayImportanceFactor" => 0.5, "ionized" => false); 
    
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(3888.648, 500, "He I", $brightBag); 
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(4921.931, 20, "He I", $faintBag); 
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(4026.191, 50, "He I", $faintBag); 

    $ret[] =  awl_notImportant__NIST_intensityNotWidth(6678.151, 200, 'He I', $faintBag);
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(5015.6783, 100, "He I", $faintBag); 

    $ret[] =  awl_notImportant__NIST_intensityNotWidth(4471.5, 225, 'He I', $faintBag);
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(4713.146, 30, 'He I', $faintBag);
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(7065.2, 180, 'He I', $faintBag2);
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(7281.35, 50, 'He I', $faintBag);
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(10830.2, 1650, '~He I', $brightBag);
    
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(4685.7, 45, "~He II", $faintBag); 
    

    polyfill_item_with_chromosphere_info($ret);

    return $ret;
  }    


  function get_basic_and_additional_wavelengths(){
    $ret = get_basic_wavelengths();
    

    $ret[] = array("lambda_A" => 3685.196, "caption" => "Ti II %wavelength%", "width_mA" => 275);
    $ret[] = array("lambda_A" => 3694.199, "caption" => "Yb II %wavelength%", "width_mA" => 67);

    $ret[] = awl_helper(3706.037, "Ca II", 290);
    $ret[] = awl_helper(3710.292, "Y II", 74);
    $ret[] = awl_helper(3712.898, "Cr II", 111);
    $ret[] = awl_helper(3715.180, "Cr II", 58);
    $ret[] = awl_helper(3715.476, "Ti I, V II", 58);
    $ret[] = awl_helper(3721.635, "Ti II, Fe I", 110);
    $ret[] = awl_helper(3727.347, "V II, (Cr II)", 59);

    $ret[] = awl_helper(3732.752, "V II", 64);
    $ret[] = awl_helper(3736.917, "Ca II", 290);
    $ret[] = awl_helper(3741.64, "Ti II", 133, 3);
    $ret[] = awl_helper(3759.299, "Ti II", 334);
    $ret[] = awl_helper(3761.690, "Cr II", 60);
    $ret[] = awl_helper(3769.463, "Ni II", 68);
    $ret[] = awl_helper(3774.336, "Y II", 74);
    $ret[] = awl_helper(3776.059, "Ti II", 84);
    $ret[] = awl_helper(3783.349, "Fe II", 68);
    $ret[] = awl_helper(3794.773, "La II", 48);
    $ret[] = awl_helper(3813.394, "Ti II", 138);
    $ret[] = awl_helper(3819.688, "Eu II", 43);
    $ret[] = awl_helper(3821.937, "Fe II p", 64);
    $ret[] = awl_helper(3823.51,  "Mn I", 116);
    $ret[] = awl_helper(3829.365, "Mg I", 874);
    $ret[] = awl_helper(3831.7,   "Ni I", 129);
    $ret[] = awl_helper(3832.310, "Mg I", 1685);
    $ret[] = awl_helper(3834.233, "Fe I", 624);
    $ret[] = awl_helper(3838.302, "Mg I", 1920);
    $ret[] = awl_helper(3859.922, "Fe I", 1554);
    $ret[] = awl_helper(3905.532, "Si I", 816);
    // already in basic $ret[] = awl_helper(3913.470, "Ti II", 138, 3);
    $ret[] = awl_helper(3914.512, "Fe II", 64, 3);
    $ret[] = awl_helper(3916.405, "V II", 85);
    $ret[] = awl_helper(3944.016, "Al I", 488, 3);
    $ret[] = awl_helper(3950.358, "Y II", 55);
    $ret[] = awl_helper(3961.535, "Al I", 621, 3);
    $ret[] = awl_helper(3986.760, "Mg I, Mn I", 267);
    $ret[] = awl_helper(4005.254, "Fe I", 416);
    $ret[] = awl_helper(4012.390, "Ce II, Ti II", 93);
    $ret[] = awl_helper(4028.346, "Ti II", 90);
    $ret[] = awl_helper(4030.7, "Mn I", 326);

    $ret[] = awl_helper(4030.7, "Mn I", 326);
    $ret[] = awl_helper(4053.824, "Ti II", 65);
    $ret[] = awl_helper(4065.087, "V II", 52);
    $ret[] = awl_helper(4077.347, "La II", 41);
    $ret[] = awl_helper(4077.724, "Sr II", 428, 3, array("displayImportanceFactor" => 2));
    $ret[] = awl_helper(4086.713, "La II", 42);
    $ret[] = awl_helper(4094.938, "Ca I", 100, 6);
    $ret[] = awl_helper(4109.450, "Nd II", 39);
    $ret[] = awl_helper(4128.742, "Fe II", 50);
    $ret[] = awl_helper(4129.724, "Eu II", 54);
    $ret[] = awl_helper(4149.202, "Zr II", 62);
    $ret[] = awl_helper(4161.208, "Zr II", 58);
    $ret[] = awl_helper(4163.654, "Ti II", 107);
    $ret[] = awl_helper(4165.595, "Ce II", 48);
    $ret[] = awl_helper(4167.277, "Mg I", 200);
    $ret[] = awl_helper(4173.470, "Fe II", 90);
    $ret[] = awl_helper(4173.542, "Ti II", 59);
    $ret[] = awl_helper(4178.859, "Fe II", 79);
    $ret[] = awl_helper(4184.312, "Ti II", 76);
    $ret[] = awl_helper(4186.622, "Ce II", 95);

    $ret[] = awl_helper(4202.348, "V II", 63);
    $ret[] = awl_helper(4215.539, "Sr II", 233, 3);
    $ret[] = awl_helper(4220.051, "V II", 48);
    $ret[] = awl_helper(4226.740, "Ca I", 1476, 3);
    $ret[] = awl_helper(4233.169, "Fe II, Cr II", 139, 3);
    $ret[] = awl_helper(4246.837, "Sc II", 171, 2);
    $ret[] = awl_helper(4250.706, "Mo II, Fe I", 400, 3);
    $ret[] = awl_helper(4254.34, "Cr I", 393);
    $ret[] = awl_helper(4289.729, "Cr I", 230);
    $ret[] = awl_helper(4290.22, "Ti II", 117);
    $ret[] = awl_helper(4294.781, "Sc II", 62, 3, array("displayImportanceFactor" => 2));
    $ret[] = awl_helper(4300.053, "Ti II", 166);
    $ret[] = awl_helper(4301.92, "Ti II", 128, 4);
    $ret[] = awl_helper(4302.539, "Ca I", 165, 2);
    $ret[] = awl_helper(4303.177, "Fe II", 103, 4);
    $ret[] = awl_helper(4303.595, "Nd II", 65);
    $ret[] = awl_helper(4305.713, "Sc II", 67);
    $ret[] = awl_helper(4314.981, "Ti II", 82, 3);
    $ret[] = awl_helper(4320.749, "Sc II", 94, 4);
    $ret[] = awl_helper(4320.958, "Ti II", 63, 4);
    $ret[] = awl_helper(4337.925, "Ti II", 89, 4);
    $ret[] = awl_helper(4351.921, "Mg I", 283, 3);
    $ret[] = awl_helper(4354.615, "Se II", 70);
    $ret[] = awl_helper(4374.944, "Y II", 88, 4);    
    $ret[] = awl_helper(4383.557, "Fe I", 1008, 2);
    $ret[] = awl_helper(4385.387, "Fe II", 81, 3);
    $ret[] = awl_helper(4395.040, "Ti II", 135, 2, array("displayClusterBoundaryMarker" => "2"));
    $ret[] = awl_helper(4399.778, "Ti II", 115, 4);
    $ret[] = awl_helper(4404.761, "Fe I", 898, 3);
    $ret[] = awl_helper(4415.135, "Fe I", 417, 3);
    $ret[] = awl_helper(4443.812, "Ti II", 124, 2);
    $ret[] = awl_helper(4468.500, "Ti II", 120, 2, array("displayImportanceFactor" => 1.5));
    $ret[] = awl_helper(4481.2, "~Mg II, Ti I", 150, 4);
    $ret[] = awl_helper(4501.278, "Ti II", 111, 2);
    $ret[] = awl_helper(4508.289, "Fe II", 74, 2);
    $ret[] = awl_helper(4515.343, "Fe II, Cr I", 75, 4);
    $ret[] = awl_helper(4520.229, "Fe II", 69, 3);
    $ret[] = awl_helper(4522.638, "Fe II, Fe I", 101, 3);
    $ret[] = awl_helper(4533.970, "Ti II", 109, 3);
    $ret[] = awl_helper(4534.171, "Fe II", 53, 4);
    $ret[] = awl_helper(4549.5, "~(Fe II, Ti II)", 260, 2, array("displayImportanceFactor" => 2));
    $ret[] = awl_helper(4554.036, "Ba II", 159, 2, array("displayImportanceFactor" => 1.5));
    $ret[] = awl_helper(4558.650, "Cr II", 66, 3);
    $ret[] = awl_helper(4563.766, "Ti II", 120, 3);
    $ret[] = awl_helper(4571.982, "Ti II", 126, 2);    
    $ret[] = awl_helper(4583.839, "Fe I, Fe II", 124, 3);
    $ret[] = awl_helper(4588.204, "Cr II", 66, 4);
    $ret[] = awl_helper(4620.520, "Fe II", 47, 3);
    $ret[] = awl_helper(4703.003, "Mg I", 326, 2);
    $ret[] = awl_helper(4824.143, "Cr II", 94, 3);

    $TODO_unknown_photogeny_class = 9;

    $ret[] = awl_helper(4883.690, "Y II", 51, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(4891.502, "Fe I", 312,  $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(4900.124,  "Y II", 54, $TODO_unknown_photogeny_class);

    $ret[] = awl_helper(4900.124,  "Y II", 54, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(4911.199, "Ti II", 50, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(4923.930, "Fe II", 167, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(4934.095, "Fe I, Ba II", 207, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(4957.613, "Fe I (c)", 696, $TODO_unknown_photogeny_class);

    $ret[] = awl_helper(5018.45, "Fe II", 210, 1, array("displayImportanceFactor" => 3));

    $ret[] = awl_helper(5105.545, "Cu I", 82,  $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5129.162, "Ti II", 70, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5154.075, "Ti II", 73, $TODO_unknown_photogeny_class);

    $ret[] = awl_helper(5167.4, "~ Mg I b4, Fe I", 935, 2);
    $ret[] = awl_helper(5169.050, "Fe II b3", 154, 2);    
    $ret[] = awl_helper(5172.698, "Mg I b2", 1259, 2);
    $ret[] = awl_helper(5183.619, "Mg I b1", 1584, 2);
    $ret[] = awl_helper(5188.7, "~ Ti II, Ca I", 202, 3);

    $ret[] = awl_helper(5197.576, "Fe II", 80, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5205.730, "Y II", 52, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5226.545, "Ti II", 94, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5262.2, "~ Ti II, Ca I", 128, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5264.808, "Fe II", 45, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5276.0, "~ Fe II, Cr I, Co I", 152, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5316.7, "~ Fe II", 200, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5336.79, "Ti II", 71,  $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5528.418, "Mg I", 293,  $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5657.880, "Sc II", 64,  $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5682.647, "Na I", 104, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5688.217, "Na I", 121, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5711.095, "Mg I", 107, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(5853.688, "Ba II", 55, $TODO_unknown_photogeny_class, array("displayClusterBoundaryMarker" => "4"));
    $ret[] = awl_helper(5991.378, "Fe II", 29, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(6122.226, "Ca I", 222, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(6141.7278, "Ba II", 113, 2);
    $ret[] = awl_helper(6162.180, "Ca I", 222, 4);
    $ret[] = awl_helper(6245.620, "Sc II", 30, 4);
    $ret[] = awl_helper(6347.095, "Si II", 54, 4);
    $ret[] = awl_helper(6416.928, "Fe II", 47.5, $TODO_unknown_photogeny_class);
    $ret[] = awl_helper(6496.908, "Ba II", 98, $TODO_unknown_photogeny_class);

    $ret[] = array("lambda_A" => 5889.973, "caption" =>"Na I D2", "width_mA" =>752, "photogenyClass" => 2, "displayImportanceFactor" => 2.5, "ionized" => false); 
    $ret[] = array("lambda_A" => 5895.940, "caption" =>"Na I D1", "width_mA" =>564, "photogenyClass" => 2, "ionized" => false, "displayClusterBoundaryMarker" => "1");

    $ret[] = array(
      "lambda_A" => 5875.62, 
      "caption" => "He I D3", 
      "relativeIntensity" => 900,
      "photogenyClass" => 2, 
      "displayImportanceFactor" => 2.5, 
      "ionized" => false,
      "must_include" => true,
      "displayImportance" => 1000
    );     

    $ret[] = array(
      "lambda_A" => 10830.5, 
      "caption" => "He I 10830", 
      "relativeIntensity" => 900*4,
      "photogenyClass" => 1, 
      "displayImportanceFactor" => 2.5, 
      "ionized" => false,
      "must_include" => true,
      "displayImportance" => 1000
    );     


    $ret = wavelengthInfo_getPolyfilledItemArray($ret, array("must_include" => true));
    polyfill_item_with_chromosphere_info($ret);
    
    return $ret;
  }

  function awl_notImportant($lambda_A, $caption, $width_mA){
    $displayImportance = 99;
    if (is_string($width_mA)){
      // convenience order
      return awl_helper($lambda_A, $width_mA, $caption, $displayImportance);
    }
    return awl_helper($lambda_A, $caption, $width_mA, $displayImportance);
  }

  function awl_notImportant__NIST_intensityNotWidth($lambda_A, $caption_, $relativeIntensity_, $bag = false){
    $displayImportance = 99;
    $ret = array();
    
    if (is_string($relativeIntensity_)){
      $relativeIntensity = $caption_;
      $caption = $relativeIntensity_;
    }else{
      $relativeIntensity = $relativeIntensity_;
      $caption = $caption_;
    }

    $ret["lambda_A"] = $lambda_A;
    $ret["relativeIntensity"] /* see NIST */ = $relativeIntensity;
    // ignore, see NIST $ret["width_mA"] = $width_mA;
    $ret["caption"] = $caption." %wavelength%";
    polyfill_item_with_chromosphere_info($ret);

    if (is_array($bag)){
        foreach ($bag as $key=>$value){
            $ret[$key] = $value;
        }
    }

    return $ret;
  }

  function awl_coronalLine($lambda_A, $caption, $irradiance){
    $lambda_A_string = number_format($lambda_A, 1, '.', ""); 
    // may not display in the table

    $ret = array(
      "lambda_A" => $lambda_A, 
      "caption" => $caption." (corona) ".$lambda_A_string,
      "photogenyClass" => 6 - $irradiance / 1000,
      "widthForCalculations" => 100,
      "must_include" => true,
      "displayImportance" => round($irradiance / 10),
      "irradiance" => $irradiance,
    );    
    return $ret;
    
  }

  function getCoronalWavelengths(){
    $ret = array();    
    // https://iopscience.iop.org/article/10.3847/1538-4357/aa9edf
    //lambda_A, ion, intensity

    //start-sorted-section
    $ret[] = awl_coronalLine(3800.8,       'Fe IX',  14);
    $ret[] = awl_coronalLine(3986.8,       'Fe XI',  21);
    $ret[] = awl_coronalLine(4087.1,       'Ca XIII', 105);
    $ret[] = awl_coronalLine(4231.2,       'Ni XII',  68);
    $ret[] = awl_coronalLine(4311.8,       'Fe X',   5);
    $ret[] = awl_coronalLine(4359.4,       'Fe IX',  9 );
    $ret[] = awl_coronalLine(4413,         'Ar XIV',  83);
    $ret[] = awl_coronalLine(4566.2,       'Fe XI',  6 );
    $ret[] = awl_coronalLine(4585.3,       'Fe IX',   4);
    $ret[] = awl_coronalLine(4744,         'Ni XVII',   8);
    $ret[] = awl_coronalLine(5116.03,      'Ni XIII', 114);
    $ret[] = awl_coronalLine(5302.86,      'Fe XIV', 1481);
    $ret[] = awl_coronalLine(5446.0,       'Ca XV',  95);
    $ret[] = awl_coronalLine(5694.42,      'Ca XV', 186);
    $ret[] = awl_coronalLine(6374.56,      'Fe X', 163);
    $ret[] = awl_coronalLine(6701.47,      'Ni XV', 216 );
    //end-sorted-section
    polyfill_item_with_chromosphere_info($ret);

    return $ret;
  }

function getMagneticWavelengths(){
  $ret = array();
  // https://articles.adsabs.harvard.edu/pdf/1973SoPh...28....9H

  //start-sorted-section
  $ret[] = awl_notImportant(3598.982,        87, '~ Fe I (magnetic)');
  $ret[] = awl_notImportant(3712.948,       111, 'Cr II (magnetic)');
  $ret[] = awl_notImportant(4070.278,        66, 'Mn I (magnetic)');
  $ret[] = awl_notImportant(4080.880,        61, 'Fe I (magnetic)');
  $ret[] = awl_notImportant(4116.477,        60, '~ V I (magnetic)');
  $ret[] = awl_notImportant(4210.355,       183, 'Fe I (magnetic)');
  $ret[] = awl_notImportant(4220.051,        48, 'V II (magnetic)');
  $ret[] = awl_notImportant(4654.730,       171, '~Fe I, Cr I (magnetic)');
  $ret[] = awl_notImportant(5220.894,        30, '~~Cr I (magnetic)');
  $ret[] = awl_notImportant(5807.787,         7, '~Fe I (magnetic)');
  $ret[] = awl_notImportant(6258.585,        14, 'V I (magnetic)');
  //end-sorted-section
  polyfill_item_with_chromosphere_info($ret);
  return $ret;
}



  function getFurtherWavelengthWorthyToLabelOnScreenButNotWithDedicatedButtons(){
    $ret = array();     

    //start-sorted-section
    $ret[] = awl_notImportant(368.90,        79, '~Fe I, Ti I');
    $ret[] = awl_notImportant(3300.170,      47, 'Ce II?, Nd II?');
    $ret[] = awl_notImportant(3301.225,      43, 'Fe I');
    $ret[] = awl_notImportant(3301.681,      41, 'Ti II');
    $ret[] = awl_notImportant(3302.105,      86, '~Ti II');
    $ret[] = awl_notImportant(3302.383,     112, 'Na I');
    $ret[] = awl_notImportant(3302.863,      72, 'Fe II');
    $ret[] = awl_notImportant(3302.982,      83, 'Na I (Zn I)');
    $ret[] = awl_notImportant(3303.474,      69, 'Fe II');
    $ret[] = awl_notImportant(3303.571,      76, 'Fe I');
    $ret[] = awl_notImportant(3305.156,      72, 'Fe I, Zr II');
    $ret[] = awl_notImportant(3305.627,      57, 'Fe II');
    $ret[] = awl_notImportant(3305.977,     153, 'Fe I');
    $ret[] = awl_notImportant(3306.284,      49, 'Zr II');
    $ret[] = awl_notImportant(3306.378,     145, 'Fe I');
    $ret[] = awl_notImportant(3307.037,      99, 'Fe I, Ni I, Cr II');
    $ret[] = awl_notImportant(3307.717,      97, 'Fe I, Ti II, Cr I');
    $ret[] = awl_notImportant(3308.819,      84, 'Co I, Ti II');
    $ret[] = awl_notImportant(3309.530,      73, 'Ti I');
    $ret[] = awl_notImportant(3310.344,      76, 'Fe I');
    $ret[] = awl_notImportant(3311.935,      68, 'Cr II');
    $ret[] = awl_notImportant(3312.699,      76, 'Ti I, Fe II, Sc II');
    $ret[] = awl_notImportant(3313.009,      66, 'Ni I');
    $ret[] = awl_notImportant(3314.748,      90, 'Fe I');
    $ret[] = awl_notImportant(3315.329,      96, 'Ti II');
    $ret[] = awl_notImportant(3315.679,     153, 'Ni I');
    $ret[] = awl_notImportant(3317.133,      74, 'Fe I');
    $ret[] = awl_notImportant(3318.031,     103, 'Ti II');
    $ret[] = awl_notImportant(3319.078,      59, 'Ti II');
    $ret[] = awl_notImportant(3320.262,     122, 'Ni I');
    $ret[] = awl_notImportant(3321.707,      92, 'Ti II');
    $ret[] = awl_notImportant(3322.325,     108, 'Ni I');
    $ret[] = awl_notImportant(3322.9,       495, '~NH, Ti II, Zr II');
    $ret[] = awl_notImportant(3323.752,      84, 'Fe I');
    $ret[] = awl_notImportant(3324.071,      87, 'Cr II');
    $ret[] = awl_notImportant(3326.777,     214, 'Ti II, Zr II');
    $ret[] = awl_notImportant(3327.886,      64, 'Y II');
    $ret[] = awl_notImportant(3328.357,      66, 'Cr II');
    $ret[] = awl_notImportant(3329.438,     183, 'Ti II, Co I');
    $ret[] = awl_notImportant(3329.95,      118, '~Mg I, Fe I');
    $ret[] = awl_notImportant(3332.15,      194, '~Ti II, Mg I');
    $ret[] = awl_notImportant(3334.225,      92, 'Fe I');
    $ret[] = awl_notImportant(3335.185,     144, 'Ti II');
    $ret[] = awl_notImportant(3335.535,     121, 'Fe I');
    $ret[] = awl_notImportant(3336.689,     416, 'Mg I');
    $ret[] = awl_notImportant(3338.628,      93, 'Fe I');
    $ret[] = awl_notImportant(3339.801,      97, 'Co I, Cr II');
    $ret[] = awl_notImportant(3340.356,     157, 'Ti II');
    $ret[] = awl_notImportant(3341.835,     152, 'Ti I, Ti II');
    $ret[] = awl_notImportant(3341.930,     194, 'Fe I');
    $ret[] = awl_notImportant(3342.226,     110, 'Fe I');
    $ret[] = awl_notImportant(3342.585,      89, 'Cr II');
    $ret[] = awl_notImportant(3343.776,      90, 'Ti II');
    $ret[] = awl_notImportant(3346.746,     119, 'Ti II');
    $ret[] = awl_notImportant(3348.910,     122, 'Ti II');
    $ret[] = awl_notImportant(3349.447,     546, 'Ti II');
    $ret[] = awl_notImportant(3353.129,      67, 'Cr II');
    $ret[] = awl_notImportant(3353.742,      80, 'Sc II');
    $ret[] = awl_notImportant(3356.414,      73, 'Fe I');
    $ret[] = awl_notImportant(3358.515,     100, 'Cr II');
    $ret[] = awl_notImportant(3359.114,     106, 'Ni I');
    $ret[] = awl_notImportant(3359.502,      90, 'Fe I, NH');
    $ret[] = awl_notImportant(3359.689,     102, 'Sc II');
    $ret[] = awl_notImportant(3361.25,      939, '~Ti II, Ti I, Sc II');
    $ret[] = awl_notImportant(3362.802,      89, 'Ni I');
    $ret[] = awl_notImportant(3363.10,      134, 'Fe I, Y II');
    $ret[] = awl_notImportant(3365.773,     107, 'Ni I');
    $ret[] = awl_notImportant(3366.176,     116, 'Ti I, Ti II, Ni I');
    $ret[] = awl_notImportant(3366.84,      180, '~Fe I, Ni I');
    $ret[] = awl_notImportant(3367.13,      146, '~Co I, Fe I');
    $ret[] = awl_notImportant(3368.058,     189, 'Cr II');
    $ret[] = awl_notImportant(3368.948,     101, 'Sc II, Fe I');
    $ret[] = awl_notImportant(3369.578,     407, 'Ni I, Fe I');
    $ret[] = awl_notImportant(3370.798,     118, 'Fe I');
    $ret[] = awl_notImportant(3371.457,     107, 'Ti I');
    $ret[] = awl_notImportant(3371.988,     104, 'Ni I');
    $ret[] = awl_notImportant(3372.089,     118, 'Fe I, NH');
    $ret[] = awl_notImportant(3372.2,       216, '~Sc II, Ti II, NH');
    $ret[] = awl_notImportant(3372.812,     459, 'Ti II');
    $ret[] = awl_notImportant(3374.222,     150, 'Ni I, Fe I');
    $ret[] = awl_notImportant(3374.642,      98, 'Ni I');
    $ret[] = awl_notImportant(3378.71,      143, '~Fe I, Co I, NH, Fe I');
    $ret[] = awl_notImportant(3379.024,      92, 'Fe I');
    $ret[] = awl_notImportant(3380.585,     809, 'Ni I');
    $ret[] = awl_notImportant(3382.413,     123, 'Fe I');
    $ret[] = awl_notImportant(3382.689,      85, 'Cr II');
    $ret[] = awl_notImportant(3383.73,      430, '~Fe I, Ti II');
    $ret[] = awl_notImportant(3385.225,      77, 'Co I');
    $ret[] = awl_notImportant(3387.852,     187, 'Ti II, Zr II');
    $ret[] = awl_notImportant(3388.760,      89, 'Ti II');
    $ret[] = awl_notImportant(3391.039,     238, 'Ni I');
    $ret[] = awl_notImportant(3391.99,      113, '~Zr II, Fe I');
    $ret[] = awl_notImportant(3392.5,       144, '~Fe I, V II, Ti I');
    $ret[] = awl_notImportant(3393.0,       570, '~Ni I, Cr II');
    $ret[] = awl_notImportant(3393.845,      90, 'Cr II');
    $ret[] = awl_notImportant(3394.58,      204, '~Ti II, Fe I');
    $ret[] = awl_notImportant(3395.386,     111, 'Co I (Fe II)');
    $ret[] = awl_notImportant(3399.355,     159, 'Fe I, Zr II');
    $ret[] = awl_notImportant(3401.530,     104, 'Fe I');
    $ret[] = awl_notImportant(3402.420,      95, 'Ti II, Cr II');
    $ret[] = awl_notImportant(3403.3,       155, '~Fe I, Cr II');
    $ret[] = awl_notImportant(3404.31,      176, '~Fe I, Mo I');
    $ret[] = awl_notImportant(3405.126,     206, 'Co I');
    $ret[] = awl_notImportant(3406.81,      136, 'Fe I');
    $ret[] = awl_notImportant(3407.205,     101, 'Ti II');
    $ret[] = awl_notImportant(3407.51,      248, '~Fe I');
    $ret[] = awl_notImportant(3408.779,     131, 'Cr II');
    $ret[] = awl_notImportant(3409.18,      134, '~Co I, Fe I');
    $ret[] = awl_notImportant(3409.579,     100, 'Ni I');
    $ret[] = awl_notImportant(3412.349,     123, 'Co I');
    $ret[] = awl_notImportant(3413.143,     156, 'Fe I');
    $ret[] = awl_notImportant(3413.947,     112, 'Ni I');
    $ret[] = awl_notImportant(3414.779,     816, 'Ni I');
    $ret[] = awl_notImportant(3417.169,     121, 'Co I');
    $ret[] = awl_notImportant(3417.85,      139, '~Co I, Fe I');
    $ret[] = awl_notImportant(3418.522,     111, 'Fe I');
    $ret[] = awl_notImportant(3421.221,     103, 'Cr II');
    $ret[] = awl_notImportant(3422.496,     113, 'Fe I (Gd II)');
    $ret[] = awl_notImportant(3422.769,     165, 'Cr II (Ce II)');
    $ret[] = awl_notImportant(3423.715,     366, 'Ni I');
    $ret[] = awl_notImportant(3424.299,     128, 'Fe I');
    $ret[] = awl_notImportant(3426.37,      143, 'Fe I');
    $ret[] = awl_notImportant(3426.65,      121, 'Fe I');
    $ret[] = awl_notImportant(3426.992,     101, 'Fe I');
    $ret[] = awl_notImportant(3427.129,     218, 'Fe I');
    $ret[] = awl_notImportant(3428.207,     117, 'Fe I');
    $ret[] = awl_notImportant(3428.45,      110, '~Ni I, Fe I');
    $ret[] = awl_notImportant(3431.586,     106, 'Co I');
    $ret[] = awl_notImportant(3431.830,      90, 'Fe I');
    $ret[] = awl_notImportant(3432.728,      42, 'Nb II');
    $ret[] = awl_notImportant(3433.048,      11, 'Co I');
    $ret[] = awl_notImportant(3433.318,      80, 'Cr II');
    $ret[] = awl_notImportant(3433.579,     402, 'Ni I, Cr I');
    $ret[] = awl_notImportant(3436.196,      80, 'Cr I');
    $ret[] = awl_notImportant(3437.054,     126, 'Fe I');
    $ret[] = awl_notImportant(3437.291,     184, 'Ni I');
    $ret[] = awl_notImportant(3438.28,      132, '~Zr II, Fe I');
    $ret[] = awl_notImportant(3438.99,      140, '~Mn II, Fe I');
    $ret[] = awl_notImportant(3439.805,      66, 'Gd II');
    $ret[] = awl_notImportant(3440.626,    1243, 'Fe I');
    $ret[] = awl_notImportant(3441.019,     634, 'Fe I');
    $ret[] = awl_notImportant(3441.982,     329, 'Mn II');
    $ret[] = awl_notImportant(3443.381,      60, 'Ti II');
    $ret[] = awl_notImportant(3443.655,     141, 'Co I');
    $ret[] = awl_notImportant(3443.884,     655, 'Fe I');
    $ret[] = awl_notImportant(3444.3,       148, '~Ni I, Ti II');
    $ret[] = awl_notImportant(3445.125,     137, 'Fe I');
    $ret[] = awl_notImportant(3446.271,     470, 'Ni I');
    $ret[] = awl_notImportant(3447.285,     100, 'Fe I');
    $ret[] = awl_notImportant(3449.175,     115, 'Co I');
    $ret[] = awl_notImportant(3449.448,     138, 'Co I');
    $ret[] = awl_notImportant(3451.922,     110, 'Fe I');
    $ret[] = awl_notImportant(3452.284,     150, 'Fe I');
    $ret[] = awl_notImportant(3452.475,      68, 'Ti II');
    $ret[] = awl_notImportant(3452.905,     247, 'Ni I');
    $ret[] = awl_notImportant(3453.512,     310, 'Co I');
    $ret[] = awl_notImportant(3455.245,     114, 'Co I, Cr I');
    $ret[] = awl_notImportant(3456.394,     115, 'Ti II');
    $ret[] = awl_notImportant(3458.467,     656, 'Ni I');
    $ret[] = awl_notImportant(3459.918,     159, 'Fe I');
    $ret[] = awl_notImportant(3460.040,     100, 'Mn II');
    $ret[] = awl_notImportant(3460.326,     181, 'Mn II');
    $ret[] = awl_notImportant(3461.499,     120, 'Ti II');
    $ret[] = awl_notImportant(3461.667,     758, 'Ni I');
    $ret[] = awl_notImportant(3464.474,      63, 'Sr II (Fe II)');
    $ret[] = awl_notImportant(3465.880,     544, 'Fe I');
    $ret[] = awl_notImportant(3467.509,      96, 'Ni I');
    $ret[] = awl_notImportant(3468.686,      66, 'Fe II');
    $ret[] = awl_notImportant(3469.493,     180, 'Ni I');
    $ret[] = awl_notImportant(3471.31,      169, '~Fe I');
    $ret[] = awl_notImportant(3472.558,     374, 'Ni I');
    $ret[] = awl_notImportant(3474.060,     203, 'Mn II');
    $ret[] = awl_notImportant(3474.150,      78, 'Mn II');
    $ret[] = awl_notImportant(3475.133,      49, 'Cr II');
    $ret[] = awl_notImportant(3475.457,     622, 'Fe I');
    $ret[] = awl_notImportant(3475.665,     112, 'Fe I');
    $ret[] = awl_notImportant(3476.712,     465, 'Fe I');
    $ret[] = awl_notImportant(3477.186,     102, 'Ti II');
    $ret[] = awl_notImportant(3479.923,      78, 'Fe II');
    $ret[] = awl_notImportant(3480.886,      75, 'Ti II');
    $ret[] = awl_notImportant(3481.164,      74, 'Zr II');
    $ret[] = awl_notImportant(3481.78,       78, '~Gd II');
    $ret[] = awl_notImportant(3482.574,      50, 'Cr II');
    $ret[] = awl_notImportant(3482.909,     153, 'Mn II');
    $ret[] = awl_notImportant(3483.017,     109, 'Fe I');
    $ret[] = awl_notImportant(3483.414,     114, 'Co I');
    $ret[] = awl_notImportant(3483.784,     198, 'Ni I');
    $ret[] = awl_notImportant(3487.992,      65, 'Fe II');
    $ret[] = awl_notImportant(3488.678,     126, 'Mn II');
    $ret[] = awl_notImportant(3489.163,      98, 'Fe II');
    $ret[] = awl_notImportant(3489.407,     135, 'Co I');
    $ret[] = awl_notImportant(3489.72,      130, '~Fe I, Ti II');
    $ret[] = awl_notImportant(3490.594,     830, 'Fe I');
    $ret[] = awl_notImportant(3491.056,     104, 'Ti II');
    $ret[] = awl_notImportant(3492.975,     826, 'Ni I');
    $ret[] = awl_notImportant(3494.7,        94, '~Fe II, Ni I');
    $ret[] = awl_notImportant(3495.26,      116, '~Fe I');
    $ret[] = awl_notImportant(3495.383,      61, 'Cr II');
    $ret[] = awl_notImportant(3495.68,      138, '~Co I, Fe II, Ti I');
    $ret[] = awl_notImportant(3495.85,      141, '~Fe I, Mn II');
    $ret[] = awl_notImportant(3496.085,      48, 'Y II');
    $ret[] = awl_notImportant(3496.209,      96, 'Fe I, Zr II');
    $ret[] = awl_notImportant(3496.681,     101, 'Co I');
    $ret[] = awl_notImportant(3496.813,      77, 'Mn II');
    $ret[] = awl_notImportant(3497.16,      166, '~Fe I');
    $ret[] = awl_notImportant(3497.529,      59, 'Mn II');
    $ret[] = awl_notImportant(3497.843,     726, 'Fe I');
    $ret[] = awl_notImportant(3500.335,      90, 'Ti II');
    $ret[] = awl_notImportant(3500.715,      84, '~Co II');
    $ret[] = awl_notImportant(3500.857,     163, 'Ni I');
    $ret[] = awl_notImportant(3502.291,     111, 'Co I');
    $ret[] = awl_notImportant(3502.6,       111, '~Ni I, Co I');
    $ret[] = awl_notImportant(3503.473,      50, 'Fe II');
    $ret[] = awl_notImportant(3504.442,      65, 'V II, Fe I');
    $ret[] = awl_notImportant(3504.892,     132, 'Fe I, Ti II');
    $ret[] = awl_notImportant(3506.328,     140, 'Co I');
    $ret[] = awl_notImportant(3506.506,     132, 'Fe I');
    $ret[] = awl_notImportant(3507.698,      80, 'Ni I');
    $ret[] = awl_notImportant(3508.5,       105, '~Fe I');
    $ret[] = awl_notImportant(3509.853,     105, 'Co I, Fe I, Ti II');
    $ret[] = awl_notImportant(3510.327,     489, 'Ni I');
    $ret[] = awl_notImportant(3510.846,      87, 'Ti II');
    $ret[] = awl_notImportant(3511.839,      90, 'Cr II');
    $ret[] = awl_notImportant(3512.646,     132, 'Co I');
    $ret[] = awl_notImportant(3513.825,     307, 'Fe I');
    $ret[] = awl_notImportant(3515.066,     718, 'Ni I');
    $ret[] = awl_notImportant(3518.348,      98, 'Co I');
    $ret[] = awl_notImportant(3519.764,     171, 'Ni I');
    $ret[] = awl_notImportant(3520.5,       114, '~V II, Co I');
    $ret[] = awl_notImportant(3521.270,     381, 'Fe I');
    $ret[] = awl_notImportant(3521.57,      109, '~Co I');
    $ret[] = awl_notImportant(3524.536,    1271, 'Ni I');
    $ret[] = awl_notImportant(3526.170,     422, 'Fe I');
    $ret[] = awl_notImportant(3526.847,     209, 'Co I');
    $ret[] = awl_notImportant(3527.795,     107, 'Fe I');
    $ret[] = awl_notImportant(3529.823,     148, 'Fe I');
    $ret[] = awl_notImportant(3532.120,     101, 'Mn I');
    $ret[] = awl_notImportant(3533.203,     223, 'Fe I');
    $ret[] = awl_notImportant(3535.412,      79, 'Ti II');
    $ret[] = awl_notImportant(3536.567,     189, 'Fe I');
    $ret[] = awl_notImportant(3537.903,     107, 'Fe I');
    $ret[] = awl_notImportant(3540.126,      93, 'Fe I');
    $ret[] = awl_notImportant(3541.095,     214, 'Fe I');
    $ret[] = awl_notImportant(3542.090,     224, 'Fe I');
    $ret[] = awl_notImportant(3545.644,     108, 'Fe I');
    $ret[] = awl_notImportant(3547.799,     124, 'Mn I');
    $ret[] = awl_notImportant(3548.033,     107, 'Mn I, Fe I');
    $ret[] = awl_notImportant(3548.190,     139, 'Ni I, Mn I');
    $ret[] = awl_notImportant(3552.845,     120, 'Fe I');
    $ret[] = awl_notImportant(3553.483,      96, 'Ni I');
    $ret[] = awl_notImportant(3553.746,     116, 'Fe I');
    $ret[] = awl_notImportant(3554.122,     127, 'Fe I');
    $ret[] = awl_notImportant(3554.937,     404, 'Fe I');
    $ret[] = awl_notImportant(3556.803,     143, 'V II');
    $ret[] = awl_notImportant(3556.896,     243, 'Fe I');
    $ret[] = awl_notImportant(3558.532,     485, 'Fe I, Sc II');
    $ret[] = awl_notImportant(3559.464,      94, '~Fe I');
    $ret[] = awl_notImportant(3560.589,      62, 'V II');
    $ret[] = awl_notImportant(3560.897,      82, 'Co I');
    $ret[] = awl_notImportant(3561.582,      58, 'Ti II');
    $ret[] = awl_notImportant(3561.757,      77, 'Ni I');
    $ret[] = awl_notImportant(3565.396,     990, 'Fe I');
    $ret[] = awl_notImportant(3566.383,     458, 'Ni I');
    $ret[] = awl_notImportant(3567.72,      110, '~Sc II, Fe I');
    $ret[] = awl_notImportant(3569.384,     116, 'Co I');
    $ret[] = awl_notImportant(3570.044,    1380, 'Fe I');
    $ret[] = awl_notImportant(3571.875,     237, 'Ni I');
    $ret[] = awl_notImportant(3572.478,     106, 'Zr II');
    $ret[] = awl_notImportant(3572.573,     112, 'Sc II');
    $ret[] = awl_notImportant(3573.735,      84, 'Ti II');
    $ret[] = awl_notImportant(3573.87,      156, '~Fe I');
    $ret[] = awl_notImportant(3574.967,      90, 'Co I');
    $ret[] = awl_notImportant(3576.35,      116, '~Sc II');
    $ret[] = awl_notImportant(3576.766,      87, 'Fe I, Ni II');
    $ret[] = awl_notImportant(3577.875,     105, 'Mn I');
    $ret[] = awl_notImportant(3578.693,     488, 'Cr I');
    $ret[] = awl_notImportant(3580.927,      54, 'Sc II');
    $ret[] = awl_notImportant(3581.209,    2144, 'Fe I');
    $ret[] = awl_notImportant(3583.339,     122, 'Fe I');
    $ret[] = awl_notImportant(3583.697,     112, 'Fe I, V I');
    $ret[] = awl_notImportant(3584.520,      44, 'Y II');
    $ret[] = awl_notImportant(3584.661,     182, 'Fe I');
    $ret[] = awl_notImportant(3585.339,     839, 'Fe I, Cr II');
    $ret[] = awl_notImportant(3585.714,     168, 'Fe I');
    $ret[] = awl_notImportant(3586.118,     122, 'Fe I');
    $ret[] = awl_notImportant(3586.544,      74, 'Mn I');
    $ret[] = awl_notImportant(3586.990,     532, 'Fe I');
    $ret[] = awl_notImportant(3587.230,     250, 'Co I, Fe I');
    $ret[] = awl_notImportant(3587.617,     110, 'Fe I?');
    $ret[] = awl_notImportant(3587.760,     112, 'Fe I');
    $ret[] = awl_notImportant(3587.943,     129, 'Ni I, Zr II');
    $ret[] = awl_notImportant(3588.6,       161, '~Fe I');
    $ret[] = awl_notImportant(3589.112,     104, 'Fe I');
    $ret[] = awl_notImportant(3589.461,      97, 'Fe I');
    $ret[] = awl_notImportant(3589.632,     108, 'Sc II');
    $ret[] = awl_notImportant(3589.767,     102, 'V II');
    $ret[] = awl_notImportant(3590.489,     136, 'Sc II');
    $ret[] = awl_notImportant(3592.027,      75, 'V II');
    $ret[] = awl_notImportant(3593.495,     436, 'Cr I');
    $ret[] = awl_notImportant(3594.638,     146, 'Fe I');
    $ret[] = awl_notImportant(3596.054,      95, 'Ti II');
    $ret[] = awl_notImportant(3597.712,     181, 'Ni I');
    $ret[] = awl_notImportant(3602.085,     103, 'Co I, Fe I');
    $ret[] = awl_notImportant(3602.544,     172, '~Fe I');
    $ret[] = awl_notImportant(3603.210,     119, 'Fe I');
    $ret[] = awl_notImportant(3603.8,       155, '~Cr II, Fe I');
    $ret[] = awl_notImportant(3605.339,     495, 'Cr I, Co I');
    $ret[] = awl_notImportant(3606.694,     271, 'Fe I');
    $ret[] = awl_notImportant(3608.869,    1046, 'Fe I');
    $ret[] = awl_notImportant(3610.166,     231, 'Fe I, Ti I');
    $ret[] = awl_notImportant(3610.48,      250, '~Ni I');
    $ret[] = awl_notImportant(3612.075,     118, 'Fe I');
    $ret[] = awl_notImportant(3612.744,     160, 'Ni I');
    $ret[] = awl_notImportant(3613.13,      139, '~Zr II, Fe I, Cr II');
    $ret[] = awl_notImportant(3613.85,      194, '~Sc II');
    $ret[] = awl_notImportant(3618.777,    1410, 'Fe I');
    $ret[] = awl_notImportant(3619.400,     568, 'Ni I');
    $ret[] = awl_notImportant(3621.201,      72, 'V II, Co II');
    $ret[] = awl_notImportant(3621.467,     140, 'Fe I');
    $ret[] = awl_notImportant(3622.009,     127, 'Fe I');
    $ret[] = awl_notImportant(3623.192,     105, 'Fe I');
    $ret[] = awl_notImportant(3624.08,      139, '~Fe I, Ca I');
    $ret[] = awl_notImportant(3624.30,       95, 'Fe I, Co I');
    $ret[] = awl_notImportant(3624.733,     132, 'Ni I');
    $ret[] = awl_notImportant(3624.839,     122, 'Ti II (Fe II)');
    $ret[] = awl_notImportant(3625.147,     106, 'Fe I');
    $ret[] = awl_notImportant(3627.813,      98, 'Co I');
    $ret[] = awl_notImportant(3628.707,      57, 'Y II');
    $ret[] = awl_notImportant(3630.754,     133, 'Ca I, Sc II');
    $ret[] = awl_notImportant(3631.475,    1364, 'Fe I, Cr II');
    $ret[] = awl_notImportant(3632.049,     117, 'Fe I');
    $ret[] = awl_notImportant(3634.332,     136, 'Fe I');
    $ret[] = awl_notImportant(3634.952,     129, 'Ni I');
    $ret[] = awl_notImportant(3636.2,       132, '~Fe I');
    $ret[] = awl_notImportant(3641.335,     109, 'Ti II');
    $ret[] = awl_notImportant(3642.806,     150, 'Sc II');
    $ret[] = awl_notImportant(3644.417,     141, 'Ca I');
    $ret[] = awl_notImportant(3645.313,     132, 'Sc II');
    $ret[] = awl_notImportant(3645.497,      90, 'Fe I');
    $ret[] = awl_notImportant(3645.827,     103, 'Fe I');
    $ret[] = awl_notImportant(3647.851,     970, 'Fe I');
    $ret[] = awl_notImportant(3651.800,     114, 'Sc II');
    $ret[] = awl_notImportant(3651.921,      64, 'Fe I, CH');
    $ret[] = awl_notImportant(3652.551,      70, 'Co I');
    $ret[] = awl_notImportant(3653.352,      44, 'Fe I');
    $ret[] = awl_notImportant(3653.501,      98, 'Ti I');
    $ret[] = awl_notImportant(3653.761,      66, 'Fe I');
    $ret[] = awl_notImportant(3653.94,      102, '~Cr I, Fe I');
    $ret[] = awl_notImportant(3654.598,      64, 'Ti I (Gd II)');
    $ret[] = awl_notImportant(3654.673,      35, 'Fe I');
    $ret[] = awl_notImportant(3655.003,      50, 'Fe I');
    $ret[] = awl_notImportant(3655.355,      40, 'Fe I');
    $ret[] = awl_notImportant(3655.472,      88, 'Fe I');
    $ret[] = awl_notImportant(3655.661,     100, 'Fe I');
    $ret[] = awl_notImportant(3656.225,     116, '~Fe I, Cr I');
    $ret[] = awl_notImportant(3657.137,      72, 'Fe I');
    $ret[] = awl_notImportant(3657.423,      48, 'Fe I');
    $ret[] = awl_notImportant(3657.711,      57, 'Ni I, Fe I');
    $ret[] = awl_notImportant(3657.905,     102, 'Fe I');
    $ret[] = awl_notImportant(3658.024,      40, 'Fe I');
    $ret[] = awl_notImportant(3658.099,      71, 'Ti I');
    $ret[] = awl_notImportant(3658.550,      50, 'Fe I');
    $ret[] = awl_notImportant(3659.524,      98, 'Fe I');
    $ret[] = awl_notImportant(3659.762,     103, 'Ti II');
    $ret[] = awl_notImportant(3660.329,      60, 'Fe I');
    $ret[] = awl_notImportant(3660.636,      50, 'Ti I');
    $ret[] = awl_notImportant(3660.778,      50, '?');
    $ret[] = awl_notImportant(3661.372,      64, '~Sm II, Fe I, V II');
    $ret[] = awl_notImportant(3661.957,      70, 'Ni I');
    $ret[] = awl_notImportant(3662.170,      59, 'Co I (Zr II)');
    $ret[] = awl_notImportant(3662.240,      94, 'Ti II');
    $ret[] = awl_notImportant(3662.87,       96, '~Cr I, Fe I, Sm II');
    $ret[] = awl_notImportant(3663.23,      100, '~Cr I, Fe I');
    $ret[] = awl_notImportant(3663.43,      116, '~Fe I');
    $ret[] = awl_notImportant(3663.698,      40, 'Zr I');
    $ret[] = awl_notImportant(3664.097,     130, 'Ni I');
    $ret[] = awl_notImportant(3664.22,       48, '~Sc II?');
    $ret[] = awl_notImportant(3664.405,     103, 'Fe I');
    $ret[] = awl_notImportant(3664.540,     103, 'Fe I');
    $ret[] = awl_notImportant(3664.623,      68, 'Y II');
    $ret[] = awl_notImportant(3664.701,      63, 'Fe I');
    $ret[] = awl_notImportant(3665.997,      34, 'Cr I');
    $ret[] = awl_notImportant(3666.064,     105, 'Fe I');
    $ret[] = awl_notImportant(3666.27,      105, '~Fe I');
    $ret[] = awl_notImportant(3666.539,      83, 'Sc II');
    $ret[] = awl_notImportant(3666.770,      75, 'Fe I');
    $ret[] = awl_notImportant(3666.931,      92, 'Fe I');
    $ret[] = awl_notImportant(3667.261,     103, 'Fe I');
    $ret[] = awl_notImportant(3667.996,      89, 'Fe I, Ce II');
    $ret[] = awl_notImportant(3668.217,      82, 'Fe I, Ni I');
    $ret[] = awl_notImportant(3668.48,       53, '~Zr II, Y II');
    $ret[] = awl_notImportant(3668.891,      47, 'Fe I');
    $ret[] = awl_notImportant(3668.969,      78, 'Fe I');
    $ret[] = awl_notImportant(3669.244,     103, 'Ni I');
    $ret[] = awl_notImportant(3669.406,      40, 'V II');
    $ret[] = awl_notImportant(3669.526,     120, 'Fe I');
    $ret[] = awl_notImportant(3670.06,      143, '~Fe I, Co I');
    $ret[] = awl_notImportant(3670.431,     120, 'Ni I');
    $ret[] = awl_notImportant(3670.542,      37, 'Mn I');
    $ret[] = awl_notImportant(3670.72,      138, '~Sm II, Fe I');
    $ret[] = awl_notImportant(3671.276,      37, 'Zr II');
    $ret[] = awl_notImportant(3671.524,      43, 'Fe I');
    $ret[] = awl_notImportant(3671.682,      67, 'Ti I');
    $ret[] = awl_notImportant(3672.712,      68, 'Fe I');
    $ret[] = awl_notImportant(3673.08,       97, '~Fe I');
    $ret[] = awl_notImportant(3673.426,      40, 'V I, Ca I');
    $ret[] = awl_notImportant(3673.683,      31, 'Fe I');
    $ret[] = awl_notImportant(3673.773,      53, 'Fe II');
    $ret[] = awl_notImportant(3673.888,      63, 'Fe I');
    $ret[] = awl_notImportant(3674.1,       178, 'Fe I, Ni I');
    $ret[] = awl_notImportant(3674.413,      58, 'Fe I');
    $ret[] = awl_notImportant(3674.75,      106, '~Zr II, Fe I');
    $ret[] = awl_notImportant(3675.294,      42, 'Ca I');
    $ret[] = awl_notImportant(3675.689,      38, 'V I');
    $ret[] = awl_notImportant(3676.322,     102, 'Fe I');
    $ret[] = awl_notImportant(3676.562,      66, 'Co I');
    $ret[] = awl_notImportant(3676.878,      73, 'Fe I');
    $ret[] = awl_notImportant(3677.318,      98, 'Fe I');
    $ret[] = awl_notImportant(3677.462,      85, 'Fe I');
    $ret[] = awl_notImportant(3677.628,     147, 'Fe I');
    $ret[] = awl_notImportant(3677.695,      68, 'Cr II');
    $ret[] = awl_notImportant(3677.855,      98, 'Cr II');
    $ret[] = awl_notImportant(3677.909,      88, 'Fe I, Cr II');
    $ret[] = awl_notImportant(3678.234,      67, 'Ca I');
    $ret[] = awl_notImportant(3678.869,      89, 'Fe I, Zr II');
    $ret[] = awl_notImportant(3679.685,      43, 'Ti II');
    $ret[] = awl_notImportant(3679.96,      448, '~Fe I');
    $ret[] = awl_notImportant(3680.389,      52, 'Fe I');
    $ret[] = awl_notImportant(3680.665,      70, 'Fe I');
    $ret[] = awl_notImportant(3680.802,     128, 'Fe I');
    $ret[] = awl_notImportant(3680.944,      66, 'Fe I');
    $ret[] = awl_notImportant(3681.230,      59, 'Fe I');
    $ret[] = awl_notImportant(3681.65,       72, 'Fe I');
    $ret[] = awl_notImportant(3682.2,       202, '~Fe I');
    $ret[] = awl_notImportant(3683.07,      166, '~Co I, Fe I, V I');
    $ret[] = awl_notImportant(3683.623,      52, 'Fe I');
    $ret[] = awl_notImportant(3684.123,     120, 'Fe I');
    $ret[] = awl_notImportant(3685.196,     275, 'Ti II');
    $ret[] = awl_notImportant(3685.527,      56, 'Cr I');
    $ret[] = awl_notImportant(3686.004,     151, 'Fe I');
    $ret[] = awl_notImportant(3686.263,      85, 'Fe I, V I');
    $ret[] = awl_notImportant(3686.787,      44, 'Cr I');
    $ret[] = awl_notImportant(3687.102,      73, 'Fe I');
    $ret[] = awl_notImportant(3687.466,     564, 'Fe I, V I');
    $ret[] = awl_notImportant(3687.660,      59, 'Fe I');
    $ret[] = awl_notImportant(3688.173,      47, 'Fe I');
    $ret[] = awl_notImportant(3688.44,      134, '~Fe I, Eu II, Ni I');
    $ret[] = awl_notImportant(3689.469,     158, 'Fe I');
    $ret[] = awl_notImportant(3690.459,      63, 'Fe I');
    $ret[] = awl_notImportant(3690.731,      86, 'Fe I (Co I)');
    $ret[] = awl_notImportant(3691.314,      50, 'Fe I');
    $ret[] = awl_notImportant(3692.226,      40, 'V I');
    $ret[] = awl_notImportant(3692.650,      47, 'Fe I (Mo II)');
    $ret[] = awl_notImportant(3692.816,      11, 'Mn I');
    $ret[] = awl_notImportant(3693.032,      86, 'Fe I');
    $ret[] = awl_notImportant(3693.120,      35, 'Co I');
    $ret[] = awl_notImportant(3693.478,      61, 'Co I');
    $ret[] = awl_notImportant(3693.666,      36, 'Mn I');
    $ret[] = awl_notImportant(3694.027,     272, 'Fe I');
    $ret[] = awl_notImportant(3694.199,      67, 'Y II');
    $ret[] = awl_notImportant(3694.436,      38, 'Ti I');
    $ret[] = awl_notImportant(3695.056,      98, 'Fe I');
    $ret[] = awl_notImportant(3695.521,      41, 'Fe I');
    $ret[] = awl_notImportant(3695.652,      55, 'Fe I');
    $ret[] = awl_notImportant(3695.869,      42, 'V I');
    $ret[] = awl_notImportant(3696.038,      37, 'Fe I');
    $ret[] = awl_notImportant(3696.383,      46, 'Ti II');
    $ret[] = awl_notImportant(3696.55,       53, '~Fe I, Mn I');
    $ret[] = awl_notImportant(3697.433,     101, 'Fe I, Zr II');
    $ret[] = awl_notImportant(3697.537,      82, 'Fe I');
    $ret[] = awl_notImportant(3698.017,      37, 'Cr II, Fe I');
    $ret[] = awl_notImportant(3698.167,      52, 'Zr II, Ti I');
    $ret[] = awl_notImportant(3698.609,      75, 'Fe I');
    $ret[] = awl_notImportant(3699.144,      69, 'Fe I');
    $ret[] = awl_notImportant(3699.825,      32, 'Fe I');
    $ret[] = awl_notImportant(3700.3,        34, 'Tm II, V II');
    $ret[] = awl_notImportant(3700.95,      186, '~CN?, Rh I?, V II, Fe I');
    $ret[] = awl_notImportant(3705.577,     562, 'Fe I');
    $ret[] = awl_notImportant(3706.220,     290, 'Ti II');
    $ret[] = awl_notImportant(3719.947,    1664, 'Fe I');
    $ret[] = awl_notImportant(3734.874,    3027, 'Fe I');
    $ret[] = awl_notImportant(3738.34,      136, 'Fe I, Cr II');
    $ret[] = awl_notImportant(3745.574,    1202, 'Fe I');
    $ret[] = awl_notImportant(3749.49,     1907, 'Fe I');
    $ret[] = awl_notImportant(3754.53,      120, 'Fe I, Cr II');
    $ret[] = awl_notImportant(3758.245,    1647, 'Fe I');
    $ret[] = awl_notImportant(3761.37,      277, '~Ti II, Fe I');
    $ret[] = awl_notImportant(3763.803,     829, 'Fe I');
    $ret[] = awl_notImportant(3765.551,     174, 'Fe I');
    $ret[] = awl_notImportant(3765.710,      68, 'Fe I');
    $ret[] = awl_notImportant(3767.204,     820, 'Fe I');
    $ret[] = awl_notImportant(3774.832,     100, 'Fe I');
    $ret[] = awl_notImportant(3786.682,     132, 'Fe I');
    $ret[] = awl_notImportant(3787.891,     512, 'Fe I');
    $ret[] = awl_notImportant(3789.419,     146, 'Fe I');
    $ret[] = awl_notImportant(3799.558,     622, 'Fe I');
    $ret[] = awl_notImportant(3801.683,     108, 'Fe I');
    $ret[] = awl_notImportant(3801.815,     112, 'Fe I');
    $ret[] = awl_notImportant(3801.990,     105, 'Fe I');
    $ret[] = awl_notImportant(3805.349,     171, 'Fe I');
    $ret[] = awl_notImportant(3807.151,     193, 'Ni I');
    $ret[] = awl_notImportant(3810.22,     1000, 'CN');
    $ret[] = awl_notImportant(3825.89,     1519, 'Fe I');
    $ret[] = awl_notImportant(3878.580,     724, 'Fe I');
    $ret[] = awl_notImportant(3886.294,     920, 'Fe I');
    $ret[] = awl_notImportant(3887.059,     219, 'Fe I');
    $ret[] = awl_notImportant(3888.422,      23, 'Fe I');
    $ret[] = awl_notImportant(3888.524,     265, 'Fe I');
    $ret[] = awl_notImportant(3888.829,      49, 'Fe I');
    $ret[] = awl_notImportant(3891.781,      30, 'Ba II');
    $ret[] = awl_notImportant(3891.934,      88, 'Fe I, (Mg I)');
    $ret[] = awl_notImportant(3893.4,       182, '~Fe I');
    $ret[] = awl_notImportant(3894.07,      154, '~Fe, Cr I, Co I');
    $ret[] = awl_notImportant(3895.66,      361, '~Fe I, Mg I');
    $ret[] = awl_notImportant(3902.9,       560, '~Fe I, Cr I, (Mo I)');
    $ret[] = awl_notImportant(3903.88,      224, '~Mg I, Fe I');
    $ret[] = awl_notImportant(3915.939,      47, 'Zr II');
    $ret[] = awl_notImportant(3922.923,     414, 'Fe I');
    $ret[] = awl_notImportant(3927.933,     187, 'Fe I');
    $ret[] = awl_notImportant(3950.358,      55, 'Y II');
    $ret[] = awl_notImportant(3951.964,      63, 'V II');
    $ret[] = awl_notImportant(3952.57,      133, '~Ce II, Fe I');
    $ret[] = awl_notImportant(3953.158,      97, 'Fe I, Cr I');
    $ret[] = awl_notImportant(3953.861,      50, 'Fe I');
    $ret[] = awl_notImportant(3957.041,     123, 'Fe I, Ca I');
    $ret[] = awl_notImportant(3960.284,      50, 'Fe I');
    $ret[] = awl_notImportant(4007.926,      68, '?');
    $ret[] = awl_notImportant(4008.89,      106, '~Fe I, Ti I');
    $ret[] = awl_notImportant(4009.68,      122, '~Ti I, Fe I');
    $ret[] = awl_notImportant(4012.253,      39, 'Nd II');
    $ret[] = awl_notImportant(4012.390,      93, 'Ce II, Ti II');
    $ret[] = awl_notImportant(4013.816,     102, 'Fe I');
    $ret[] = awl_notImportant(4013.960,      34, 'Co I');
    $ret[] = awl_notImportant(4018.104,     139, 'Mn I');
    $ret[] = awl_notImportant(4034.43,      213, '~Mn I, CH');
    $ret[] = awl_notImportant(4035.732,     216, '~Mn I, V II, Co I, Ti I');
    $ret[] = awl_notImportant(4045.825,    1174, 'Fe I');
    $ret[] = awl_notImportant(4048.72,      138, 'Zr II, Mn I, Cr I');
    $ret[] = awl_notImportant(4054.83,      135, '~Fe I');
    $ret[] = awl_notImportant(4055.551,     114, 'Mn I');
    $ret[] = awl_notImportant(4057.515,     197, 'Mg I');
    $ret[] = awl_notImportant(4063.605,     787, 'Fe I');
    $ret[] = awl_notImportant(4067.988,     133, 'Fe I');
    $ret[] = awl_notImportant(4068.544,      27, 'Co I');
    $ret[] = awl_notImportant(4069.070,      48, 'Fe I');
    $ret[] = awl_notImportant(4070.281,      66, 'Mn I');
    $ret[] = awl_notImportant(4070.777,      94, 'Fe I');
    $ret[] = awl_notImportant(4071.749,     723, 'Fe I');
    $ret[] = awl_notImportant(4073.767,      90, 'Fe I');
    $ret[] = awl_notImportant(4074.684,      42, 'Fe I');
    $ret[] = awl_notImportant(4074.794,     110, 'Fe I');
    $ret[] = awl_notImportant(4075.103,      62, '(Nd II)');
    $ret[] = awl_notImportant(4075.949,      83, 'Fe I');
    $ret[] = awl_notImportant(4076.226,      58, 'Fe I');
    $ret[] = awl_notImportant(4076.495,      62, 'Fe I');
    $ret[] = awl_notImportant(4076.637,     127, 'Fe I');
    $ret[] = awl_notImportant(4076.808,      94, 'Fe I');
    $ret[] = awl_notImportant(4077.347,      41, 'La II');
    $ret[] = awl_notImportant(4078.365,     124, 'Fe I');
    $ret[] = awl_notImportant(4078.475,      56, 'Ti I');
    $ret[] = awl_notImportant(4079.21,      144, '~Fe I, Mn I');
    $ret[] = awl_notImportant(4079.38,      104, '~Mn I');
    $ret[] = awl_notImportant(4082.943,      94, 'Mn I');
    $ret[] = awl_notImportant(4092.396,     108, 'Fe I');
    $ret[] = awl_notImportant(4092.669,     115, 'Ca I, V I');
    $ret[] = awl_notImportant(4102.943,     106, 'Si I');
    $ret[] = awl_notImportant(4111.787,     106, 'V I');
    $ret[] = awl_notImportant(4118.555,     154, 'Fe I');
    $ret[] = awl_notImportant(4118.782,     148, 'Co I');
    $ret[] = awl_notImportant(4121.325,     125, 'Co I');
    $ret[] = awl_notImportant(4128.098,     107, 'V I, (Si II)');
    $ret[] = awl_notImportant(4132.067,     404, 'Fe I');
    $ret[] = awl_notImportant(4132.908,     123, 'Fe I (Sc I)');
    $ret[] = awl_notImportant(4134.6,       300, '~Fe I');
    $ret[] = awl_notImportant(4143.878,     466, 'Fe I');
    $ret[] = awl_notImportant(4177.58,      151, '~Y II, Fe I');
    $ret[] = awl_notImportant(4179.383,     126, 'V I, Cr II');
    $ret[] = awl_notImportant(4198.3,       234, '~ Fe I');
    $ret[] = awl_notImportant(4202.040,     326, 'Fe I');
    $ret[] = awl_notImportant(4224.300,      32, 'Zr II, Fe I');
    $ret[] = awl_notImportant(4224.860,      91, 'CH, Cr II');
    $ret[] = awl_notImportant(4225.215,      32, 'V II');
    $ret[] = awl_notImportant(4225.461,     120, 'Fe I');
    $ret[] = awl_notImportant(4225.962,      74, 'Fe I');
    $ret[] = awl_notImportant(4226.431,      71, 'Fe I');
    $ret[] = awl_notImportant(4226.568,      18, 'Ge I');
    $ret[] = awl_notImportant(4227.440,     185, 'Fe I');
    $ret[] = awl_notImportant(4227.756,      35, 'Ce II, Zr I');
    $ret[] = awl_notImportant(4228.312,      11, 'C I');
    $ret[] = awl_notImportant(4228.720,      24, 'Fe I');
    $ret[] = awl_notImportant(4229.408,      28, 'Fe I');
    $ret[] = awl_notImportant(4229.520,      81, 'Fe I, Ni I');
    $ret[] = awl_notImportant(4229.774,     115, 'Fe I (CH)');
    $ret[] = awl_notImportant(4231.026,      95, 'Ni I');
    $ret[] = awl_notImportant(4231.64,       70, '~CH, Zr II, Fe I');
    $ret[] = awl_notImportant(4231.954,      45, '?');
    $ret[] = awl_notImportant(4232.734,      58, 'Fe I');
    $ret[] = awl_notImportant(4232.927,      62, 'CH, V I');
    $ret[] = awl_notImportant(4235.144,      64, 'Mn I');
    $ret[] = awl_notImportant(4235.291,      91, 'Mn I');
    $ret[] = awl_notImportant(4235.736,      20, 'Y II');
    $ret[] = awl_notImportant(4238.029,     111, 'Fe I');
    $ret[] = awl_notImportant(4238.77,      155, '~Fe I');
    $ret[] = awl_notImportant(4239.367,      66, 'Fe I');
    $ret[] = awl_notImportant(4239.733,      78, 'Mn I, Fe I');
    $ret[] = awl_notImportant(4242.379,     160, '~Cr II, CH');
    $ret[] = awl_notImportant(4245.264,     118, 'Fe I');
    $ret[] = awl_notImportant(4247.432,     162, 'Fe I');
    $ret[] = awl_notImportant(4254.979,     108, 'Fe I, CH');
    $ret[] = awl_notImportant(4261.95,      107, '~Cr II, CH');
    $ret[] = awl_notImportant(4264.25,      102, 'Fe I, CH');
    $ret[] = awl_notImportant(4271.774,     756, 'Fe I');
    $ret[] = awl_notImportant(4274.806,     196, 'Cr I');
    $ret[] = awl_notImportant(4282.412,     146, 'Fe I');
    $ret[] = awl_notImportant(4283.014,     133, 'Ca I');
    $ret[] = awl_notImportant(4285.450,     120, 'Fe I');
    $ret[] = awl_notImportant(4286.015,     119, 'Ti I');
    $ret[] = awl_notImportant(4286.477,     114, 'Fe I, CH');
    $ret[] = awl_notImportant(4294.142,     217, 'Fe I, Ti II');
    $ret[] = awl_notImportant(4296.584,     105, 'Fe II, (CH)');
    $ret[] = awl_notImportant(4298.036,     111, 'Fe I');
    $ret[] = awl_notImportant(4299.249,     212, 'Fe I, Ti I, (CH)');
    $ret[] = awl_notImportant(4303.937,     119, 'CH');
    $ret[] = awl_notImportant(4305.456,     124, 'Fe I, Sr II, (Cr I, CH)');
    $ret[] = awl_notImportant(4305.918,     156, 'Ti I');
    $ret[] = awl_notImportant(4307.912,     723, 'Fe I, Ti II (CH)');
    $ret[] = awl_notImportant(4309.040,     131, 'Fe I');
    $ret[] = awl_notImportant(4309.383,     126, 'Fe I, CH');
    $ret[] = awl_notImportant(4312.875,     153, 'Ti II, CH');
    $ret[] = awl_notImportant(4315.098,     153, 'Fe I');
    $ret[] = awl_notImportant(4318.659,     116, 'Ca I, Ti I');
    $ret[] = awl_notImportant(4323.226,     101, 'CH');
    $ret[] = awl_notImportant(4323.512,     105, 'CH');
    $ret[] = awl_notImportant(4323.851,     113, 'CH');
    $ret[] = awl_notImportant(4325.775,     793, 'Fe I');
    $ret[] = awl_notImportant(4337.055,     120, 'Fe I');
    $ret[] = awl_notImportant(4343.3,       112, '~Fe I, Cr I');
    $ret[] = awl_notImportant(4351.750,     133, '~CH, Cr I, Fe II');
    $ret[] = awl_notImportant(4352.743,     142, 'Fe I');
    $ret[] = awl_notImportant(4354.615,      70, 'Se II');
    $ret[] = awl_notImportant(4355.093,     104, 'Ca I');
    $ret[] = awl_notImportant(4358.718,      75, 'Y II');
    $ret[] = awl_notImportant(4359.623,     139, 'Ni I');
    $ret[] = awl_notImportant(4367.594,     143, 'Fe I, CH');
    $ret[] = awl_notImportant(4371.286,     110, 'Cr I');
    $ret[] = awl_notImportant(4374.2,       108, '~CH, Cr I');
    $ret[] = awl_notImportant(4374.472,     110, 'Sc II, Fe I');
    $ret[] = awl_notImportant(4375.944,     152, 'Fe I');
    $ret[] = awl_notImportant(4378.255,      83, 'CH');
    $ret[] = awl_notImportant(4379.238,     110, 'V I');
    $ret[] = awl_notImportant(4384.712,     110, 'V I');
    $ret[] = awl_notImportant(4388.414,     103, 'Fe I');
    $ret[] = awl_notImportant(4401.552,     115, 'Ni I');
    $ret[] = awl_notImportant(4408.425,     130, 'Fe I');
    $ret[] = awl_notImportant(4422.53,      117, '~Fe I, V I, Y II');
    $ret[] = awl_notImportant(4425.444,     145, 'Ca I');
    $ret[] = awl_notImportant(4427.317,     147, 'Fe I');
    $ret[] = awl_notImportant(4430.622,     115, 'Fe I');
    $ret[] = awl_notImportant(4431.360,      30, 'Sc II');
    $ret[] = awl_notImportant(4435.688,     127, 'Ca I');
    $ret[] = awl_notImportant(4442.385,     171, '~Fe I, Ni I');
    $ret[] = awl_notImportant(4447.76,      177, '~Fe I');
    $ret[] = awl_notImportant(4454.793,     176, 'Ca I');
    $ret[] = awl_notImportant(4455.819,      48, 'Mn I');
    $ret[] = awl_notImportant(4455.893,     106, 'Ca I');
    $ret[] = awl_notImportant(4459.09,      190, 'Ni I, Fe I');
    $ret[] = awl_notImportant(4461.660,     116, 'Fe I');
    $ret[] = awl_notImportant(4466.562,     125, 'Fe I');
    $ret[] = awl_notImportant(4469.32,      110, '~Fe I');
    $ret[] = awl_notImportant(4476.05,      152, '~ Fe I');
    $ret[] = awl_notImportant(4494.573,     139, 'Fe I');
    $ret[] = awl_notImportant(4525.19,      120, '~Fe I');
    $ret[] = awl_notImportant(4528.627,     275, '~Fe I (Ce II, V II)');
    $ret[] = awl_notImportant(4531.158,     106, 'Fe I');
    $ret[] = awl_notImportant(4535.98,      147, '~Ti I');
    $ret[] = awl_notImportant(4552.5,       109, '~Ti I, Fe I');
    $ret[] = awl_notImportant(4581.519,     201, 'Ca I, Fe I, Co I');
    $ret[] = awl_notImportant(4585.92,      134, '~Ca I, V I');
    $ret[] = awl_notImportant(4611.194,     131, '~Fe I');
    $ret[] = awl_notImportant(4668.1,       112, '~Fe I');
    $ret[] = awl_notImportant(4668.572,      39, 'Na I');
    $ret[] = awl_notImportant(4669.176,      60, 'Fe I');
    $ret[] = awl_notImportant(4669.323,      31, 'Cr I');
    $ret[] = awl_notImportant(4670.173,      27, 'Fe II');
    $ret[] = awl_notImportant(4670.413,      55, 'Sc II');
    $ret[] = awl_notImportant(4672.837,      31, 'Fe I');
    $ret[] = awl_notImportant(4675.112,      30, 'Ti I');
    $ret[] = awl_notImportant(4678.172,      62, '?');
    $ret[] = awl_notImportant(4678.854,      97, 'Fe I');
    $ret[] = awl_notImportant(4679.230,      47, 'Fe I');
    $ret[] = awl_notImportant(4680.142,      42, 'Zn I');
    $ret[] = awl_notImportant(4680.306,      43, 'Fe I');
    $ret[] = awl_notImportant(4680.52,       58, '~Fe I, Cr I');
    $ret[] = awl_notImportant(4681.91,       64, 'Ti I');
    $ret[] = awl_notImportant(4682.121,      49, 'Fe I');
    $ret[] = awl_notImportant(4682.351,      39, 'Y II, Co I');
    $ret[] = awl_notImportant(4683.567,      46, 'Fe I');
    $ret[] = awl_notImportant(4684.601,      22, 'Cr I');
    $ret[] = awl_notImportant(4685.034,      16, 'Fe I');
    $ret[] = awl_notImportant(4685.275,      53, 'Ca I');
    $ret[] = awl_notImportant(4686.222,      56, 'Ni I');
    $ret[] = awl_notImportant(4687.34,       44, '~Fe I');
    $ret[] = awl_notImportant(4688.184,      40, 'Fe I');
    $ret[] = awl_notImportant(4688.688,      30, 'C2');
    $ret[] = awl_notImportant(4689.361,      31, 'Cr I');
    $ret[] = awl_notImportant(4690.144,      51, 'Fe I');
    $ret[] = awl_notImportant(4691.38,      104, '~Ti I, Fe I');
    $ret[] = awl_notImportant(4698.43,       65, '~Co I, Ni I, Cr I');
    $ret[] = awl_notImportant(4698.623,      45, 'Cr I');
    $ret[] = awl_notImportant(4698.771,      40, 'Ti I');
    $ret[] = awl_notImportant(4699.340,      64, '?');
    $ret[] = awl_notImportant(4700.162,      52, 'Fe I');
    $ret[] = awl_notImportant(4701.542,      46, 'Ni I');
    $ret[] = awl_notImportant(4703.003,     326, 'Mg I');
    $ret[] = awl_notImportant(4703.818,      58, 'Ni I');
    $ret[] = awl_notImportant(4704.954,      58, 'Fe I');
    $ret[] = awl_notImportant(4707.285,     107, 'Fe I');
    $ret[] = awl_notImportant(4708.019,      52, 'Cr I');
    $ret[] = awl_notImportant(4708.672,      46, 'Ti II');
    $ret[] = awl_notImportant(4708.99,      111, '~Fe I, Ti I');
    $ret[] = awl_notImportant(4709.0,       111, '~Fe I, Ti I');
    $ret[] = awl_notImportant(4709.718,      62, 'Mn I');
    $ret[] = awl_notImportant(4710.25,       98, '~Ti I, Fe I');
    $ret[] = awl_notImportant(4714.39,      132, '~Fe I, Ni I');
    $ret[] = awl_notImportant(4714.40,      132, '~Fe I, Ni I');
    $ret[] = awl_notImportant(4715.767,      68, 'Ni I');
    $ret[] = awl_notImportant(4718.423,      60, 'Cr I');
    $ret[] = awl_notImportant(4727.44,      122, '~Fe I, Mn I');
    $ret[] = awl_notImportant(4754.039,     130, 'Mn I (V I)');
    $ret[] = awl_notImportant(4762.375,     105, 'Mn I (C I)');
    $ret[] = awl_notImportant(4775.877,      20, 'C I');
    $ret[] = awl_notImportant(4783.424,     157, 'Mn I');
    $ret[] = awl_notImportant(4786.542,     110, 'Ni I, V I');
    $ret[] = awl_notImportant(4786.814,      95, 'Fe I');
    $ret[] = awl_notImportant(4789.658,      96, 'Fe I');
    $ret[] = awl_notImportant(4800.653,      72, 'Fe I');
    $ret[] = awl_notImportant(4805.05,      128, '~Ti II');
    $ret[] = awl_notImportant(4806.994,      70, 'Ni I');
    $ret[] = awl_notImportant(4817.376,       9, 'C I');
    $ret[] = awl_notImportant(4823.514,     165, 'Mn I');
    $ret[] = awl_notImportant(4859.747,     108, 'Fe I');
    $ret[] = awl_notImportant(4871.325,     228, 'Fe I');
    $ret[] = awl_notImportant(4872.144,     195, 'Fe I');
    $ret[] = awl_notImportant(4878.2,       187, '~ Ca I, Fe I');
    $ret[] = awl_notImportant(4889.05,      121, '~Fe I');
    $ret[] = awl_notImportant(4903.29,      157, '~Cr I, Fe I');
    $ret[] = awl_notImportant(4920.514,     471, '~Co I, Fe I, Nd II');
    $ret[] = awl_notImportant(4932.04,       45, 'V I, C I');
    $ret[] = awl_notImportant(4938.82,      119, 'Fe I');
    $ret[] = awl_notImportant(4946.395,     113, 'Fe I');
    $ret[] = awl_notImportant(4966.095,     114, 'Fe I');
    $ret[] = awl_notImportant(4980.23,      112, '~Ni I, Fe I');
    $ret[] = awl_notImportant(4981.74,      112, 'Ti I');
    $ret[] = awl_notImportant(4983.260,     114, 'Fe I');
    $ret[] = awl_notImportant(4983.859,     123, 'Fe I');
    $ret[] = awl_notImportant(4985.554,     103, 'Fe I');
    $ret[] = awl_notImportant(4991.072,     102, 'Ti I');
    $ret[] = awl_notImportant(4999.51,      104, 'Ti I');
    $ret[] = awl_notImportant(5001.87,      168, 'Fe I');
    $ret[] = awl_notImportant(5005.719,     136, 'Fe I');
    $ret[] = awl_notImportant(5007.25,      174, '~Ti I, Fe I');
    $ret[] = awl_notImportant(5012.11,      154, '~Fe I');
    $ret[] = awl_notImportant(5013.74,       55, '~ Ti II, C2');
    $ret[] = awl_notImportant(5017.584,      90, 'Ni I');
    $ret[] = awl_notImportant(5020.031,      86, 'Ti I (Ca II)');
    $ret[] = awl_notImportant(5022.241,     114, 'Fe I');
    $ret[] = awl_notImportant(5027.130,     105, 'Fe I');
    $ret[] = awl_notImportant(5035.37,      109, 'Ni I');
    $ret[] = awl_notImportant(5035.94,      115, '~Ti I, Ni I');
    $ret[] = awl_notImportant(5039.060,      16, 'C I');
    $ret[] = awl_notImportant(5040.122,       7, 'C I');
    $ret[] = awl_notImportant(5041.450,      37, 'C I');
    $ret[] = awl_notImportant(5041.805,     158, '~Fe I');
    $ret[] = awl_notImportant(5049.827,     135, 'Fe I');
    $ret[] = awl_notImportant(5051.642,     111, 'Fe I');
    $ret[] = awl_notImportant(5052.151,      40, 'C I');
    $ret[] = awl_notImportant(5068.771,     129, 'Fe I');
    $ret[] = awl_notImportant(5074.753,     115, 'Fe I');
    $ret[] = awl_notImportant(5098.707,     102, 'Fe I');
    $ret[] = awl_notImportant(5105.545,      82, 'Cu I');
    $ret[] = awl_notImportant(5107.651,      97, 'Fe I');
    $ret[] = awl_notImportant(5110.4,       126, '~Fe I');
    $ret[] = awl_notImportant(5123.73,      101, 'Fe I');
    $ret[] = awl_notImportant(5133.75,      165, '~Fe I, C2');
    $ret[] = awl_notImportant(5137.080,      92, 'Ni I');
    $ret[] = awl_notImportant(5137.393,     102, 'Fe I');
    $ret[] = awl_notImportant(5139.261,     137, 'Fe I');
    $ret[] = awl_notImportant(5139.473,     152, 'Fe I');
    $ret[] = awl_notImportant(5141.746,      90, 'Fe I');
    $ret[] = awl_notImportant(5142.530,     117, 'Fe I');
    $ret[] = awl_notImportant(5142.936,     111, 'Fe I');
    $ret[] = awl_notImportant(5150.89,      114, '~Fe I, Fe II');
    $ret[] = awl_notImportant(5151.917,     100, 'Fe I');
    $ret[] = awl_notImportant(5154.075,      73, 'Ti II');
    $ret[] = awl_notImportant(5155.132,      52, 'Ni I');
    $ret[] = awl_notImportant(5162.281,     154, 'Fe I');
    $ret[] = awl_notImportant(5171.61,      160, 'Fe I');
    $ret[] = awl_notImportant(5188.698,     202, 'Ti II, Ca I');
    $ret[] = awl_notImportant(5192.353,     176, 'Fe I');
    $ret[] = awl_notImportant(5195.48,      114, 'Fe I');
    $ret[] = awl_notImportant(5200.415,      37, 'Y II');
    $ret[] = awl_notImportant(5204.56,      212, '~Fe I, Cr I');
    $ret[] = awl_notImportant(5206.1,       216, '~Cr I');
    $ret[] = awl_notImportant(5208.432,     247, 'Cr I');
    $ret[] = awl_notImportant(5215.188,     116, 'Fe I');
    $ret[] = awl_notImportant(5216.283,     108, 'Fe I');
    $ret[] = awl_notImportant(5217.396,     102, 'Fe I');
    $ret[] = awl_notImportant(5226.545,      94, 'Ti II');
    $ret[] = awl_notImportant(5227.192,     277, 'Fe I');
    $ret[] = awl_notImportant(5229.86,      124, 'Fe I');
    $ret[] = awl_notImportant(5232.952,     346, 'Fe I');
    $ret[] = awl_notImportant(5250.216,      62, 'Fe I (magnetic)');
    $ret[] = awl_notImportant(5263.314,     121, 'Fe I');
    $ret[] = awl_notImportant(5264.2,       153, '~Cr I, Ca I');
    $ret[] = awl_notImportant(5264.808,      45, 'Fe II');
    $ret[] = awl_notImportant(5265.560,     112, 'Ca I');
    $ret[] = awl_notImportant(5266.51,      252, '~Ti I, Co I, Fe I');
    $ret[] = awl_notImportant(5269.55,      478, 'Fe I');
    $ret[] = awl_notImportant(5269.550,     478, 'Fe I');
    $ret[] = awl_notImportant(5270.3,       255, '~Ca I, Fe I');
    $ret[] = awl_notImportant(5273.170,     103, 'Fe I');
    $ret[] = awl_notImportant(5273.389,     104, 'Fe I');
    $ret[] = awl_notImportant(5276.071,     152, '~ Fe II, Cr I, Co I');
    $ret[] = awl_notImportant(5281.7,       164, '~Ni I, Fe I');
    $ret[] = awl_notImportant(5283.5,       212, '~Ti I, Fe I');
    $ret[] = awl_notImportant(5298.283,     110, 'Cr I');
    $ret[] = awl_notImportant(5300.751,      56, 'Cr I');
    $ret[] = awl_notImportant(5302.307,     157, 'Fe I');
    $ret[] = awl_notImportant(5324.15,      334, 'Fe I, Cr I (?)');
    $ret[] = awl_notImportant(5328.051,     375, 'Fe I');
    $ret[] = awl_notImportant(5328.332,      74, 'Cr I');
    $ret[] = awl_notImportant(5328.542,     210, 'Fe I');
    $ret[] = awl_notImportant(5329.147,      78, 'Cr I');
    $ret[] = awl_notImportant(5332.665,      45, 'V II');
    $ret[] = awl_notImportant(5332.908,      96, 'Fe I');
    $ret[] = awl_notImportant(5334.870,      32, 'Cr II');
    $ret[] = awl_notImportant(5336.794,      71, 'Ti II');
    $ret[] = awl_notImportant(5337.735,      35, '~Fe II, Cr II');
    $ret[] = awl_notImportant(5339.937,     161, 'Fe I');
    $ret[] = awl_notImportant(5341.1,       180, '~Fe I, Mn I, Sc I');
    $ret[] = awl_notImportant(5345.807,     107, 'Cr I');
    $ret[] = awl_notImportant(5362.8,       110, '~ Fe I, Co I, Fe II');
    $ret[] = awl_notImportant(5364.880,     133, 'Fe I');
    $ret[] = awl_notImportant(5367.476,     157, 'Fe I');
    $ret[] = awl_notImportant(5371.42,      294, '~Ni I, Fe I');
    $ret[] = awl_notImportant(5380.322,      26, 'C I');
    $ret[] = awl_notImportant(5383.380,     240, 'Fe I');
    $ret[] = awl_notImportant(5393.176,     153, 'Fe I');
    $ret[] = awl_notImportant(5397.141,     239, 'Fe I');
    $ret[] = awl_notImportant(5400.511,     143, 'Fe I');
    $ret[] = awl_notImportant(5404.145,     239, 'Fe I');
    $ret[] = awl_notImportant(5405.785,     266, 'Fe I');
    $ret[] = awl_notImportant(5409.799,     154, 'Cr I');
    $ret[] = awl_notImportant(5410.918,     169, 'Fe I');
    $ret[] = awl_notImportant(5415.210,     212, 'Fe I');
    $ret[] = awl_notImportant(5424.080,     239, 'Fe I');
    $ret[] = awl_notImportant(5429.77,      285, '~Fe I');
    $ret[] = awl_notImportant(5432.548,      46, 'Mn I');
    $ret[] = awl_notImportant(5432.955,      72, 'Fe I');
    $ret[] = awl_notImportant(5434.534,     184, 'Fe I');
    $ret[] = awl_notImportant(5445.053,     121, 'Fe I');
    $ret[] = awl_notImportant(5446.924,     238, 'Fe I');
    $ret[] = awl_notImportant(5455.465,     112, 'Fe I');
    $ret[] = awl_notImportant(5455.624,     219, 'Fe I');
    $ret[] = awl_notImportant(5463.289,     118, 'Fe I');
    $ret[] = awl_notImportant(5470.636,      46, 'Mn I');
    $ret[] = awl_notImportant(5473.910,      80, 'Fe I');
    $ret[] = awl_notImportant(5476.25,       86, '~Fe I');
    $ret[] = awl_notImportant(5476.576,     104, 'Fe I');
    $ret[] = awl_notImportant(5476.921,     164, 'Ni I');
    $ret[] = awl_notImportant(5497.4,       128, '~Fe I (Y II)');
    $ret[] = awl_notImportant(5501.477,     115, 'Fe I');
    $ret[] = awl_notImportant(5506.791,     120, 'Fe I');
    $ret[] = awl_notImportant(5512.989,      94, 'Ca I');
    $ret[] = awl_notImportant(5525.135,      13, 'Fe II');
    $ret[] = awl_notImportant(5525.552,     102, 'Fe I');
    $ret[] = awl_notImportant(5528.418,     293, 'Mg I');
    $ret[] = awl_notImportant(5534.848,      63, 'Fe II');
    $ret[] = awl_notImportant(5535.51,      113, '~ Fe I, Ba I');
    $ret[] = awl_notImportant(5554.900,     102, 'Fe I');
    $ret[] = awl_notImportant(5569.631,     162, 'Fe I');
    $ret[] = awl_notImportant(5572.851,     205, 'Fe I');
    $ret[] = awl_notImportant(5576.099,     113, 'Fe I');
    $ret[] = awl_notImportant(5577.341,       6, 'C2, O I');
    $ret[] = awl_notImportant(5586.771,     245, 'Fe I');
    $ret[] = awl_notImportant(5588.764,     141, 'Ca I');
    $ret[] = awl_notImportant(5594.471,     117, 'Ca I');
    $ret[] = awl_notImportant(5598.3,       200, '~Ca I, Fe I');
    $ret[] = awl_notImportant(5602.864,     215, 'Ca I, Fe I');
    $ret[] = awl_notImportant(5615.658,     288, 'Fe I');
    $ret[] = awl_notImportant(5624.558,     140, 'Fe I, V I');
    $ret[] = awl_notImportant(5658.668,     222, 'Fe I');
    $ret[] = awl_notImportant(5669.040,      34, 'Sc II');
    $ret[] = awl_notImportant(5682.647,     104, 'Na I');
    $ret[] = awl_notImportant(5688.217,     121, 'Na I');
    $ret[] = awl_notImportant(5700.24,       29, '~Sc I, Cu I');
    $ret[] = awl_notImportant(5701.108,      40, 'Si I');
    $ret[] = awl_notImportant(5701.557,      86, 'Fe I');
    $ret[] = awl_notImportant(5702.328,      27, 'Cr I');
    $ret[] = awl_notImportant(5703.587,      26, 'V I');
    $ret[] = awl_notImportant(5706.008,      74, 'Fe I');
    $ret[] = awl_notImportant(5707.01,       43, '~V I, Fe I');
    $ret[] = awl_notImportant(5708.102,      37, 'Fe I');
    $ret[] = awl_notImportant(5708.405,      77, 'Si I');
    $ret[] = awl_notImportant(5709.386,     103, 'Fe I');
    $ret[] = awl_notImportant(5709.555,      90, 'Ni I');
    $ret[] = awl_notImportant(5711.09,      107, 'Mg I');
    $ret[] = awl_notImportant(5712.138,      54, 'Fe I');
    $ret[] = awl_notImportant(5715.094,      73, 'Ni I');
    $ret[] = awl_notImportant(5727.057,      37, 'V I');
    $ret[] = awl_notImportant(5731.772,      59, 'Fe I');
    $ret[] = awl_notImportant(5754.666,      73, 'Ni I');
    $ret[] = awl_notImportant(5763.002,     101, 'Fe I');
    $ret[] = awl_notImportant(5772.149,      47, 'Si I');
    $ret[] = awl_notImportant(5775.088,      48, 'Fe I');
    $ret[] = awl_notImportant(5780.388,      22, 'Si I');
    $ret[] = awl_notImportant(5780.608,      29, 'Fe I');
    $ret[] = awl_notImportant(5780.812,      29, 'Ti I, Fe I');
    $ret[] = awl_notImportant(5781.759,      16, 'Cr I (magnetic)');
    $ret[] = awl_notImportant(5782.136,      62, 'Cu I');
    $ret[] = awl_notImportant(5783.866,      34, 'Cr I');
    $ret[] = awl_notImportant(5785.285,      40, 'Mg I, Fe I');
    $ret[] = awl_notImportant(5790.990,      74, 'Cr I, Fe I');
    $ret[] = awl_notImportant(5793.079,      38, 'Si I');
    $ret[] = awl_notImportant(5806.732,      51, 'Fe I');
    $ret[] = awl_notImportant(5809.224,      50, 'Fe I');
    $ret[] = awl_notImportant(5816.380,      85, 'Atm O2, Fe I');
    $ret[] = awl_notImportant(5831.606,      22, 'Ni I');
    $ret[] = awl_notImportant(5848.07,       38, '~Fe I');
    $ret[] = awl_notImportant(5852.228,      36, 'Fe I');
    $ret[] = awl_notImportant(5857.459,     132, 'Ca I');
    $ret[] = awl_notImportant(5857.758,      56, 'Ni I');
    $ret[] = awl_notImportant(5862.368,      87, 'Fe I');
    $ret[] = awl_notImportant(5877.797,      16, 'Fe I, Ti I');
    $ret[] = awl_notImportant(5881.279,      16, 'Fe I');
    $ret[] = awl_notImportant(5883.814,      95, 'Fe I');
    $ret[] = awl_notImportant(5892.883,      66, 'Ni I');
    $ret[] = awl_notImportant(5899.304,      26, 'Ti I');
    $ret[] = awl_notImportant(5905.680,      58, 'Fe I');
    $ret[] = awl_notImportant(5909.983,      30, 'Fe I');
    $ret[] = awl_notImportant(5914.17,      139, '~Fe I');
    $ret[] = awl_notImportant(5930.191,      86, 'Fe I');
    $ret[] = awl_notImportant(5934.665,      78, 'Fe I');
    $ret[] = awl_notImportant(5948.548,      88, 'Si I');
    $ret[] = awl_notImportant(5983.688,      68, 'Fe I');
    $ret[] = awl_notImportant(5984.826,      84, 'Fe I');
    $ret[] = awl_notImportant(5997.782,      67, 'Fe I');
    $ret[] = awl_notImportant(6003.022,      86, 'Fe I');
    $ret[] = awl_notImportant(6013.497,      86, 'Mn I');
    $ret[] = awl_notImportant(6016.78,       92, '~Mn I, Fe I');
    $ret[] = awl_notImportant(6020.186,      94, 'Fe I');
    $ret[] = awl_notImportant(6021.803,      96, 'Mn I');
    $ret[] = awl_notImportant(6024.0,       117, '~Fe I');
    $ret[] = awl_notImportant(6027.0,        61, 'Fe I');
    $ret[] = awl_notImportant(6042.104,      51, 'Fe I');
    $ret[] = awl_notImportant(6056.013,      73, 'Fe I');
    $ret[] = awl_notImportant(6065.494,     115, 'Fe I');
    $ret[] = awl_notImportant(6078.499,      91, 'Fe I');
    $ret[] = awl_notImportant(6079.016,      55, 'Fe I');
    $ret[] = awl_notImportant(6096.671,      36, 'Fe I');
    $ret[] = awl_notImportant(6102.183,      84, 'Fe I');
    $ret[] = awl_notImportant(6102.727,     135, 'Ca I');
    $ret[] = awl_notImportant(6103.190,      89, '~Fe I');
    $ret[] = awl_notImportant(6108.125,      60, 'Ni I');
    $ret[] = awl_notImportant(6111.078,      36, 'Ni I');
    $ret[] = awl_notImportant(6113.329,      17, 'Fe II');
    $ret[] = awl_notImportant(6116.22,       65, '~Ni I, Fe I');
    $ret[] = awl_notImportant(6136.624,    1637, 'Fe I');
    $ret[] = awl_notImportant(6137.702,     129, 'Fe I');
    $ret[] = awl_notImportant(6141.727,     113, 'Ba II, Fe I');
    $ret[] = awl_notImportant(6147.79,       76, 'Fe II, Fe I');
    $ret[] = awl_notImportant(6151.623,      41, 'Fe I');
    $ret[] = awl_notImportant(6155.17,       72, '~Si I, Fe II');
    $ret[] = awl_notImportant(6156.80,        5, 'O I');
    $ret[] = awl_notImportant(6158.171,       5, 'O I');
    $ret[] = awl_notImportant(6163.754,      49, 'Ca I');
    $ret[] = awl_notImportant(6165.363,      33, 'Fe I');
    $ret[] = awl_notImportant(6166.440,      54, 'Ca I');
    $ret[] = awl_notImportant(6169.044,      85, 'Ca I');
    $ret[] = awl_notImportant(6169.564,      98, 'Ca I');
    $ret[] = awl_notImportant(6170.516,      66, 'Fe I (Ni I)');
    $ret[] = awl_notImportant(6173.341,      50, 'Fe I');
    $ret[] = awl_notImportant(6175.370,      36, 'Ni I');
    $ret[] = awl_notImportant(6176.816,      50, 'Ni I');
    $ret[] = awl_notImportant(6180.209,      40, 'Fe I');
    $ret[] = awl_notImportant(6191.186,      56, 'Ni I');
    $ret[] = awl_notImportant(6191.571,     110, 'Fe I');
    $ret[] = awl_notImportant(6200.321,      55, 'Fe I');
    $ret[] = awl_notImportant(6213.437,      61, 'Fe I');
    $ret[] = awl_notImportant(6219.287,      82, 'Fe I');
    $ret[] = awl_notImportant(6229.232,      33, 'Fe I');
    $ret[] = awl_notImportant(6230.736,     151, 'Fe I, V I');
    $ret[] = awl_notImportant(6232.648,      76, 'Fe I');
    $ret[] = awl_notImportant(6237.328,      60, 'Si I');
    $ret[] = awl_notImportant(6238.390,      41, 'Fe II (Si I)');
    $ret[] = awl_notImportant(6240.653,      40, 'Fe I');
    $ret[] = awl_notImportant(6243.823,      43, 'Si I');
    $ret[] = awl_notImportant(6245.620,      30, 'Sc II');
    $ret[] = awl_notImportant(6246.327,     112, 'Fe I');
    $ret[] = awl_notImportant(6247.562,      49, 'Fe II');
    $ret[] = awl_notImportant(6252.565,     109, 'Fe I');
    $ret[] = awl_notImportant(6254.21,      115, '~Si I, Fe I');
    $ret[] = awl_notImportant(6256.367,      81, 'Fe I, Ni I');
    $ret[] = awl_notImportant(6258.110,      42, 'Ti I');
    $ret[] = awl_notImportant(6258.713,      43, 'Ti I');
    $ret[] = awl_notImportant(6261.106,      40, 'Ti I');
    $ret[] = awl_notImportant(6265.141,      72, 'Fe I');
    $ret[] = awl_notImportant(6270.231,      46, 'Fe I');
    $ret[] = awl_notImportant(6290.974,      66, 'Fe I');
    $ret[] = awl_notImportant(6297.799,      65, 'Fe I');
    $ret[] = awl_notImportant(6300.311,       5, 'O I');
    $ret[] = awl_notImportant(6301.508,     127, 'Fe I');
    $ret[] = awl_notImportant(6302.499,      83, 'Fe I');
    $ret[] = awl_notImportant(6302.499,      83, 'Fe I (magnetic)');
    $ret[] = awl_notImportant(6314.668,      67, 'Ni I');
    $ret[] = awl_notImportant(6315.314,      52, 'Fe I');
    $ret[] = awl_notImportant(6318.027,      96, 'Fe I');
    $ret[] = awl_notImportant(6318.61,       49, 'Ca I');
    $ret[] = awl_notImportant(6318.708,      37, 'Mg I');
    $ret[] = awl_notImportant(6322.694,      75, 'Fe I');
    $ret[] = awl_notImportant(6327.604,      36, 'Ni I');
    $ret[] = awl_notImportant(6335.337,     103, 'Fe I');
    $ret[] = awl_notImportant(6336.830,     121, 'Fe I');
    $ret[] = awl_notImportant(6338.880,      42, 'Fe I');
    $ret[] = awl_notImportant(6339.118,      44, 'Ni I');
    $ret[] = awl_notImportant(6343.71,       70, 'Ca I');
    $ret[] = awl_notImportant(6344.155,      56, 'Fe I');
    $ret[] = awl_notImportant(6355.035,      62, 'Fe I');
    $ret[] = awl_notImportant(6358.687,      82, 'Fe I');
    $ret[] = awl_notImportant(6361.94,       89, 'Ca I');
    $ret[] = awl_notImportant(6362.350,      23, 'Zn I');
    $ret[] = awl_notImportant(6363.79,        3, 'O I');
    $ret[] = awl_notImportant(6371.3,        35, 'Si II');
    $ret[] = awl_notImportant(6380.750,      40, 'Fe I');
    $ret[] = awl_notImportant(6393.612,     117, 'Fe I');
    $ret[] = awl_notImportant(6400.009,     181, 'Fe I');
    $ret[] = awl_notImportant(6408.026,      80, 'Fe I');
    $ret[] = awl_notImportant(6409.799,     154, 'Cr I');
    $ret[] = awl_notImportant(6411.658,     129, 'Fe I');
    $ret[] = awl_notImportant(6414.987,      45, 'Si I');
    $ret[] = awl_notImportant(6416.928,    47.5, 'Fe II');
    $ret[] = awl_notImportant(6419.956,      80, 'Fe I');
    $ret[] = awl_notImportant(6421.360,      87, 'Fe I');
    $ret[] = awl_notImportant(6430.856,     106, 'Fe I');
    $ret[] = awl_notImportant(6432.683,      38, 'Fe II');
    $ret[] = awl_notImportant(6439.083,     156, 'Ca I');
    $ret[] = awl_notImportant(6449.820,      98, 'Ca I');
    $ret[] = awl_notImportant(6455.605,      48, 'Ca I');
    $ret[] = awl_notImportant(6456.391,      57, 'Fe II');
    $ret[] = awl_notImportant(6462.6,       216, '~ Ca I, Fe I');
    $ret[] = awl_notImportant(6469.192,      52, 'Fe I');
    $ret[] = awl_notImportant(6471.668,      83, 'Ca I');
    $ret[] = awl_notImportant(6475.632,      57, 'Fe I');
    $ret[] = awl_notImportant(6481.878,      63, 'Fe I');
    $ret[] = awl_notImportant(6482.809,      38, 'Ni I');
    $ret[] = awl_notImportant(6491.6,        45, '~Ti II, Mn I');
    $ret[] = awl_notImportant(6493.788,     133, 'Ca I');
    $ret[] = awl_notImportant(6494.994,     165, 'Fe I');
    $ret[] = awl_notImportant(6496.472,      69, 'Fe I');
    $ret[] = awl_notImportant(6498.945,      43, 'Fe I');
    $ret[] = awl_notImportant(6499.654,      81, 'Ca I');
    $ret[] = awl_notImportant(6516.083,      61, 'Fe II');
    $ret[] = awl_notImportant(6518.373,      61, 'Fe I');
    $ret[] = awl_notImportant(6527.215,      53, 'Si I');
    $ret[] = awl_notImportant(6546.252,     103, 'Fe I, Ti I');
    $ret[] = awl_notImportant(6569.224,      71, 'Fe I');
    $ret[] = awl_notImportant(6572.795,      26, 'Ca I');
    $ret[] = awl_notImportant(6574.254,      22, 'Fe I');
    $ret[] = awl_notImportant(6575.037,      64, 'Fe I');
    $ret[] = awl_notImportant(6586.319,      35, 'Ni I');
    $ret[] = awl_notImportant(6592.522,      23, 'Ni I');
    $ret[] = awl_notImportant(6592.926,     123, 'Fe I');
    $ret[] = awl_notImportant(6593.884,      89, 'Fe I');
    $ret[] = awl_notImportant(6597.571,      44, 'Fe I (Cr I)');
    $ret[] = awl_notImportant(6598.611,      26, 'Ni I');
    $ret[] = awl_notImportant(6604.600,      36, 'Sc II');
    $ret[] = awl_notImportant(6609.118,      76, 'Fe I');
    $ret[] = awl_notImportant(6627.560,      24, 'Fe I');
    $ret[] = awl_notImportant(6633.427,      30, 'Fe I');
    $ret[] = awl_notImportant(6633.758,      70, 'Fe I');
    $ret[] = awl_notImportant(6634.123,      40, 'Fe I');
    $ret[] = awl_notImportant(6635.137,      19, 'Ni I');
    $ret[] = awl_notImportant(6643.638,      83, 'Ni I');
    $ret[] = awl_notImportant(6663.246,      31, 'Fe I');
    $ret[] = awl_notImportant(6663.448,      76, 'Fe I');
    $ret[] = awl_notImportant(6677.997,     122, 'Fe I');
    $ret[] = awl_notImportant(6696.032,      33, 'Al I');
    $ret[] = awl_notImportant(6698.669,      21, 'Al I');
    $ret[] = awl_notImportant(6703.576,      32, 'Fe I');
    $ret[] = awl_notImportant(6705.105,      42, 'Fe I');
    $ret[] = awl_notImportant(6715.386,      33, 'Fe I (Cr I)');
    $ret[] = awl_notImportant(6717.687,     120, 'Ca I');
    $ret[] = awl_notImportant(6719.62,       21, '?');
    $ret[] = awl_notImportant(6726.282,       1, 'O I');
    $ret[] = awl_notImportant(6726.673,      50, 'Fe I');
    $ret[] = awl_notImportant(6750.164,      75, 'Fe I');
    $ret[] = awl_notImportant(6757.195,      19, 'S I');
    $ret[] = awl_notImportant(6767.784,      83, 'Ni I');
    $ret[] = awl_notImportant(6772.321,      51, 'Ni I');
    $ret[] = awl_notImportant(6784.214,      11, '?');
    $ret[] = awl_notImportant(6786.860,      16, 'Fe I');
    $ret[] = awl_notImportant(6806.856,      24, 'Fe I');
    $ret[] = awl_notImportant(6810.267,      42, 'Fe I');
    $ret[] = awl_notImportant(6814.961,      12, 'Co I');
    $ret[] = awl_notImportant(6819.595,       5, 'Fe I');
    $ret[] = awl_notImportant(6820.374,      37, 'Fe I');
    $ret[] = awl_notImportant(6828.596,      56, 'Fe I');
    $ret[] = awl_notImportant(6837.013,      15, 'Fe I');
    $ret[] = awl_notImportant(6838.85,       31, 'Fe I');
    $ret[] = awl_notImportant(6839.835,      30, 'Fe I');
    $ret[] = awl_notImportant(6841.19,        8, 'Mg I');
    $ret[] = awl_notImportant(6841.341,      65, 'Fe I');
    $ret[] = awl_notImportant(6842.043,      26, 'Ni I');
    $ret[] = awl_notImportant(6842.689,      41, 'Fe I');
    $ret[] = awl_notImportant(6843.655,      59, 'Fe I');
    $ret[] = awl_notImportant(6848.566,      15, 'Si I');
    $ret[] = awl_notImportant(6855.166,      85, 'Fe I');
    $ret[] = awl_notImportant(6855.723,      23, 'Fe I');
    $ret[] = awl_notImportant(6857.251,      27, 'Fe I');
    $ret[] = awl_notImportant(6858.155,      57, 'Fe I');
    $ret[] = awl_notImportant(6861.50,       12, 'Ti I');
    $ret[] = awl_notImportant(6861.945,      22, 'Fe I');
    $ret[] = awl_notImportant(6862.496,      39, 'Fe I');
    $ret[] = awl_notImportant(6875.995,      12, 'Fe I');
    $ret[] = awl_notImportant(6880.637,      14, 'Fe I');
    $ret[] = awl_notImportant(6881.716,      28, 'Cr I');
    $ret[] = awl_notImportant(6882.502,      34, 'Cr I');
    $ret[] = awl_notImportant(6883.070,      31, 'Cr I');
    $ret[] = awl_notImportant(6885.754,     175, 'Atm O2, Fe I');
    $ret[] = awl_notImportant(6898.307,      16, 'Fe I');
    $ret[] = awl_notImportant(6902.874,      22, 'Fe I');
    $ret[] = awl_notImportant(6905.317,      14, '?');
    $ret[] = awl_notImportant(6911.522,      14, 'Fe I');
    $ret[] = awl_notImportant(6914.564,      83, 'Ni I');
    $ret[] = awl_notImportant(6916.686,      60, 'Fe I');
    $ret[] = awl_notImportant(6918.592,       7, '?');
    $ret[] = awl_notImportant(6925.280,      45, 'Cr I');
    $ret[] = awl_notImportant(6926.097,      21, 'Cr I');
    $ret[] = awl_notImportant(6930.605,      17, 'Fe I');
    $ret[] = awl_notImportant(6933.026,      16, 'Fe I');
    $ret[] = awl_notImportant(6933.605,      54, 'Atm H2O, Fe I');
    $ret[] = awl_notImportant(6936.496,       6, 'Fe I');
    $ret[] = awl_notImportant(6938.737,       3, 'K I');
    $ret[] = awl_notImportant(6945.210,      82, 'Fe I');
    $ret[] = awl_notImportant(6947.55,       88, 'Fe I, H2O');
    $ret[] = awl_notImportant(6955.040,      13, 'Ni I');
    $ret[] = awl_notImportant(6960.330,      13, 'Fe I');
    $ret[] = awl_notImportant(6965.408,      25, 'Mg I');
    $ret[] = awl_notImportant(6971.917,      17, 'Fe I');
    $ret[] = awl_notImportant(6976.504,      47, 'Si I');
    $ret[] = awl_notImportant(6978.383,      68, 'Cr I');
    $ret[] = awl_notImportant(6978.862,      90, 'Fe I');
    $ret[] = awl_notImportant(6979.806,      41, 'Cr I');
    $ret[] = awl_notImportant(6988.533,      36, 'Fe I');
    $ret[] = awl_notImportant(6999.885,      71, 'Fe I');
    $ret[] = awl_notImportant(7000.623,      23, 'Fe I');
    $ret[] = awl_notImportant(7001.551,      11, 'Ni I');
    $ret[] = awl_notImportant(7002.128,      18, 'Atm H2O, O I');
    $ret[] = awl_notImportant(7003.574,      81, 'Si I');
    $ret[] = awl_notImportant(7005.900,      89, 'Si I');
    $ret[] = awl_notImportant(7007.976,      31, 'Fe I');
    $ret[] = awl_notImportant(7012.612,      47, '?');
    $ret[] = awl_notImportant(7014.996,      13, 'Fe I');
    $ret[] = awl_notImportant(7016.067,      62, 'Fe I');
    $ret[] = awl_notImportant(7016.442,     146, 'Fe I');
    $ret[] = awl_notImportant(7016.68,       38, '~Co I, Si I');
    $ret[] = awl_notImportant(7017.666,      51, 'Si I');
    $ret[] = awl_notImportant(7022.957,      72, 'Fe I');
    $ret[] = awl_notImportant(7024.065,      31, 'Fe I');
    $ret[] = awl_notImportant(7024.644,      51, 'Fe I');
    $ret[] = awl_notImportant(7024.86,       34, 'Ni I, Atm H2O');
    $ret[] = awl_notImportant(7030.021,      23, 'Ni I');
    $ret[] = awl_notImportant(7032.319,      33, '?');
    $ret[] = awl_notImportant(7034.910,      80, 'Si I');
    $ret[] = awl_notImportant(7038.220,      76, 'Fe I');
    $ret[] = awl_notImportant(7038.765,      40, 'Fe I, Ti I');
    $ret[] = awl_notImportant(7044.65,       14, 'Fe I');
    $ret[] = awl_notImportant(7052.87,       13, 'Co I');
    $ret[] = awl_notImportant(7055.927,      20, '?');
    $ret[] = awl_notImportant(7060.446,      47, 'Atm H2O, Mg I');
    $ret[] = awl_notImportant(7062.978,      13, 'Ni I');
    $ret[] = awl_notImportant(7067.460,      13, 'Fe II');
    $ret[] = awl_notImportant(7068.423,      64, 'Fe I');
    $ret[] = awl_notImportant(7071.866,      35, 'Fe I');
    $ret[] = awl_notImportant(7083.394,      27, 'Fe I');
    $ret[] = awl_notImportant(7083.960,      21, 'Al I, Si I');
    $ret[] = awl_notImportant(7084.975,      61, 'Co I (atm H2O)');
    $ret[] = awl_notImportant(7090.390,      73, 'Fe I');
    $ret[] = awl_notImportant(7095.407,      32, 'Ni I, Fe I');
    $ret[] = awl_notImportant(7107.468,      24, 'Fe I');
    $ret[] = awl_notImportant(7110.905,      41, 'Ni I');
    $ret[] = awl_notImportant(7111.450,      23, 'C I');
    $ret[] = awl_notImportant(7112.170,      33, 'Fe I');
    $ret[] = awl_notImportant(7113.171,      30, 'C I');
    $ret[] = awl_notImportant(7115.17,       33, 'C I');
    $ret[] = awl_notImportant(7116.963,      21, 'C I');
    $ret[] = awl_notImportant(7122.206,     107, 'Ni I');
    $ret[] = awl_notImportant(7127.573,      29, 'Fe I');
    $ret[] = awl_notImportant(7130.925,     105, 'Fe I');
    $ret[] = awl_notImportant(7132.985,      44, 'Fe I');
    $ret[] = awl_notImportant(7142.517,      44, 'Fe I');
    $ret[] = awl_notImportant(7145.312,      42, 'Fe I');
    $ret[] = awl_notImportant(7148.150,     157, 'Ca I');
    $ret[] = awl_notImportant(7151.464,      24, 'Fe I');
    $ret[] = awl_notImportant(7155.634,      45, 'Fe I');
    $ret[] = awl_notImportant(7157.73,        7, '?');
    $ret[] = awl_notImportant(7158.508,      14, 'Fe I');
    $ret[] = awl_notImportant(7158.776,      20,  '?, Atm H2O');
    $ret[] = awl_notImportant(7164.432,     153, 'Fe I');
    $ret[] = awl_notImportant(7165.578,      93, 'Si I');
    $ret[] = awl_notImportant(7180.004,      19, 'Fe I');
    $ret[] = awl_notImportant(7181.198,      71, 'Fe I');
    $ret[] = awl_notImportant(7181.955,      81, 'Ni I, Fe I');
    $ret[] = awl_notImportant(7187.388,     240, 'Fe I (atm H2O)');
    $ret[] = awl_notImportant(7193.183,      60, 'Mg I');
    $ret[] = awl_notImportant(7202.208,     124, 'Ca I');
    $ret[] = awl_notImportant(7207.396,     150, 'Fe I');
    $ret[] = awl_notImportant(7219.680,      53, 'Fe I');
    $ret[] = awl_notImportant(7221.204,      49, 'Fe I');
    $ret[] = awl_notImportant(7222.397,      27, 'Fe II');
    $ret[] = awl_notImportant(7226.208,      46, 'Si I');
    $ret[] = awl_notImportant(7228.700,      29, 'Fe I');
    $ret[] = awl_notImportant(7275.36,       94, 'Si I (atm H2O)');
    $ret[] = awl_notImportant(7282.844,      68, 'Si I');
    $ret[] = awl_notImportant(7289.188,     116, 'Si I');
    $ret[] = awl_notImportant(7311.080,      67, 'Fe I');
    $ret[] = awl_notImportant(7320.689,      72, 'Fe I, Fe II');
    $ret[] = awl_notImportant(7326.160,     136, 'Ca I');
    $ret[] = awl_notImportant(7333.58,       36, 'Fe I');
    $ret[] = awl_notImportant(7343.226,      20, '?');
    $ret[] = awl_notImportant(7344.759,      40, 'Ti I');
    $ret[] = awl_notImportant(7355.891,      79, 'Cr I');
    $ret[] = awl_notImportant(7357.739,      26, 'Ti I');
    $ret[] = awl_notImportant(7362.291,      43, 'Al I');
    $ret[] = awl_notImportant(7386.336,      94, 'Fe I');
    $ret[] = awl_notImportant(7387.700,     118, 'Mg I');
    $ret[] = awl_notImportant(7389.391,     144, 'Fe I');
    $ret[] = awl_notImportant(7393.609,     112, 'Ni I');
    $ret[] = awl_notImportant(7400.188,      89, 'Cr I');
    $ret[] = awl_notImportant(7405.790,     108, 'Si I');
    $ret[] = awl_notImportant(7409.100,      72, 'Si I');
    $ret[] = awl_notImportant(7409.352,      98, 'Ni I');
    $ret[] = awl_notImportant(7411.162,     140, 'Fe I');
    $ret[] = awl_notImportant(7415.958,     118, 'Si I');
    $ret[] = awl_notImportant(7422.286,     106, 'Ni I');
    $ret[] = awl_notImportant(7423.509,     120, 'Si I (N I)');
    $ret[] = awl_notImportant(7424.647,      22, 'Si I (atm H2O)');
    $ret[] = awl_notImportant(7430.846,      32, 'Fe I, Si I');    
    $ret[] = awl_notImportant(7435.584,      32, '?');
    $ret[] = awl_notImportant(7440.919,      68, 'Fe I');
    $ret[] = awl_notImportant(7443.026,      36, 'Fe I');
    $ret[] = awl_notImportant(7445.758,     178, 'Fe I');
    $ret[] = awl_notImportant(7462.342,     119, 'Cr I (Fe II)');
    $ret[] = awl_notImportant(7495.077,     174, 'Fe I');
    $ret[] = awl_notImportant(7511.031,     221, 'Fe I');
    $ret[] = awl_notImportant(7531.153,     101, 'Fe I');
    $ret[] = awl_notImportant(7555.607,      98, 'Ni I');
    $ret[] = awl_notImportant(7568.906,      90, 'Fe I');
    $ret[] = awl_notImportant(7586.027,     132, 'Fe I');
    $ret[] = awl_notImportant(7605.635,       1, 'Atm O2 (Fe I)');
    $ret[] = awl_notImportant(7614.516,       8, 'Ti I');
    $ret[] = awl_notImportant(7616.980,     120, 'Ni I');
    $ret[] = awl_notImportant(7617.245,      12, 'Fe I');
    $ret[] = awl_notImportant(7619.214,      69, 'Ni I');
    $ret[] = awl_notImportant(7620.513,      72, 'Fe I');
    $ret[] = awl_notImportant(7624.500,       1, 'Atm O2 (Ni I)');
    $ret[] = awl_notImportant(7647.8,         9, 'Fe I');
    $ret[] = awl_notImportant(7650.975,      54, '~Atm O2, Fe I');
    $ret[] = awl_notImportant(7653.757,      49, 'Fe I');
    $ret[] = awl_notImportant(7655.48,       15, 'Fe II');
    $ret[] = awl_notImportant(7657.606,     142, 'Mg I');
    $ret[] = awl_notImportant(7659.91,       31, 'Mg I');
    $ret[] = awl_notImportant(7661.198,      79, 'Fe I');
    $ret[] = awl_notImportant(7662.42,        8, 'C I');
    $ret[] = awl_notImportant(7664.294,     120, 'Fe I');
    $ret[] = awl_notImportant(7664.872,     521, 'K I, atm O2');
    $ret[] = awl_notImportant(7669.668,      63, 'Atm O2, Si I');
    $ret[] = awl_notImportant(7680.267,     106, 'Si I (Mn I)');
    $ret[] = awl_notImportant(7691.52,      172, '~atm O2, Mg I');    
    $ret[] = awl_notImportant(7698.977,     154, 'K I');
    $ret[] = awl_notImportant(7710.367,      70, 'Fe I');
    $ret[] = awl_notImportant(7711.731,      48, 'Fe II');
    $ret[] = awl_notImportant(7714.310,     103, 'Ni I');
    $ret[] = awl_notImportant(7715.591,      48, 'Ni I');
    $ret[] = awl_notImportant(7719.046,      27, 'Fe I');
    $ret[] = awl_notImportant(7722.64,       16, 'Mg I');
    $ret[] = awl_notImportant(7723.210,      41, 'Fe I');
    $ret[] = awl_notImportant(7727.616,      94, 'Ni I');
    $ret[] = awl_notImportant(7742.722,     126, 'Fe I');
    $ret[] = awl_notImportant(7748.284,     103, 'Fe I');
    $ret[] = awl_notImportant(7748.894,      92, 'Ni I');
    $ret[] = awl_notImportant(7751.116,      46, 'Fe I');
    $ret[] = awl_notImportant(7760.641,      17, 'Si I');
    $ret[] = awl_notImportant(7771.954,      75, 'O I');
    $ret[] = awl_notImportant(7774.17,       60, 'O I');
    $ret[] = awl_notImportant(7775.39,       55, 'O I');
    $ret[] = awl_notImportant(7780.568,     102, 'Fe I');
    $ret[] = awl_notImportant(7788.933,      82, 'Ni I');
    $ret[] = awl_notImportant(7797.588,      79, 'Ni I');
    $ret[] = awl_notImportant(7800.000,      61, 'Si I');
    $ret[] = awl_notImportant(7807.916,      64, 'Fe I');
    $ret[] = awl_notImportant(7810.815,      13, 'Fe I');
    $ret[] = awl_notImportant(7811.16,       46, 'Mg I');
    $ret[] = awl_notImportant(7832.208,     150, 'Fe I');
    $ret[] = awl_notImportant(7849.984,      66, 'Si I');
    $ret[] = awl_notImportant(7855.405,      25, 'Fe I');
    $ret[] = awl_notImportant(7869.635,      26, 'Fe I');
    $ret[] = awl_notImportant(7877.059,      20, 'Mg II');
    $ret[] = awl_notImportant(7896.378,      28, 'Mg II');
    $ret[] = awl_notImportant(7912.384,      15, 'Si I');
    $ret[] = awl_notImportant(7912.870,      40, 'Fe I');
    $ret[] = awl_notImportant(7913.438,      17, 'Si I');
    $ret[] = awl_notImportant(7918.383,     100, 'Si I');
    $ret[] = awl_notImportant(7930.819,      44, 'Mg I');
    $ret[] = awl_notImportant(7932.351,      90, 'Si I');
    $ret[] = awl_notImportant(7933.12,       22, 'Cu I');
    $ret[] = awl_notImportant(7937.150,     166, 'Fe I');
    $ret[] = awl_notImportant(7941.096,      38, 'Fe I');
    $ret[] = awl_notImportant(7944.001,     147, 'Si I (Ti I)');
    $ret[] = awl_notImportant(7945.858,     185, 'Fe I');
    $ret[] = awl_notImportant(7955.71,       27, 'Fe I');
    $ret[] = awl_notImportant(7994.488,      50, 'Fe I');
    $ret[] = awl_notImportant(7998.953,     172, 'Fe I');
    $ret[] = awl_notImportant(8026.925,      41, 'Si I, Atm H2O');    
    $ret[] = awl_notImportant(8028.318,      70, 'Fe I');
    $ret[] = awl_notImportant(8035.608,      32, 'Si I');
    $ret[] = awl_notImportant(8046.058,     146, 'Fe I');
    $ret[] = awl_notImportant(8047.625,      58, 'Fe I');
    $ret[] = awl_notImportant(8049.42,       42, 'Mg I');
    $ret[] = awl_notImportant(8054.311,      52, 'Mg I');
    $ret[] = awl_notImportant(8070.620,      29, 'Si I, CN');
    $ret[] = awl_notImportant(8075.158,      33, 'Fe I');
    $ret[] = awl_notImportant(8080.582,      28, 'Ti I');
    $ret[] = awl_notImportant(8085.175,     150, 'Fe I');
    $ret[] = awl_notImportant(8092.640,      38, 'Cu I');
    $ret[] = awl_notImportant(8093.232,      66, 'Si I, V I?');
    $ret[] = awl_notImportant(8094.041,      34, 'S I');
    $ret[] = awl_notImportant(8096.874,      36, 'Fe I');
    $ret[] = awl_notImportant(8098.746,     114, '~Mg I, atm H2O');
    $ret[] = awl_notImportant(8145.478,      18, 'Fe I');
    $ret[] = awl_notImportant(8171.239,      23, 'Si I');
    $ret[] = awl_notImportant(8183.25,      180, 'Na I');
    $ret[] = awl_notImportant(8194.836,     304, 'Na I');
    $ret[] = awl_notImportant(8198.98,      129, 'Fe I, Atm H2O');
    $ret[] = awl_notImportant(8201.695,      42, 'Ca II, Atm');
    $ret[] = awl_notImportant(8207.749,      64, 'Fe I');
    $ret[] = awl_notImportant(8213.041,     157, 'Mg I');
    $ret[] = awl_notImportant(8215.155,      35, 'Si I, CN');
    $ret[] = awl_notImportant(8220.388,     221, 'Fe I');
    $ret[] = awl_notImportant(8232.319,      91, 'Fe I');
    $ret[] = awl_notImportant(8242.5,        11, 'N I');
    $ret[] = awl_notImportant(8242.50,       11, 'N I');
    $ret[] = awl_notImportant(8248.137,      81, 'Fe I');
    $ret[] = awl_notImportant(8248.802,      98, 'Ca II');
    $ret[] = awl_notImportant(8254.681,      24, 'Ca II');
    $ret[] = awl_notImportant(8263.445,     107, 'Atm H2O');
    $ret[] = awl_notImportant(8263.850,      32, 'Fe I');
    $ret[] = awl_notImportant(8264.276,      26, 'Fe I');
    $ret[] = awl_notImportant(8275.899,      36, 'Fe I');
    $ret[] = awl_notImportant(8293.52,       52, 'Fe I');
    $ret[] = awl_notImportant(8305.617,      31, 'Mg I');
    $ret[] = awl_notImportant(8305.640,      51, 'Fe I');
    $ret[] = awl_notImportant(8310.252,      60, 'Mg I');
    $ret[] = awl_notImportant(8327.061,     193, 'Fe I');
    $ret[] = awl_notImportant(8328.950,      17, '?');
    $ret[] = awl_notImportant(8331.926,     130, 'Fe I');
    $ret[] = awl_notImportant(8335.150,     114, 'C I');
    $ret[] = awl_notImportant(8336.236,      18, '?');
    $ret[] = awl_notImportant(8339.413,     109, 'Fe I');
    $ret[] = awl_notImportant(8346.131,     146, 'Mg I');
    $ret[] = awl_notImportant(8358.504,      21, 'Fe I');
    $ret[] = awl_notImportant(8360.795,      50, 'Fe I');
    $ret[] = awl_notImportant(8365.640,      51, 'Fe I');
    $ret[] = awl_notImportant(8377.870,      25, 'Ti I');
    $ret[] = awl_notImportant(8382.541,      29, 'Ti I');
    $ret[] = awl_notImportant(8382.781,      23, 'Ti I');
    $ret[] = awl_notImportant(8384.243,      16, 'Ti I');
    $ret[] = awl_notImportant(8387.782,     170, 'Fe I');
    $ret[] = awl_notImportant(8396.900,      23, 'Ti I');
    $ret[] = awl_notImportant(8401.401,      25, 'Fe I');
    $ret[] = awl_notImportant(8412.356,      44, 'Ti I');
    $ret[] = awl_notImportant(8419.292,      18, '?');
    $ret[] = awl_notImportant(8424.139,      32, 'Fe I');
    $ret[] = awl_notImportant(8426.514,      43, 'Ti I');
    $ret[] = awl_notImportant(8434.968,      57, 'Ti I');
    $ret[] = awl_notImportant(8435.655,      52, 'Ti I');
    $ret[] = awl_notImportant(8439.581,      79, 'Fe I');
    $ret[] = awl_notImportant(8443.975,      32, 'Si I');
    $ret[] = awl_notImportant(8446.359,      74, 'O I');
    $ret[] = awl_notImportant(8446.741,      50, 'O I');
    $ret[] = awl_notImportant(8468.418,     128, 'Fe I');
    $ret[] = awl_notImportant(8471.744,      44, 'Fe I');
    $ret[] = awl_notImportant(8473.663,      11, 'Mg I');
    $ret[] = awl_notImportant(8481.986,      22, 'Fe I');
    $ret[] = awl_notImportant(8490.994,      34, 'Fe I');
    $ret[] = awl_helper(8498.062,          'Ca II', 1470, '', array('displayImportance' => 150));
    $ret[] = awl_notImportant(8501.553,      34, 'Si I');
    $ret[] = awl_notImportant(8502.228,      50, 'Si I');
    $ret[] = awl_notImportant(8514.082,     108, 'Fe I');
    $ret[] = awl_notImportant(8515.122,      79, 'Fe I');
    $ret[] = awl_notImportant(8526.676,      58, 'Fe I');
    $ret[] = awl_notImportant(8536.163,      58, 'Si I');
    $ret[] = awl_notImportant(8538.021,      31, 'Fe I');
    $ret[] = awl_helper(8542.144,          'Ca II', 3670, '', array('displayImportance' => 350));
    $ret[] = awl_notImportant(8556.797,     134, 'Si I');
    $ret[] = awl_notImportant(8571.807,      36, 'Fe I');
    $ret[] = awl_notImportant(8582.271,      86, 'Fe I');
    $ret[] = awl_notImportant(8583.310,      22, 'Ca I');
    $ret[] = awl_notImportant(8592.969,      48, 'Fe I');
    $ret[] = awl_notImportant(8611.812,      99, 'Fe I');
    $ret[] = awl_notImportant(8648.472,     161, 'Si I');
    $ret[] = awl_helper(8662.170,          'Ca II', 2600, '', array('displayImportance' => 250));
    $ret[] = awl_notImportant(8674.756,     113, 'Fe I');
    $ret[] = awl_notImportant(8679.646,      41, 'Fe I, S I');
    $ret[] = awl_notImportant(8688.642,     268, 'Fe I');
    $ret[] = awl_notImportant(8694.641,      34, 'S I');
    $ret[] = awl_notImportant(8698.717,      20, 'Fe I');
    $ret[] = awl_notImportant(8699.461,      73, 'Fe I');
    $ret[] = awl_notImportant(8703.73,       23, 'Mn I');
    $ret[] = awl_notImportant(8710.21,       21, 'Mg I');
    $ret[] = awl_notImportant(8710.398,      82, 'Fe I');
    $ret[] = awl_notImportant(8712.701,      57, 'Mg I');
    $ret[] = awl_notImportant(8713.208,      58, 'Fe I');
    $ret[] = awl_notImportant(8717.833,     105, 'Mg I');
    $ret[] = awl_notImportant(8728.024,     107, 'Si I');
    $ret[] = awl_notImportant(8736.040,     289, 'Mg I');
    $ret[] = awl_notImportant(8742.466,      97, 'Si I');
    $ret[] = awl_notImportant(8752.025,      94, 'Si I');
    $ret[] = awl_notImportant(8757.199,      91, 'Fe I');
    $ret[] = awl_notImportant(8763.978,      99, 'Fe I');
    //end-sorted-section

    $ret = array_merge($ret, getHeliumLines());


    $ret = array_merge($ret, getMagneticWavelengths());
    $ret = array_merge($ret, getCoronalWavelengths());
    $ret = array_merge($ret, getRedBookInfraredWavelengths());

    $ret = wavelengthInfo_getPolyfilledItemArray($ret, array("must_include" => false));

    return $ret;
  }


function numberFrom($s, $default_value = 0){
  $s = $s.'';
  $a = '-0123456789.';
  $ret = '';
  for ($i=0; $i<strlen($s); $i++){
    $c = substr($s, $i, 1);
    if (strpos($a, $c)!==false){
      $ret .= $c;
    }
  }
  if ('' == $ret){
    $ret = $default_value;
  }
  return $ret;
}  

function interpolateRedBook($i, $p1, $p2){
  $i = $p1[1] + ($i - $p1[0]) * (($p1[1] - $p2[1]) / ($p1[0] - $p2[0]));
  if ($i > 100){
    // lose the digit we have zero right to have
    $i = round($i / 10);
    $i *= 10;
  }
  return $i;
}
function redBookIntensityToBlueBookValue($disk_intensity, $spot_intensity){  
  $invalid_value = -100;
  $di = floatval(numberFrom($disk_intensity, $invalid_value));
  $si = floatval(numberFrom($spot_intensity, $di - 10));
  $i = max($di, $si);
  if ($di != $invalid_value){
    if ($si != $invalid_value){
      if (abs($di - $si) > 5){        
        $disk_mul = 9;
        $spot_mul = 1;
        $i = ($di*$disk_mul + $si*$spot_mul)/($spot_mul + $disk_mul);
      }
    }
  }
  $min_i = -10;
  $max_i = 30;
  $i = max($min_i, $i);

  if (stripos($disk_intensity.$spot_intensity, 'N')!==false){
    $i = $i - $min_i;
    $i *= 0.68;
    $i = $i + $min_i;
  }
  if (stripos($disk_intensity.$spot_intensity, 'NN')!==false){
    $i = $i - $min_i;
    $i *= 0.68;
    $i = $i + $min_i;
  }
  $i = min($max_i, $i);

  $known_from_overlap_raw = array(
    // red, blue
    array(-1, 12),
    array(0, 17),
    array(1, 26),
    array(2, 54), 
    array(3, 58),
    array(4, 73),
    array(5, 75),
    array(6, 99),
    array(7, 113),
    array(8, 134),
   array(10, 289),
   array(12, 268),
   array(25, 3600)   
  );

  $known_from_overlap = array(
    // red, blue
    array(-3, 2),
    array(-1, 10),
    array(0, 17),
    array(1, 26),
    array(2, 54), 
    array(3, 62),
    array(4, 73),
    array(5, 85),
    array(6, 100),
    array(7, 120),
    array(8, 140),
    array(9, 190),
   array(10, 240),
   array(12, 290),
   array(25, 3600)   
  );
  $i  =  max($i, $known_from_overlap[0][0]);
  $i  =  min($i, $known_from_overlap[count($known_from_overlap)-1][0]);

  //$range = $max_i - $min_i;
  //$scaled_i = ($i - $min_i) / $range;
  
  if ($i < $known_from_overlap[0][0]){
    return interpolateRedBook(
      $i, 
      $known_from_overlap[0], 
      $known_from_overlap[1]
    );    
  }
  if ($i > $known_from_overlap[count($known_from_overlap)-1][0]){
    return interpolateRedBook(
      $i, 
      $known_from_overlap[count($known_from_overlap)-2], 
      $known_from_overlap[count($known_from_overlap)-1]
    );
  }

  for ($q =1; $q<count($known_from_overlap); $q++){
    if ($i >= $known_from_overlap[$q-1][0]){
      if ($i<=$known_from_overlap[$q][0]){
        return interpolateRedBook($i,
          $known_from_overlap[$q-1],
          $known_from_overlap[$q]
        );  
      }
    }
  }

  
  return $i;    
}

function awl_infraredLineWithIntensity($lambda_A, $disk_intensity, $spot_intensity, $caption, $displayImportance = false){
  if (is_string($displayImportance)){
    if (strpos($displayImportance, 'page') === 0){
      // this denotes a page and/or column anchor,
      //   ignore it
      $displayImportance = false;
    }
  }
  $bag = false;
  $lambda_A_string = number_format($lambda_A, 1, '.', ""); 
  
  if (strpos($caption, 'Pasch') === false){
    $caption = explode(',', $caption);
    foreach ($caption as &$kap){
      $kap = trim($kap);   
      if (strpos($kap, 'Pasch')!==false){
        // leave the label as is
      }else{
        $kap .= ' '; 
        $kap = str_replace(') ', ' )', $kap);
        if (strpos($kap, ' I') === false){
          $kap = str_replace(' ', ' I', $kap);
        }
      }    
    }
    $caption = implode(', ', $caption);
  }else{
    $caption = trim($caption);
  }


  $scaled_i = redBookIntensityToBlueBookValue($disk_intensity, $spot_intensity);
  $scaled_i_r = $scaled_i;
  

  $wavelength_penalty_cmos = log(1 + max(0, $lambda_A - 600), 2);    
  $calculated_displayImportance = ($scaled_i_r * 100) / $wavelength_penalty_cmos;
  $calculated_displayImportance = round($calculated_displayImportance /75);

  if ($displayImportance === false){
    $displayImportance = $calculated_displayImportance;
  }

  $displayImportance = str_replace('$', $calculated_displayImportance, $displayImportance);


  try {
    eval('$displayImportance = '.$displayImportance.';');
  } catch (Exception $e) {
    die("durma");
  }


    // intensity-width_mA correspondence:
    //  LAMBDA | BLUE BOOK | RED BOOK
    //  8736   |    289    |  10N 
    //  8248.8 |     98    |   4
    //  8648.5 |    161    |  10N
    //  7423.5 |    120    |   8N

    $ret = array(
      "lambda_A" => $lambda_A, 
      "caption" => $caption,
      "photogenyClass" => floor(6 - $scaled_i_r*3),
      "widthForCalculations" => $scaled_i,
      "width_mA" => round($scaled_i).' (iri)',
      "must_include" => false,      
      "redBookIntensity_disk" => $disk_intensity,
      "redBookIntensity_spot" => $spot_intensity,      
      "displayImportance" => $displayImportance
    );

    polyfill_item_with_chromosphere_info($ret);

    return $ret;
    
}

function wavelengthVacuumToAir($lambda_A){
    // source: https://classic.sdss.org/dr7/products/spectra/vacwavelength.php
    // AIR = VAC / (1.0 + 2.735182E-4 + 131.4182 / VAC^2 + 2.76249E8 / VAC^4)
    $lambda_A_p2 = $lambda_A*$lambda_A;
    $lambda_A_p4 = $lambda_A_p2*$lambda_A_p2;
    return  $lambda_A / (1.0 + 2.735182E-4 + 131.4182 / $lambda_A_p2 + 2.76249E8 / $lambda_A_p4);
}

function getExtraPaschenList(){
  // The Blue book has
  //
  // 8437.96
  // 8467.26
  // 8502.49
  // 8545.38
  // 8598.39
  // 8665.02
  //
  //
  // source:
  // https://www.gemini.edu/observing/resources/near-ir-resources/spectroscopy/hydrogen-recombination-lines
  //
  
  $micron_to_angstrom = 10*1000;

  $ret = array();
  $ret[] = array("label" => "H (Paschen n=12)", "wavelength_A" => wavelengthVacuumToAir(0.87529*$micron_to_angstrom));
  $ret[] = array("label" => "H (Paschen n=13)", "wavelength_A" => wavelengthVacuumToAir(0.86674*$micron_to_angstrom));
  $ret[] = array("label" => "H (Paschen n=14)", "wavelength_A" => wavelengthVacuumToAir(0.86008*$micron_to_angstrom));
  $ret[] = array("label" => "H (Paschen n=15)", "wavelength_A" => wavelengthVacuumToAir(0.85477*$micron_to_angstrom));
  $ret[] = array("label" => "H (Paschen n=16)", "wavelength_A" => wavelengthVacuumToAir(0.84696*$micron_to_angstrom));
  $ret[] = array("label" => "H (Paschen n=17)", "wavelength_A" => wavelengthVacuumToAir(0.84403*$micron_to_angstrom));

  return $ret;
}
function  getRedBookInfraredWavelengths(){
  $ret = array();
  //start-sorted-section
  $ret[] = awl_infraredLineWithIntensity(8770.681,            0,        -1, 'Ni', 'page 56');
  $ret[] = awl_infraredLineWithIntensity(8772.884,            5,         6, 'Al', 'page 56');
  $ret[] = awl_infraredLineWithIntensity(8773.906,            6,         7, 'Al', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8780.757,            2,      'ob', '?', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8784.444,            1,         0, 'Fe', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8790.454,            6,         3, 'Fe, Si', 'page 57');  
  $ret[] = awl_infraredLineWithIntensity(8793.350,            6,         7, 'Fe', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8804.637,            3,         6, 'Fe', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8806.775,           14,        16, 'Mg', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8808.173,            2,        -1, 'Fe', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8809.406,            1,         0, 'Ni', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8819.51,          '0N',      '1N', 'Fe', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8824.234,           10,        15, 'Fe', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8838.441,            6,         9, 'Fe', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8841.23,         '-1N',      '0N', 'Al', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8846.750,            3,         0, 'Fe', 'page 57');
  $ret[] = awl_infraredLineWithIntensity(8862.563,            3,         4, 'Ni', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8863,               '',        '', 'H (Paschen n=11)', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8863.588,            1,     '-1N', 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8866.943,            9,        12, 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8868.444,            3,         5, 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8874.478,         '1N',      '2N', 'S?', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8876.030,            1,         0, 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8878.271,            1,      '0w', 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8878.775,            0,        -1, 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8880.69,         '-1N',      'ob', 'S', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8892.738,            4,         3, 'Si', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8899.222,            2,      '0W', 'Atm, Si, Si?', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8905.989,            1,         0, 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8912.101,            7,         3, 'Ca II', 'page 58'); 
  $ret[] = awl_infraredLineWithIntensity(8920.036,            3,         5, 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8922.643,            0,        -2, 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8923.570,            3,         4, 'Al, Mg', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8925.288,            1,     '-1W', 'Si', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8925.91,            -3,      '3W', 'Cr', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8927.392,            7,         3, 'Ca II', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8929.072,            6,         6, 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8943.058,            2,      '4W', 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8945.198,            5,         6, 'Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8946.336,        '8nl',     '9nl', 'Atm, Fe', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8947.197,            0,      '1W', 'Cr', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8949.06,          '2N',         0, 'Si', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8965.94,            -1,      'ob', 'Ni', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8967.72,             0,     '-2W', '?', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8968.20,             1,        -3, 'Ni', 'page 58');
  $ret[] = awl_infraredLineWithIntensity(8975.413,            1,         3, 'Fe', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(8979.23,            -1,        -2, 'Ti II', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(8984.898,            1,         0, 'Fe', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(8997.16,             0,     '-2N', 'Mg?', 'page 59'); 
  $ret[] = awl_infraredLineWithIntensity(8999.580,            3,         5, 'Fe', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9006.81,             1,        '', 'Atm, ~Fe', 'page 59');  
  $ret[] = awl_infraredLineWithIntensity(9008.52,             4,         1, 'Fe', 'page 59');  
  $ret[] = awl_infraredLineWithIntensity(9009.835,            3,     '10W', 'Cr', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9010.573,            1,         2, 'Fe', 'page 59'); 
  $ret[] = awl_infraredLineWithIntensity(9013.98,             0,      '4W', 'Fe', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9014.92,        '-2NN',        '', 'H (Paschen n=10)', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9019.77,             0,         1, 'Fe', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9021.57,             3,         8, 'Cr', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9024.38,             2,      '3W', 'Fe', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9024.70,            -1,      'ob', 'Fe', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9030.75,             0,         1, 'Fe', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9035.88,             0,      '3W', 'Cr', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9061.443,            7,      'ob', 'C', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9062.26,             1,      'ob', 'Fe', 'page 59'); 
  $ret[] = awl_infraredLineWithIntensity(9062.48,             3,      'ob', 'C', 'page 59'); 
  $ret[] = awl_infraredLineWithIntensity(9070.416,            1,      '2W', 'Fe', 'page 59'); 
  $ret[] = awl_infraredLineWithIntensity(9078.28,             7,     'ob?', 'C', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9079.413,        '2nl',         2, 'Atm, Fe?', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9080.532,        '1Ns',         1, 'Fe', 'page 59');
  $ret[] = awl_infraredLineWithIntensity(9088.391,       '11nl',        10, 'Fe, C', 'page 60');
  $ret[] = awl_infraredLineWithIntensity(9089.422,            3,         4, 'Fe', 'page 60');
  $ret[] = awl_infraredLineWithIntensity(9094.82,             8,      'ob', 'C', 'page 60');
  $ret[] = awl_infraredLineWithIntensity(9111.877,            9,         0, 'C', 'page 60');
  $ret[] = awl_infraredLineWithIntensity(9116.26,             1,      '1W', 'Fe', 'page 60');
  $ret[] = awl_infraredLineWithIntensity(9117.08,            -1,        '', 'Fe', 'page 60');
  $ret[] = awl_infraredLineWithIntensity(9146.16,             3,         5, 'Fe', 'page 60');
  $ret[] = awl_infraredLineWithIntensity(9148.00,             0,         1, 'Fe', 'page 60');
  $ret[] = awl_infraredLineWithIntensity(9154.122,        '-1N',      '0N', 'Na?', 'page 60');
  $ret[] = awl_infraredLineWithIntensity(9156.26,             0,         0, 'Fe', 'page 60');
  $ret[] = awl_infraredLineWithIntensity(9164.570,            1,         0, 'Fe', 'page 60');
  $ret[] = awl_infraredLineWithIntensity(9208.31,             0,      'ob', 'Cr?', 'page 61');
  $ret[] = awl_infraredLineWithIntensity(9210.036,            3,         4, 'Fe', 'page 61');
  $ret[] = awl_infraredLineWithIntensity(9214.649,        '7nl',        '', 'Atm, Fe II?', 'page 61');
  $ret[] = awl_infraredLineWithIntensity(9218.251,            3,      'ob', 'Mg II', 'page 61'); 
  $ret[] = awl_infraredLineWithIntensity(9228.101,            6,         0, 'S', 'page 61'); 
  $ret[] = awl_infraredLineWithIntensity(9229,               '',        '', 'H (Paschen n=9)', 'page 61');
  $ret[] = awl_infraredLineWithIntensity(9237.56,             6,     '-3N', 'S', 'page 61');
  $ret[] = awl_infraredLineWithIntensity(9242.26,             1,         1, 'Fe', 'page 61');
  $ret[] = awl_infraredLineWithIntensity(9244.25,         '0NN',      'ob', 'Mg II', 'page 61'); 
  $ret[] = awl_infraredLineWithIntensity(9246.50,             1,      '2W', 'Fe', 'page 61');
  $ret[] = awl_infraredLineWithIntensity(9248.76,             0,         1, 'Fe', 'page 61');
  $ret[] = awl_infraredLineWithIntensity(9253.71,             3,         2, 'Fe', 'page 61');
  $ret[] = awl_infraredLineWithIntensity(9255.79,         '10N',      '8N', 'Mg', 'page 61');
  $ret[] = awl_infraredLineWithIntensity(9258.280,            3,      '3W', 'Fe', 'page 62'); 
  $ret[] = awl_infraredLineWithIntensity(9260.58,            -3,        '', 'O', 'page 62, see note 23 in the red book');
  $ret[] = awl_infraredLineWithIntensity(9260.98,             0,        '', 'O', 'page 62, see note 23 in the red book');
  $ret[] = awl_infraredLineWithIntensity(9262.76,          '0N',      'ob', 'O', 'page 62'); 
  $ret[] = awl_infraredLineWithIntensity(9265.96,          '2N',      'ob', 'O', 'page 62'); 
  $ret[] = awl_infraredLineWithIntensity(9269.33,             0,         1, '?', 'page 62');
  $ret[] = awl_infraredLineWithIntensity(9283.36,         '-1N',     '-1N', '?', 'page 62');
  $ret[] = awl_infraredLineWithIntensity(9289.44,            -1,        -2, 'Fe', 'page 62');
  $ret[] = awl_infraredLineWithIntensity(9290.468,            2,      '6W', 'Cr', 'page 62');
  $ret[] = awl_infraredLineWithIntensity(9294.659,         '1N',      '0N', 'Fe', 'page 62');
  $ret[] = awl_infraredLineWithIntensity(9297.14,             1,         1, 'Fe', 'page 62');
  $ret[] = awl_infraredLineWithIntensity(9318.22,             1,        -2, 'Fe, Si', 'page 62');
  $ret[] = awl_infraredLineWithIntensity(9347.582,            1,         1, '?', 'page 63');
  $ret[] = awl_infraredLineWithIntensity(9349.22,          '0N',      '0N', 'Atm? ?', 'page 63');  
  $ret[] = awl_infraredLineWithIntensity(9359.39,             1,      '2W', 'Fe', 'page 63');
  $ret[] = awl_infraredLineWithIntensity(9362.31,         '1ns',         3, 'Cr?', 'page 63');
  $ret[] = awl_infraredLineWithIntensity(9372.86,             2,      '1N', 'Fe', 'page 63');
  $ret[] = awl_infraredLineWithIntensity(9403.27,             1,         0, 'Fe II', 'page 63');   
  $ret[] = awl_infraredLineWithIntensity(9404.90,             1,         2, 'Fe', 'page 63');
  $ret[] = awl_infraredLineWithIntensity(9405.74,          '5N',     'ob?', 'C', 'page 63');   
  $ret[] = awl_infraredLineWithIntensity(9414.95,         '10N',     '10N', 'Mg', 'page 63');
  $ret[] = awl_infraredLineWithIntensity(9434.78,         '1NN',     '1NN', 'Mg?', 'page 64');
  $ret[] = awl_infraredLineWithIntensity(9447.03,             3,        10, 'Cr', 'page 64');
  $ret[] = awl_infraredLineWithIntensity(9462.94,             2,      '1W', 'Fe', 'page 64'); 
  $ret[] = awl_infraredLineWithIntensity(9506.02,             1,         2, 'Ti', 'page 64');
  $ret[] = awl_infraredLineWithIntensity(9513.23,             1,      '0N', 'Fe', 'page 64');
  $ret[] = awl_infraredLineWithIntensity(9520.03,             3,         2, 'Ni', 'page 65');
  $ret[] = awl_infraredLineWithIntensity(9546,               '',        '', 'H (Paschen-epsilon, n=8)', 'page 65');
  $ret[] = awl_infraredLineWithIntensity(9569.93,             2,         0, 'Fe, Si', 'page 65');
  $ret[] = awl_infraredLineWithIntensity(9574.29,             3,         5, 'Cr', 'page 65');
  $ret[] = awl_infraredLineWithIntensity(9603.143,        '4Nd',     '-1N', 'C', 'page 66');
  $ret[] = awl_infraredLineWithIntensity(9620.98,          '2N',      'ob', 'C, Fe', 'page 66');
  $ret[] = awl_infraredLineWithIntensity(9634.17,             1,      '2W', 'Fe', 'page 66');
  $ret[] = awl_infraredLineWithIntensity(9638.39,             2,         5, 'Ti', 'page 66');
  $ret[] = awl_infraredLineWithIntensity(9658.40,          '8N',     '-1N', 'C', 'page 66'); // why marked in the red book?
  $ret[] = awl_infraredLineWithIntensity(9675.571,            4,        10, 'Ti', 'page 67'); 
  $ret[] = awl_infraredLineWithIntensity(9688.87,            '',        10, 'Ti', 'page 67');
  $ret[] = awl_infraredLineWithIntensity(9689.37,             2,      'ob', 'Si', 'page 67');
  $ret[] = awl_infraredLineWithIntensity(9705.679,            0,        10, 'Ti', 'page 67');
  $ret[] = awl_infraredLineWithIntensity(9763.32,             3,         4, 'Fe', 'page 67');
  $ret[] = awl_infraredLineWithIntensity(9800.29,            -1,      '0N', 'Fe', 'page 68');
  $ret[] = awl_infraredLineWithIntensity(9839.36,             1,     '-1N', 'Si', 'page 68');
  $ret[] = awl_infraredLineWithIntensity(9889.050,            5,         7, 'Fe', 'page 68');
  $ret[] = awl_infraredLineWithIntensity(9891.61,          '2N',         0, 'Si', 'page 69');
  $ret[] = awl_infraredLineWithIntensity(9913.19,             2,     '1N?', 'Si', 'page 69');
  $ret[] = awl_infraredLineWithIntensity(9941.46,          '2N',     '4N?', '(Ti)', 'page 69');  
  $ret[] = awl_infraredLineWithIntensity(9944.220,            3,         2, 'Fe', 'page 69');
  $ret[] = awl_infraredLineWithIntensity(9967.30,         '3ns',     '4ns', 'Fe', 'page 69');
  $ret[] = awl_infraredLineWithIntensity(9980.48,             2,         2, 'Fe', 'page 69');
  $ret[] = awl_infraredLineWithIntensity(9993.17,          '5N',      '2N', 'Mg?', 'page 69');
  $ret[] = awl_infraredLineWithIntensity(10025.86,         '2N',      '0N', 'Si', 'page 70');
  $ret[] = awl_infraredLineWithIntensity(10036.670,           5,      '4W', 'Sr II', 'page 70');
  $ret[] = awl_infraredLineWithIntensity(10049.37,       '50NN',      'ob', 'H (Paschen-delta, n=7)', 'page 70'); 
  $ret[] = awl_infraredLineWithIntensity(10057.68,            2,        10, 'Ti', 'page 70');
  $ret[] = awl_infraredLineWithIntensity(10065.070,           8,        10, 'Fe', 'page 70');
  $ret[] = awl_infraredLineWithIntensity(10123.895,           8,         4, '??', 'page 70, see the He II vs whatever question');
  $ret[] = awl_infraredLineWithIntensity(10145.580,           9,        12, 'Fe', 'page 70');
  $ret[] = awl_infraredLineWithIntensity(10153.096,       '3nl',     '3nl', 'Si, Fe', 'page 70');
  $ret[] = awl_infraredLineWithIntensity(10167.50,            2,         5, 'Fe', 'page 70');
  $ret[] = awl_infraredLineWithIntensity(10193.245,           4,      '5W', 'Ni', 'page 70');
  $ret[] = awl_infraredLineWithIntensity(10216.335,          10,        12, 'Fe', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10218.415,           3,         6, 'Fe', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10288.950,           6,      '3W', 'Si', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10302.62,            2,         2, 'Ni', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10327.360,           7,         9, 'Sr II', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10330.22,            2,         1, 'Ni', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10340.900,           3,         6, 'Fe', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10343.840,           8,        25, 'Ca', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10347.975,           3,         1, 'Fe', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10371.285,           9,      '8W', 'Si', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10395.795,           4,         8, 'Fe', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10396.81,            2,        10, 'Ti', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10452.756,           3,         4, 'Fe', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10455.455,           8,         2, 'S', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10456.753,           4,      'ob', 'S', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10459.436,           7,         1, 'S', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10469.680,           7,         9, 'Fe', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10486.289,           3,         5, 'Cr', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10496.170,           3,        10, 'Ti', 'page 71');
  $ret[] = awl_infraredLineWithIntensity(10532.236,           4,         6, 'Fe', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10584.77,            0,        10, 'Ti', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10585.137,          12,     '10?', 'Si', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10603.426,          10,         8, 'Si', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10611.669,           3,         2, '?', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10627.63,            8,         5, 'Si', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10660.99,           10,         6, 'Si', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10661.63,            0,        10, 'Ti', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10667.48,            2,         5, 'Cr', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10672.22,            0,         4, 'Cr', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10683.09,           10,      '2N', 'C', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10685.36,            8,      '1N', 'C', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10689.71,            8,         7, 'Si', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10691.24,           12,      '3N', 'C', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10694.25,            8,         7, 'Si', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10707.36,            8,      '1N', 'C', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10726.36,           '',        10, 'Ti', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10727.42,            9,         8, 'Si', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10729.588,           7,      '0N', 'C', 'page 72');
  $ret[] = awl_infraredLineWithIntensity(10749.39,           12,        10, 'Si', 'page 73');
  $ret[] = awl_infraredLineWithIntensity(10784.57,            3,         2, 'Si', 'page 73');
  $ret[] = awl_infraredLineWithIntensity(10786.85,            7,      '7W', 'Si', 'page 73');
  $ret[] = awl_infraredLineWithIntensity(10811.14,         '5N',      '4N', 'Mg', 'page 73'); 
  $ret[] = awl_infraredLineWithIntensity(10818.31,            1,         2, 'Fe', 'page 73');
  $ret[] = awl_infraredLineWithIntensity(10827.14,           12,     '12W', 'Si', 'page 73');
  $ret[] = awl_infraredLineWithIntensity(10830.38,        '5NN',     '5NN', 'He', 'max(150, $)');
  $ret[] = awl_infraredLineWithIntensity(10834.02,            5,         5, 'Na' /* nist-modified*/, 'page 73');
  $ret[] = awl_infraredLineWithIntensity(10839.03,            0,      '3N', 'Ca', 'page 73');
  $ret[] = awl_infraredLineWithIntensity(10843.88,            5,         4, 'Si', 'page 73');   
  $ret[] = awl_infraredLineWithIntensity(10849.47,            3,         3, 'Fe?', 'page 73');
  $ret[] = awl_infraredLineWithIntensity(10869.57,            4,         3, 'Si', 'page 73'); 
  $ret[] = awl_infraredLineWithIntensity(10882.84,            3,     'ob?', 'Si', 'page 73');
  $ret[] = awl_infraredLineWithIntensity(10885.37,            3,         1, 'Si', 'page 73'); 
  $ret[] = awl_infraredLineWithIntensity(10914.25,            3,      'ob', 'Mg II', 'page 74'); 
  $ret[] = awl_infraredLineWithIntensity(10914.88,            5,         6, 'Sr II', 'page 74');
  $ret[] = awl_infraredLineWithIntensity(10938.1,         '50N',     '5NN', 'H (Paschen-gamma, n=6)', 'page 74');
  $ret[] = awl_infraredLineWithIntensity(10951.82,            0,      'ob', 'Mg II', 'page 74');
  $ret[] = awl_infraredLineWithIntensity(10962.30,        '3ns',         4, 'Mg?', 'page 74');
  $ret[] = awl_infraredLineWithIntensity(10965.47,            5,      '8n', 'Mg?', 'page 74');
  $ret[] = awl_infraredLineWithIntensity(10979.34,            4,     '1NN', 'Si', 'page 74');
  $ret[] = awl_infraredLineWithIntensity(11119.81,            1,      '3N', 'Fe', 'page 76');
  $ret[] = awl_infraredLineWithIntensity(11246.95,            2,         3, 'Ti', 'page 77');
  $ret[] = awl_infraredLineWithIntensity(11403.80,            5,        20, 'Na', 'page 78');
  $ret[] = awl_infraredLineWithIntensity(11422.38,            8,        '', 'Fe', 'page 78');
  $ret[] = awl_infraredLineWithIntensity(11439.12,           10,        '', 'Fe', 'page 78');
  $ret[] = awl_infraredLineWithIntensity(11502.68,         '3N',        '', 'Si', 'page 78');
  $ret[] = awl_infraredLineWithIntensity(11508.00,         '3N',      '4N', '(?)', 'page 78');
  $ret[] = awl_infraredLineWithIntensity(11611.44,       '15ns',        '', 'Si', 'page 79');
  $ret[] = awl_infraredLineWithIntensity(11638.33,           10,        '', 'Fe', 'page 79');
  $ret[] = awl_infraredLineWithIntensity(11753.42,         '5N',        '', 'C', 'page 80');
  $ret[] = awl_infraredLineWithIntensity(11754.84,         '5N',        '', 'C', 'page 80');
  $ret[] = awl_infraredLineWithIntensity(11828.20,        '5NN',        '', 'Mg', 'page 81');
  $ret[] = awl_infraredLineWithIntensity(11876.32,       '10NN',        '', '(?)', 'page 81');
  $ret[] = awl_infraredLineWithIntensity(11984.50,       '10Ns',        '', 'Si', 'page 82');
  $ret[] = awl_infraredLineWithIntensity(12031.56,       '10nl',        '', 'Si', 'page 82');
  $ret[] = awl_infraredLineWithIntensity(12083.79,         '8N',        '', 'Mg', 'page 82');
  $ret[] = awl_infraredLineWithIntensity(12103.63,            4,        '', 'Si', 'page 82');
  $ret[] = awl_infraredLineWithIntensity(12239.53,            5,        '', 'Fe(?)', 'page 82');
  $ret[] = awl_infraredLineWithIntensity(12818.23,           20,        '', 'H (Paschen-beta)', 'page 84');  
  $ret[] = awl_infraredLineWithIntensity(18751.3,    round((20/32000)*51000),        '', 'H (Paschen-alpha)', 'page NIST');
  //end-sorted-section
  foreach (getExtraPaschenList() as $hh){
    $ret[] = awl_infraredLineWithIntensity($hh["wavelength_A"],   '',   '', $hh["label"]);      
  }
  polyfill_item_with_chromosphere_info($ret);
  return $ret;
}