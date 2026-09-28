<?php

function prep_sortable_wavelength_line($a){
    $a = explode('(', $a);
    $a[] = '';
    $a = $a[1];
    $a = explode(',', $a);
    $a = $a[0];
    $a = trim($a);
    if ($a == ''){
        $a = 0;
    }
    $a = floatval($a);
    return $a;
}

$starter = 'start'.'-sorted-section';
$ender = 'end'.'-sorted-section';

foreach (glob(dirname(__FILE__).'/*.php') as $filename){
    if ($filename != __FILE__){
        $f = file_get_contents($filename);
        if (strpos($f, $starter)!==false){        
            $f = file($filename);
            $new_lines = array();
            $inside_sorted_section = false;
            $sorted_section = array();
            foreach ($f as $line){
                $line = str_replace("\t", '  ', $line);
                if (strpos($line, $ender) !== false){
                    $inside_sorted_section = false;                                        
                    usort($sorted_section, function ($a, $b){
                        $a = prep_sortable_wavelength_line($a);
                        $b = prep_sortable_wavelength_line($b);
                        $delta = $a - $b;                        
                        if ($delta > 0){
                            $delta = 1;
                        }
                        if ($delta < 0){
                            $delta = -1;
                        }
                        return $delta;
                    });

                    $last_sorted_line = '';
                    foreach ($sorted_section as $sorted_line){
                        $has_both_quotes = false;
                        if (strpos($sorted_line, '"') !== false){
                            if (strpos($sorted_line, '\'') !== false){
                                $has_both_quotes =  true;
                            }
                        }
                        if ($has_both_quotes){
                            // something is going on
                        }else{
                            $sorted_line = str_replace('"', '\'', $sorted_line);
                        }
                        if (trim($last_sorted_line) != trim($sorted_line)){
                            $last_sorted_line = $sorted_line;
                            $new_lines[] = $sorted_line;
                        }
                    }
                    $sorted_section = array();
                }
                if ($inside_sorted_section){
                    $line = str_replace('$ret[] =  awl_', '$ret[] = awl_', $line);
                    $line = str_replace(',  \'', ', \'', $line);
                    $line = str_replace(',   \'', ', \'', $line);
                    $line = str_replace(',    \'', ', \'', $line);
                    $line = str_replace(',     \'', ', \'', $line);
                    $line = str_replace(',\'', ', \'', $line);
                    if (trim($line) == ''){
                        $line = trim($line);
                    }
                    // commmas to be a nice table
                    if (strpos($line, '=')!== false){
                        $b = 42;
                        $line_1_min_len = 4;
                        if (strpos($line, 'awl_infraredLineWithIntensity')!==false){
                            $b += 10;
                            $line_1_min_len += 6;
                        }
                        
                        $line = explode(',', $line);
                        $line[1] = trim($line[1]);                                                
                        $line[0] = rtrim($line[0]);
                        while (strlen($line[0]) < $b){
                            $line[0] .= ' ';
                        }
                        while (strlen($line[1]) < $line_1_min_len){
                            $line[1] = ' '.$line[1];
                        }
                        if (count($line) > 2){
                            $line_2_has_enter = false;
                            $line2_min_len = $line_1_min_len;
                            foreach (array("\r\n", "\r", "\n") as $l2enter){
                                if (false === $line_2_has_enter){
                                    if (strpos($line[2], $l2enter)!==false){
                                        $line_2_has_enter = $l2enter;
                                        $line2_min_len += 2;
                                    }
                                }
                            } 
                            $line[2] = ' '.trim($line[2]);
                            while (strlen($line[2]) < $line2_min_len){
                                $line[2] = ' '.$line[2];
                            }
                            if ($line_2_has_enter){
                                $line[2] .= $line_2_has_enter;
                            }
                        }
                        $line = implode(',', $line);
                        for ($q = 0; $q<60; $q++){
                            $line = str_replace(' ,', ', ', $line);
                        }
                    }
                    $sorted_section[] = $line;
                }else{
                    $new_lines[] = $line;
                }
                if (strpos($line, $starter) !== false){
                    $inside_sorted_section = true;
                }
            }

            file_put_contents($filename, implode('', $new_lines));
        }    
    }
}