<?php

foreach (glob(dirname(__FILE__).'/cubes-info/*.json') as $filename){
    //var_dump($filename);
    for ($trials = 0; $trials < 10; $trials++){
        $f = file_get_contents($filename);
        $j = false;
        try {
            $j = json_decode($f, true);        
        }catch(Exception $e){
            //
        }
        if (!$j){
            echo "Parse error: ".$filename."\r\n";
            // the file could not be parsed, so perhaps it contains "forbidden math"
            $esc_q = 'ew5r4thwrthwhhgrtwrtehwrtyhryhwr5';
            $f = str_replace('\\"', $esc_q, $f);

            $in_string = false;            
            $word = '';
            $expressions = array();
            for ($i=0; $i<strlen($f); $i++){                
                $c = substr($f, $i, 1);
                $handled = false;                    

                if ('"' == $c){
                    $in_string = !$in_string;
                    if (!$in_string){
                        $word = '';
                    }
                    $handled = true;
                }
                if (!$in_string){                    
                    if (':' == $c){
                        $word = '';
                        $handled = true;
                    }
                    if (in_array($c, array(',', '}', ']'))){
                        // could be an expression
                        $handled = true;
                        if (trim($word) == ''){
                            $word = '';
                        }
                        if ($word != ''){
                            if ((strpos($word, '+')!==false) || (strpos($word, '-')!==false)){
                                $invalid_value = 'ewrhiokwej5r0igojwer0tygh45';
                                $eval_v = $invalid_value;
                                try {
                                    eval('$eval_v = '.$word.';');
                                }catch(Exception $e){
                                    //
                                }
                                if ($invalid_value != $eval_v){
                                    var_dump($word . ' expression?');
                                    $expressions[] = array($word.$c, $eval_v.$c);
                                }                                                            
                            }
                        }
                        $word = '';
                    }
                    if (!$handled){
                        $word .= $c;
                    }                    
                }
            }

            $f = str_replace($esc_q, '\\"', $f);
            foreach ($expressions as $mati){
                $f = str_replace($mati[0], $mati[1], $f);
            }
            file_put_contents($filename, $f);
        }
    }
}