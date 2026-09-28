<?php


$is_test_run = false;
$flat_folder_part = '2026-06-09-0833_7-Sun-10780';
$magic_angle = "-597,125";



$run_argo = array(    
    "dir"          => "F:/0000-flattening/".$flat_folder_part."/cube-jpg/*.jpg",    
    "sunspot-mask" => "F:/0000-flattening/".$flat_folder_part."/debug/sunspot-mask.jpg",
    "angle" => $magic_angle,


    "sunspot-heal-size" => 5,
    "sunspot-heal-extra-rotations" => 1,
    "run-deg-90" => true,

    "angle2" => 0,

    "triggered" => true,
    "exec_all" => false,
    "exec_modulo_by" => 64 /* thread count */,
    "exec_when_modulo_is" => "for",
    "halt-after" => 9999,
);

if ($is_test_run){
    $run_argo["halt-after"] = 1;
    $run_argo["exec_modulo_by"] = 2;
}

$executables = array();

for ($i = 0; $i<$run_argo["exec_modulo_by"]; $i++){
    $run_argo["exec_when_modulo_is"] = $i;
    $instance = realpath(dirname(__FILE__).'/'.'self_flat_instance.php');
    $cmd = 'php "'.$instance.'" ';
    foreach ($run_argo as $k=>$v){
        if (false === $v){
            $v = 'false';
        }
        $cmd .= '--'.$k.' "'.$v.'" ';
    }
    echo $cmd."\r\n";
    $executables[] = $cmd;
    // on windows
    //$cmd = 'start ""  '.$cmd;
    //exec($cmd);
}

foreach (glob(sys_get_temp_dir().'/borges_*.bat') as $filename){
    unlink($filename);
}


$c = 0;
$session = md5(mt_rand().date("U"));
$master_bat_filename = sys_get_temp_dir().'/borges_'.$session.'_'.$c.'.bat';
$master_bat = array();
$c++;


foreach ($executables as $e){
    $bat = sys_get_temp_dir().'/borges_'.$session.'_'.$c.'.bat';   
    $master_bat[] = 'start  "" "'.$bat.'"';
    file_put_contents($bat, $e);
    $c++;
} 
file_put_contents($master_bat_filename, implode("\r\n", $master_bat));

$to_run = '"'.$master_bat_filename.'"';
echo "To run:"."\r\n";
echo $to_run."\r\n";
 
if ($is_test_run){
    exec('start "" '.$master_bat_filename);
}

 