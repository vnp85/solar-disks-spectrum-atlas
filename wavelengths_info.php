<?php

require_once("wavelength_info_helpers.php");

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

      array("lambda_A" => 5889.95, "caption" => "Na doublet",  "photogenyClass" => 2, "ionized" => false),
  
    ));
    $a = wavelengthInfo_getPolyfilledItemArray($a, array("must_include" => true));
    return $a;
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

    $ret[] = array("lambda_A" => 5875.62, "caption" => "He I D3", "width_mA" => 500,"photogenyClass" => 2, "displayImportanceFactor" => 2.5, "ionized" => false); 
    $ret[] = array("lambda_A" => 5889.973, "caption" =>"Na I D2", "width_mA" =>752, "photogenyClass" => 2, "displayImportanceFactor" => 2.5, "ionized" => false); 
    $ret[] = array("lambda_A" => 5895.940, "caption" =>"Na I D1", "width_mA" =>564, "photogenyClass" => 2, "ionized" => false, "displayClusterBoundaryMarker" => "1");

    $ret[] = array("lambda_A" => 6678.151 , "caption" => "He I %wavelength%", "width_mA" => 500,"photogenyClass" => 5, "displayImportanceFactor" => 0.5, "ionized" => false); 
    $ret[] = array("lambda_A" => 6867, "caption" => "He I %wavelength%", "width_mA" => 500,"photogenyClass" => 5, "displayImportanceFactor" => 0.5, "ionized" => false); 
    $ret[] = array("lambda_A" => 5015.6783, "caption" => "He I %wavelength%", "width_mA" => 500,"photogenyClass" => 5, "displayImportanceFactor" => 0.5, "ionized" => false); 
    $ret[] = array("lambda_A" => 4471.5, "caption" => "He I %wavelength%", "width_mA" => 500,"photogenyClass" => 5, "displayImportanceFactor" => 0.5, "ionized" => false); 
    $ret[] = array("lambda_A" => 3888.648, "caption" => "He I %wavelength%", "width_mA" => 500,"photogenyClass" => 5, "displayImportanceFactor" => 0.5, "ionized" => false); 
    $ret[] = array("lambda_A" => 4685.7038, "caption" => "He II %wavelength%", "width_mA" => 500,"photogenyClass" => 5, "displayImportanceFactor" => 0.5, "ionized" => true); 
    $ret[] = array("lambda_A" => 4921.931, "caption" => "He I %wavelength%", "width_mA" => 500,"photogenyClass" => 5, "displayImportanceFactor" => 0.5, "ionized" => false); 
    


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

  function awl_coronalLine($lambda_A, $caption, $irradiance){
    $lambda_A_string = number_format($lambda_A, 1, '.', ""); 
    // may not display in the table

    $ret = array(
      "lambda_A" => $lambda_A, 
      "caption" => $caption." (corona) ".$lambda_A_string,
      "photogenyClass" => 6 - $irradiance / 1000,
      "widthForCalculations" => 100,
      "must_include" => true,
      "displayImportance" => round($irradiance / 10)
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
    $ret[] = awl_notImportant(3623.192, 105, 'Fe I');
    $ret[] = awl_notImportant(3624.733, 132, 'Ni I');
    $ret[] = awl_notImportant(3627.813, 98, 'Co I');
    $ret[] = awl_notImportant(3628.707, 57, 'Y II');
    $ret[] = awl_notImportant(3630.754, 133, 'Ca I, Sc II');
    $ret[] = awl_notImportant(3631.475, 1364, 'Fe I, Cr II');
    $ret[] = awl_notImportant(3632.049, 117, 'Fe I');
    $ret[] = awl_notImportant(3634.332, 136, 'Fe I');
    $ret[] = awl_notImportant(3641.335, 109, 'Ti II');
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
    $ret[] = awl_notImportant(4358.718, 75, "Y II");
    $ret[] = awl_notImportant(4359.623, 139, "Ni I");
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
    $ret[] = awl_notImportant(5429.77, 285, "~Fe I");
    $ret[] = awl_notImportant(5434.534, 184, 'Fe I');
    $ret[] = awl_notImportant(5446.924, 238, 'Fe I');
    $ret[] = awl_notImportant(5463.289, 118, 'Fe I');
    $ret[] = awl_notImportant(5455.465, 112,  'Fe I');
    $ret[] = awl_notImportant(5455.624, 219,  'Fe I');
    $ret[] = awl_notImportant(5476.921, 164, 'Ni I');
    $ret[] = awl_notImportant(5512.989, 94, 'Ca I'  );
    $ret[] = awl_notImportant(5525.552, 102, 'Fe I');
    $ret[] = awl_notImportant(5528.418, 293, 'Mg I');
    $ret[] = awl_notImportant(5535.51, 113, "~ Fe I, Ba I");
    $ret[] = awl_notImportant(5554.900, 102, 'Fe I');
    $ret[] = awl_notImportant(5572.851, 205, 'Fe I');
    $ret[] = awl_notImportant(5586.771, 245, 'Fe I');

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
    $ret[] = awl_notImportant(5930.191, 86, "Fe I");
    $ret[] = awl_notImportant(5934.665, 78, "Fe I");

    
    $ret[] = awl_notImportant(6065.494, 115, "Fe I");
    $ret[] = awl_notImportant(6136.624, 1637, "Fe I");
    $ret[] = awl_notImportant(6141.727, 113, "Ba II, Fe I");
    $ret[] = awl_notImportant(6219.287, 82, "Fe I");
    $ret[] = awl_notImportant(6247.562, 49, "Fe II");
    $ret[] = awl_notImportant(6252.565, 109, "Fe I");
    $ret[] = awl_notImportant(6229.232, 33, "Fe I");
    $ret[] = awl_notImportant(6230.736, 151, "Fe I, V I");
    $ret[] = awl_notImportant(6238.390, 41, "Fe II (Si I)");
    $ret[] = awl_notImportant(6380.750, 40, "Fe I");    
    $ret[] = awl_notImportant(6393.612, 117, "Fe I");
    $ret[] = awl_notImportant(6400.009, 181, "Fe I");
    $ret[] = awl_notImportant(6411.658, 129, "Fe I");
    $ret[] = awl_notImportant(6416.928, 47.5, "Fe II");
    $ret[] = awl_notImportant(6439.083, 156, "Ca I");
    $ret[] = awl_notImportant(6462.6, 216, "~ Ca I, Fe I");
    $ret[] = awl_notImportant(6493.788, 133, "Ca I");
    $ret[] = awl_notImportant(6546.252, 103, "Fe I, Ti I");
    $ret[] = awl_notImportant(6592.926, 123, "Fe I");


    $ret[] =  awl_notImportant(5781.759, 16, "Cr I (magnetic)"); 
    $ret[] =  awl_notImportant(5782.136, 62, "Cu I");
    $ret[] =  awl_notImportant(5781.759, 16, "Cr I (magnetic)"); 
    $ret[] =  awl_notImportant(6302.499, 83, "Fe I (magnetic)");

    $ret[] =  awl_notImportant(5534.848, 63, "Fe II (spot)");
    $ret[] =  awl_notImportant(5588.764, 141, "Ca I");
    $ret[] =  awl_notImportant(5200.415, 37, "Y II");
    $ret[] =  awl_notImportant(6456.391, 57, "Fe II");
    $ret[] =  awl_notImportant(6416.928, 47.5, "Fe II");
    $ret[] =  awl_notImportant(6169.564, 98, "Ca I");
    $ret[] =  awl_notImportant(5763.002, 101, "Fe I");

    $ret[] =  awl_notImportant(5206.1, 216, "~Cr I");
    $ret[] =  awl_notImportant(5862.368, 87, "Fe I");
    $ret[] =  awl_notImportant(5852.228, 36, "Fe I");
    $ret[] =  awl_notImportant(5324.15, 334, "Fe I, Cr I (?)");

    $ret[] =  awl_notImportant(5020.031, 86, "Ti I (Ca II)");
    $ret[] =  awl_notImportant(5017.584, 90, "Ni I");
    $ret[] =  awl_notImportant(5022.241, 114, "Fe I");
    $ret[] =  awl_notImportant(5027.130, 105, "Fe I");
    $ret[] =  awl_notImportant(5013.74, 55, "~ Ti II, C2");


    $ret = array_merge($ret, getMagneticWavelengths());
    $ret = array_merge($ret, getCoronalWavelengths());

    $ret = wavelengthInfo_getPolyfilledItemArray($ret, array("must_include" => false));

    return $ret;
  }