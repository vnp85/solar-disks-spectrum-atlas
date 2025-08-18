<?php

require_once("wavelength_info_helpers.php");
require_once("wavelength_info_nist.php");

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
    -------------------------------------------------------------------------

    photogenyClass is my addition for the amateur astronomer
*/


function get_hydrogen_balmer_series(){
  $ret = array(
      array("lambda_A" => 6562.808, "caption" => "H alpha", "width_mA" => 4020, "photogenyClass" => 1, "displayImportanceFactor" => 3),
      array("lambda_A" => 4861.35, "caption" => "H beta", "width_mA" => 3680, "photogenyClass" => 1, "displayImportanceFactor" => 0.8),
      array("lambda_A" => 4340.47, "caption"=> "H gamma", "width_mA" => 2855, "photogenyClass" => 2),
      array("lambda_A" => 4101.75, "caption"=> "H delta", "width_mA" => 3133, "photogenyClass" => 2, "displayImportanceFactor" => 0.7),
      array("lambda_A" => 3970.0, "caption"=> "H epsilon", "photogenyClass" => 3, "displayClusterBoundaryMarker" => "3"),
      array("lambda_A" => 3889.064, "caption" => "H 8 (dzeta)", "photogenyClass" => 4, "width_mA" => 2346, "displayImportanceFactor" => 0.2),
      array("lambda_A" => 3835.397, "caption" => "H 9 (eta)", "photogenyClass" => 4, "width_mA" => 2362, "displayImportanceFactor" => 0.2),

      array("lambda_A" => 3797.90, "caption" => "H 10 (?theta?)", "width_mA" => 3463, "photogenyClass" => 5, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3770.63, "caption" => "H 11", "width_mA" => 1860, "photogenyClass" => 5, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3760.15, "caption" => "H 12", "photogenyClass" => 5, "width_mA" => 1388, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3734.37, "caption" => "H 13", "photogenyClass" => 5, "width_mA" => 1014, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3721.94, "caption" => "H 14", "width_mA" => 536, "photogenyClass" => 5, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3711.97, "caption" => "H 15", "photogenyClass" => 5, "displayImportanceFactor" => 0.1),
      array("lambda_A" => 3703.86, "caption" => "H 16", "photogenyClass" => 5, "displayImportanceFactor" => 0.1),    
      array("lambda_A" => 3697.15, "caption" => "H 17", "photogenyClass" => 5, "displayImportanceFactor" => 0.1),          
  );

  for ($i=0; $i<count($ret); $i++){
    $ret[$i]["ionized"] = true;
  }

  $ret = wavelengthInfo_getPolyfilledItemArray($ret, array("must_include" => true));
  return $ret;
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

    $ret[] =  awl_notImportant__NIST_intensityNotWidth(6678.151, 200, 'He I', $faintBag);
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(5015.6783, 100, "He I", $faintBag); 

    $ret[] =  awl_notImportant__NIST_intensityNotWidth(4471.5, 225, 'He I', $faintBag);
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(7065.2, 180, 'He I', $faintBag2);
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(10830.2, 1650, '~He I', $brightBag);
    
    $ret[] =  awl_notImportant__NIST_intensityNotWidth(4685.7, 45, "~He II", $faintBag); 

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
    $ret[] = awl_helper(4703.003, "Mn I", 326, 2);
    $ret[] = awl_helper(4824.143, "Cr II", 94, 3);

    $unkown_todo = 9;
    $ret[] = awl_helper(4883.690, "Y II", 51, $unkown_todo);
    $ret[] = awl_helper(4891.502, "Fe I", 312,  $unkown_todo);
    $ret[] = awl_helper(4900.124,  "Y II", 54, $unkown_todo);

    $ret[] = awl_helper(4900.124,  "Y II", 54, $unkown_todo);
    $ret[] = awl_helper(4911.199, "Ti II", 50, $unkown_todo);
    $ret[] = awl_helper(4923.930, "Fe II", 167, $unkown_todo);
    $ret[] = awl_helper(4934.095, "Fe I, Ba II", 207, $unkown_todo);
    $ret[] = awl_helper(4957.613, "Fe I (c)", 696, $unkown_todo);

    $ret[] = awl_helper(5018.45, "Fe II", 210, 1, array("displayImportanceFactor" => 3));

    $ret[] = awl_helper(5105.545, "Cu I", 82,  $unkown_todo);
    $ret[] = awl_helper(5129.162, "Ti II", 70, $unkown_todo);
    $ret[] = awl_helper(5154.075, "Ti II", 73, $unkown_todo);

    $ret[] = awl_helper(5167.4, "~ Mg I b4, Fe I", 935, 2);
    $ret[] = awl_helper(5169.050, "Fe II b3", 154, 2);    
    $ret[] = awl_helper(5172.698, "Mg I b2", 1259, 2);
    $ret[] = awl_helper(5183.619, "Mg I b1", 1584, 2);
    $ret[] = awl_helper(5188.7, "~ Ti II, Ca I", 202, 3);

    $ret[] = awl_helper(5197.576, "Fe II", 80, $unkown_todo);
    $ret[] = awl_helper(5205.730, "Y II", 52, $unkown_todo);
    $ret[] = awl_helper(5226.545, "Ti II", 94, $unkown_todo);
    $ret[] = awl_helper(5262.2, "~ Ti II, Ca I", 128, $unkown_todo);
    $ret[] = awl_helper(5264.808, "Fe II", 45, $unkown_todo);
    $ret[] = awl_helper(5276.0, "~ Fe II, Cr I, Co I", 152, $unkown_todo);
    $ret[] = awl_helper(5316.7, "~ Fe II", 200, $unkown_todo);
    $ret[] = awl_helper(5336.79, "Ti II", 71,  $unkown_todo);
    $ret[] = awl_helper(5528.418, "Mg I", 293,  $unkown_todo);
    $ret[] = awl_helper(5657.880, "Sc II", 64,  $unkown_todo);
    $ret[] = awl_helper(5682.647, "Na I", 104, $unkown_todo);
    $ret[] = awl_helper(5688.217, "Na I", 121, $unkown_todo);
    $ret[] = awl_helper(5711.095, "Mg I", 107, $unkown_todo);
    $ret[] = awl_helper(5853.688, "Ba II", 55, $unkown_todo, array("displayClusterBoundaryMarker" => "4"));
    $ret[] = awl_helper(5991.378, "Fe II", 29, $unkown_todo);
    $ret[] = awl_helper(6122.226, "Ca I", 222, $unkown_todo);
    $ret[] = awl_helper(6141.7278, "Ba II", 113, 2);
    $ret[] = awl_helper(6162.180, "Ca I", 222, 4);
    $ret[] = awl_helper(6245.620, "Sc II", 30, 4);
    $ret[] = awl_helper(6347.095, "Si II", 54, 4);
    $ret[] = awl_helper(6416.928, "Fe II", 47.5, $unkown_todo);
    $ret[] = awl_helper(6496.908, "Ba II", 98, $unkown_todo);

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


    $ret = wavelengthInfo_getPolyfilledItemArray($ret, array("must_include" => true));
    
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
    $ret[] = awl_coronalLine(3800.8,  'Fe IX', 14);
    $ret[] = awl_coronalLine(3986.8,  'Fe XI', 21);
    $ret[] = awl_coronalLine(4087.1,  'Ca XIII', 105);
    $ret[] = awl_coronalLine(4231.2,  'Ni XII', 68);
    $ret[] = awl_coronalLine(4311.8,  'Fe X', 5);
    $ret[] = awl_coronalLine(4359.4,  'Fe IX', 9 );
    $ret[] = awl_coronalLine(4413,    'Ar XIV', 83);
    $ret[] = awl_coronalLine(4566.2,  'Fe XI', 6 );
    $ret[] = awl_coronalLine(4585.3,  'Fe IX', 4);
    $ret[] = awl_coronalLine(4744,    'Ni XVII', 8);
    $ret[] = awl_coronalLine(5116.03, 'Ni XIII', 114);
    $ret[] = awl_coronalLine(5302.86, 'Fe XIV', 1481);
    $ret[] = awl_coronalLine(5446.0,  'Ca XV', 95);
    $ret[] = awl_coronalLine(5694.42, 'Ca XV', 186);
    $ret[] = awl_coronalLine(6374.56, 'Fe X', 163);
    $ret[] = awl_coronalLine(6701.47, 'Ni XV', 216 );       

    return $ret;
  }

