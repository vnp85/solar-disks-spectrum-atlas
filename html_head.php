<!DOCTYPE html>
<html class="client-nojs vector-feature-language-in-header-enabled vector-feature-language-in-main-page-header-disabled vector-feature-page-tools-pinned-disabled vector-feature-toc-pinned-clientpref-1 vector-feature-main-menu-pinned-disabled vector-feature-limited-width-clientpref-1 vector-feature-limited-width-content-enabled vector-feature-custom-font-size-clientpref-1 vector-feature-appearance-pinned-clientpref-1 vector-feature-night-mode-enabled skin-theme-clientpref-day vector-sticky-header-enabled vector-toc-available" lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
<title>Solar Disks Spectrum Atlas</title>
<style>
    body {
        font-family: Helvetica, Arial, Sans-Serif;
        margin-left: 0px;
        margin-right: 0px;
    }

    .display-notes-of-active-cube {
        max-width: 800px;
    }

    .greek {
        font-family: 'Times New Roman', Georgia, serif;
    }

    .help-paragraph {
        margin-bottom: 15px;
    }
    .help-sub-paragraph {
        margin-top: 15px;
    }

    div.stripes {
        height: 300px;
        width: 300px;
        background-image: linear-gradient(-45deg, #000 25%, transparent 25%, transparent 50%, #000 50%, #000 75%, transparent 75%, #fff);
        background-size: 50px 50px;
    }
    .wavelength-button {
        margin: 2px;
    }
    .blinker-marker-class {
        background-color: red !important;
    }
    .section-title {
        margin-top: 20px;
        margin-bottom: 5px;
        font-size: 150%;
    }
	.help-q-mark {
		cursor: pointer;
	}

    .spectrum-or-diagram-opacity {
        width: 80px;
        font-size: 75%;  
    }      
    
    .woi-column-visibility-wrapper {
        font-size: 50%;
        text-align: left;
    }
    
    #current-slice-subtitle {
        font-size: 75%;
    }

    .brightness-and-gamma-display-wrapper {
        background-color: #eee; 
        display: inline-block; 
        border-radius: 10px; 
        padding: 2px;
    }
    .add-to-playlist-wrapper {
        display: inline-block; 
    }
    .hamburger-component {
        width: 20px;
        height: 2px;
        background-color: black;
        margin: 4px 0;
    }
    .hamburger-list {
        display: inline-block;
        zoom: 0.5;
    }


