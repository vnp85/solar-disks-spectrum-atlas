var FS = require('fs');
var Path = require('path');

var evalBasename = 'wavelengths_to_colors.js';
var evalFilename = Path.join(Path.dirname(__filename), evalBasename);
eval(FS.readFileSync(evalFilename)+'');

var absDomain_nm = [100, 2000];
var phpCode = [
    '<?php',
    '// generated code, see >> nodejs '+Path.basename(__filename),
    '// datasource: as indicated in "'+evalBasename+'"',
    'function wavelengthToColor($lambda_A, $colorspace = 1){',   
    '  $extremes_nm_abs1 = array('+absDomain_nm[0]+', '+absDomain_nm[1]+');',
    '  $extremes_nm_colored = array('+Wavelengths_getColoredDomain_nm()[0]+','+Wavelengths_getColoredDomain_nm()[1]+');',
    '  $lambda_A = round($lambda_A);',
    '  $lambda_nm = round($lambda_A / 10);',
    '  $lambda_nm = min($lambda_nm, $extremes_nm_abs1[1]);',
    '  $lambda_nm = max($lambda_nm, $extremes_nm_abs1[0]);',
    '  $lambda_nm_original = $lambda_nm;',
    '  if (0 == $colorspace){ return array(255, 255, 255); };', 
    '  if (2 == $colorspace){ $lambda_nm = min($lambda_nm, $extremes_nm_colored[1]); $lambda_nm = max($lambda_nm, $extremes_nm_colored[0]); };',
    '  $colors = array(',
    '    // nm, r, g, b',
];

for (var lambda_nm = absDomain_nm[0]; lambda_nm <= absDomain_nm[1]; lambda_nm++){
    var s = wlNanoMetersToRGB_raw(lambda_nm);
    console.log(s);
    s = '    array('+lambda_nm+', '+s.join(',')+'),';
    phpCode.push(s);
}
phpCode.push('  );');
phpCode.push('$pink_colors = array(')
for (var lambda_nm = absDomain_nm[0]; lambda_nm <= absDomain_nm[1]; lambda_nm++){
    var s = wlNanoToPinkIrVioletUVRGB_raw(lambda_nm);
    console.log(s);
    s = '    array('+lambda_nm+', '+s.join(',')+'),';
    phpCode.push(s);
}
phpCode.push('  );');
phpCode.push('  $ret = array(255,255,255);');
phpCode.push('  if (3 == $colorspace){');
phpCode.push('    foreach ($pink_colors as $col){ if ($col[0] == $lambda_nm_original){ $ret = array($col[1], $col[2], $col[3]); }; };');
phpCode.push('  }else{ ');
phpCode.push('    foreach ($colors as $col){ if ($col[0] == $lambda_nm){ $ret = array($col[1], $col[2], $col[3]); }; };');
phpCode.push('  };');
phpCode.push('  return $ret;');
phpCode.push('}');


FS.writeFileSync(Path.join(Path.dirname(__filename), 'wavelengths_to_colors.php'), phpCode.join("\r\n"));



