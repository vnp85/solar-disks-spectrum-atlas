
<script>

    function Url_proxifyIfNeeded(url){
        var isLocalFile = false;
        <?php 
           // do we have the magic file structure
           if (count(getRequireStack()) > 1){
              // we are in a proxied repo              
              echo '//---- '."\r\n";
              echo '/* we are in a proxied-repo context '.json_encode(getRequireStack(), JSON_PRETTY_PRINT).'*/'."\r\n";
              echo 'isLocalFile = true;'."\r\n";
              echo '//---- '."\r\n";
           }
        ?>
        if (url.indexOf("\\\\") > -1){
            isLocalFile = true;
        }        
        if (url.indexOf(":") > -1){
            isLocalFile = true;
        };
        var magic = 'rtekhjhrtioyhrtuoihuirteguihertouiherto';
        url = url.split('://').join(magic).split('//').join('/').split(magic).join('://');
        if (document.URL.indexOf("localhost") > -1){
            isLocalFile = true;
        }
        
        if (isLocalFile){
            return "?imgproxy="+encodeURIComponent(url);
        }else{
            console.log("keeping"+url);
        }        
        return url;
    }

    function OnImage_setExplicitPixelshiftRequestedAt(img, px){
        img = OnImage_domify(img);
        var o = { requestedAt: Date.now(), pixelShift: px};
        img.setAttribute("data-explicit-pixel-shift", JSON.stringify(o));
    }

    function OnImage_getExplicitPixelshiftRequest_falseOtherwise(img){
        img = OnImage_domify(img);
        var ret = false;
        var d = img.getAttribute("data-explicit-pixel-shift");
        try {
            d = JSON.parse(d);
            if (Date.now() - d.requestedAt < 25){
                ret = d.pixelShift;
            }
        }catch(err){
            // silence
        }
        return ret;
    }    


    function OnImage_setCursorToPixel(img, px, color = false){
        console.trace();
        var dk = 'data-spectrum-cursor';
        img.setAttribute("data-cursor-set-to-real-px", px);        
        var ki = img.getAttribute(dk);
        if (!ki){
            ki = OnImage_setMarkerToPixel(img, px);
            img.setAttribute(dk, ki);
        }else{
            document.getElementById(ki).style.left = (OnImage_realPixToDisplayPix(img, px))+'px';
        }
        
        var cwl_px = OnImage_getCwl_px(img);
        var shift_px = px - cwl_px;

        var explicitPixelShiftReqtested = OnImage_getExplicitPixelshiftRequest_falseOtherwise(img);

        if (false === explicitPixelShiftReqtested){
            // ignore
        }else{
            console.log("explicit pixel shift request mode", explicitPixelShiftReqtested);
            shift_px = explicitPixelShiftReqtested;
            px = shift_px + cwl_px;
        }


        var parentId = img.getAttribute('data-parent-id');
        if (parentId){
            //console.log("parentid: "+parentId);
            var parentImg = document.getElementById(parentId);
            if (parentImg){
                OnImage_updateTheInputsFromWavelength(parentImg, OnImage_pixelToWavelength_A(img, px));
            }
        }

        if (OnImage_doesOperateOnTheCubeSlices(img)){
            if (CubeViewer_isItOpen(img.id)){
                var list = OnImage_getImageList(img, shift_px);
                var sliceView = document.getElementById("current-slice");
                var subtitle = document.getElementById("current-slice-subtitle");
                if (list.length > 0){
                    var basename = list[0].split('/').pop();
                    console.log("slice showing "+list[0]);                    
                    sliceView.src = Url_proxifyIfNeeded(list[0]);
                    sliceView.style.display = "";
                    subtitle.innerHTML = basename;
                }else{
                    console.log("slice hiding");
                    sliceView.src = "";
                    sliceView.style.display = "none";
                    subtitle.innerHTML = "";
                }
            }
        }
    };    



    function SpectrumKeydownListener(e){  
        var img = e.target.getElementsByClassName("spectrum-image")[0];
        switch (event.key) {
            case "ArrowLeft":
                SpectrumPixelShiftInputClick(img.id, -1);
                break;
            case "ArrowRight":
                SpectrumPixelShiftInputClick(img.id, 1);
                break;
            case "ArrowUp":
                // Up pressed
                break;
            case "ArrowDown":
                // Down pressed
                break;
        }
        
    }    


    function SpectrumMouseMoveListener(e){        
        var img = e.target;
        var real_px = OnImage_displayPixToRealPix(img, e.clientX);
        var lambda_A = OnImage_pixelToWavelength_A(img, real_px);
        var reversedToPixel = OnImage_wavelengthAToPixel(img, lambda_A);
        var cwl_px = OnImage_wavelengthAToPixel(img, OnImage_getCwl_A(img));
        var p = img.getAttribute("data-lambda-precision");
        OnImage_setTheLambdaEditorValue(img, lambda_A.toFixed(parseInt(p) || 0));        
        OnImage_getThePixelShiftEditor(img).value = Math.round(-(cwl_px - reversedToPixel));
        
        img.setAttribute("data-real-px", real_px);

        //console.log(real_px, lambda_A, reversedToPixel);
        if (1 == e.buttons){
            OnImage_placeClickOntoRealPixel(img, real_px);
            SpectrumMouseClick(e);
        }
    }


    function SpectrumMouseClick(e){
        var img = e.target;
        Spectrum_showWavelengthA(OnImage_getLastClickOnRealWavelength(img));

        var real_px = OnImage_getLastClickOnRealPixel(img);
        var cwl_px = OnImage_getCwl_px(img);
        var limits = false;
        var pixelShift = "n/a";
        try {
            pixelShift = real_px - cwl_px;
            limits = OnImage_getSpectrumExtremes(img);
            pixelShift = Math.max(Math.min(pixelShift, limits.pixelShifts[1]), limits.pixelShifts[0]);
            real_px = cwl_px + pixelShift;
            img.setAttribute("data-real-px", real_px);
        }catch(err){
            console.log(err);
        }
        OnImage_placeClickOntoRealPixel(img, real_px);
        OnImage_setCursorToPixel(img, real_px);
        URLmanager.pushState({
            wavelength_A: "n/a",
            preferCube: img.id,
            pixelShift: pixelShift,
        });
    };

    function SpectrumMouseClickListener(e){                     
        var img = e.target;
        var real_px = OnImage_displayPixToRealPix(img, e.clientX);
        OnImage_placeClickOntoRealPixel(img, real_px);
        SpectrumMouseMoveListener(e);                
        SpectrumMouseClick(e);
    }



    function SpectrumMouseLeaveListener(e){
        var img = e.target;
        var p = img.getAttribute("data-lambda-precision"); 
        var cwl_px = OnImage_wavelengthAToPixel(img, OnImage_getCwl_A(img));
        var lambda_A = OnImage_getLastClickOnRealWavelength(img);        
        var reversedToPixel = OnImage_wavelengthAToPixel(img, lambda_A);        
        OnImage_setTheLambdaEditorValue(img, lambda_A.toFixed(parseInt(p) || 0));
        OnImage_getThePixelShiftEditor(img).value = Math.round(-(cwl_px - reversedToPixel));
    }

    function SpectrumLambdaEditorEnterListener(img){
        SpectrumLambdaInputClick(img.id);
    };
    function SpectrumPixelShiftEditorEnterListener(img){
        SpectrumPixelShiftInputClick(img.id, 0);
    };           

    function getImgFromSender(sender){
        var id = sender; 
        if (typeof id === "string"){
            // ok
        }else{
            id = sender.getAttribute("data-img-id");
        };    
        var img = document.getElementById(id);
        return img;
    }

    function SpectrumLambdaInputClick(sender){
        var img = getImgFromSender(sender);
        var lambda_A = parseFloat(OnImage_getTheLambdaEditor(img).value);
        var limits = false;
        try {
            limits = OnImage_getSpectrumExtremes(img);
            lambda_A = Math.max(Math.min(lambda_A, limits.lambdas_A[1]), limits.lambdas_A[0]);
        }catch(err){
            console.log(err);
        }
        Spectrum_showWavelengthA(lambda_A, img);

        var cwl_px = OnImage_wavelengthAToPixel(img, OnImage_getCwl_A(img));
        var reversedToPixel = OnImage_wavelengthAToPixel(img, lambda_A);
        OnImage_getThePixelShiftEditor(img).value = Math.round(-(cwl_px - reversedToPixel));
        OnImage_setTheLambdaEditorValue(lambda_A);
        
        
    }

    function OnImage_processReceivedLambdaEditorValue(img){
        var e = OnImage_getTheLambdaEditor(img);
        var fieldname = 'lastIdentifiedWavelengthCaptionWasAtLambda';
        if (typeof window[fieldname] === "undefined"){
            window[fieldname] = -1;
        }
        var currentS = e.value+e.id;
        var emptyValue = ''
        var destString = emptyValue;
        var wavelength_A = -1;
        //if (window[fieldname] != currentS){
            try {
                wavelength_A = parseFloat(e.value);
                var ci = Spectrum_getWavelengthClosestEnoughTo(wavelength_A);
                if (ci){
                    destString = ci.caption;
                    destString = destString.replace('%wavelength%', '');
                }
                destString = destString.split(' ').filter(function (e){                    
                    var fc = e.substring(0,1);
                    if (parseInt(fc) === parseInt(fc)){
                        // it is a number
                    }else{
                        // not a number
                        return true;
                    }
                    if (e.indexOf('&Aring;') > -1){
                        // it has angstrom in it
                    }else{
                        // not angstrom, not wavelength
                        return true;
                    }
                    return false;
                }).join(' ').trim();
                window[fieldname] = currentS;
            }catch(err){
                console.log("dorka", err);
            }

        //}else{
        //    console.log("emptying label");
        //}
        var d = OnImage_getTheChemicalHolder(img);
        var dColor = 'silver';
        var notImaged = '(no img data)';
        if (wavelength_A === wavelength_A){
            // it is a number
            if (wavelength_A > 0){
                if (isWavelengthCoveredByCubes(wavelength_A)){
                    dColor = 'black';
                }else{
                    dColor = 'silver';
                    if (emptyValue == destString){
                        destString = notImaged;
                    }else {
                        destString += ' ' + notImaged;
                    }
                }
            }
        }else{
            // not even a number
        }
        d.style.color = dColor;      

        d.value = destString;  
    }

    function OnImage_setTheLambdaEditorValue(img, lambda_A){
        var e = OnImage_getTheLambdaEditor(img);
        e.value = lambda_A;
        OnImage_processReceivedLambdaEditorValue(img);
    };
    function lambda_input_changed(sender){
        console.log("lambda input changed callback");
        var img = sender.getAttribute('data-cube-id');
        console.log(sender);
        OnImage_processReceivedLambdaEditorValue(img);
    }

    function SpectrumPixelShiftInputClick(sender, delta = 0){
        var img = getImgFromSender(sender);
        var cwl_px = OnImage_wavelengthAToPixel(img, OnImage_getCwl_A(img));
        var pixelShift = parseFloat(OnImage_getThePixelShiftEditor(img).value);

        pixelShift += delta;
        var limits = false;
        try {
            limits = OnImage_getSpectrumExtremes(img);
            pixelShift = Math.max(Math.min(pixelShift, limits.pixelShifts[1]), limits.pixelShifts[0]);
        }catch(err){
            console.log(err);
        }
        var desiredWavelength_A = OnImage_pixelToWavelength_A(img, cwl_px+pixelShift);
        console.log("pixelshift", pixelShift, "lambda", desiredWavelength_A);
        OnImage_setExplicitPixelshiftRequestedAt(img, pixelShift);
        OnImage_getThePixelShiftEditor(img).value = pixelShift;
        OnImage_updateTheInputsFromWavelength(img, desiredWavelength_A);
        Spectrum_showWavelengthA(desiredWavelength_A, img);        
    }

    function Cube_findContainerById(id){
        id = "template-container-of-spectrum-"+id;
        return document.getElementById(id);
    }
    function CubeListItem_divClick(elem, wavelength_A = false){
        console.log("div clicked");
        return CubeListItem_click(elem, wavelength_A);
    }    
    var insideCubeListItem_click = false;
    function CubeListItem_click(elem, wavelength_A = false){
        if (insideCubeListItem_click){
            return ;
        }
        insideCubeListItem_click = true;
        console.log("cube button clicked", elem, wavelength_A);
        var cubeId = elem.getAttribute("data-cube-id");
        var l = document.getElementsByClassName("cube-spectrum-container");
        var desiredElemId = Cube_findContainerById(cubeId).id;
        window.location.hash = 'cubeid='+cubeId;
        URLmanager.pushState({
            wavelength_A: wavelength_A,
            preferCube: cubeId,
            pixelShift: "n/a",
        });
        for (var i=0; i<l.length; i++){
            if (l[i].id == desiredElemId){
                l[i].style.display = "";
            }else{
                l[i].style.display = "none";
            }
        }
        if (wavelength_A){
            CubeViewer_clickOnWavelengthByCubeId(cubeId, wavelength_A);
        }else{
            CubeViewer_clickOnCwlByCubeId(cubeId);
        }
        setTimeout(function (){
            console.log("SERVATIUS "+cubeId);
            OnImage_processReceivedLambdaEditorValue(cubeId);
        }, 100);
        insideCubeListItem_click = false;
    }

    function MainSpectrum_setCursorToWavelengthA(lambda_A, favoringCubeId = false){
        console.log("main wavelength requested: "+lambda_A+", favoringCubeId="+favoringCubeId);
        var img = document.getElementById("main_spectrum");
        OnImage_placeClickOntoRealWavelength(img, lambda_A);
        OnImage_updateTheInputsFromWavelength(img, lambda_A);
        OnImage_setCursorToWavelengthA(img, lambda_A);
    }

    function CubeViewer_clickOnWavelengthByCubeId(cubeId, wavelength_A = false){
        var img = document.getElementById(cubeId);
        console.log("clicked on my friend", cubeId, wavelength_A); 
        if (wavelength_A){
           console.log(cubeId, "wavelength requested", wavelength_A);
        }else{
            OnImage_placeClickOntoRealPixel(img, OnImage_getLastClickOnRealPixel(img));
            wavelength_A = OnImage_getLastClickOnRealWavelength(img);
            console.log(cubeId, "wavelength from last click", wavelength_A);
        }
        OnImage_placeClickOntoRealWavelength(img, wavelength_A);
        img.setAttribute("data-real-px", OnImage_getLastClickOnRealPixel(img));
        OnImage_setCursorToWavelengthA(img, wavelength_A);
        MainSpectrum_setCursorToWavelengthA(wavelength_A, cubeId);        
        SpectrumMouseClick({ target: img});
        OnImage_updateTheInputsFromWavelength(img, wavelength_A);
        URLmanager.pushState({
            wavelength_A: wavelength_A,
            preferCube: cubeId,
            pixelShift: OnImage_getThePixelShiftEditor(img).value,
        });
        UI_updateButtonColors();
        
    }

    function CubeViewer_clickOnCwlByCubeId(cubeId){
        console.log("cube id cwl reauested", cubeId);
        CubeViewer_clickOnWavelengthByCubeId(cubeId, false);
    }    

    function CubeViewer_getWavelengthRange(cubeId){
        var img = document.getElementById(cubeId);
        return OnImage_getSpectrumExtremes(img);
    }

    function CubeViewer_isItOpen(cubeId){
        var k = Cube_findContainerById(cubeId);
        if ("none" == k.style.display){
            return false;
        }
        return true;
    }

    function Elem_setColorClass(e, color){
        e.style.backgroundColor = color;            
    }

    function UI_updateButtonColors(){
        var openColor = 'yellow';
        var inRangeColor = 'white';
        var outOfRangeColor = 'silver';

        var img = document.getElementById("main_spectrum");
        var cursorAt_A = OnImage_getCursorWavelengthA(img);
        var general_range_A = 20;

        var l = document.getElementsByClassName("cube-list-item");        
        for (var i=0; i<l.length; i++){
            var currentCubeId = l[i].getAttribute("data-cube-id");
            var range = CubeViewer_getWavelengthRange(currentCubeId);
            var color = outOfRangeColor;
            if ((range.lambdas_A[0] <= cursorAt_A) && (range.lambdas_A[1] >= cursorAt_A)){
                color = inRangeColor;
                
            }
            if (CubeViewer_isItOpen(currentCubeId)){
                color = openColor;
            }
            Elem_setColorClass(l[i], color);
        }        

        var wavelengthsOfInterstButtons = document.getElementsByClassName("wavelength-button");
        for (var i=0; i<wavelengthsOfInterstButtons.length; i++){
            color = outOfRangeColor;
            var w = wavelengthsOfInterstButtons[i].getAttribute("data-wavelength-angstrom");
            if (Math.abs(w - cursorAt_A) < general_range_A){
                color = inRangeColor;
            }
            if (Math.abs(w - cursorAt_A) < general_range_A / 5){
                color = openColor;
            }
            Elem_setColorClass(wavelengthsOfInterstButtons[i], color);
        }
    }

    function MainSpectrum_onCursorMoved(img){
        var magic = "data-main-spectrum-cursor-moved-listener";
        if (1 == img.getAttribute(magic)){
            return ;
        }
        img.setAttribute(magic, "1");
        var cursorAt_A = img.getAttribute("last-click-on-real-wavelength");       
        img.setAttribute(magic, "0");    
    }

    function Spectrum_showWavelengthA(lambda_A, preferCube = false){
        lambda_A = Spectrum_interpretInCaseItIsAString(lambda_A);
        URLmanager.pushState({
            wavelength_A: lambda_A,
            preferCube: preferCube,
            pixelShift: "n/a",
        });
        MainSpectrum_setCursorToWavelengthA(lambda_A, preferCube);
        UI_updateButtonColors();

        var l = document.getElementsByClassName("cube-list-item");
        console.log("cube-list-item-count");
        var candidates = [];
        for (var i=0; i<l.length; i++){
            var currentCubeId = l[i].getAttribute("data-cube-id");
            var range = CubeViewer_getWavelengthRange(currentCubeId);
            var lambda_A_wouldBePixelShift = Math.round(OnImage_wavelengthAToPixel(currentCubeId, lambda_A) - OnImage_getCwl_px(currentCubeId));
            var ranging = {
                range: range,
                lambda_A: lambda_A,
                found: false,
                lambda_A_wouldBePixelShift: lambda_A_wouldBePixelShift,
                inRangeByWavelength: (range.lambdas_A[0] <= lambda_A) && (range.lambdas_A[1] >= lambda_A),
                inRangeByPixelShift: (lambda_A_wouldBePixelShift >= range.pixelShifts[0]) && (lambda_A_wouldBePixelShift <= range.pixelShifts[1])
            };

            if ((ranging.inRangeByWavelength) || (ranging.inRangeByPixelShift)){
                candidates.push([currentCubeId, l[i]]);
                ranging.found = true;
            }            
        }  
        if (preferCube){
            if (typeof preferCube != "string"){
                preferCube = preferCube.id;
            }
            var sortingFor = {
                preferCube: preferCube,
                candidates: candidates,
                wavelength_A: lambda_A
            };
            candidates.sort(function (a, b){
                if (a[0] == preferCube){
                    return -1;
                }
                if (b[0] == preferCube){
                    return 1;
                }
                var a_delta = Math.abs( OnImage_getCwl_A(a[0]) - lambda_A);
                var b_delta = Math.abs( OnImage_getCwl_A(b[0]) - lambda_A);
                return a_delta - b_delta;
                return 0;
            });
            console.log("sorted for ", sortingFor);
        }
        console.log("kandidato", candidates);      
        var openCandidates = candidates.filter(function (e){
            return CubeViewer_isItOpen(e[0]);
        });    
        console.log("open candidates are", openCandidates);    

        if ((candidates.length > 0)&&(openCandidates.length == 0)){
            console.log("opening candidate 0: ");
            CubeListItem_click(candidates[0][1], lambda_A);
        }else{           
            if (candidates.length > 0){
                // this goes recoursive            
                var delta = Math.abs(lambda_A - OnImage_getLastClickOnRealWavelength(openCandidates[0][0]));
                if (delta < 0.005){
                    // recoursive
                }else{
                    console.log("re-opening? candidate 0: ");
                    CubeListItem_click(openCandidates[0][1], lambda_A);
                }
            }else{

            } 
        }    
        
        if (0 == candidates.length){
            Blink_blinkPrepared();
        } 
    };


    function Blinker_prepare(elem){
        elem.setAttribute("data-prepared-for-blinking", Date.now());
    }

    function Blinker_executeBlinkOn(elem){
        console.log(elem, "should blink");
        elem.setAttribute("data-blinks-left", 6);
        var magic = "blinker-marker-class";
        var i = setInterval(function (){
            var a = elem.getAttribute("data-blinks-left");
            var newClassName = elem.className+'';
            newClassName = newClassName.split(" ").filter(function (e){
                return (e != '')&&(e != magic);
            });
            if (0 == a){
                //nothing, to make sure the class doens't get stuck
            }else{
                if (a % 2 == 0){
                    newClassName.push(magic);
                }
            }
            elem.className = newClassName.join(' ');
            a--;            
            elem.setAttribute("data-blinks-left", a);

            if (a <= 0){
                clearInterval(i);                                
            }
        }, 300);
    }

    function Blink_blinkPrepared(){
        var e = document.querySelectorAll('[data-prepared-for-blinking]:not([data-prepared-for-blinking=""])');
        var present = Date.now();
        for (var i=0; i<e.length; i++){
            var preppedAt = e[i].getAttribute("data-prepared-for-blinking");
            if (Date.now() - preppedAt < 1000){
                Blinker_executeBlinkOn(e[i]);
            }
        }        
    };

    function WavelengthOfInterestClicked(elem){
        Blinker_prepare(elem);
        Spectrum_showWavelengthA(elem.getAttribute("data-wavelength-angstrom"));
    }

    var SpectrumIntervals = {
        id: -1,
        startedAt: 0,
        stop: function (){
            if (this.id > -1){
                clearInterval(this.id);
                this.id = -1;
            }
        },
        stopIfOld: function (){
            if (Date.now() - this.startedAt > 100){
              this.stop();
            }
        },
        start: function (cb){
            this.stop();
            this.startedAt = Date.now();
            this.id = setInterval(function (){
               cb();
            }, 500);
        },
    };

    document.body.addEventListener("click", function (){
        SpectrumIntervals.stopIfOld(); 
    });    
    document.body.addEventListener("onmousedown", function (){
        SpectrumIntervals.stopIfOld(); 
    });    


    function SpectrumPixelShiftPlaybackClick(sender, delta){
        var img = getImgFromSender(sender);
        var pixelShift = parseFloat(OnImage_getThePixelShiftEditor(img).value);
        var limits = OnImage_getSpectrumExtremes(img);
        console.log(limits);
        var cwl_px = OnImage_wavelengthAToPixel(img, OnImage_getCwl_A(img));

        SpectrumIntervals.start(function (){
            pixelShift += delta;
            if (pixelShift > limits.pixelShifts[1]){
                pixelShift = limits.pixelShifts[0];
            }
            if (pixelShift < limits.pixelShifts[0]){
                pixelShift = limits.pixelShifts[1];
            }
            var desiredWavelength_A = OnImage_pixelToWavelength_A(img, cwl_px+pixelShift);
            OnImage_updateTheInputsFromWavelength(img, desiredWavelength_A);
            Spectrum_showWavelengthA(desiredWavelength_A, img);        
        });
    }

    function SpectrumPixelShiftPlaybackLoopClick(sender, delta, width){
        var img = getImgFromSender(sender);
        var pixelShift = parseFloat(OnImage_getThePixelShiftEditor(img).value);
        var limits = OnImage_getSpectrumExtremes(img);
        console.log(limits);
        var cwl_px = OnImage_wavelengthAToPixel(img, OnImage_getCwl_A(img));
        var direction = 1;

        var maxLimit = Math.min(limits.pixelShifts[1], pixelShift + width);
        var minLimit = Math.max(limits.pixelShifts[0], pixelShift - width);

        SpectrumIntervals.start(function (){
            pixelShift += delta *direction;
            if ((pixelShift > maxLimit) || (pixelShift < minLimit)){
                direction *= -1;
                pixelShift += (delta*2) *direction;                
            }
            var desiredWavelength_A = OnImage_pixelToWavelength_A(img, cwl_px+pixelShift);
            OnImage_updateTheInputsFromWavelength(img, desiredWavelength_A);
            Spectrum_showWavelengthA(desiredWavelength_A, img);        
        });
    }

    function SpectrumPixelShiftStopPlaybackClick(elem, dummy){
        SpectrumIntervals.stop();
    }


    function cubeSlice_gammaSet(sender, baseValue, adjustBy){
        var i = document.getElementById("current-slice");
        var k = "data-brightness";
        if (!i.getAttribute(k)){
            i.setAttribute(k, 100);
        }
        if (false === baseValue){
            baseValue = parseInt(i.getAttribute(k));
        }
        if (true === baseValue){
            baseValue = 100;
        }
        adjustBy *= 10;

        var newValue = baseValue*1 + adjustBy;

        newValue = Math.min(200, Math.max(newValue, 30));
        i.setAttribute(k, newValue);

        i.style.filter = 'brightness('+newValue+'%)';
    }
</script>