</style>
<script rationale="if we have jquery at this location, we are in a known context, so lets send usage reports" type='text/javascript' src='../jquery/jquery-1.9.1.min.js' charset='utf-8'></script>
<script>  
    const FrontendEvents = {
        _data: [],
        _inited: false,
        _intervalId: false, 
        _whereURL: "../ext_beacon.php",       
        _hasJquery: function (){
           var ret = false;
           try {
              if (typeof $.post === 'function'){
                 ret = true;
              } 
           }catch(err){
              //
           }
           return ret;
        },
        init: function (){
            if (this._inited){
              // already inited
            }else{
                // maybe there is no jquery loaded
                var that = this;
                this._inited = true;
                if (this._hasJquery()){
                    //carry on
                }else{
                    return this;
                }
                this._intervalId = setInterval(function (){
                    if (that._data.length > 0){
                        $.post(that._whereURL, {
                            frontendevents: 1,
                            solarDisksAtlas: 1,
                            data: JSON.parse(JSON.stringify(that._data))
                        });
                        that._data = [];
                    }
                }, 5000);
            }
            return this;
        },    
        push: function (d){
            d.utcseconds = Math.floor(Date.now() / 1000);
            this._data.push(d);
            this.init();
        },
    };


    const URLmanager = {
        _timerId: false,
        _accumulator: [],
        pushPartialState: function (s){
            console.log('partial', s);
            var that = this;
            try {
                if (this._lastPushedState){
                    var lp = JSON.parse(JSON.stringify(this._lastPushedState));
                    Object.keys(s).forEach(function (key){
                        lp[key] = s[key];
                    });                                        
                    that.pushState(lp);
                }
            }catch(err){
                console.err(err);
            }
        },
        pushState: function (s){
            console.log("PUSH STATE ", s);
            console.trace();
            s = JSON.parse(JSON.stringify(s));
            s['open-gallery-view'] = isGalleryViewOpen() ? '1' : '0';
            getDisplayFiltersIntoPartialState(s);
            if (parseFloat(s.wavelength_A) === parseFloat(s.wavelength_A)){
                s.wavelength_A = parseFloat(s.wavelength_A).toFixed(2);
            }
            //s.now = Date.now();
            if (this._timerId){
                clearTimeout(this._timerId);
            };
            this._lastPushedState = s;
            this._accumulator.push(s);
            var that = this;
            this._timerId = setTimeout(function (){
                console.log(that._accumulator);                
                that.showState(that.getCollatedAccumulator());
                that._timerId = false;
                that._accumulator = [];
            }, 500);
        },
        getCollatedAccumulator: function (){
            var ret = {};
            this._accumulator.forEach(function (e){
                Object.keys(e).forEach(function (k){
                    if (ret.hasOwnProperty(k)){
                      // carry on
                    }else{
                      ret[k] = e[k];
                    }
                    if (e[k] === false){
                        e[k] = ret[k];
                    }
                    if (e[k] === "n/a"){
                        e[k] = ret[k];
                    }
                    ret[k] = e[k];
                });
            });
            return ret;
        },
        showState: function (s){
            var u = [];
            if (s.pixelShift == 'n/a'){
                try {
                    s.pixelShift = OnImage_getThePixelShiftEditor(s.cubeId).value;
                }catch(err){
                    //
                }
            }
            s.pixelShift = Math.round(s.pixelShift);
            Object.keys(s).forEach(function (k){
               u.push(k+'='+encodeURIComponent(s[k]));
            });
            u = u.join('&');
            window.history.pushState("object or string", "SDSA", "?"+u);

            var sendingUrl = document.URL.split('#')[0].split('?')[0]+'?'+u;
            sendingUrl = sendingUrl.split("csillagtura.ro").pop();
            FrontendEvents.push({
                url: sendingUrl
            });
        },
        _defaultWavelength_A: "CaK",
        _defaultCubeId: "cube_20250501_3930",
        applyDefaultState: function (){
            Spectrum_showWavelengthA(this._defaultWavelength_A, this._defaultCubeId);
            return this;
        },

        isNanIsh: function (e){
            if (typeof e === 'undefined'){
                return true;
            }
            if (e === false){
                return true;
            }
            if (e === 'false'){
                return true;
            }
            if (e === 'NaN'){
                return true;
            }
            if (e === 'n/a'){
                return true;
            }
            return false;
        },

        stringOrFalse: function (dict, key){
            if (typeof dict[key] === 'string'){
                if (this.isNanIsh(dict[key])){
                    dict[key] = false;
                }
            }else{
                dict[key] = false;
            }
        },

        parseStateFromUrl: function (){
            var ret = false;
            var u = document.URL.split('#')[0];
            u = u.split('?');
            u.push('');
            u = u[1];
            var that = this;            
            var triggerKeys = [
                'open-gallery-view',
                'wavelength_A',
            ];
            var hasTriggerKey = false;
            triggerKeys.forEach(function (tk){
                if (u.indexOf(tk) > -1){
                    hasTriggerKey = true;
                };    
            });


            if (hasTriggerKey){
                ret = true;
                var acc = {};
                u.split('&').forEach(function (e){
                    e = e.split('=');
                    e.push('n/a');
                    acc[e[0]] = e[1];
                });
                if (that.isNanIsh(acc["wavelength_A"])){
                    acc["wavelength_A"] = that._defaultWavelength_A;
                }
                that.stringOrFalse(acc, 'cubeId');
                that.stringOrFalse(acc, 'preferCube');
                console.log("parsed from the URL: "+JSON.stringify(acc));
                
                if (false === acc['cubeId']){
                    if (false !== acc['preferCube']){
                        acc['cubeId'] = acc['preferCube'];
                    }
                }
                if (1 == acc['open-gallery-view']){
                    openGalleryView();
                }
                Spectrum_showWavelengthA(acc["wavelength_A"], acc['cubeId']);
                applyDisplayFiltersFromPartialState(acc);
            };    
            return ret;
        }
    }
    

