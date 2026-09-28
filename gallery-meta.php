<?php

$expected_fields = array(
    'lambda_A',
    'datetime',
    'chemicals',
    'caption',
    'instrumentId',
    'processing-steps',
    'filename-color',
    'filename-mono',
    'prefer-cube',
    'prefer-gamma-x100',
);

$jhtm_separator = '****';

function getGalleryDir(){
      return dirname(__FILE__).'/gallery-processed-disks/';
}


function filenameToJhtmlName($filename){
    $stub1 = str_replace('color', 'mono', basename($filename));
    $stub = str_replace('.jpg', '.jhtm', $stub1);
    return $stub;
}
function Gallery_traverse(){
    global $expected_fields;
    $ret = array();
    foreach (glob(getGalleryDir().'*.jpg') as $filename){
        $stub = filenameToJhtmlName($filename);
        $lambda_A = floatval(explode('-', basename($filename))[0]);
        $isMono = strpos($filename, 'mono')!==false;
        if (!isset($ret[$stub])){
            $ret[$stub] = array();            
            foreach ($expected_fields as $key){
                $ret[$stub][$key] = '';
            }
        }
        $ret[$stub]['lambda_A'] = $lambda_A;
        $ret[$stub][$isMono ? 'filename-mono' : 'filename-color'] = basename($filename);
    }
    return $ret;
}

function Gallery_parseJhtml($filename, $meta = false){
    global $expected_fields, $jhtm_separator;
    $ret = array();
    $ret['json'] = array();
    $ret['html'] = '';
    $ret['valid'] = false;

    if (file_exists($filename)){        
        try {
            $f = explode($jhtm_separator, file_get_contents($filename));
            $ret['json'] = json_decode(trim($f[0]), true);
            $ret['html'] = trim($f[1]);
            $ret['valid'] = true;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }        
    }else{
        // carry on
    }
    foreach ($expected_fields as $key){
        if (!isset($ret['json'][$key])){
            $ret['json'][$key] = '';
            if (false === $meta){
                //carry on
            }else{
                if (isset($meta[$key])){
                    $ret['json'][$key] = $meta[$key];
                }
            }
        }
    }       
    return $ret;
}

function Gallery_saveParsedJhtml($filename, $p){
    global $jhtm_separator;
    $s = json_encode($p["json"], JSON_PRETTY_PRINT)."\r\n".$jhtm_separator."\r\n".trim($p["html"])."\r\n";
    file_put_contents($filename, $s);
}

function Gallery_getList($allow_populate = false){
    if ($allow_populate){
        $t = Gallery_traverse();        
        foreach ($t as $basename => $meta){
            $filename = getGalleryDir().$basename;
            $p = Gallery_parseJhtml($filename, $meta);        
            Gallery_saveParsedJhtml($filename, $p);           
            $t[$basename] = array(
                'json' => $meta,
                'html' => trim($p['html']),
                'source' => 'parse-populate',
                'valid' => $p['valid'],
            );
            foreach ($p['json'] as $k=>$v){
                $t[$basename]['json'][$k] = $v;
            }        
        }
    }else{
        $t = array();
        foreach (glob(getGalleryDir().'*.jhtm') as $jhtm){
            $stub = filenameToJhtmlName($jhtm);
            $t[$stub] = Gallery_parseJhtml($jhtm);
            $t[$stub]['source'] = 'parsed';
        }
    };    

    foreach (array_keys($t) as $key){
        if (empty($t[$key]['json']['datetime'])){
            $d = basename($t[$key]['json']['filename-mono']);
            $d = explode('-', $d);
            array_shift($d);
            $d = implode('-', $d);
            $d = explode('_', $d);
            $d = $d[0];
            $d = explode('-', $d);
            $clock = array_pop($d);
            $d = implode('-', $d);
            $clock = substr($clock, 0, 2).':'.substr($clock, 2);
            $t[$key]['json']['datetime'] = $d.' '.$clock;
        };   
    }

       
    return $t;
}

//Gallery_getList(true);