function getMagneticWavelengths(){
  $ret = array();
  // https://articles.adsabs.harvard.edu/pdf/1973SoPh...28....9H
  $ret[] =  awl_notImportant(3598.982, 87, "~ Fe I (magnetic)");
  $ret[] =  awl_notImportant(3712.948, 111,  "Cr II (magnetic)");
  $ret[] =  awl_notImportant(4070.278, 66, "Mn I (magnetic)");
  $ret[] =  awl_notImportant(4116.477, 60,  "~ V I (magnetic)");
  $ret[] =  awl_notImportant(4080.880, 61, "Fe I (magnetic)");
  $ret[] =  awl_notImportant(4210.355, 183, "Fe I (magnetic)");
  $ret[] =  awl_notImportant(4220.051, 48, "V II (magnetic)");
  $ret[] =  awl_notImportant(4654.730, 171, "~Fe I, Cr I (magnetic)");
  $ret[] =  awl_notImportant(5220.894, 30, "~~Cr I (magnetic)");
  $ret[] =  awl_notImportant(5807.787, 7, "~Fe I (magnetic)");
  $ret[] =  awl_notImportant(6258.585, 14, "V I (magnetic)");
  return $ret;
}



  function getFurtherWavelengthWorthyToLabelOnScreenButNotWithDedicatedButtons(){
    $ret = array();    

    $ret[] = awl_notImportant(3500.335, 90, "Ti II");
    $ret[] = awl_notImportant(3500.857, 163, "Ni I");
    $ret[] = awl_notImportant(3500.715, 84, "~Co II");
    $ret[] = awl_notImportant(3502.291, 111, "Co I");
    $ret[] = awl_notImportant(3502.6, 111, "~Ni I, Co I");
    $ret[] = awl_notImportant(3503.473, 50, "Fe II");
    $ret[] = awl_notImportant(3504.442, 65, "V II, Fe I");
    $ret[] = awl_notImportant(3504.892, 132, "Fe I, Ti II");
    $ret[] = awl_notImportant(3506.328, 140, "Co I");
    $ret[] = awl_notImportant(3506.506, 132, "Fe I");
    $ret[] = awl_notImportant(3507.698, 80, "Ni I");
    $ret[] = awl_notImportant(3508.5, 105, "~Fe I");
    $ret[] = awl_notImportant(3509.853, 105, "Co I, Fe I, Ti II");
    $ret[] = awl_notImportant(3510.327, 489, 'Ni I');
    $ret[] = awl_notImportant(3510.846, 87, 'Ti II');
    $ret[] = awl_notImportant(3511.839, 90, 'Cr II');
    $ret[] = awl_notImportant(3512.646, 132, 'Co I');
    $ret[] = awl_notImportant(3513.825, 307, 'Fe I');
    $ret[] = awl_notImportant(3515.066, 718, 'Ni I');
    $ret[] = awl_notImportant(3518.348, 98, 'Co I');
    $ret[] = awl_notImportant(3519.764, 171, 'Ni I');
    $ret[] = awl_notImportant(3520.5, 114, '~V II, Co I');
    $ret[] = awl_notImportant(3521.270, 381, 'Fe I');
    $ret[] = awl_notImportant(3521.57, 109, "~Co I");
    $ret[] = awl_notImportant(3524.536, 1271, 'Ni I');
    $ret[] = awl_notImportant(3526.170, 422, 'Fe I');
    $ret[] = awl_notImportant(3526.847, 209, 'Co I');
    $ret[] = awl_notImportant(3527.795, 107, 'Fe I'); 
    $ret[] = awl_notImportant(3529.823, 148, 'Fe I');
    $ret[] = awl_notImportant(3532.120, 101, 'Mn I');
    $ret[] = awl_notImportant(3533.203, 223, 'Fe I');
    $ret[] = awl_notImportant(3535.412, 79, "Ti II");
    $ret[] = awl_notImportant(3536.567, 189, "Fe I");
    $ret[] = awl_notImportant(3537.903, 107, "Fe I");
    $ret[] = awl_notImportant(3540.126, 93, "Fe I");
    $ret[] = awl_notImportant(3541.095, 214, "Fe I");
    $ret[] = awl_notImportant(3542.090, 224, "Fe I");
    $ret[] = awl_notImportant(3545.644, 108, "Fe I");
    $ret[] = awl_notImportant(3547.799, 124, "Mn I");
    $ret[] = awl_notImportant(3548.033, 107, "Mn I, Fe I");
    $ret[] = awl_notImportant(3548.190, 139, "Ni I, Mn I");
    $ret[] = awl_notImportant(3552.845, 120, "Fe I");
    $ret[] = awl_notImportant(3553.483, 96, "Ni I");
    $ret[] = awl_notImportant(3553.746, 116, "Fe I");


    $ret[] = awl_notImportant(3554.122, 127, "Fe I");
    $ret[] = awl_notImportant(3554.937, 404, 'Fe I');
    $ret[] = awl_notImportant(3556.803, 143, "V II");
    $ret[] = awl_notImportant(3556.896, 243, "Fe I");

    $ret[] = awl_notImportant(3558.532, 485, 'Fe I, Sc II');
    $ret[] = awl_notImportant(3559.464, 94, '~Fe I');
    $ret[] = awl_notImportant(3560.589, 62, 'V II');
    $ret[] = awl_notImportant(3560.897, 82, 'Co I');
    $ret[] = awl_notImportant(3561.582, 58, 'Ti II');
    $ret[] = awl_notImportant(3561.757, 77, 'Ni I');
    $ret[] = awl_notImportant(3565.396, 990, 'Fe I');
    $ret[] = awl_notImportant(3566.383, 458, 'Ni I');
    $ret[] = awl_notImportant(3567.72, 110, '~Sc II, Fe I');
    $ret[] = awl_notImportant(3569.384, 116, 'Co I');
    $ret[] = awl_notImportant(3570.044, 1380, 'Fe I');
    $ret[] = awl_notImportant(3571.875, 237, 'Ni I');
    $ret[] = awl_notImportant(3572.478, 106, 'Zr II');
    $ret[] = awl_notImportant(3572.573, 112, 'Sc II');
    $ret[] = awl_notImportant(3573.735, 84, 'Ti II');
    $ret[] = awl_notImportant(3574.967, 90, 'Co I');
    $ret[] = awl_notImportant(3576.35, 116, '~Sc II');
    $ret[] = awl_notImportant(3576.766, 87, 'Fe I, Ni II');
    $ret[] = awl_notImportant(3577.875, 105, 'Mn I');
    $ret[] = awl_notImportant(3578.693, 488, 'Cr I');
    $ret[] = awl_notImportant(3580.927, 54, 'Sc II');
    $ret[] = awl_notImportant(3581.209, 2144, 'Fe I');
    $ret[] = awl_notImportant(3583.339, 122, 'Fe I');
    $ret[] = awl_notImportant(3583.697, 112, 'Fe I, V I');
    $ret[] = awl_notImportant(3584.520, 44, 'Y II');
    $ret[] = awl_notImportant(3584.661, 182, 'Fe I');
    $ret[] = awl_notImportant(3585.339, 839, 'Fe I, Cr II');
    $ret[] = awl_notImportant(3585.714, 168, 'Fe I');
    $ret[] = awl_notImportant(3586.118, 122, 'Fe I');
    $ret[] = awl_notImportant(3586.544, 74, 'Mn I');
    $ret[] = awl_notImportant(3586.990, 532, 'Fe I');
    $ret[] = awl_notImportant(3587.230, 250, 'Co I, Fe I');
    $ret[] = awl_notImportant(3587.617, 110, 'Fe I?');
    $ret[] = awl_notImportant(3587.760, 112, 'Fe I');
    $ret[] = awl_notImportant(3587.943, 129, 'Ni I, Zr II');
    $ret[] = awl_notImportant(3588.6, 161, '~Fe I');
    $ret[] = awl_notImportant(3589.112, 104, 'Fe I');
    $ret[] = awl_notImportant(3589.461, 97, 'Fe I');
    $ret[] = awl_notImportant(3589.632, 108, 'Sc II');
    $ret[] = awl_notImportant(3589.767, 102, 'V II');
    $ret[] = awl_notImportant(3590.489, 136, 'Sc II');
    $ret[] = awl_notImportant(3592.027, 75, 'V II');
    $ret[] = awl_notImportant(3593.495, 436, 'Cr I');
    $ret[] = awl_notImportant(3594.638, 146, 'Fe I');
    $ret[] = awl_notImportant(3596.054, 95, 'Ti II');
    $ret[] = awl_notImportant(3597.712, 181, 'Ni I');
    $ret[] = awl_notImportant(3602.085, 103, 'Co I, Fe I');
    $ret[] = awl_notImportant(3602.544, 172, '~Fe I');
    $ret[] = awl_notImportant(3603.210, 119, 'Fe I');
    $ret[] = awl_notImportant(3603.8, 155, '~Cr II, Fe I');
    $ret[] = awl_notImportant(3605.339, 495, 'Cr I, Co I');
    $ret[] = awl_notImportant(3606.694, 271, 'Fe I');
    $ret[] = awl_notImportant(3608.869, 1046, 'Fe I');
    $ret[] = awl_notImportant(3610.166, 231, 'Fe I, Ti I');
    $ret[] = awl_notImportant(3610.48, 250, '~Ti I');
    $ret[] = awl_notImportant(3612.075, 118, 'Fe I');
    $ret[] = awl_notImportant(3612.744, 160, 'Ni I');
    $ret[] = awl_notImportant(3613.13, 139, '~Zr II, Fe I, Cr II');
    $ret[] = awl_notImportant(3613.85, 194, '~Sc II');
    $ret[] = awl_notImportant(3618.777, 1410, 'Fe I');
    $ret[] = awl_notImportant(3619.400, 568, 'Ni I');
    $ret[] = awl_notImportant(3621.201, 72, 'V II, Co II');
    $ret[] = awl_notImportant(3621.467, 140, 'Fe I');
    $ret[] = awl_notImportant(3622.009, 127, 'Fe I');
    $ret[] = awl_notImportant(3662.240, 94, "Ti II");
    $ret[] = awl_notImportant(3623.192, 105, 'Fe I');
    $ret[] = awl_notImportant(3624.733, 132, 'Ni I');
    $ret[] = awl_notImportant(3627.813, 98, 'Co I');
    $ret[] = awl_notImportant(3628.707, 57, 'Y II');
    $ret[] = awl_notImportant(3630.754, 133, 'Ca I, Sc II');
    $ret[] = awl_notImportant(3631.475, 1364, 'Fe I, Cr II');
    $ret[] = awl_notImportant(3632.049, 117, 'Fe I');
    $ret[] = awl_notImportant(3634.332, 136, 'Fe I');
    $ret[] = awl_notImportant(3641.335, 109, 'Ti II');
    $ret[] = awl_notImportant(3645.313, 132, 'Sc II');
    $ret[] = awl_notImportant(3642.806, 150, "Sc II");
    $ret[] = awl_notImportant(3645.497, 90, 'Fe I');    
    $ret[] = awl_notImportant(3645.827, 103, 'Fe I');    
    $ret[] = awl_notImportant(3644.417, 141, "Ca I");
    $ret[] = awl_notImportant(3647.851, 970, "Fe I");
    $ret[] = awl_notImportant(3664.405, 103, 'Fe I');

    $ret[] = awl_notImportant(3705.577, "Fe I", 562);
    $ret[] = awl_notImportant(3706.220, "Ti II", 290);


    $ret[] = awl_notImportant(5269.55, 478, "Fe I");
    $ret[] = awl_notImportant(5270.3, 255, '~Ca I, Fe I');

    $ret[] = awl_notImportant(5273.170, 103, 'Fe I');
    $ret[] = awl_notImportant(5273.389, 104, 'Fe I');
    $ret[] = awl_notImportant(5281.7, 164, "~Ni I, Fe I");
    $ret[] = awl_notImportant(5283.5, 212, "~Ti I, Fe I");


    $ret[] = awl_notImportant(3642.806, "Sc II", 150);

    $ret[] = awl_notImportant(3647.851, "Fe I", 970);

    $ret[] = awl_notImportant(3719.947, "Fe I", 1664);
    $ret[] = awl_notImportant(3734.874, "Fe I", 3027);
    $ret[] = awl_notImportant(3745.574, "Fe I", 1202);
    $ret[] = awl_notImportant(3749.49, "Fe I", 1907);    
    $ret[] = awl_notImportant(3758.245, "Fe I", 1647);  
    $ret[] = awl_notImportant(3763.803, "Fe I", 829);
    $ret[] = awl_notImportant(3767.204, 820, "Fe I");
    $ret[] = awl_notImportant(3799.558, 622, "Fe I");
    $ret[] = awl_notImportant(3810.22, 1000, "CN");
    $ret[] = awl_notImportant(3825.89, 1519, "Fe I");
    $ret[] = awl_notImportant(3878.580, 724, "Fe I");
    $ret[] = awl_notImportant(3886.294, 920, "Fe I");
    $ret[] = awl_notImportant(3922.923, 414, "Fe I");
    $ret[] = awl_notImportant(3927.933, 187, "Fe I");
    $ret[] = awl_notImportant(4045.825, 1174,  "Fe I");
    $ret[] = awl_notImportant(4063.605, 787, "Fe I");
    $ret[] = awl_notImportant(4071.749, 723, "Fe I");
    $ret[] = awl_notImportant(4092.396, 108, "Fe I");
    $ret[] = awl_notImportant(4092.669, 115, "Ca I, V I");
    $ret[] = awl_notImportant(4143.878, 466, "Fe I");
    $ret[] = awl_notImportant(4198.3, 234, "~ Fe I");
    $ret[] = awl_notImportant(4202.040, 326, "Fe I");
    $ret[] = awl_notImportant(4271.774, 756, "Fe I");
    $ret[] = awl_notImportant(4325.775, 793, "Fe I");
    $ret[] = awl_notImportant(4354.615, 70, "Se II");
    $ret[] = awl_notImportant(4355.093, 104, "Ca I");
    $ret[] = awl_notImportant(4358.718, 75, "Y II");
    $ret[] = awl_notImportant(4359.623, 139, "Ni I");
    $ret[] = awl_notImportant(4367.594, 143, "Fe I, CH");
    $ret[] = awl_notImportant(4375.944, 152, "Fe I");
    $ret[] = awl_notImportant(4371.286, 110, "Cr I");

    $ret[] = awl_notImportant(4427.317, 147, "Fe I");
    $ret[] = awl_notImportant(4431.360, 30, "Sc II");
    $ret[] = awl_notImportant(4454.793, 176, "Ca I");
    $ret[] = awl_notImportant(4476.05, 152, "~ Fe I");
    $ret[] = awl_notImportant(4494.573, 139, "Fe I");
    $ret[] = awl_notImportant(4581.519, 201, "Ca I, Fe I, Co I");
    $ret[] = awl_notImportant(4878.2, 187, "~ Ca I, Fe I");

    $ret[] = awl_notImportant(5188.698, 202, 'Ti II, Ca I');
    $ret[] = awl_notImportant(5215.188, 116, 'Fe I');
    $ret[] = awl_notImportant(5226.545, 94, 'Ti II');
    $ret[] = awl_notImportant(5232.952, 346, 'Fe I');
    $ret[] = awl_notImportant(5250.216, 62, 'Fe I (magnetic)');
    $ret[] = awl_notImportant(5264.808, 45, 'Fe II');
    $ret[] = awl_notImportant(5269.550, 478, 'Fe I');
    $ret[] = awl_notImportant(5276.071, 152, '~ Fe II, Cr I, Co I');
    $ret[] = awl_notImportant(5300.751, 56, "Cr I");
    $ret[] = awl_notImportant(5336.794, 71, 'Ti II');
    $ret[] = awl_notImportant(5332.665, 45, 'V II');
    $ret[] = awl_notImportant(5362.8, 110, '~ Fe I, Co I, Fe II');
    $ret[] = awl_notImportant(5341.1, 180, '~Fe I, Mn I, Sc I');
    $ret[] = awl_notImportant(5345.807, 107, 'Cr I');
    $ret[] = awl_notImportant(5383.380, 240, 'Fe I');
    $ret[] = awl_notImportant(5364.880, 133, 'Fe I');
    $ret[] = awl_notImportant(5393.176, 153, 'Fe I');
    $ret[] = awl_notImportant(5397.141, 239, 'Fe I');
    $ret[] = awl_notImportant(5405.785, 266, 'Fe I');
    $ret[] = awl_notImportant(5400.511, 143, 'Fe I');
    $ret[] = awl_notImportant(6409.799, 154, 'Cr I');
    $ret[] = awl_notImportant(5410.918, 169, 'Fe I');
    $ret[] = awl_notImportant(5404.145, 239, 'Fe I');
    $ret[] = awl_notImportant(5415.210, 212, 'Fe I');
    $ret[] = awl_notImportant(5424.080, 239, 'Fe I');
    $ret[] = awl_notImportant(5429.77, 285, "~Fe I");
    $ret[] = awl_notImportant(5432.548, 46, 'Mn I');
    $ret[] = awl_notImportant(5432.955, 72, 'Fe I');
    $ret[] = awl_notImportant(5434.534, 184, 'Fe I');
    $ret[] = awl_notImportant(5446.924, 238, 'Fe I');
    $ret[] = awl_notImportant(5463.289, 118, 'Fe I');
    $ret[] = awl_notImportant(5455.465, 112,  'Fe I');
    $ret[] = awl_notImportant(5455.624, 219,  'Fe I');
    $ret[] = awl_notImportant(5470.636, 46, 'Mn I');
    $ret[] = awl_notImportant(5473.910, 80, 'Fe I');
    $ret[] = awl_notImportant(5476.921, 164, 'Ni I');
    $ret[] = awl_notImportant(5497.4, 128, '~Fe I (Y II)');
    $ret[] = awl_notImportant(5501.477, 115, 'Fe I');
    $ret[] = awl_notImportant(5506.791, 120, 'Fe I');

    $ret[] = awl_notImportant(5512.989, 94, 'Ca I');
    $ret[] = awl_notImportant(5525.135, 13, 'Fe II');
    $ret[] = awl_notImportant(5525.552, 102, 'Fe I');
    $ret[] = awl_notImportant(5528.418, 293, 'Mg I');
    $ret[] = awl_notImportant(5535.51, 113, "~ Fe I, Ba I");
    $ret[] = awl_notImportant(5554.900, 102, 'Fe I');
    $ret[] = awl_notImportant(5572.851, 205, 'Fe I');
    $ret[] = awl_notImportant(5586.771, 245, 'Fe I');

    $ret[] = awl_notImportant(5594.471, 117, 'Ca I');

    $ret[] = awl_notImportant(5598.3, 200, '~Ca I, Fe I');
    $ret[] = awl_notImportant(5602.864, 215, 'Ca I, Fe I');

    $ret[] = awl_notImportant(5615.658, 288, "Fe I");
    $ret[] = awl_notImportant(5658.668, 222, "Fe I");


    $ret[] = awl_notImportant(5669.040, 34, "Sc II");
    $ret[] = awl_notImportant(5682.647, 104, "Na I");
    $ret[] = awl_notImportant(5688.217, 121, "Na I");

    

    $ret[] = awl_notImportant(5701.557, 86, "Fe I");
    $ret[] = awl_notImportant(5709.386, 103, "Fe I");
    $ret[] = awl_notImportant(5711.09, 107, "Mg I");
    $ret[] = awl_notImportant(5727.057, "V I", 37);
    $ret[] = awl_notImportant(5754.666, 73, "Ni I");
    $ret[] = awl_notImportant(5763.002, "Fe I", 101);
    $ret[] = awl_notImportant(5790.990, 74, "Cr I, Fe I");
    $ret[] = awl_notImportant(5857.459, "Ca I", 132);
    $ret[] = awl_notImportant(5883.814, "Fe I", 95);

    $ret[] = awl_notImportant(5905.680, 58, "Fe I"); 
    $ret[] = awl_notImportant(5914.17, 139, "~Fe I");
    $ret[] = awl_notImportant(5930.191, 86, "Fe I");
    $ret[] = awl_notImportant(5934.665, 78, "Fe I");

    $ret[] = awl_notImportant(5983.688, 68, "Fe I");
    $ret[] = awl_notImportant(5984.826, 84, "Fe I");
    $ret[] = awl_notImportant(5997.782, 67, "Fe I");

    $ret[] = awl_notImportant(6003.022, 86, "Fe I");
    $ret[] = awl_notImportant(6013.497, 86, "Mn I");
    $ret[] = awl_notImportant(6016.78, 92, "~Mn I, Fe I");
    $ret[] = awl_notImportant(6020.186, 94, "Fe I");
    $ret[] = awl_notImportant(6021.803, 96, "Mn I");
    $ret[] = awl_notImportant(6024.0, 117, "~Fe I");
    $ret[] = awl_notImportant(6027.0, 61, "Fe I");
    $ret[] = awl_notImportant(6042.104, 51, "Fe I");
    $ret[] = awl_notImportant(6056.013, 73, "Fe I");
    $ret[] = awl_notImportant(6065.494, 115, "Fe I");    

    $ret[] = awl_notImportant(6078.499, 91, "Fe I");
    $ret[] = awl_notImportant(6079.016, 55, "Fe I");
    $ret[] = awl_notImportant(6096.671, 36, "Fe I");
    $ret[] = awl_notImportant(6102.183, 84,  'Fe I');
    $ret[] = awl_notImportant(6102.727, 135, 'Ca I');
    $ret[] = awl_notImportant(6103.190, 89,  '~Fe I');
    $ret[] = awl_notImportant(6108.125, 60, 'Ni I');
    $ret[] = awl_notImportant(6111.078, 36, 'Ni I');
    $ret[] = awl_notImportant(6113.329, 17, 'Fe II');
    $ret[] = awl_notImportant(6116.22, 65, '~Ni I, Fe I');

    $ret[] = awl_notImportant(6136.624, 1637, "Fe I");
    $ret[] = awl_notImportant(6137.702, 129, 'Fe I');
    $ret[] = awl_notImportant(6141.727, 113, "Ba II, Fe I");
    $ret[] = awl_notImportant(6147.79, 76, 'Fe II, Fe I');
    $ret[] = awl_notImportant(6151.623, 41, 'Fe I');
    $ret[] = awl_notImportant(6155.17, 72, '~Si I, Fe II');
    $ret[] = awl_notImportant(6163.754, 49, 'Ca I');
    $ret[] = awl_notImportant(6165.363, 33, 'Fe I');
    $ret[] = awl_notImportant(6166.440, 54, 'Ca I');
    $ret[] = awl_notImportant(6169.044, 85, 'Ca I');
    $ret[] = awl_notImportant(6169.564, 98, 'Ca I');
    $ret[] = awl_notImportant(6170.516, 66, 'Fe I (Ni I)');
    $ret[] = awl_notImportant(6173.341, 50, 'Fe I');
    $ret[] = awl_notImportant(6175.370, 36, 'Ni I');
    $ret[] = awl_notImportant(6176.816, 50, 'Ni I');
    $ret[] = awl_notImportant(6180.209, 40, 'Fe I');
    $ret[] = awl_notImportant(6191.186, 56, 'Ni I');
    $ret[] = awl_notImportant(6191.571, 110, 'Fe I');
    $ret[] = awl_notImportant(6200.321, 55, 'Fe I');
    $ret[] = awl_notImportant(6213.437, 61, "Fe I");
    
    $ret[] = awl_notImportant(6219.287, 82, "Fe I");
    $ret[] = awl_notImportant(6232.648, 76, 'Fe I');
    $ret[] = awl_notImportant(6237.328, 60, 'Si I');
    $ret[] = awl_notImportant(6240.653, 40, 'Fe I');
    $ret[] = awl_notImportant(6243.823, 43, 'Si I');
    $ret[] = awl_notImportant(6245.620, 30, 'Sc II');
    $ret[] = awl_notImportant(6246.327, 112, 'Fe I');



    $ret[] = awl_notImportant(6247.562, 49, "Fe II");
    $ret[] = awl_notImportant(6252.565, 109, "Fe I");
    $ret[] = awl_notImportant(6254.21, 115, '~Si I, Fe I');
    $ret[] = awl_notImportant(6256.367, 81, 'Fe I, Ni I');
    $ret[] = awl_notImportant(6258.110, 42, 'Ti I');
    $ret[] = awl_notImportant(6229.232, 33, "Fe I");
    $ret[] = awl_notImportant(6230.736, 151, "Fe I, V I");
    $ret[] = awl_notImportant(6238.390, 41, "Fe II (Si I)");
    $ret[] = awl_notImportant(6258.713, 43, 'Ti I');
    $ret[] = awl_notImportant(6261.106, 40, 'Ti I');
    $ret[] = awl_notImportant(6265.141, 72, 'Fe I');
    $ret[] = awl_notImportant(6270.231, 46, 'Fe I');
    $ret[] = awl_notImportant(6290.974, 66, 'Fe I');
    $ret[] = awl_notImportant(6297.799, 65, 'Fe I');
    $ret[] = awl_notImportant(6301.508, 127, 'Fe I');
    $ret[] = awl_notImportant(6302.499, 83, 'Fe I');
    $ret[] = awl_notImportant(6314.668, 67, 'Ni I');
    $ret[] = awl_notImportant(6315.314, 52, 'Fe I');
    $ret[] = awl_notImportant(6318.027, 96, 'Fe I');
    $ret[] = awl_notImportant(6318.61, 49, 'Ca I');
    $ret[] = awl_notImportant(6318.708, 37, 'Mg I');
    $ret[] = awl_notImportant(6322.694, 75, 'Fe I');
    $ret[] = awl_notImportant(6327.604, 36, 'Ni I');
    $ret[] = awl_notImportant(6335.337, 103, 'Fe I');
    $ret[] = awl_notImportant(6336.830, 121, 'Fe I');
    $ret[] = awl_notImportant(6338.880, 42, 'Fe I');
    $ret[] = awl_notImportant(6339.118, 44, 'Ni I');
    $ret[] = awl_notImportant(6343.71, 70, 'Ca I');
    $ret[] = awl_notImportant(6344.155, 56, 'Fe I');
    $ret[] = awl_notImportant(6355.035, 62, 'Fe I');
    $ret[] = awl_notImportant(6358.687, 82, 'Fe I');
    $ret[] = awl_notImportant(6361.94, 89, 'Ca I');
    $ret[] = awl_notImportant(6362.350, 23, 'Zn I'); 
    $ret[] = awl_notImportant(6380.750, 40, "Fe I");    
    $ret[] = awl_notImportant(6393.612, 117, "Fe I");
    $ret[] = awl_notImportant(6400.009, 181, "Fe I");
    $ret[] = awl_notImportant(6408.026, 80, 'Fe I');
    $ret[] = awl_notImportant(6411.658, 129, "Fe I");
    $ret[] = awl_notImportant(6414.987, 45, 'Si I');
    $ret[] = awl_notImportant(6416.928, 47.5, "Fe II");
    $ret[] = awl_notImportant(6419.956, 80, 'Fe I');
    $ret[] = awl_notImportant(6421.360, 87, 'Fe I');
    $ret[] = awl_notImportant(6432.683, 38, 'Fe II');

    $ret[] = awl_notImportant(6439.083, 156, "Ca I");
    $ret[] = awl_notImportant(6499.654, 81, "Ca I");
    $ret[] = awl_notImportant(6496.472, 69, "Fe I");
    $ret[] = awl_notImportant(6494.994, 165, "Fe I");
    $ret[] = awl_notImportant(6449.820, 98, "Ca I");
    $ret[] = awl_notImportant(6455.605, 48, 'Ca I');
    $ret[] = awl_notImportant(6456.391, 57, 'Fe II');
    $ret[] = awl_notImportant(6462.6, 216, "~ Ca I, Fe I");
    
    $ret[] = awl_notImportant(6493.788, 133, "Ca I");
    $ret[] = awl_notImportant(6516.083, 61, 'Fe II');
    $ret[] = awl_notImportant(6546.252, 103, "Fe I, Ti I");
    $ret[] = awl_notImportant(6592.926, 123, "Fe I");

    $ret[] = awl_notImportant(6491.6, 45, '~Ti II, Mn I');

    $ret[] = awl_notImportant(6469.192, 52, 'Fe I');
    $ret[] = awl_notImportant(6471.668, 83, 'Ca I');
    $ret[] = awl_notImportant(6475.632, 57, 'Fe I');
    $ret[] = awl_notImportant(6481.878, 63, 'Fe I');
    $ret[] = awl_notImportant(6482.809, 38, 'Ni I');


    $ret[] = awl_notImportant(4783.424, 157, "Mn I");
    $ret[] = awl_notImportant(4786.542, 110, "Ni I, V I");
    $ret[] = awl_notImportant(4786.814, 95, "Fe I");
    $ret[] = awl_notImportant(4789.658, 96, "Fe I");
    $ret[] = awl_notImportant(4806.994, 70, "Ni I");

    $ret[] = awl_notImportant(4762.375, 105, "Mn I (C I)");
    $ret[] = awl_notImportant(4754.039, 130, "Mn I (V I)");

    $ret[] = awl_notImportant(4118.555, 154, "Fe I");
    $ret[] = awl_notImportant(4118.782, 148, "Co I");
    $ret[] = awl_notImportant(4132.067, 404, "Fe I");
    $ret[] = awl_notImportant(4132.908, 123, "Fe I (Sc I)");
    $ret[] = awl_notImportant(4134.6, 300, "~Fe I");
    $ret[] = awl_notImportant(4057.515, 197, "Mg I");
    $ret[] = awl_notImportant(4055.551, 114, "Mn I");
    $ret[] = awl_notImportant(4054.83, 135, "~Fe I");

    $ret[] = awl_notImportant(4018.104, 139, "Mn I");

    $ret[] = awl_notImportant(5780.388, 22, 'Si I');
    $ret[] = awl_notImportant(5780.608, 29, 'Fe I');
    $ret[] = awl_notImportant(5780.812, 29, 'Ti I, Fe I');

    $ret[] =  awl_notImportant(5781.759, 16, "Cr I (magnetic)"); 
    $ret[] =  awl_notImportant(5782.136, 62, "Cu I");
    $ret[] =  awl_notImportant(5781.759, 16, "Cr I (magnetic)"); 
    $ret[] =  awl_notImportant(6302.499, 83, "Fe I (magnetic)");
    
    

    $ret[] =  awl_notImportant(4800.653, 72, "Fe I");

    $ret[] =  awl_notImportant(5534.848, 63, "Fe II");
    $ret[] =  awl_notImportant(5588.764, 141, "Ca I");
    $ret[] =  awl_notImportant(5200.415, 37, "Y II");
    $ret[] =  awl_notImportant(6456.391, 57, "Fe II");
    $ret[] =  awl_notImportant(6416.928, 47.5, "Fe II");
    $ret[] =  awl_notImportant(6169.564, 98, "Ca I");
    $ret[] =  awl_notImportant(5763.002, 101, "Fe I");

    $ret[] =  awl_notImportant(5302.307, 157, 'Fe I');
    $ret[] =  awl_notImportant(5206.1, 216, "~Cr I");
    $ret[] =  awl_notImportant(5862.368, 87, "Fe I");
    $ret[] =  awl_notImportant(5852.228, 36, "Fe I");
    $ret[] =  awl_notImportant(5324.15, 334, "Fe I, Cr I (?)");

    $ret[] =  awl_notImportant(5328.051, 375, 'Fe I');
    $ret[] =  awl_notImportant(5328.332,  74, 'Cr I');
    $ret[] =  awl_notImportant(5328.542, 210, 'Fe I');
    $ret[] =  awl_notImportant(5329.147, 78, 'Cr I');
    $ret[] =  awl_notImportant(5332.665, 45, 'V II');
    $ret[] =  awl_notImportant(5332.908, 96, 'Fe I');
    $ret[] =  awl_notImportant(5334.870, 32, 'Cr II');
    $ret[] =  awl_notImportant(5337.735, 35, '~Fe II, Cr II');
    $ret[] =  awl_notImportant(5336.794, 71, 'Ti II');
    $ret[] =  awl_notImportant(5339.937, 161, 'Fe I');
    


    $ret[] =  awl_notImportant(5020.031, 86, "Ti I (Ca II)");
    $ret[] =  awl_notImportant(5017.584, 90, "Ni I");
    $ret[] =  awl_notImportant(5022.241, 114, "Fe I");
    $ret[] =  awl_notImportant(5027.130, 105, "Fe I");
    $ret[] =  awl_notImportant(5013.74, 55, "~ Ti II, C2");

    $ret[] =  awl_notImportant(6643.638, 83, "Ni I");
    $ret[] =  awl_notImportant(6663.448, 76, "Fe I");
    $ret[] =  awl_notImportant(6677.997, 122, "Fe I");
    $ret[] =  awl_notImportant(6717.687, 120, 'Ca I');
    $ret[] =  awl_notImportant(6750.164, 75, 'Fe I');
    $ret[] =  awl_notImportant(6767.784, 83, 'Ni I');
    $ret[] =  awl_notImportant(6855.166, 85, 'Fe I');
    $ret[] =  awl_notImportant(6914.564, 83, 'Ni I');
    $ret[] =  awl_notImportant(6999.885, 71, 'Fe I');
    $ret[] =  awl_notImportant(7003.574, 81, 'Si I');
    $ret[] =  awl_notImportant(7005.900, 89, 'Si I');
    $ret[] =  awl_notImportant(7016.442, 146, 'Fe I');
    $ret[] =  awl_notImportant(7022.957, 72, 'Fe I');
    $ret[] =  awl_notImportant(7034.910, 80, 'Si I');
    $ret[] =  awl_notImportant(7068.423, 64, 'Fe I');
    $ret[] =  awl_notImportant(7090.390, 73, 'Fe I');
    $ret[] =  awl_notImportant(7122.206, 107, 'Ni I');
    $ret[] =  awl_notImportant(7130.925, 105, 'Fe I');
    $ret[] =  awl_notImportant(7148.150, 157, 'Ca I');
    $ret[] =  awl_notImportant(7164.432, 153, 'Fe I');
    $ret[] =  awl_notImportant(7165.578, 93, 'Si I');
    $ret[] =  awl_notImportant(7187.388, 240, 'Fe I (atm H2O)');
    $ret[] =  awl_notImportant(7202.208, 124, 'Ca I');
    $ret[] =  awl_notImportant(7207.396, 150, 'Fe I');
    $ret[] =  awl_notImportant(7289.188, 116, 'Si I');
    $ret[] =  awl_notImportant(7326.160, 136, 'Ca I');
    $ret[] =  awl_notImportant(7386.336, 94, 'Fe I');
    $ret[] =  awl_notImportant(7387.700, 118, 'Mg I');
    $ret[] =  awl_notImportant(7389.391, 144, 'Fe I');
    $ret[] =  awl_notImportant(7393.609, 112, 'Ni I');
    $ret[] =  awl_notImportant(7400.188, 89, 'Cr I');
    $ret[] =  awl_notImportant(7405.790, 108, 'Si I');
    $ret[] =  awl_notImportant(7409.100, 72, 'Si I');
    $ret[] =  awl_notImportant(7409.352, 98, 'Ni I');
    $ret[] =  awl_notImportant(7411.162, 140, 'Fe I');
    $ret[] =  awl_notImportant(7415.958, 118, 'Si I');

    $ret[] =  awl_notImportant(7422.286, 106,  'Ni I');
    $ret[] =  awl_notImportant(7423.509, 120,  'Si I (N I)');
    $ret[] =  awl_notImportant(7445.758, 178,  'Fe I');
    $ret[] =  awl_notImportant(7462.342, 119,  'Cr I (Fe II)');
    $ret[] =  awl_notImportant(7495.077, 174,  'Fe I');
    $ret[] =  awl_notImportant(7511.031, 221,  'Fe I');
    $ret[] =  awl_notImportant(7531.153, 101,  'Fe I');
    $ret[] =  awl_notImportant(7555.607, 98,   'Ni I');
    $ret[] =  awl_notImportant(7568.906, 90,   'Fe I');
    $ret[] =  awl_notImportant(7586.027, 132,  'Fe I');
    $ret[] =  awl_notImportant(7657.606, 142,  'Mg I');
    $ret[] =  awl_notImportant(7661.198, 79,   'Fe I');
    $ret[] =  awl_notImportant(7664.872, 521,  'K I, atm O2');
    $ret[] =  awl_notImportant(7691.52, 172,   '~atm O2, Mg I');    
    $ret[] =  awl_notImportant(7680.267, 106,  'Si I (Mn I)');
    $ret[] =  awl_notImportant(7698.977, 154,  'K I');
    $ret[] =  awl_notImportant(7714.310, 103,  'Ni I');
    $ret[] =  awl_notImportant(7742.722, 126,  'Fe I');
    $ret[] =  awl_notImportant(7748.284, 103,  'Fe I');
    $ret[] =  awl_notImportant(7771.954, 75,   'O I');
    $ret[] =  awl_notImportant(7727.616, 94,   'Ni I');
    $ret[] =  awl_notImportant(7780.568, 102,  'Fe I');
    $ret[] =  awl_notImportant(7832.208, 150,  'Fe I');
    $ret[] =  awl_notImportant(7937.150, 166,  'Fe I');
    $ret[] =  awl_notImportant(7918.383, 100,  'Si I');
    $ret[] =  awl_notImportant(7944.001, 147,  'Si I (Ti I)');
    $ret[] =  awl_notImportant(7945.858, 185,  'Fe I');
    $ret[] =  awl_notImportant(7932.351, 90,   'Si I');
    $ret[] =  awl_notImportant(7998.953, 172,  'Fe I');
    $ret[] =  awl_notImportant(8046.058, 146,  'Fe I'); 
    $ret[] =  awl_notImportant(8085.175, 150,  'Fe I');
    $ret[] =  awl_notImportant(8098.746, 114,  '~Mg I, atm H2O');
    $ret[] =  awl_notImportant(8183.25, 180,   'Na I');
    $ret[] =  awl_notImportant(8207.749, 64,   'Fe I');
    $ret[] =  awl_notImportant(8194.836, 304,  'Na I');
    $ret[] =  awl_notImportant(8220.388, 221,  'Fe I');
    $ret[] =  awl_notImportant(8248.137, 81,   'Fe I');
    $ret[] =  awl_notImportant(8248.802, 98,   'Ca II');
    $ret[] =  awl_notImportant(8232.319, 91,   'Fe I');
    $ret[] =  awl_notImportant(8327.061, 193,  'Fe I');
    $ret[] =  awl_notImportant(8331.926, 130,  'Fe I');
    $ret[] =  awl_notImportant(8335.150, 114,  'C I');
    $ret[] =  awl_notImportant(8339.413, 109,  'Fe I');
    $ret[] =  awl_notImportant(8346.131, 146,  'Mg I');
    $ret[] =  awl_notImportant(8387.782, 170,  'Fe I');
    $ret[] =  awl_notImportant(8439.581, 79,   'Fe I');
    $ret[] =  awl_notImportant(8468.418, 128,  'Fe I');
    $ret[] =  awl_notImportant(8498.062, 1470, 'Ca II');
    $ret[] =  awl_notImportant(8514.082, 108,  'Fe I');
    $ret[] =  awl_notImportant(8515.122, 79,   'Fe I');
    $ret[] =  awl_notImportant(8542.144, 3670, 'Ca II');
    $ret[] =  awl_notImportant(8556.797, 134,  'Si I');
    $ret[] =  awl_notImportant(8582.271, 86,   'Fe I');
    $ret[] =  awl_notImportant(8611.812, 99,   'Fe I');
    $ret[] =  awl_notImportant(8648.472, 161,  'Si I');
    $ret[] =  awl_notImportant(8662.170, 2600, 'Ca II');
    $ret[] =  awl_notImportant(8674.756, 113,  'Fe I');
    $ret[] =  awl_notImportant(8717.833, 105,  'Mg I');
    $ret[] =  awl_notImportant(8688.642, 268,  'Fe I');
    $ret[] =  awl_notImportant(8717.833, 105,  'Mg I');
    $ret[] =  awl_notImportant(8728.024, 107,  'Si I');
    $ret[] =  awl_notImportant(8736.040, 289,  'Mg I');
    $ret[] =  awl_notImportant(8742.466, 97,   'Si I');
    $ret[] =  awl_notImportant(8752.025, 94,   'Si I');
    $ret[] =  awl_notImportant(8757.199, 91,   'Fe I');
    $ret[] =  awl_notImportant(8763.978, 99,   'Fe I');


    $ret[] =  awl_notImportant(6498.945, 43, 'Fe I');
    $ret[] =  awl_notImportant(6499.654, 81, 'Ca I');
    $ret[] =  awl_notImportant(6516.083, 61, 'Fe II');
    $ret[] =  awl_notImportant(6518.373, 61, 'Fe I');
    $ret[] =  awl_notImportant(6527.215, 53, 'Si I');

    $ret[] =  awl_notImportant(5899.304, 26, 'Ti I');
    $ret[] =  awl_notImportant(5905.680, 58, 'Fe I');
    $ret[] =  awl_notImportant(5892.883, 66, 'Ni I');
    $ret[] =  awl_notImportant(5909.983, 30, 'Fe I');

    $ret = array_merge($ret, getHeliumLines());


    $ret = array_merge($ret, getMagneticWavelengths());
    $ret = array_merge($ret, getCoronalWavelengths());

    $ret = wavelengthInfo_getPolyfilledItemArray($ret, array("must_include" => false));

    return $ret;
  }