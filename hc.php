<?php

require_once("hypercube.php");
require_once("cube_utils.php");


$argo = array(
    'pack' => false,
    'unpack' => false,    
    'test' => false,    
    'packtest' => false,    
);

function getCubesFromHint($hint){
   $cubes = getSimpleParsedCubeFiles();
   $ret = array();
   foreach ($cubes as $k){
      if (strpos($k['cubeLocation'], $hint)!== false){
        unset($k['lambda_A_at_pixel']);
        $ret[] = $k;
      }
   }
   return $ret;
}

function dehintify($hint){
    if (!$hint){
        return $hint;
    }
    if (strpos($hint, '\\')===false){
        if (strpos($hint, '/')===false){
            $hint = getCubesFromHint($hint);
            if (count($hint) != 1){
                var_dump($hint);
                 die("candidates error");
            }
            $hint = dirname($hint[0]['realpath_of_average_file']);
        }
    }
    return $hint;
}



$prev = '';
foreach ($argv as $a){
    foreach (array_keys($argo) as $key){
        if ($prev === '--'.$key){
            $argo[$key] = $a;
        }
    }    
    $prev = $a;
}

$argo['pack'] = dehintify($argo["pack"]);
$argo['unpack'] = dehintify($argo["unpack"]);
$argo['test'] = dehintify($argo["test"]);
$argo['packtest'] = dehintify($argo["packtest"]);

var_dump($argo);

if ($argo["pack"]){    
    $hc = new HyperCube();
    $hc->packCubeAt($argo["pack"]);
}

if ($argo["unpack"]){
    $hc = new HyperCube();
    $hc->unpackCubeAt($argo["unpack"]);
}
if ($argo["packtest"]){
    $hc1 = new HyperCube();
    $hc1->packCubeAt($argo["packtest"]);
    $argo["test"] = $argo["packtest"];
}

if ($argo["test"]){
    $hc = new HyperCube();
    $hc->openPack($argo["test"].'/hyper.cube');
    $files = $hc->listContents();
    $files_i = count($files)-2;
    $b = $files[$files_i]['basename'];        
    $debug_bag = array(
        "basename" => $b
    );    
    $raw = $argo["test"].'/'.$debug_bag["basename"];
    $debug_bag["hash_packed"] = md5($hc->getBlob($debug_bag["basename"]));
    $debug_bag["hash_raw"]    = md5_file($raw);
    $debug_bag["hash_match"]  = $debug_bag["hash_raw"] == $debug_bag["hash_packed"];
    $debug_bag["raw_size"] = filesize($raw);
    $debug_bag["pack_size"] = $files[$files_i]['blob-size'];
    var_dump($debug_bag);
    if (!$debug_bag["hash_match"]){
        $p = $hc->getBlob($debug_bag["basename"]);
        $r = file_get_contents($raw);
        for ($i=0; $i<strlen($p); $i++){
            $pc = substr($p, $i, 1);
            $rc = substr($r, $i, 1);
            if ($pc != $rc){
                die('First discrepancy at '.$i.' '.ord($pc).' '.ord($rc));
            }
        }
    }
    if ($debug_bag["hash_match"]){        
        echo "\r\nTEST OK\r\n";
    }else{
        echo "\r\nTEST FAIL\r\n";
    }
    
}
