<?php

foreach (glob('f:/0000-flattening/*') as $filename){
    foreach (glob($filename.'/cube-jpg/*.jpg') as $subfilename){
        if (strpos(basename($subfilename), '_P0000')!==false){
            $bag = array();
            $bag['src'] = $subfilename;
            $bag['dest'] = $bag['src'];
            $bag['dest'] = str_replace('cube-jpg', 'debug', $bag['dest']);
            $bag['dest'] = str_replace(basename($bag['dest']), 'sunspot-mask.jpg', $bag['dest']);
            if (!file_exists($bag['dest'])){
                copy($bag['src'], $bag['dest']);
                echo 'COPY '.$bag['dest']."\r\n";
            }else{
                echo 'SKIP '.$bag['dest']."\r\n";
            }
            
        }
    }
}