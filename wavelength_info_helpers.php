<?php




function awl_helper($lambda, $caption, $width_mA = "", $photogenyClass = "", $bag = false){
    $ret = array("lambda_A" => $lambda, "caption" => $caption." %wavelength%");
    if ("" === $width_mA){
      // ignore
    }else{
      $ret["width_mA"] = $width_mA;
    }
    if ("" == $photogenyClass){
      // ignore
    }else{
      $ret["photogenyClass"] = $photogenyClass;
    }
    if (is_array($bag)){
        foreach ($bag as $key=>$value){
            $ret[$key] = $value;
        }
    }
    return $ret;
}


function wavelengthInfo_getPolyfilledItem($i, $bag = false){
    if (is_array($bag)){
        foreach ($bag as $key=>$value){
            if (!isset($i[$key])){
                $i[$key] = $value;
            }
        }
    }
    $bare_minimum_mA = 80;
    if (!isset($i["displayImportanceFactor"])){
        $i["displayImportanceFactor"] = 1;
    }
    if (!isset($i["widthForCalculations"])){
      if (isset($i["width_mA"])){
        $i["widthForCalculations"] = $i["width_mA"];
      }else{
        $i["widthForCalculations"] = $bare_minimum_mA;
      }  
    }else{
      // carry on
    }
  
    if (isset($i["photogenyClassForCalculations"])){
      // carry on
    }else{
      if (isset($i["photogenyClass"])){
        $i["photogenyClassForCalculations"] = $i["photogenyClass"];
      }else{
        $i["photogenyClassForCalculations"] = 10;
      }    
    }

    if (0 == $i["photogenyClassForCalculations"]){
        $i["photogenyClassForCalculations"] = 10;
    }
  
    if (!isset($i["displayImportance"])){
      $i["displayImportance"] = ($i["displayImportanceFactor"] * $i["widthForCalculations"]) / (2*$i["photogenyClassForCalculations"]);
    }else{
      // carry on
    }
    
    if (!isset($i["must_include"])){
        $i["must_include"] = false;
    }

    if (!isset($i["loadedFrom"])){
        $i["loadedFrom"] = "n/a";
    }

    $p = 1000;
    $i["displayImportance"] = round($i["displayImportance"]*$p) / $p;

    if (!isset($i["ionized"])){
        $i["ionized"] = false;
        if (isset(($i["max_ionization_level"]))){
            if ($i["max_ionization_level"] > 0){
                $i["ionized"] = true;
            }
        }
        // yes, some of these are equivalent, as far as the strpos is concerned
        $roman_numbers_from_two = array('II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII', 'XIV', 'XV', 'XX');
        foreach ($roman_numbers_from_two as $rom){
            if (strpos($i["caption"], ' '.$rom) !== false){
                $i["ionized"] = true;
            }    
        }
    }

    if (!isset($i["uid"])){
        $i["uid"] = md5(serialize($i) . mt_rand());
    }
    return $i;
}  

function wavelengthInfo_getPolyfilledItemArray($a, $bag = false){
    for ($q =0; $q<count($a); $q++){
        $a[$q] = wavelengthInfo_getPolyfilledItem($a[$q], $bag);
    }
    return $a;
}

function wavelengthInfo_getDistinctBoundaryMarkerValues(&$woi){
    $ret = array();
    foreach ($woi as $i){
        if (isset($i["displayClusterBoundaryMarker"])){
            if (!in_array($i["displayClusterBoundaryMarker"], $ret)){
                $ret[] = $i["displayClusterBoundaryMarker"];
            }
        }
    }   
    $ret[] = 'dummy-to-include-all-items-by-never-stopping-the-loop'; 
    return $ret;
}

function wavelengthInfo_getSpectralOriginatorsOfItem($i){
    if (isset($i["spectralOriginators"])){
        $e = $i["spectralOriginators"];
    }else{
        $e = $i['caption'];
        $e = str_ireplace('G-band', 'Ca ', $e);
        $e = str_ireplace('L-band', 'Fe ', $e);
        $e = str_ireplace('Mg-', 'Mg ', $e);
        $e = str_ireplace('CaK', 'Ca ', $e);
        $e = str_ireplace('CaH', 'Ca ', $e);
        $e = str_ireplace('(', ' (', $e);
        $e = str_replace('~', '', $e);
        $e = str_replace('(', '', $e);
        $e = explode(',', $e);
        $r = array();
        foreach ($e as $ei){
            $ei = explode(' ', trim($ei));            
            $r[] = array_shift($ei);
        }
        $e = implode(',', $r);
    };
    return $e;
}

function wavelengthInfo_getDistinctSpectralOriginators(&$woi){
    $ret = array();
    foreach ($woi as $i){
        foreach (explode(',', wavelengthInfo_getSpectralOriginatorsOfItem($i)) as $e){
            if ($e != ''){
                $ret[] = $e;
            }
        };        
    };
    $ret = array_unique($ret);
    return $ret;    
}

function wavelengthInfo_getClustersByClassIncludingAndDownTo_1best5worst($woi, $n){    
    if ($n < 1){
        $n = 1;
    }
    usort($woi, function ($a, $b){
        return -1 * ($a["displayImportance"] - $b["displayImportance"]);
    });
    $n--;
    $dm = wavelengthInfo_getDistinctBoundaryMarkerValues($woi);
    $ret = array();
    foreach ($woi as $i){
        $ret[] = $i;
        if (isset($i["displayClusterBoundaryMarker"])){
            if ($n < count($dm)){
                if ($i["displayClusterBoundaryMarker"] == $dm[$n]){
                    break;
                }
            }
        }
    }
    return $ret;
}


function wavelengthInfo_getClustersByClassEnumerations($woi){
    $b = wavelengthInfo_getDistinctBoundaryMarkerValues($woi);
    $ret = array();
    for ($i=0; $i<count($b); $i++){
        $ret[] = wavelengthInfo_getClustersByClassIncludingAndDownTo_1best5worst($woi, $i+1);
    }
    return $ret;
}

function wavelengthInfo_getItemsWithSpectralOriginatorsAndIonization($woi, $origi = '*', $ionized = '*'){
    $ret = array();
    foreach ($woi as $i){
        if ($origi == '*'){
            // carry on
        }else{
            $c = $i['caption'];
            $c = str_replace('(', '', $c);
            $c = str_replace('~', '', $c);
            $c = ',' . $c;
            if (false === strpos($c, $origi.' ')){
                continue;
            }
        }

        if ($ionized == '*'){
            // don't care
        }else{
            if ($i['ionized'] != $ionized){
                continue;
            }
        }
        $ret[] = $i;
    };

    return $ret;
}

