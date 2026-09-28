<?php

$argo = array(
    // these below are a template only, are set when called from the orchestrator
    "dir" => 'l:/2026-05-06/Sun-8900_filter-n99--Skywatcher-OtaR62Ap400F-SHG/2026-05-06-0723_3-Sun-8900/cube-jpg/*.jpg',
    "sunspot-mask" => "D:/20260506_rot_linS_img_C0505_M0125_masked.jpg",
    "triggered" => true,
    "exec_all" => false,
    "exec_modulo_by" => 10,
    "exec_when_modulo_is" => 9,
    "run-deg-90" => false,
    "halt-after" => 1,
    "angle" => "-469,198",
    "angle2" => 0,
    "sunspot-heal-size" => 3,
    "sunspot-heal-extra-rotations" => 0,
);

$prev = '';
foreach ($argv as $a){
    echo $a."\r\n";
    foreach (array_keys($argo) as $k){
        if ($prev == '--'.$k){            
            $v = $a;
            if (is_bool($argo[$k])){
                if ('false' === $a){
                    $v = false;
                }
                if ('true' === $a){
                    $v = true;
                }
                if ('0' === $a){
                    $v = false;
                }
                if ('1' === $a){
                    $v = true;
                }
            }
            if (is_numeric($argo[$k])){
                $v = floatval($v);
            }
            $argo[$k] = $v;
        }
    }
    $prev = $a;
}

$argo["dir"] = str_replace('ASTERIX', '*', $argo["dir"]);
if (strpos($argo["dir"], '*.') === false){
    $argo["dir"] .= '/*.jpg';
}

var_dump($argo);


function isFlatCandidate($filename){
    $ret = true;
    if (strpos(basename($filename), 'average') !== false){
        $ret = false;
    };    
    if (strpos(basename($filename), 'marked') !== false){
        $ret = false;
    };    
    if (strpos(basename($filename), 'avg_twin') !== false){
        $ret = false;
    };    
    return $ret;
}

function flatInstanceCommit($argo, $instance){
    $has_run = false;
    $cmd = 'php "'.realpath(dirname(__FILE__).'/self_flat_impl.php').'" ';
    foreach ($argo as $key=>$value){
        $cmd .= ' --'.$key.' "'.$value.'" ';
    }

    echo 'Instance: '.$instance."\r\n".json_encode($argo, JSON_PRETTY_PRINT)."\r\n";
    
    
    if (!file_exists($argo['output'])){
        echo $cmd."\r\n";
        @mkdir(dirname($argo['sunspot-healed-intermediary-should-be']), 0777, true);
        @mkdir(dirname($argo['output']), 0777, true);            
        if (!empty($argo['debug-folder'])){
            @mkdir($argo['debug-folder'], 0777, true);            
        }
        echo shell_exec($cmd);        
        $has_run = true;
    }
    return $has_run;
};

function runFilename($filename, $global_argo){         
        $argo = array();
        $filename = str_replace("\\", '/', $filename);
        $argo['input'] = $filename;
        $argo['output'] = str_replace('.png', '.jpg', $filename);
        //$argo['debug-folder'] = dirname(str_replace('/cube-jpg/', '/cube-jpg-debug/', $argo['output']));
        $argo['sunspot-healed-intermediary-should-be'] = str_replace('/cube-jpg/', '/cube-jpg-healed/', $argo['output']);
        if (!empty($global_argo["sunspot-mask"])){
            $argo['sunspot-mask'] = $global_argo["sunspot-mask"];
        }
        $argo['output'] = str_replace('/cube-jpg/', '/cube-jpg-flattened/', $argo['output']);
        $argo['angle']  = $global_argo["angle"];
        $argo['angle2'] = $global_argo["angle2"];
        $argo["sunspot-heal-size"] = $global_argo["sunspot-heal-size"];
        $argo["sunspot-heal-extra-rotations"] = $global_argo["sunspot-heal-extra-rotations"];
        $has_run = flatInstanceCommit($argo, 1);

        if ($global_argo["run-deg-90"]){
            if ($has_run){                
                $argo['angle2'] = floatval($argo['angle2'])+90;
                $temp_name = $argo['input'].'.temp';
                copy($argo['output'], $temp_name);            
                $argo['input'] = $temp_name;                
                unlink($argo['output']);                
                flatInstanceCommit($argo, 2);
                unlink($temp_name);
            }
        }
        return $has_run;
}
            


$fileCounter = 0;
foreach (glob($argo["dir"]) as $filename) if (isFlatCandidate($filename)){    
    $needed = false;
    if ($fileCounter % $argo['exec_modulo_by'] == $argo['exec_when_modulo_is']){
        $needed = true;
    }
    if ($argo['exec_all']){
        $needed = true;
    }
    if (strpos($filename, 'M0118')!==false){
         $argo['triggered'] = true;
    };
    if ($argo['halt-after'] <= 0){
        $needed = false;
    }
    if (($argo['triggered'])&&($needed)){
        echo 'running filename: '.$filename."\r\n";
        $start_time = microtime(true);
        $has_run = runFilename($filename, $argo);
        if ($has_run){
            $argo['halt-after']--;
            $end_time = microtime(true);
            $delta_seconds = round($end_time - $start_time);
            echo 'Has run for: '.$delta_seconds." seconds \r\n";
        }
    }    
    $fileCounter++;
}