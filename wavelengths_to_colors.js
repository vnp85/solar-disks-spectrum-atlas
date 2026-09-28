
// separate js file
function Wavelengths_getColoredDomain_nm(){
    return [380, 720];
}

function Wavelengths_A_rangeToRemainColored(colorWavelengthsOfExtremes_A){
    var d_nm = Wavelengths_getColoredDomain_nm();
    var d_A = [d_nm[0]*10, d_nm[1]*10];
    function into(a){
        a = Math.max(d_A[0], a);
        a = Math.min(d_A[1], a);
        return a;
    }
    colorWavelengthsOfExtremes_A.blue = into(colorWavelengthsOfExtremes_A.blue);
    colorWavelengthsOfExtremes_A.red = into(colorWavelengthsOfExtremes_A.red);
}         

function wlNanoMetersToRGB_raw(w){     
    // based on https://405nm.com/wavelength-to-color/   
    var extremes = Wavelengths_getColoredDomain_nm();
    if (w < 0){
        var cwl = (extremes[0] + extremes[1]) / 2;
        w = Math.abs(w);
        var delta = w-cwl;
        w = cwl - delta; 
        return wlNanoMetersToRGB_raw(w)
    }
    var red = 127;
    var green = 127;
    var blue = 127;
    var factor = 1;
    if ((w < extremes[0])||(w > extremes[1])){
        return [Math.round(red*2), Math.round(green*2), Math.round(blue*2)];            
    }        

    if(w>=380&&w<440){red=-(w-440)/(440-380);green=0.0;blue=1.0;}else if(w>=440&&w<490){red=0.0;green=(w-440)/(490-440);blue=1.0;}
    else if(w>=490&&w<510)
    {red=0.0;green=1.0;blue=-(w-510)/(510-490);}
    else if(w>=510&&w<580)
    {red=(w-510)/(580-510);green=1.0;blue=0.0;}
    else if(w>=580&&w<645)
    {red=1.0;green=-(w-645)/(645-580);blue=0.0;}
    else if(w>=645&&w<809)
    {red=1.0;green=0.0;blue=0.0;}
    else
    {red=1.0;green=1.0;blue=1.0;}
    if(w>=380&&w<420)
    factor=0.3+0.7*(w-380)/(420-380);else if(w>=420&&w<645)
    factor=1.0;else if(w>=645&&w<809)
    factor=0.3+0.7*(809-w)/(809-644);else
    factor=0.0;
    var gamma=0.80;
    var R=(red>0?255*Math.pow(red*factor,gamma):0);
    var G=(green>0?255*Math.pow(green*factor,gamma):0);
    var B=(blue>0?255*Math.pow(blue*factor,gamma):0);

    return [Math.round(R), Math.round(G), Math.round(B)];
};

function wlNanoMetersToRGB(w){
    return 'rgba('+wlNanoMetersToRGB_raw(w).join(',')+')';
};    

function wlNanoToPinkIrVioletUVRGB_raw(w){
    var rgb = wlNanoMetersToRGB_raw(w);
    var violet = [217, 152, 250];
    var wlSize = 25;
    var violetFrom_nm = 400;
    var violetAt_nm = violetFrom_nm-wlSize;

    var pink = [255, 204, 234];    
    var pinkFrom_nm = Wavelengths_getColoredDomain_nm()[1]-wlSize;    
    var pinkAt_nm = pinkFrom_nm+wlSize;

    if (w <= violetFrom_nm){
        if (w <= violetAt_nm){
            return violet;
        }
        rgb = wlNanoMetersToRGB_raw(violetFrom_nm);
        var pos = violetFrom_nm - w;
        var ratio = pos/(Math.abs(violetFrom_nm - violetAt_nm));        
        for (var i=0; i<3; i++){
            var c1 = rgb[i]*(1-ratio);
            var c2 = violet[i]*ratio;
            var c = c1 + c2;
            var c = Math.min(Math.round(c), 255);
            rgb[i] = c;
        }                
    }

    if (w >= pinkFrom_nm){
        if (w >= pinkAt_nm){
            return pink;
        }
        var pos = w - pinkFrom_nm;
        var ratio = pos/Math.abs(pinkFrom_nm - pinkAt_nm);
        for (var i=0; i<3; i++){
            var c1 = rgb[i]*(1-ratio);
            var c2 = pink[i]*ratio;
            var c = c1 + c2;
            var c = Math.min(Math.round(c), 255);
            rgb[i] = c;
        }                
    }

    return rgb;
};



function WavelengthToColor(lambda_A){
    return wlNanoMetersToRGB(lambda_A / 10);
}