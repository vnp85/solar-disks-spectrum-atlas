<?php
  // to be run individually
  require_once("main_scripts.php");
?>
<div  align="center"><div align="left" style="width:90%">
    <div align="center">
<table border="0" >
    <tr>
        <td align="center"><a name="gallery-view-top"></a><div class="section-title"><span style="font-size: 150%">Gallery</span></div>
        <td align="right"><button onclick="toggleGalleryView()">x</button></td>
    </tr>
    <tr>
        <td align="center">
           the essence of some wavelengths: <br>raw images, enhanced through continuum subtraction and more
        </td>   
        <td align="center">
            &nbsp;            
        </td>    
    </tr>    
    <tr>
        <td align="center" colspan="2">
            <div>&nbsp;</div>
            <div>&nbsp;</div>
            <div>&nbsp;</div>
            <div>&nbsp;</div>
        </td>
    </tr>    
</table>    
</div>

<?php 

require_once("gallery-meta.php");

foreach (Gallery_getList() as $gi){
    echo '<div>&nbsp;</div>';
    echo '<div>&nbsp;</div>';
    echo '<table style="" border="0"><tr><td></td><td></td><td></td></tr>';
    $home = str_replace(dirname(__FILE__), '', getGalleryDir());
    if (strpos($home, '/') === 0){
        $home = substr($home, 1);
    }
    $mono_url =  $home . $gi['json']['filename-mono'];
    $color_url = $home . $gi['json']['filename-color'];
    $h = md5(serialize($gi));
    
    echo '<tr>'.
      '<td colspan="3" align="center" style="font-size: 150%"><a name="'.$h.'"></a>'.$gi['json']['caption'].'<br>&nbsp;</td>'.
    '</tr>';  
    echo '<tr>'.
      '<td><img class="gallery-img-cube-slice" data-hash="'.$h.'" src="" style="width:100%"></td>'.    
      '<td><a class="gallery-img-link" href="" target="_blank"><img class="gallery-img" data-src="'.$mono_url.'" src="" style="width:100%"></a></td>'.
      '<td><a class="gallery-img-link" href="" target="_blank"><img class="gallery-img" data-src="'.$color_url.'" src="" style="width:100%"></a></td>'.      
      '</tr>';
    $html = $gi['html'];
    $html = str_replace("\r\r", "\r\n\r\n", $html);      
    $html = str_replace("\n\n", "\r\n\r\n", $html);      
    $html = str_replace("\r\n\r\n", '<br><br>', $html);      
    $html = '<br>'.$html;
    $indi = '<div style="margin-left: 20%; margin-right: 20%">';
    echo '<tr>'.
      '<td colspan="3" align="left">'.$indi.$html.'</div></td>'.
      '</tr>';
      
    $load_as_cube_slice = ' <button onclick="Gallery_loadCubeSlice(this, '.$gi['json']['lambda_A'].', \''.$gi['json']['prefer-cube'].'\', '.$gi['json']['prefer-gamma-x100'].')" style="cursor: pointer">&#9788; Load cube slice</button>';
    //$load_as_cube_slice .= ' <button onclick="Gallery_loadCubeSlice(this, '.$gi['json']['lambda_A'].', \"'.$gi['json']['prefer-cube'].'\")" style="cursor: pointer">gamma</button>';

    $seri = array();
    $seri[] = '&nbsp;';
    $seri[] = 'Wavelength: ~'.$gi['json']['lambda_A'].' &Aring;'.$load_as_cube_slice;
    $seri[] = 'Datetime: ~'.$gi['json']['datetime'].' UT';
    $seri[] = 'Chemicals: '.$gi['json']['chemicals'];
    $seri[] = 'Instrument id: '.$gi['json']['instrumentId'].'';
    $seri[] = 'Processing steps: '.$gi['json']['processing-steps'].'';
    echo '<tr>'.
      '<td colspan="3" align="">'.$indi.'<div>'.implode('</div><div>', $seri).'</div>'.'</div>'.'</td>'.
    '</tr>';  
    echo '</table>';  
    //var_dump($gi);
    echo '  
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
            ';
    echo '<hr>';
}
?>
</div></div>
<script>
    setTimeout(function (){
        var l = document.getElementsByClassName("gallery-img");
        for (var i=0; i<l.length; i++){
            l[i].src = Url_proxifyIfNeeded(l[i].getAttribute('data-src'));
            l[i].parentNode.href = Url_proxifyIfNeeded(l[i].getAttribute('data-src'));
        }
    }, 100);
function Gallery_cubeSlice_setGamma(target, value){
    var gammaFilterString = getSvgGammaClosestToPercent(value, true);
    var filterString = '';
    filterString+= ' '+"url('#"+gammaFilterString+"')";
    target.style.filter = filterString; 
    console.log("setting gamma on", arguments, filterString.trim());
}    
function Gallery_loadCubeSlice(caller, lambda_A, preferCube, preferGamma){    
    var defaultGamma = 100;
    if (' ' === preferCube){
        preferCube = false;
    }
    if (preferCube){
        Spectrum_showWavelengthA(lambda_A, preferCube);
    }else{
        Spectrum_showWavelengthA(lambda_A);
    }

    if (' ' == preferGamma){
        preferGamma = false;
    }
    if (preferGamma){
        // trust the source
    }else{
        preferGamma = defaultGamma;
    }

    var p = caller;
    while (p){
        if ((p.tagName+'').toLowerCase() == 'table'){
            break;
        }
        p = p.parentNode;
    }
    setTimeout(function (){
        var d = p.getElementsByClassName("gallery-img-cube-slice");
        if (1 == d[0].getAttribute('data-handled')){
            //already handled
        }else{
            d[0].setAttribute('data-handled', 1);
            var div = document.createElement('div');
            div.align = 'center';
            if (isWavelengthCoveredByCubes(lambda_A)){
                var datetime = (window.CubeViewer_lastLoadedCubeId || '')+'';
                datetime = datetime.replace('cube', '');
                datetime = datetime.split('_').join(' ').trim().split(' ')[0];
                if (datetime.length == 8){
                    datetime = datetime.split('');
                    datetime = datetime[0]+datetime[1]+datetime[2]+datetime[3]+'-'+datetime[4]+datetime[5]+'-'+datetime[6]+datetime[7];
                }
                div.innerHTML = '<em>cube slice, single scan ('+datetime+')</em>';

                var gammaButtons = document.createElement("div");                
                gammaButtons.innerHTML = 'gamma: ';
                ["default", "recommended", "1", "2", "3", "4"].forEach(function (g, gi){
                    var v = g;          
                    var handled = false;          
                    if ("default" == v){
                        v = defaultGamma;
                        handled = true;
                    }
                    if ("recommended" == v){
                        v = preferGamma;
                        handled = true;
                    }
                    if (!handled){
                        v *= 100;
                    }
                    var b = document.createElement("button");
                    b.innerHTML = g;
                    b.addEventListener("click", function (){
                        Gallery_cubeSlice_setGamma(d[0], v);                        
                    });
                    gammaButtons.appendChild(b);

                });

                d[0].src = document.getElementById("current-slice").src;
                div.appendChild(gammaButtons); 
            }else{
                div.innerHTML = '<em>no cube data</em>';
            }
            d[0].parentNode.width="33%";
            d[0].parentNode.appendChild(div);
        }

        setTimeout(function (){
            document.location.hash = d[0].getAttribute('data-hash');
        }, 50);
    }, 500);
}    
function Gallery_openViewer(){
    
}    

</script>
<div align="center">
    Close the gallery <button onclick="toggleGalleryView()">x</button>
</div>    
