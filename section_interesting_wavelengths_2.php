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

        var columns = [
            {
                fieldName: "lambda_A", 
                caption: "wavelength [Angstrom]",
            },
            {
                fieldName: "width_mA",
                caption: "line width [milliAngstrom], or<br> realitive intensity (int), or<br>irradiance (irr)"
            },
            {
                fieldName: "caption",
                caption: "line name, chemical etc."
            },    
            {
                fieldName: "displayImportance",
                caption: "imaging ranking<br>[arbitrary points,<br>the higher the better]"
            }
        ];
        var rows = [];
        function captionHelper(a){
            a = a.caption;
            a = a.split('~').join('');
            a = a.split('(').join(''); 
            a = a.trim(); 
            return a;
        }
        function parseFloatOrDefault(f, d){
            f = (f+'').replace('(i)', '');
            f = (f+'').replace('(int)', '');
            f = (f+'').replace('(irr)', '');
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
            if (localSortby == 'caption'){
                a = captionHelper(a);
                b = captionHelper(b);
                delta = a.localeCompare(b);
                if (0 == delta){
                   localSortby = 'lambda_A';
                }
            };
            if (localSortby != 'caption'){
                a = parseFloatOrDefault(a[localSortby], 50);
                b = parseFloatOrDefault(b[localSortby], 50);
                a = Math.round(a*1000);
                b = Math.round(b*1000);
                delta = (a-b);
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
                fieldName: c.fieldName    
            });
        });
        
        return { rows: rows, cols: cols };
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
        var woi = Spectrum_getWavelengthList_asTable(sortby);
        console.log(woi);
        var editorValue = (editor.value+'').trim().toLowerCase().split(', ').join(',').split(' ').filter(function (e){
            return e.length > 0;
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

        if (editorValueIsNumberPairOf.length == 2){
                if (editorValueIsNumberPairOf[1] < 200){
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


        woi.rows = woi.rows.filter(function (r){            
            if (editorValueIsNumberPairOf.length == 2){
                return (r.lambda_A >= editorValueIsNumberPairOf[0]) && (r.lambda_A <= editorValueIsNumberPairOf[1]);
            }
            return true;
        }).filter(function (r){    
            var s = r.caption.split('(').join('').split('~').join('').split(', ').join(',').trim();
            if ('he' == editorValue){
                // special case: helium
                s = s.replace('theta', '');
            }
            if ('al' == editorValue){
                // special case: aluminium
                s = s.replace('alpha', '');
            }
            if ('co' == editorValue){
                // special case: cobalt
                s = s.replace('corona', '');
            }
            if ('h' == editorValue){
                console.log(s);
                // special case: hydrogen
                if (s.indexOf('H ') == 0){
                    return true;
                }else{
                    return false;
                }                
            }
            if ('v' == editorValue){
                console.log(s);
                // special case: vanadium
                if (s.indexOf('V ') == 0){
                    return true;
                }else{
                    return false;
                }                
            }
            if ('ionized' == editorValue){
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
            }
           return s.toLowerCase().indexOf(editorValue) > -1;
        });

        var table = document.createElement("table");
        table.width="80%";
        table.border="1";
        table.style=" border-collapse: collapse;";
        var tr = document.createElement("tr"); 
        tr.style.backgroundColor = "silver";   
        woi.cols.forEach(function (c){
            var td = document.createElement("td");
            td.style.backgroundColor = "silver";   
            var k = "Spectrum_constructTableInto('"+elem.id+"', '"+c.onclickSortby+"')";
            td.innerHTML = '<strong><span style="cursor:pointer" onclick="'+k+'">'+c.caption+'</span></strong>';                        
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