</script>
<script>
    <?php require_once("wavelengths_to_colors.js"); ?>
</script>    
<script>
    function OnImage_domify(img){
        if (typeof img === "string"){
            img = document.getElementById(img);
        }    
        return img;
    }
    function OnImage_getMappedData(img){
        img = OnImage_domify(img);
        var mapData = JSON.parse(img.getAttribute("data-mapping"));        
        if (mapData.hasOwnProperty('instrument-id')){
            // there is something declared, 
            // use that
        }else{
            // it is probably the solex, instrument #1
            mapData['instrument-id'] = 1;
        }
        return mapData;
    }

    function OnImage_pixelShiftToWavelength_A(img, shift_px){        
        var cwl_is_at_pix = OnImage_getCwl_px(img);
        return OnImage_pixelToWavelength_A(img, cwl_is_at_pix + shift_px);
    }

    function OnImage_pixelToWavelength_A(img, real_px){
        img = OnImage_domify(img);
        var mapData = OnImage_getMappedData(img);        
        mapData.pixelWavelengthPairs.sort(function (a, b){
            a = Math.abs(a.px - real_px);
            b = Math.abs(b.px - real_px);
            return a - b;
        });    
        var angstrom_per_pixel = 
          (mapData.pixelWavelengthPairs[0].lambda_A - mapData.pixelWavelengthPairs[1].lambda_A) / 
            (mapData.pixelWavelengthPairs[0].px - mapData.pixelWavelengthPairs[1].px); 
        var gotten_lambda_angstrom = mapData.pixelWavelengthPairs[0].lambda_A + (real_px - mapData.pixelWavelengthPairs[0].px)*angstrom_per_pixel;
        return gotten_lambda_angstrom;
    }

    function OnImage_doesOperateOnTheCubeSlices(img){
        img = OnImage_domify(img);
        return 1 == img.getAttribute("data-operates-on-the-cube-slices");
    }

    function OnImage_getCwl_A(img){
        img = OnImage_domify(img);
        var mapData = OnImage_getMappedData(img);        
        var ret = mapData.cwl_A;                
        ret = parseFloat(ret);
        return ret;
    }
    function OnImage_getCwl_px(img){
        img = OnImage_domify(img);
        var mapData = OnImage_getMappedData(img);        
        var ret = mapData.cwl_px;        
        return ret;
    }

    function OnImage_wavelengthAToPixel(img, lambda_A){
        img = OnImage_domify(img);
        var mapData = OnImage_getMappedData(img);        
        mapData.pixelWavelengthPairs.sort(function (a, b){
            a = Math.abs(a.lambda_A - lambda_A);
            b = Math.abs(b.lambda_A - lambda_A);
            return a - b;
        });    
        var angstrom_per_pixel = 
          (mapData.pixelWavelengthPairs[0].lambda_A - mapData.pixelWavelengthPairs[1].lambda_A) / 
            (mapData.pixelWavelengthPairs[0].px - mapData.pixelWavelengthPairs[1].px); 
        var gotten_lambda_angstrom = mapData.pixelWavelengthPairs[0].px + (lambda_A - mapData.pixelWavelengthPairs[0].lambda_A)/angstrom_per_pixel;
        return gotten_lambda_angstrom;

    }

    function OnImage_getLastClickOnRealWavelength(img){
        img = OnImage_domify(img);
        var w = img.getAttribute("last-click-on-real-wavelength"); 
        w = parseFloat(w);
        if (w === w){
            // already a number
        }else{
            w = OnImage_getCwl_A(img);
        }
        return w;
    };

    function OnImage_getLastClickOnRealPixel(img){
        img = OnImage_domify(img);
        var w = img.getAttribute("last-click-on-real-pixel"); 
        w = parseFloat(w);
        if (w === w){
            // already a number
        }else{
            w = OnImage_getCwl_px(img);
        }
        return w;
    };

    function OnImage_placeClickOntoRealPixel(img, real_px){
        img = OnImage_domify(img);
        img.setAttribute("data-real-px", real_px);
        img.setAttribute("last-click-on-real-pixel", real_px);
        img.setAttribute("last-click-on-real-wavelength", OnImage_pixelToWavelength_A(img, real_px));
    }
    function OnImage_placeClickOntoRealWavelength(img, lambda_A){
        img = OnImage_domify(img);
        img.setAttribute("last-click-on-real-wavelength", lambda_A);        
        img.setAttribute("last-click-on-real-pixel", OnImage_wavelengthAToPixel(img, lambda_A));
        img.setAttribute("data-real-px", OnImage_wavelengthAToPixel(img, lambda_A));
    }

    function OnImage_setMarkerToWavelength(img, lambda_A, color = false){
        img = OnImage_domify(img);
        var px = OnImage_wavelengthAToPixel(img, lambda_A);
        OnImage_setMarkerToPixel(img, px, color);
    };

    function OnImage_setPartialMarkerToWavelength(img, lambda_A, color = false, height = -1){
        img = OnImage_domify(img);
        var px = OnImage_wavelengthAToPixel(img, lambda_A);
        OnImage_setPartialMarkerToPixel(img, px, color, height);
    };

    function OnImage_getNatural(img, what){
        if ('width' == what){
            what = 'w';
        }
        if ('height' == what){
            what = 'h';
        }
        if ('x' == what){
            what = 'w';
        }
        if ('y' == what){
            what = 'h';
        }
        var ret = 0;
        try {
            if ("w" == what){
                ret = img.naturalWidth;
            };
            if ("h" == what){
                ret = img.naturalHeight;
            };
            if ((!ret) || (ret < 10)){
                // not set yet? maybe we have artificial metadata
                var am = img.getAttribute('data-naturals');
                if (am){
                    am = am.split(',');
                    if ('h' == what){
                        ret = parseFloat(am[1]);
                    }
                    if ('w' == what){
                        ret = parseFloat(am[0]);
                    }
                }else{
                    var md = OnImage_getMappedData(img);
                    if (md.averageFileImageSY && md.averageFileImageSX){
                        img.setAttribute('data-naturals', [md.averageFileImageSX, md.averageFileImageSY].join(','));
                    }
                    if ("h" == what){
                        ret = md.averageFileImageSY || 0;
                    };    
                    if ("w" == what){
                        ret = md.averageFileImageSX || 0;
                    };    
                }
            }
        }catch(err){
            console.log(err);
        }
        return ret;
    }

    function OnImage_displayPixToRealPix(img, px){
        img = OnImage_domify(img);
        var factor = OnImage_getNatural(img, 'x') / img.clientWidth;
        var b = img.getBoundingClientRect();
        var display_px = px - b.x;
        var real_px = display_px * factor;
        return real_px;
    }
    function OnImage_realPixToDisplayPix(img, px){
        var offset = 0;
        img = OnImage_domify(img);
        var factor = OnImage_getNatural(img, 'x') / img.clientWidth;                
        var b = img.getBoundingClientRect();
        // b is implicit here, not needed to add math
        var display_px = px/factor + offset;
        return display_px;
    }
    function OnImage_wavelengthAToDisplayPix(img, lambda_A){
        img = OnImage_domify(img);
        var real_px = OnImage_wavelengthAToPixel(img, lambda_A);
        var displayPix = OnImage_realPixToDisplayPix(img, real_px);
        //console.log("OnImage_wavelengthAToDisplayPix", img, lambda_A, real_px, displayPix);
        return displayPix;
    }    

    function appendArrowDownToTopBorder(parent, color){
        var size = 5;

        parent.overflow = "visible";
        var lefti = 0.5;

        var wrapper = document.createElement("div");
        wrapper.className = "cursor-triangle-wrapper";
        wrapper.style.height = "100%";
        wrapper.style.position = "relative";
        parent.appendChild(wrapper);

        var div = document.createElement("div");
        div.style.width = 0;
        div.style.height = 0;
        div.style.borderLeft = size+"px solid transparent";
        div.style.borderRight = size+"px solid transparent";
        div.style.borderTop = size*2+"px solid "+color;
        div.style.left = "-"+(size+lefti)+"px";
        div.style.position = "absolute";
        div.style.top = "0";
        wrapper.appendChild(div);
        

        var div = document.createElement("div");
        div.style.width = 0;
        div.style.height = 0;
        div.style.borderLeft = size+"px solid transparent";
        div.style.borderRight = size+"px solid transparent";
        div.style.borderBottom = size*2+"px solid "+color;
        div.style.left = "-"+(size+lefti)+"px";
        div.style.bottom = "0";
        div.style.position = "absolute";
        wrapper.appendChild(div);        
    }

    function OnImage_setPartialMarkerToPixel(img, px, color = false, height = -1, top = null){
        img = OnImage_domify(img);
        var left = (OnImage_realPixToDisplayPix(img, px));
        if ((left < 230) || (left > 20000)){
            //console.log(JSON.parse(img.getAttribute("data-mapping")), left);
        }else{
            //console.log("looks fine", left);
        }
        var div = document.createElement("div");
        var randid = 'marker-'+Date.now()+Math.random()+Math.random()+Math.random();
        randid = randid.split('.').join('-');
        div.id = randid;
        color = color || 'orange';
        if (height < 0){
            div.style.height = "100%";
            appendArrowDownToTopBorder(div, color);            
        }else{
            div.style.height = height;
        }
        div.style.position = "absolute";
        if (null === top){
            div.style.top = "-10%";
        }else{
            div.style.top = top;
        }
        div.style.width = "0px";
        div.style.borderLeft = "1px solid "+color;
        div.style.left = left+'px';
        div.style.pointerEvents = "none";

        img.parentNode.appendChild(div);
        return div.id;
    };

    function OnImage_setPartialMarkerToWavelength(img, lambda_A, color = false, height = -1, top = null){
        img = OnImage_domify(img);
        var px = OnImage_wavelengthAToPixel(img, lambda_A);
        return OnImage_setPartialMarkerToPixel(img, px, color, height, top);
    };


    function OnImage_setMarkerToPixel(img, px, color = false){
        return OnImage_setPartialMarkerToPixel(img, px, color, -1);
    };
    function OnImage_getCursorWavelengthA(img){
        img = OnImage_domify(img);
        var mapData = JSON.parse(img.getAttribute("data-mapping")); 
        var n = img.getAttribute("data-cursor-set-to-real-px");        
        if (parseFloat(n) === parseFloat(n)){
            // it is a number
            n = parseFloat(n);
        }else{
            n = mapData.cwl_px;
        }
        var lambda_A = OnImage_pixelToWavelength_A(img,n); 
        return lambda_A;
    };

    function OnAllImages_getSpectrumOpacities(desiredOpacity = false){
        if (false === desiredOpacity){
            desiredOpacity = window.lastSetSpectrumOpacity ? window.lastSetSpectrumOpacity : 0.5;
        }
        return desiredOpacity;
    }

    function OnAllImages_adjustSpectrumOpacities(direction){
        var desiredOpacity = OnAllImages_getSpectrumOpacities(desiredOpacity);
        desiredOpacity = Math.round(desiredOpacity*10);
        if (direction > 0){
            desiredOpacity++;
        }
        if (direction < 0){
            desiredOpacity--;
        }
        desiredOpacity = Math.min(desiredOpacity, 10);
        desiredOpacity = Math.max(desiredOpacity, 1);
        desiredOpacity /= 10;
        OnAllImages_setSpectrumOpacities(desiredOpacity);
    };    
    function OnAllImages_setSpectrumOpacities(desiredOpacity = false){
        desiredOpacity = OnAllImages_getSpectrumOpacities(desiredOpacity);
        window.lastSetSpectrumOpacity = desiredOpacity;
        var list = document.getElementsByClassName("spectrum-image");
        for (var i=0; i<list.length; i++){
            var diagramTwin = document.getElementById("diagram-twin-of-"+list[i].id);
            var localValue = desiredOpacity;
            var adjusterButtonsStyleDisplayValue = "none";
            if (diagramTwin){
                if ((diagramTwin.getAttribute("src") + '').length > 20){
                    // it has some md5 in it
                    adjusterButtonsStyleDisplayValue = "";
                }else{
                    localValue = 1;
                }
            }else{
                localValue = 1;
            }
            list[i].style.opacity = localValue;

            var cont = document.getElementById("template-container-of-spectrum-"+list[i].id);
            if (cont){
                var list2 = cont.getElementsByClassName("spectrum-or-diagram-opacity");
                for (var j=0; j<list2.length; j++){
                    list2[j].style.display = adjusterButtonsStyleDisplayValue;
                }
            }
        }
        Cubes_onSetSpectrumOpacities(adjusterButtonsStyleDisplayValue);        
    }
        
    function ImageFilename_doesItHaveThePixelshift(filename, pixelShift){
        if (false === pixelShift){
            return true;
        }
        pixelShift = Math.round(pixelShift);

        filename = (filename+'').split('\\').join('/').split('/').pop();        
        //console.log("should have the basename now", filename);
        filename = filename.split('.').join('_');
        filename = filename.split('img_C').pop(); 
        filename = filename.split('_');
        filename.shift();
        filename = filename.shift();
        filename = filename.replace('P', '+').replace('M', '-');
        filename = parseInt(filename, 10);
        //console.log("pixelshift in filename", filename, "pixelshift in argument", pixelShift);
        return (filename == pixelShift);
    }

    function OnImage_getImageList(img, filter_px = false){
        img = OnImage_domify(img);
        if (false === filter_px){
            // carry on
        }else{
            // see function call
        }
        //console.log('filename should contain: ', img, filter_px);
        var p = OnImage_getFirstParentWithTagname(img, "table").getElementsByClassName("cube-slices-list-enumeration")[0];
        var ret = p.innerHTML.split(',').map(function (e){
            e = e.trim();
            return e;
        }).filter(function (e){
            if (false === filter_px){
                // don't check
            }else{
                return ImageFilename_doesItHaveThePixelshift(e, filter_px);
            }    
            return e.length > 0;
        });
        return ret;
    }
    function OnImage_getFirstParentWithTagname(img, tagname){        
        img = OnImage_domify(img);
        var go = true;
        tagname = (tagname+'').toUpperCase();
        while (go){
            if (img){
                img = img.parentNode;
            }
            if (img){
                var tn = img.tagName.toUpperCase();
                if (tn == tagname){
                    return img;
                }                
            }else{
                return false;
            }
        }
    }

    function OnImage_getTheLambdaEditor(img){
        return OnImage_getFirstParentWithTagname(img, "table").getElementsByClassName("lambda-editor")[0]; 
    }
    function OnImage_getThePixelShiftEditor(img){
        return OnImage_getFirstParentWithTagname(img, "table").getElementsByClassName("pixel-editor")[0]; 
    }
    function OnImage_getTheChemicalHolder(img){
        return OnImage_getFirstParentWithTagname(img, "table").getElementsByClassName("identified-chemical-wrapper")[0]; 
    }

    function OnImage_getSpectrumExtremes(img){
        img = OnImage_domify(img);
        var mapData = JSON.parse(img.getAttribute("data-mapping"));        
        var pxs = img.getAttribute("data-extreme-pixel-shifts").split(",").map(function (e){
            return parseFloat(e);
        });
        var lms = mapData.extremeWavelengths_A.map(function (e){
            return parseFloat(e);   
        });
        return {
            lambdas_A: lms,
            pixelShifts: pxs
        }
    }

