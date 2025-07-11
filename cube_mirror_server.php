<?php

// I may have run out of storage on my website...
//   but there is a secondary storage, somewhere...


function cube_mirrorServer_getProxyRoot(){
    // leave the URL empty to disable the feature
    $cube_mirrorServer_baseUrl = ''; 
    return $cube_mirrorServer_baseUrl;
}

function cube_mirrorServer_isConfigured(){    
    return (strlen(cube_mirrorServer_getProxyRoot()) > 10);
}

function cube_mirrorServer_getSlug(){
    return 'mirror-server-1--';
}

function cube_mirrorServer_getImageProxyUrl(){
    return cube_mirrorServer_getProxyRoot().'proxy.php?imgproxy=';
}
function cube_mirrorServer_getMainFileUrl(){
    return cube_mirrorServer_getProxyRoot().'cube_mirror_server.php';
}

function cube_mirrorServer_replaceSlug($s){
    $s = str_replace(cube_mirrorServer_getSlug(), cube_mirrorServer_getImageProxyUrl(), $s);
    return $s;
}


function cube_mirrorServer_getCubeFolderContents($intended_folder){    
    $root = cube_mirrorServer_getMainFileUrl();
    $f = @file_get_contents($root.'?as-mirror-server=1&get-cube-folder-image-contents='.$intended_folder);
    if ($f){
        try {
            $f = json_decode($f);
            for ($i=0; $i<count($f); $i++){
                $f[$i] = 'index.php?imgproxy='.cube_mirrorServer_getSlug().$f[$i];
            };                        
        }catch (Exception $exception){
            $f = array();
        }    

    }else{
        $f = array();
    }
    return $f;
}

if (cube_mirrorServer_isConfigured()){
    if (isset($_GET["as-mirror-server"])){
        $k = 'get-cube-folder-image-contents';
        if (!empty($_GET[$k])){
            $f = $_GET[$k];
            // TODO: sanitize
            echo json_encode(array_merge(glob($f.'/*.jpg'), glob($f.'/*.png')));
        }    
    }
}else{
    // not configured, ignore
}

