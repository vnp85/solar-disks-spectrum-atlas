<script>
function Filters_wavelengthToCommonFilters(lambda_A){
    if (!window.memoizedLambdaToFilter){
        window.memoizedLambdaToFilter = {
            items: [
                {
                    caption: "O III",
                    cwl: 5007,
                    fwhm: 70
                },
                {
                    caption: "H beta",
                    cwl: Spectrum_nameToWavelengthA("H beta"),
                    fwhm: 70
                },    
                {
                    caption: "H alpha",
                    cwl: Spectrum_nameToWavelengthA("H alpha"),
                    fwhm: 70
                },    
                {
                    caption: "Ca KH line",
                    cwl: (Spectrum_nameToWavelengthA("CaK")+Spectrum_nameToWavelengthA("CaH")) / 2,
                    fwhm: 70
                },    

                {
                    caption: "Ca K 3nm",
                    cwl: Spectrum_nameToWavelengthA("CaK"),
                    fwhm: 30
                },    

                {
                    caption: "Sloan u",	
                    cwl:	3543,
                    fwhm:	650,
                },

                {
                    caption: "S II",	
                    cwl:	6720,
                    fwhm:	70,
                },

                {
                    caption: "Sloan g",
                    cwl: 4770,
                    fwhm: 1490,
                },    
        
                {
                    caption: "Sloan r",
                    cwl: 6231,
                    fwhm: 1400
                },    
                
                {
                    caption: "Sloan i",
                    blue: 7000,
                    red: 8500
                },    

                {
                    caption: "Sloan z-s",
                    blue: 8250,
                    red: 9250
                },
                
                {

                    caption: "PanSTARRS Y",
                    cwl: 10004,
                    fwhm: 1080
                },

                {
                    caption: "(Chroma) Bessel U",
                    red: Spectrum_nameToWavelengthA("CaK")+20,
                    blue: 3250,
                },    
            ],
            solutions: {},
        };
        window.memoizedLambdaToFilter.items = window.memoizedLambdaToFilter.items.map(function (e){
           if (e.red){
                e.fwhm = (e.red-e.blue);
                e.cwl = e.blue + e.fwhm/2;
           }
           return e;
        });
        console.log(window.memoizedLambdaToFilter);
    }
    var lambda_A_key = 's'+Math.round(lambda_A);
    if (!window.memoizedLambdaToFilter.solutions[lambda_A_key]){
        window.memoizedLambdaToFilter.solutions[lambda_A_key] = [];
        window.memoizedLambdaToFilter.items.forEach(function (i){
            if (lambda_A >= i.cwl - i.fwhm/2){
                if (lambda_A <= i.cwl + i.fwhm/2){
                    window.memoizedLambdaToFilter.solutions[lambda_A_key].push(i.caption);
                }
            }
        });
    }
    return window.memoizedLambdaToFilter.solutions[lambda_A_key];
}
</script>
<script>
    function Spectrum_getWavelengthList_asTable(sortby=false){
        var woi = Spectrum_getWavelengthList();
        woi.sort(function (a,b){
            a = a.lambda_A*1000;
            b = b.lambda_A*1000;
            return a-b;
        });
        var newWoi = [];
        var li = -1;
        woi.forEach(function (w){
           if (Math.abs(li - w.lambda_A) > 0.01){
              li = w.lambda_A;
              newWoi.push(JSON.parse(JSON.stringify(w)));
           }else{
              // get info from
              Object.keys(w).forEach(function (key){
                 var lastI = newWoi.length-1;
                 if (lastI >= 0){
                    newWoi[lastI][key] = newWoi[lastI][key] || w[key];
                 }
              });
           }
        });
        woi = newWoi;
        woi = woi.map(function (e){
            e.inRangeOfFilters = Filters_wavelengthToCommonFilters(e.lambda_A).join(', ');
            if ('' == (e['width_mA'] || '')){
                if (e['relativeIntensity']){
                    e['width_mA'] = e['relativeIntensity']+'(int)';
                };    
            }
            if ('' == (e['width_mA'] || '')){
                if (e['irradiance']){
                    e['width_mA'] = e['irradiance']+'(irr)';
                };    
            }
            return e;
        });




        var multi = 1;
        if (!sortby){
           sortby = "lambda_A";
        }        
        if (sortby.split('')[0] == '-'){
            multi = -1;
            sortby = sortby.replace('-', '');
        }else{
            multi = 1;
        }

        function chromosphereTableViewFilter(e){
            if (e == -1){
                e = '';
            }
            return e;
        }

        var columns = [
            {
                fieldName: "lambda_A", 
                caption: "wavelength [Angstrom]",
                alwaysVisible: true,                
            },
            {
                fieldName: "width_mA",
                caption: "line width [milliAngstrom], or<br> realitive intensity (int), or<br> intensity in infrared (iri), or<br>irradiance (irr)",
                alwaysVisible: false,
                visible: true,
            },
            {
                fieldName: "caption",
                caption: "line name, chemical etc.",
                type: "string",
                alwaysVisible: true,                
            },    
            {
                fieldName: "displayImportance",
                caption: "imaging ranking<br>[arbitrary points,<br>the higher the better]",
                alwaysVisible: false,
                visible: false,
            },
            {
                fieldName: "inRangeOfFilters",
                caption: "in range of<br>common filters",
                type: "string",
                alwaysVisible: false,
                visible: false,
            },
            {
                fieldName: "chromosphere_flash_intensity",
                caption: "flash spectrum intensity",
                datasource: "https://articles.adsabs.harvard.edu/pdf/1930ApJ....71....1M",
                type: "number",
                alwaysVisible: false,
                visible: true,
                viewFilter: chromosphereTableViewFilter
            },
            {    

                fieldName: "chromosphere_disk_intensity",
                caption: "chromosphere disk intensity",
                datasource: "https://articles.adsabs.harvard.edu/pdf/1930ApJ....71....1M",
                type: "number",
                alwaysVisible: false,
                visible: false,
                viewFilter: chromosphereTableViewFilter
            },{
                
                fieldName: "chromosphere_formation_height_km",
                caption: "chromospheric formation height",
                datasource: "https://articles.adsabs.harvard.edu/pdf/1930ApJ....71....1M",
                type: "number",
                alwaysVisible: false,
                visible: false,
                viewFilter: chromosphereTableViewFilter
            },{
                
                fieldName: "chromosphere_chemical",
                caption: "chromosphere chemical",
                datasource: "https://articles.adsabs.harvard.edu/pdf/1930ApJ....71....1M",
                type: "string",
                alwaysVisible: false,
                visible: false,
                viewFilter: chromosphereTableViewFilter
            }, {
                fieldName: "redBookIntensity_disk",
                caption: "Red Book, disk intensity",
                datasource: "https://babel.hathitrust.org/cgi/pt?id=uc1.32106002409180&seq=5",
                type: "string",
                alwaysVisible: false,
                visible: false,
                viewFilter: null,
                toSortValueFilter: function (a){
                    var bag = { orig: a };                    
                    var sample = 'ZZZZZZZZZZZZZZZZ';
                    if (!a){
                        a = sample;
                    }
                    a = parseFloat(a);
                    if (a === a){
                        // number
                        a = 100000 - (100 + a);
                    }else{
                        a = 0;
                    }
                    a += '';
                    while (a.length < sample.length){
                        a = 'Z'+a;
                    }
                    bag.res = a;
                    console.log(bag);
                    return a;
                }
            }    
        ].map(function (e){
            if (e.alwaysVisible){
                e.visible = true;
            }
            if (e.type){
                // carry on
            }else{
                e.type = "number";
            }
            return e;
        });
        function getColumnType(name){
            var ret = "number";
            columns.forEach(function (k){
                if (k.fieldName == name){
                    if (k.type){
                        ret = k.type;
                    }
                }
            });
            //console.log("column type of "+name+' is '+ret);
            return ret;
        }
        function applyTheToSortValueFilter(name, value){
            columns.forEach(function (k){
                if (k.fieldName == name){
                    if (k.toSortValueFilter){
                        value = k.toSortValueFilter(value);
                    }
                }
            });            
            return value;
        }
        var rows = [];
        function captionHelper(a){
            if (!a){
                a = '';
            }
            a = a+'';
            a = a.split('~').join('');
            a = a.split('(').join(''); 
            a = a.trim(); 
            return a;
        }
        function parseFloatOrDefault(f, d){
            f = (f+'').replace('(i)', '');
            f = (f+'').replace('(int)', '');
            f = (f+'').replace('(irr)', '');
            f = (f+'').replace('(iri)', '');
            f = (f+'').trim();
            f = parseFloat(f);
            if (f === f){
                // number
            }else{
                f = d;
            }
            return f;
        }
           
        woi.sort(function (a,b){
            var delta = 0;
            
            var localSortby = sortby;
            
            if (getColumnType(localSortby) == 'string'){
                a = captionHelper(applyTheToSortValueFilter(localSortby, a[localSortby]));
                b = captionHelper(applyTheToSortValueFilter(localSortby, b[localSortby]));
                delta = a.localeCompare(b);
                if (0 == delta){
                   localSortby = 'lambda_A';
                }
            };
            if (getColumnType(localSortby) != 'string'){
                a = parseFloatOrDefault(a[localSortby], 50);
                b = parseFloatOrDefault(b[localSortby], 50);
                a = Math.round(a*1000);
                b = Math.round(b*1000);
                delta = (a-b);
            }
            if (0 == delta){
                localSortby = 'lambda_A';
                if (getColumnType(localSortby) != 'string'){
                    a = parseFloatOrDefault(a[localSortby], 50);
                    b = parseFloatOrDefault(b[localSortby], 50);
                    a = Math.round(a*1000);
                    b = Math.round(b*1000);
                    delta = (a-b);
                }
            }
           return multi * delta;
        }).map(function (e){
            e.caption = e.caption.split(' ').filter(function (w){
                return w.indexOf("Aring") == -1;
            }).join(' ');
            e.caption = e.caption.split('%wavelength%')[0].trim();
            return e; 
        }).forEach(function (e){
            var i = {};
            columns.forEach(function (c){
                i[c.fieldName] = e[c.fieldName] || '';
                if ("displayImportance" == c.fieldName){
                    i[c.fieldName] = Math.round(i[c.fieldName]);
                }
                if (c.viewFilter){
                    i[c.fieldName] = c.viewFilter(i[c.fieldName]);
                }
            });
            rows.push(i);
        });
        var cols = [];
        columns.forEach(function (c){
            var ocs = c.fieldName;
            if (c.fieldName == sortby){
                if (1 == multi){
                    ocs = '-'+ocs;
                    //arrow = '';
                }
            }            
            cols.push({
                onclickSortby: ocs,
                caption: c.caption,
                fieldName: c.fieldName,
                visible: c.visible,
                alwaysVisible: c.alwaysVisible,    
                datasource: c.datasource || false,
            });
        });

        var csvRows = [];
        var csvColumns = [
            {
                fieldName: "lambda_A",
                csvName: "lambda_A",
            },
            {
                fieldName: "caption",
                csvName: "caption",
            },
            {
                fieldName: "width_mA",
                csvName: "width mA, irradiance or intensity",
            }
        ];  
        function csvRows_add(a){
            var line = [];
            a.forEach(function (e){
                e = (e+'').split('"').join("'").split("\r").join(" ").split("\n").join(" ").trim();
                line.push(e);
            });
            csvRows.push('"'+line.join('","')+'"');
        }       
        csvRows_add(csvColumns.map(function (fn){
                return fn.csvName;
        }));

        rows.forEach(function (e){            
            var line = [];
            csvColumns.forEach(function (c){                
                line.push(e[c.fieldName]);
            });    
            csvRows_add(line);
        });
        
        var csvText = csvRows.join("\r\n");
        console.log("CSV text of the wavelength table, should someone want to copy it");
        console.log(csvText);

        return { rows: rows, cols: cols, csvRows: csvRows, csvText: csvText };
    }


    var memoizedConstructTableInto = {};
    function Spectrum_constructTableInto(elem, sortby = ""){
        var editor = document.getElementById('woi-table-search-query-text-input');
        if (!elem){
            elem = memoizedConstructTableInto.elem;
            sortby = memoizedConstructTableInto.sortby;
        }else{
            memoizedConstructTableInto = {
                elem: elem,
                sortby:sortby
            };
        }

        console.log("construct "+sortby);
        if (typeof elem === 'string'){
            elem = document.getElementById(elem);
        }
        var columnVisibilitiesWrapper = elem.parentNode.getElementsByClassName("woi-column-visibility-wrapper")[0];
        var woi = Spectrum_getWavelengthList_asTable(sortby);
        var elementsTable = [
            { text: ['helium'], symbol: "he"},
            { text: ['hydrogen'], symbol: "h", avoid: "CaH"},
            { text: ['sulphur', 'sulfur'], symbol: "s", seek: ["S I", 'S V'], avoid: ["Sc", "Sr", "Si", "Cs", "Paschen"]},
            { text: ['titan', 'titanium'], symbol: "ti"},
            { text: ['natrium', 'sodium'], symbol: "na"},
            { text: ['magnezium', 'magnesium'], symbol: "mg"},
            { text: ['calcium'], symbol: "ca"},
            { text: ['kalium', 'potassium', 'potasium'], symbol: "k", avoid: "CaK"},
            { text: ['barium'], symbol: "ba"},
            { text: ['copper', 'cupper', 'cuprum'], symbol: "cu"},
            { text: ['chrome', 'chromium'], symbol: "cr"},
            { text: ['aluminum', 'aluminium'], symbol: "al"},
            { text: ['strontium', 'stroncium'], symbol: "sr"},
            { text: ['scandium', 'skandium'], symbol: "sc"},
            { text: ['carbon'], symbol: "c", seek: ["C I", "C V"], avoid: ['Sc']},
            { text: ['silicium', 'silicon'], symbol: "si"},
            { text: ['nickel', 'nikkel', 'nichel'], symbol: "ni"},
            { text: ['mangan', 'manganese'], symbol: "mn"},
            { text: ['vanadium'], symbol: "v"},
            { text: ['cobalt'], symbol: "co"},
        ];
        var colorTable = [
            { color: "red", limits: [7000, 6300], found: false },
            { color: "orange", limits: [5800, 6400], found: false },
            { color: "yellow", limits: [5700, 5900], found: false },
            { color: "lime", limits: [5500, 5700], found: false },
            { color: "green", limits: [5000, 5500], found: false },
            { color: "tiel", limits: [4900, 5050], found: false },
            { color: "blue", limits: [4600, 4950], found: false },
            { color: "violet", limits: [3900, 4600], found: false },

            { color: ["infra", "infrared", "ir"], limits: [7000, 99999], found: false },
            { color: ["uv", "ultraviolet"],       limits: [   5,  4000], found: false },
        ].map(function (e){
            if (Array.isArray(e.color)){
                // already an array
            }else{
                e.color = [ e.color ];
            }
            e.limits.sort(function (a, b){
                var delta = a-b;
                if (delta < 0){
                    return -1;
                }
                if (delta > 0){
                    return 1;
                }
                return 0;
            });
            return e;
        });
        var weakLimit = 50;
        var strongLimit = 150;
        var infinityStrong = 50000000;
        var infinityWeak = 0;
        var strengthTable = [
            { lineStrength: "weak",   limits: [infinityWeak, weakLimit], found: false },
            { lineStrength: ["strong", "string" /* programmer's typo */ ], limits: [strongLimit, infinityStrong], found: false },
            { lineStrength: "medium", limits: [weakLimit, strongLimit], found: false },
            { lineStrength: "midweak",   limits: [infinityWeak, strongLimit], found: false },
            { lineStrength: "midstrong", limits: [weakLimit, infinityStrong], found: false },
        ].map(function (e){
            if (Array.isArray(e.lineStrength)){
                // already an array
            }else{
                e.lineStrength = [ e.lineStrength ];
            }
            e.limits.sort(function (a, b){
                var delta = a-b;
                if (delta < 0){
                    return -1;
                }
                if (delta > 0){
                    return 1;
                }
                return 0;
            });
            return e;
        });;
        console.log(woi);
        var shouldNotContain = [];
        var shouldContain = []
        var editorValue = (editor.value+'').trim().toLowerCase().split(', ').join(',').split(' ').filter(function (e){
            colorTable.forEach(function (k){                
                k.color.forEach(function (kv){
                    if (kv === e){
                        k.found = true;
                        e = '';
                    }
                });
            });
            return e.length > 0;
        }).filter(function (e){
            strengthTable.forEach(function (k){                
                k.lineStrength.forEach(function (kv){
                    if (kv === e){
                        k.found = true;
                        e = '';
                    }
                });
            });
            return e.length > 0;
        }).map(function (maybeChemical){
            var mc = maybeChemical.toLowerCase();
            elementsTable.forEach(function (elem){
                elem.text.forEach(function (longName){
                    if (longName === mc){
                        maybeChemical = elem.symbol;
                        if (elem.avoid){
                            if (!elem.avoid.forEach){
                                elem.avoid = [elem.avoid];                                
                            }
                            elem.avoid.forEach(function (avoid1){
                                avoid1.split(',').forEach(function (avo){
                                    shouldNotContain.push(avo);
                                });
                            });
                        }
                        if (elem.seek){                          
                            if (elem.seek.forEach){
                                shouldContain.push(elem.seek.join('|'));
                            }  else {
                                shouldContain.push( elem.seek );
                            }
                                
                        }                        
                    }
                });
            });
            return maybeChemical;
        }).join(' ');
        var editorValueIsNumberPairOf = [];

        var editorValueWords =editorValue.split(' ');


        for (var ew=0; ew<editorValueWords.length; ew++){
            [' ', ','].forEach(function (nc){
                if (editorValueWords[ew].indexOf(nc) > -1){
                    var n1 = parseFloat(editorValueWords[ew].split(nc)[0].trim());
                    var n2 = parseFloat(editorValueWords[ew].split(nc)[1].trim());
                    if ((n1 === n1)&&(n2 === n2)){
                        editorValueIsNumberPairOf = [n1, n2];
                    }
                    editorValueWords[ew] = '';
                }
            });
        } 

        editorValue = editorValueWords.filter(function (e){ return e.length > 0; }).join(' ');

        if (editorValue.toLowerCase().trim() == 'h i'){
            // neutral hydrogen
            editorValue = 'hydrogen';
        }
        if (editorValue.toLowerCase().trim() == 'h'){
            // neutral hydrogen
            editorValue = 'hydrogen';
        }
        if (editorValue.toLowerCase().trim() == 's'){            
            editorValue = 'sulphur';
        }
        if (editorValue.toLowerCase().trim() == 's i'){            
            editorValue = 'S I';
        }
        ['alpha', 'beta', 'gamma', 'delta', 'epsilon'].forEach(function (jorgos){
            var jorgos1 = '';
            jorgos.split('').forEach(function (c){
                jorgos1 += c;
                if (editorValue.toLowerCase().trim() == 'paschen '+jorgos1){
                    editorValue = 'paschen-'+jorgos1;
                }
                if (editorValue.toLowerCase().trim() == 'balmer '+jorgos1){
                    shouldNotContain.push('Paschen');
                    editorValue = 'H '+jorgos1;
                }
            });
        });

        if (editorValue.trim().toLowerCase() == 'balmer'){
            shouldNotContain.push('Paschen');
        }        

        
        

        
        var interestingElements = elementsTable.map(function (e){
            e = e.symbol.split('');
            e[0] = e[0].toUpperCase();
            e = e.join('');
            return e;
        });
        

        editorValue = editorValue.split(' ').filter(function (w){
            return w.length > 0;
        }).join(' ');
        var upperNeutralRequested = false;
        var upperIonizedRequested = false;
        editorValue = ' '+editorValue+' ';
        interestingElements.forEach(function (e){
            var neutru = (' neutral '+e+' ').toLowerCase();
            var neutru2 = (' '+e+' I ').toLowerCase();
            if (editorValue.toLowerCase().indexOf(neutru) > -1){
                var pattern = new RegExp(neutru, 'gi');
                editorValue = editorValue.replace(pattern, neutru2);
            };
            if (editorValue.toLowerCase().indexOf(neutru2) > -1){
                upperNeutralRequested = true;
            }
        });
        interestingElements.forEach(function (e){
            var ion = (' ionized '+e+' ').toLowerCase();
            var ion2 = (' '+e+' ^^ ').toLowerCase();
            if (editorValue.toLowerCase().indexOf(ion) > -1){
                var pattern = new RegExp(ion, 'gi');
                editorValue = editorValue.replace(pattern, ion2);
            };
            if (editorValue.toLowerCase().indexOf(ion2) > -1){
                upperIonizedRequested = true;
            }
        });


        editorValue = editorValue.split(' ').map(function (w){
            if (w.toLowerCase() == "-H2O".toLowerCase()){
                shouldNotContain.push('H2O');
                w = '';
            }
            if (w.toLowerCase() == "-O2".toLowerCase()){
                shouldNotContain.push('O2');
                w = '';
            }
            if (w.toLowerCase() == '-atm'){
                w = '';
                shouldNotContain.push('atm');
            }
            if (w.toLowerCase() == '-H'.toLowerCase()){
                shouldNotContain.push('alpha');
                shouldNotContain.push('beta');
                shouldNotContain.push('gamma');
                shouldNotContain.push('delta');
                shouldNotContain.push('epsilon');
                shouldNotContain.push('dzeta');
                shouldNotContain.push('eta');
                shouldNotContain.push('theta');
                shouldNotContain.push('H 8');
                shouldNotContain.push('H 9');
                shouldNotContain.push('H 10');
                shouldNotContain.push('H 11');
                shouldNotContain.push('H 12');
                shouldNotContain.push('H 13');
                shouldNotContain.push('H 14');
                shouldNotContain.push('H 15');

                shouldNotContain.push('paschen');                
                shouldNotContain.push('Paschen');                
                w = '';
            }
            interestingElements.forEach(function (chemical){                
                if (w.toLowerCase() == '-'+chemical.toLowerCase()){
                    if (w == '-ca'){
                        shouldNotContain.push('CaK');
                        shouldNotContain.push('CaH');
                    }
                    w = '';
                    shouldNotContain.push(chemical+' I');
                    shouldNotContain.push(chemical+' V');
                    shouldNotContain.push(chemical+' X');
                }
            });
            return w;
        }).join(' ');

        console.log("linus" + editorValue);

        editorValue = editorValue.split(' ').filter(function (e){ return e.length > 0; }).join(' ');

        if (editorValueIsNumberPairOf.length == 2){
                if (editorValueIsNumberPairOf[1] < 1000){
                    // format is cwl, fwhm
                    var lambda = editorValueIsNumberPairOf[0];
                    editorValueIsNumberPairOf[0] = lambda - editorValueIsNumberPairOf[1];
                    editorValueIsNumberPairOf[1] = lambda + editorValueIsNumberPairOf[1];
                }else{
                    // format is blue, red or red, blue
                    if (editorValueIsNumberPairOf[0] > editorValueIsNumberPairOf[1]){
                        var dummy = editorValueIsNumberPairOf[0];
                        editorValueIsNumberPairOf[0] = editorValueIsNumberPairOf[1];
                        editorValueIsNumberPairOf[1] = dummy;
                    }
                }
        };    

        var maximumIntensity = 1000000;
        var minimumIntensity = 0;

        if (editorValue.indexOf('>') > -1){
            var fish = editorValue.split('>');
            var left = fish[0].trim().split(' ').pop();
            var right = fish[1].trim().split(' ').shift();
            if (left == 'i'){
                minimumIntensity = right;
            }
            if (right == 'i'){
                maximumIntensity = left;
            }
            editorValue = editorValue.replace(left+'>'+right,' ');
            editorValue = editorValue.replace(left+' > '+right,' ');
            editorValue = editorValue.replace(left+'> '+right,' ');
            editorValue = editorValue.replace(left+' >'+right,' ');
        }
        if (editorValue.indexOf('<') > -1){
            var fish = editorValue.split('<');
            var left = fish[0].trim().split(' ').pop();
            var right = fish[1].trim().split(' ').shift();
            if (left == 'i'){
                maximumIntensity = right;
            }
            if (right == 'i'){
                minimumIntensity = left;
            }
            editorValue = editorValue.replace(left+'<'+right,' ');
            editorValue = editorValue.replace(left+' < '+right,' ');
            editorValue = editorValue.replace(left+'< '+right,' ');
            editorValue = editorValue.replace(left+' <'+right,' ');
        }
        var limits = {
            lambda_blue_A: 0,
            lambda_red_A: 10e6,
            minimumIntensity: minimumIntensity,
            maximumIntensity: maximumIntensity,
            ionizedRequested: false || upperIonizedRequested,
            neutralRequested: false || upperNeutralRequested,
            shouldNotContain: shouldNotContain,
            shouldContain: shouldContain,
            editorValue: editorValue
        };        

        colorTable.forEach(function (k){
            if (k.found){
                limits.lambda_blue_A = k.limits[0];
                limits.lambda_red_A  = k.limits[1];
            }
        });
        strengthTable.forEach(function (k){
            if (k.found){
                limits.minimumIntensity  = k.limits[0];
                limits.maximumIntensity  = k.limits[1];
            }
        });


        if (editorValue.trim().split(' ').length === 1){
            // one word or empty
            if (editorValue.trim().length > 0){
                var oneSingleNumberCandidate = parseFloat(editorValue.trim());
                if (oneSingleNumberCandidate === oneSingleNumberCandidate){
                    limits.oneSingleNumberCandidate = oneSingleNumberCandidate+'';
                }
            }
        }


        

        if (editorValue.split(' ').filter(function (w){
            return w != '';
        }).join(' ').indexOf(' i > ') > -1 ){

        }


        
        var interestingElementsPlusIonized = ['ionized'];
        interestingElements.forEach(function (e){
            interestingElementsPlusIonized.push(e.toLowerCase());
        });

        interestingElementsPlusIonized.forEach(function (elem){
            if (('ir'+elem == editorValue) || ('ir '+elem == editorValue)){
                // we are looking for infrared Ca wavelengths
                limits.lambda_blue_A = 7000;
                limits.editorValue = elem;
            };
            if (('uv'+elem == editorValue) || ('uv '+elem == editorValue)){
                // we are looking for infrared Ca wavelengths
                limits.lambda_red_A = 4000;
                limits.editorValue = elem;
            };                
        });

        if (editorValue.indexOf('ionized') > -1){
            limits.ionizedRequested = true;
            editorValue = editorValue.replace('ionized', '');
        };
        console.log("onbefore checking neutral", editorValue);
        if (editorValue.indexOf('neutral') > -1){
            console.log("neutral requested");
            limits.neutralRequested = true;
            editorValue = editorValue.replace('neutral', '');
            limits.editorValue = limits.editorValue.replace('neutral', '');
        };
        console.log("onbefore checking ionized", editorValue);
        if (editorValue.indexOf('^^') > -1){
            console.log("ionized requested");
            limits.ionizedRequested = true;
            editorValue = editorValue.replace('^^', '');
            limits.editorValue = limits.editorValue.replace('^^', '');
            if (limits.editorValue.trim() != ''){
                woi.rows = woi.rows.filter(function (r){  
                    var s = r.caption.split('(').join('').split('~').join('').split(', ').join(',').trim();
                    var a = s.toLowerCase().indexOf(limits.editorValue.trim().toLowerCase()) > -1;
                    var b = s.indexOf(' II') > -1;
                    b = b || (s.indexOf(' IV') > -1);
                    return a && b;
                });    
            }
        };
        

        console.log("on before woi: ", limits);


        woi.rows = woi.rows.filter(function (r){            
            if (editorValueIsNumberPairOf.length == 2){
                return (r.lambda_A >= editorValueIsNumberPairOf[0]) && (r.lambda_A <= editorValueIsNumberPairOf[1]);
            }
            var containsForbidden = [];
            limits.shouldNotContain.forEach(function (e){
                if (r.caption.indexOf(e) > -1){
                    containsForbidden.push(e);
                }
            });
            if (limits.shouldContain.length > 0){
                var contains = 0;
                limits.shouldContain.forEach(function (c){
                    c.split('|').forEach(function (c1){
                        if (r.caption.indexOf(c1) > -1){
                            contains++;
                        }
                    });
                });
                if (0 == contains){
                    return false;
                }
            }
            if (containsForbidden.length > 0){
                //console.log(r, containsForbidden);
                return false;
            }
            if (limits.oneSingleNumberCandidate){
                var inWhat = r.lambda_A+''; 
                if (inWhat.indexOf(limits.oneSingleNumberCandidate) > -1){
                    return true;
                };
                if (Math.abs(r.lambda_A - limits.oneSingleNumberCandidate) < 1){
                    return true;
                }
                return false;
            }

            var intensity = parseFloat(r.width_mA);
            if (intensity === intensity){
                if (intensity > limits.maximumIntensity){
                    return false;
                }
                if (intensity < limits.minimumIntensity){
                    return false;
                }
            }
            return true;
        }).filter(function (r){    
            var s = r.caption.split('(').join('').split('~').join('').split(', ').join(',').trim();

            if (r.lambda_A < limits.lambda_blue_A){
                return false;
            }
            if (r.lambda_A > limits.lambda_red_A){
                return false;
            }

            if ('vis' == limits.editorValue){
                // we are looking for visual wavelengths
                return (r.lambda_A >= 4000) && (r.lambda_A <= 7000);
            };
            if ('uv' == limits.editorValue){
                // we are looking for ultraviolet wavelengths
                return (r.lambda_A <= 4000);
            };
            if ('ir' == limits.editorValue){
                // we are looking for infrared wavelengths
                return (r.lambda_A >= 7000);
            };
            if ('nir' == limits.editorValue){
                // we are looking for infrared wavelengths
                return (r.lambda_A >= 7000)&&(r.lambda_A <= 2500);
            };            
            if ('10k' == limits.editorValue){
                // we are looking for infrared wavelengths
                return (r.lambda_A >= 9500)&&(r.lambda_A <= 11500);
            };
            if ('bluebook' == limits.editorValue){                
                return (r.lambda_A <= 8770);
            };
            if ('redbook' == limits.editorValue){                
                return (r.lambda_A >= 8770);
            };



            if ('he' == limits.editorValue){
                // special case: helium
                s = s.replace('theta', '');
                s = s.replace('paschen', '');
                s = s.replace('Paschen', '');
            }
            if ('ca ii' == limits.editorValue){
                // special case: calcium                
                ['ca', 'Ca', 'ca ', 'Ca '].forEach(function (calcium){
                    ['k', 'h', 'K', 'H'].forEach(function (letter){
                        s = s.replace(calcium+letter, calcium+letter+' '+calcium.trim()+' ii');
                    });                    
                });
            }
            if ('al' == limits.editorValue){
                // special case: aluminium
                s = s.replace('alpha', '');
            }
            if ('o' == limits.editorValue){
                // special case: oxygen
                s = s.replace('Co I', 'c0balt I');
                s = s.replace('Mo I', 'm0libden I');
                s = s.replace('corona', '');
                s = s.replace('H2O', '');
                s = s.replace('epsilon', '');
                s = s.replace('Na doublet', '');
            }
            if ('c' == limits.editorValue){
                // special case: carbon
                s = s.replace('Sc I', 'skandium I');
                s = s.replace('Ca I', 'kalcium I');
                s = s.replace('corona', '');
            }
            if ('o i' == limits.editorValue){
                // special case: oxygen
                s = s.replace('Co I', 'c0balt I');
                s = s.replace('Mo I', 'm0libden I');
                s = s.replace('corona', '');
                s = s.replace('H2O', '');
                s = s.replace('epsilon', '');
                s = s.replace('Na doublet', '');
            }
            if ('co' == limits.editorValue){
                // special case: cobalt
                s = s.replace('corona', '');
            }
            if ('na' == limits.editorValue){
                // special case: sodium
                s = s.replace('corona', '');
            }
            if (['h', 'hydrogen', 'balmer'].indexOf(limits.editorValue) > -1){
                console.log("special case hydrogen: "+s);
                // special case: hydrogen
                if (s.indexOf('H ') == 0){
                    return true;
                }else{
                    return false;
                }                
            }






            if ('v' == limits.editorValue){
                console.log(s);
                // special case: vanadium
                if (s.indexOf('V ') == 0){
                    return true;
                }else{
                    return false;
                }                
            }
            if (limits.ionizedRequested){
                if (s.indexOf(' II') > -1){
                    return true;
                }
                if (s.indexOf(' IV') > -1){
                    return true;
                }

                // should be an elem in front, to not collide with Vanadium, or rely on the comma
                if (s.indexOf(' V') > -1){
                    return true;
                }
                if (s.indexOf(' X') > -1){
                    return true;
                }
                if (s.indexOf('CaK') > -1){
                    return true;
                }
                if (s.indexOf('CaH') > -1){
                    return true;
                }
            }
            if (limits.neutralRequested){
                if (r.ionized){
                    return false;
                }
                if (s.indexOf(' II') > -1){
                    return false;
                }
                if (s.indexOf(' IV') > -1){
                    return false;
                }

                // should be an elem in front, to not collide with Vanadium, or rely on the comma
                if (s.indexOf(' V') > -1){
                    return false;
                }
                if (s.indexOf(' X') > -1){
                    return false;
                }
                if (s.indexOf('CaK') > -1){
                    return false;
                }
                if (s.indexOf('CaH') > -1){
                    return false;
                }
            }
            if (limits.oneSingleNumberCandidate){
                //we already passed to this point from a previous filter
                s += limits.oneSingleNumberCandidate;
            }
            console.log("limits editorvalue here", limits.editorValue, elementsTable, s);
            var ret = s.toLowerCase().indexOf(limits.editorValue.toLowerCase()) > -1;
            if (!ret){
                // could be elemental something, like sulphur
                elementsTable.forEach(function (e){
                    e.text.forEach(function (t){
                        if (limits.editorValue.toLowerCase() == t.toLowerCase()){
                            var localRet = false;
                            if (e.seek){                                
                                e.seek.forEach(function (es){                                    
                                    if (s.toLowerCase().indexOf(es.toLowerCase()) > -1){
                                        localRet = true;
                                    }
                                })
                            }
                            if (e.avoid){                                
                                e.avoid.forEach(function (es){                                    
                                    if (s.toLowerCase().indexOf(es.toLowerCase()) > -1){
                                        localRet = false;
                                    }
                                })
                            }
                            if (localRet){
                                ret = true;
                            }
                        }
                    });
                });
            }            
            return ret;
        });

        var table = document.createElement("table");
        table.width="80%";
        table.border="1";
        table.style=" border-collapse: collapse;";
        var tr = document.createElement("tr"); 
        tr.style.backgroundColor = "silver";   

        var populateColumnVisibilities = false;
        if (columnVisibilitiesWrapper.getElementsByTagName("input").length == 0){
            populateColumnVisibilities = true;
        }        


        woi.cols.forEach(function (c){
            if (c.alwaysVisible){
                c.visible = true;
            }
            var klassName = "woi-column-visibility-of-"+c.fieldName;
            if (populateColumnVisibilities){
                var ch = document.createElement("input");
                ch.type = "checkbox";
                ch.checked = c.visible;
                ch.className = klassName;
                ch.id = ch.className + '-'+(Math.random()+'-'+Math.random()).split('.').join('-');
                ch.addEventListener("change", function (){
                    Spectrum_constructTableInto();
                });
                var label = document.createElement("label");
                label.for = ch.id;
                var tail = '';
                if (c.datasource){
                    tail += ' (<a href="'+c.datasource+'">src</a>)';
                }
                label.innerHTML = c.caption.split('<br>').join(' ')+tail;
                label.addEventListener("click", function (){
                    ch.checked = !ch.checked;
                    Spectrum_constructTableInto();
                });
                label.style.cursor = "pointer";
                label.style.userSelect = "none";

                var div = document.createElement("div");                
                div.appendChild(ch);
                div.appendChild(label);
                if (c.alwaysVisible){
                    div.style.display = "none";
                }

                columnVisibilitiesWrapper.appendChild(div);                
            }else{
                c.visible = columnVisibilitiesWrapper.getElementsByClassName(klassName)[0].checked;
            }
        });
        woi.cols.forEach(function (c){
            var td = document.createElement("td");
            td.style.backgroundColor = "silver";   
            var k = "Spectrum_constructTableInto('"+elem.id+"', '"+c.onclickSortby+"')";
            td.setAttribute('data-onclick', k);
            if (c.datasource){
                td.setAttribute('data-source', c.datasource);
            }
            td.innerHTML = '<strong><span style="cursor:pointer" >'+c.caption+'</span></strong>';                        
            if (!c.visible){
                td.style.display = "none";
            }
            td.addEventListener("click", function (e){
                eval(k);
            });
            tr.appendChild(td);
        });
        table.appendChild(tr);

        woi.rows.forEach(function (r){
            var tr = document.createElement("tr");    
            woi.cols.forEach(function (c){
                var td = document.createElement("td");
                var pre = '';
                var post = '';
                if (c.fieldName == "lambda_A"){
                    var lambda_A = r[c.fieldName];
                    var color = WavelengthToColor(lambda_A);
                    pre = '<div style="background-color: '+color+'; width:1em; height:1em; border-radius:0.5em; display:inline-block;">&nbsp;</div> &nbsp; ';

                    if (isWavelengthCoveredByCubes(lambda_A)){
                        post += '<span onclick="Spectrum_showWavelengthA('+lambda_A+')" style="cursor: pointer">&#9788;';
                        post += '</span>';
                    }
                }
                var v = ((r[c.fieldName] || '')+'').trim();                
                td.innerHTML = pre+v+post;            
                if (!c.visible){
                    td.style.display = "none";
                }
                tr.appendChild(td);
            });
            table.appendChild(tr);
        });
        

        elem.innerHTML = '<div>&nbsp;</div>';
        elem.appendChild(table);
    }

    function Spectrum_toggleWoiTableVisibility(wrapperId){
        var parent = document.getElementById(wrapperId);
        var elem = parent;
        var editor = document.getElementById('woi-table-search-query-text-input');
        var candidates = elem.getElementsByClassName('woi-table-proper-container');

        if (candidates.length == 1){
            elem = candidates[0];
        }
        var visible = true;
        if (parent.style.display == 'none'){
            visible = false;
        }else{
        }
        if (elem.innerHTML.length < 100){
            visible = false;
        }
        if (!visible){
            parent.style.display = '';
            editor.value = '';
            Spectrum_constructTableInto(elem, "-width_mA");
        }else{
            parent.style.display = 'none';
            editor.value = '';
        }
    }

    function woiTableSearchQueryTextChanged(sender){
        Spectrum_constructTableInto();
    }

</script>    