</script>    
<script>

function OnImage_addExtremeBlurs(t, a){        
        var opacityPercent = 70;
        // add bluring divs
        var wb = OnImage_wavelengthAToDisplayPix(t, a.extremeWavelengths_A[0])-2;
        var blurBlue = document.createElement("div");
        var leftMargin = 15;
        var zi = 9988;

        var colorWavelengthsOfExtremes = {
            blue: a.extremeWavelengths_A[0],
            red: a.extremeWavelengths_A[1]
        };
        Wavelengths_A_rangeToRemainColored(colorWavelengthsOfExtremes);

        blurBlue.style.position = "absolute";
        blurBlue.style.top = "-10px";
        blurBlue.style.left = "-"+leftMargin+"px";
        blurBlue.style.width = (wb+leftMargin)+"px";
        blurBlue.style.height = "100%";
        blurBlue.style.opacity = opacityPercent+"%";
        blurBlue.style.filter = "blur(4px)";
        blurBlue.style.backgroundColor = WavelengthToColor(colorWavelengthsOfExtremes.blue);
        blurBlue.style.overflow = "hidden";
        blurBlue.style.cursor = "not-allowed";
        blurBlue.style.zIndex = zi;
        blurBlue.className = "spectrum-extreme-blur spectrum-extreme-blur-blue-half";
        //blurBlue.innerHTML = "<div class=\"stripes\">&nbsp</div>";
        t.parentNode.appendChild(blurBlue);
        
        var wr = OnImage_wavelengthAToDisplayPix(t, a.extremeWavelengths_A[1])+2;
        var blurRed = document.createElement("div");
        blurRed.style.position = "absolute";
        blurRed.style.top = "-10px";
        blurRed.style.left = wr+"px";
        blurRed.style.width = "100%";
        blurRed.style.height = "100%";
        blurRed.style.opacity = opacityPercent+"%";
        blurRed.style.filter = "blur(4px)";
        blurRed.style.backgroundColor = WavelengthToColor(colorWavelengthsOfExtremes.red);
        blurRed.style.overflow = "hidden";
        blurRed.style.cursor = "not-allowed";
        blurRed.className = "spectrum-extreme-blur spectrum-extreme-blur-red-half";
        blurRed.style.zIndex = zi;


        console.log("Adding blurs to extremities", a, wb, wr);
        //blurRed.innerHTML = "<div class=\"stripes\">&nbsp</div>";
        t.parentNode.appendChild(blurRed);
    }

    function OnImage_enqueueAddExtremeBlurs(t, a){
        function isItReady(){
            if (t.clientHeight > 50){
                if (t.clientWidth > 100){                    
                    if (t.naturalWidth > 10){
                        console.log("add extreme blurs, thought to be prepared", a.extremeWavelengths_A, t);
                        //OnImage_addExtremeBlurs
                        return true;
                    }
                }
            }
            return false;        
        }
        if (isItReady()){
            OnImage_addExtremeBlurs(t, a);
            return ;
        }
        var i = setInterval(function (){
            if (isItReady()){
                    clearInterval(i);
                    OnImage_addExtremeBlurs(t, a);
            }
        }, 100);
    }

    function OnImage_updateTheInputsFromWavelength(img, lambda_A){                        
        lambda_A = parseFloat(lambda_A);
        var parentPixelShift = OnImage_wavelengthAToPixel(img, lambda_A) - OnImage_getCwl_px(img);
        OnImage_getThePixelShiftEditor(img).value = Math.round(parentPixelShift);
        var p = img.getAttribute("data-lambda-precision");         
        OnImage_setTheLambdaEditorValue(img, lambda_A.toFixed(p));        
    };


    function OnImage_setCursorToWavelengthA(img, lambda_A, fireEventLevel = 999){
        var px = OnImage_wavelengthAToPixel(img, lambda_A);
        OnImage_setCursorToPixel(img, px, false, fireEventLevel);
    }

    function OnImage_setCursorToWavelengthShiftA(img, lambdaShift_A){
        var lambda_A = lambdaShift_A + OnImage_getCwl_A(img);
        var px = OnImage_wavelengthAToPixel(img, lambda_A);
        OnImage_setCursorToPixel(img, px);
    }

    function OnImage_setCursorToPixelShift(img, pxShift){
        OnImage_setCursorToPixel(img, OnImage_getCwl_px(img) + pxShift);
    }

    function Cubes_onSetSpectrumOpacities(v){
        var l = document.getElementsByClassName("previous-or-next-cube");
        for (var i=0; i<l.length; i++){
            l[i].style.display = v;
        }        
    };

    function Cubes_getCurrentWavelength(){
        var sliceView = document.getElementById("current-slice");        
        var lambda_A = sliceView.getAttribute("data-current-wavelength-a");
        if (!lambda_A){
            lambda_A = 'TODO';
        }
        if ('TODO' === lambda_A){
            // fallback, this UI I cobbled together is a spaghetti :(
            lambda_A = document.getElementById("lambda-main_spectrum").value;
        }
        lambda_A = parseFloat(lambda_A);
        if (lambda_A === lambda_A){
            // it is a number
        }else{
            lambda_A = -1;
        }
        return lambda_A;
    }

    function Cubes_setTimeoutToLoadSibling(elem){
        var to_ms = 100;
        var sliceView = document.getElementById("current-slice");        
        var currentCubeId = sliceView.getAttribute("data-current-cube-id");        
        console.log("proposing to load sibling", elem);
        var desiredCubeId = elem.getAttribute("data-cube-id");
        var hints = {
            x: OnImage_getSpectrumExtremes(desiredCubeId),
            preferCube: desiredCubeId,
            lambda_A: Cubes_getCurrentWavelength(),
            avoidCube: currentCubeId,
        };
        hints.inRange = (hints.lambda_A >= hints.x.lambdas_A[0]) && (hints.lambda_A <= hints.x.lambdas_A[1]);
        if (hints.inRange){
            // in range, load the wavelength as a preferred cube
            setTimeout(function (){
                Spectrum_showWavelengthA(hints.lambda_A, hints.preferCube, hints.avoidCube);
            }, to_ms);
        }else{
            // not in range, just load the subling cube
            setTimeout(function (){
                CubeListItem_divClick(elem);
            }, to_ms);                        
        }        
    }

    function Cubes_isItABrowsePrevNextCandidate(cubeId){
        try {
            var currentCubeId = cubeId.getAttribute("data-cube-id");
            cubeId = currentCubeId;
        }catch(err){

        }
        var img = OnImage_domify(cubeId);
        var mapData = JSON.parse(img.getAttribute("data-mapping")); 
        if (mapData["browse-prev-next-preference"]){
            if ("avoid" == mapData["browse-prev-next-preference"]){
                return false;
            }
        }                
        return true;
    }

    function Cubes_loadSibling(direction){
        if ("blue" === direction){
            direction = -1;
        }
        if ("red" === direction){
            direction = 1;
        }
        var sliceView = document.getElementById("current-slice");        
        var cubeId = sliceView.getAttribute("data-current-cube-id");        
        
        var l = document.getElementsByClassName("cube-list-item");   
        console.log(l);  
        
        var li = [];

        for (var i=0; i<l.length; i++){
            var currentCubeId = l[i].getAttribute("data-cube-id");
            if (Cubes_isItABrowsePrevNextCandidate(l[i]) || (currentCubeId === cubeId)){
                li.push(l[i]);
            }
        };
        for (var i=0; i<li.length; i++){
            var currentCubeId = li[i].getAttribute("data-cube-id");
            var proposedIndex = i + direction;
            if (proposedIndex >= 0){
                if (proposedIndex < li.length){
                    if (currentCubeId === cubeId){
                            Cubes_setTimeoutToLoadSibling(li[proposedIndex]);
                            return ;
                    }
                }
            }            
        }
    }
</script>
</head>
<body>
    <div align="center">
        <div style="font-size:200%">Solar Disks Spectrum Atlas</div>
        <div style="font-size:150%">"a Sun for each wavelength"</div>
        <div>to be viewed on a large desktop screen</div>
        <div style="font-size:30%">&nbsp;</div>
        <div>by P&aacute;l V&Aacute;RADI NAGY</div>
    </div>    
