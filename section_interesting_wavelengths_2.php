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
              newWoi.push(w);
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
                caption: "line width [milliAngstrom]"
            },
            {
                fieldName: "caption",
                caption: "line name, chemical etc."
            },    
            {
                fieldName: "displayImportance",
                caption: "imaging ranking<br>[arbitrary points]"
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
           
        woi.sort(function (a,b){
            var delta = 0;
            if (sortby == 'caption'){
                a = captionHelper(a);
                b = captionHelper(b);
                delta = a.localeCompare(b);
            }else{
                a = parseFloat(a[sortby]) || 50;
                b = parseFloat(b[sortby]) || 50;
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
                if (c.fieldName == "displayImportance"){
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

    function Spectrum_constructTableInto(elem, sortby = ""){
        console.log("construct "+sortby);
        if (typeof elem === 'string'){
            elem = document.getElementById(elem);
        }
        var woi = Spectrum_getWavelengthList_asTable(sortby);
        console.log(woi);
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
                td.innerHTML = pre+(r[c.fieldName] || '')+post;            
                tr.appendChild(td);
            });
            table.appendChild(tr);
        });
        

        elem.innerHTML = '<div>&nbsp;</div>';
        elem.appendChild(table);
    }

    function Spectrum_toggleWoiTableVisibility(wrapperId){
        var elem = document.getElementById(wrapperId);
        var visible = true;
        if (elem.style.display == 'none'){
            visible = false;
        }else{
        }
        if (elem.innerHTML.length < 100){
            visible = false;
        }
        if (!visible){
            elem.style.display = '';
            Spectrum_constructTableInto(elem, "-width_mA");
        }else{
            elem.style.display = 'none';
        }
    }

</script>    