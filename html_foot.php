<script>
    setTimeout(function (){
        if (URLmanager.parseStateFromUrl()){
            // handled
        }else{
            URLmanager.applyDefaultState();
        }
    }, 500);
    OnAllImages_setSpectrumOpacities(0.5);
</script> 
<?php require_once("section_help.php");    ?>
<div align="center">
    &nbsp;
    <hr>
</div>
<script>
function CubeSlicesEnumerations_checkForContinuity(){
   // the ftp server has made a number on some of the uploads, wtf
   //   so check the integrity
   var dcCount = 0; 
   var okCount = 0;
   var list = document.getElementsByClassName("cube-slices-list-enumeration");
   var itemExtremes = {};
   for (var i=0; i<list.length; i++){
      var item = list[i];
      var itemId = (item.getAttribute("id") || "no-id")+"";
      itemId = itemId.replace('cube-slice-list-cubes-info_cube_', '');
      itemId = itemId.replace('cube-slice-list-cube_', '');
      var s = (item.innerHTML+'').trim();
      if (s.indexOf(',') > -1){
        var s = s.split(',').map(function (e){
            e = e.split('img_C').pop().split('_').shift();
            return parseInt(e, 10);
        });
        itemExtremes[itemId] = [s[0], s[s.length-1]];
        for (var j=1; j<s.length;j++){
            if (s[j] - s[j-1] != 1){
                dcCount++;
                console.log('discontinuity in '+item);
            }else{
                okCount++;
            }
        }
      }      
   }
   var clientSideGenerated = {
      msg: "cube slices integrity check...",
      desc: "generated on "+document.URL,
      dcCount: dcCount,
      okCount: okCount,
      itemExtremes: itemExtremes
   };

   console.log(clientSideGenerated);

   var localhostOnDevMachineGenerated = {
    "msg": "cube slices integrity check...",
    "desc": "generated on http://localhost/solar-disks-spectrum-atlas/?wavelength_A=6355.20&preferCube=cube_20250816_1000_4_6385&pixelShift=-481",
    "dcCount": 0,
    "okCount": 72751,
    "itemExtremes": {
        "20250705_0833_1_3578": [
            0,
            610
        ],
        "20250705_0824_1_3610": [
            0,
            715
        ],
        "20250708_0856_9_3631": [
            0,
            700
        ],
        "20250708_0913_9_3645": [
            0,
            436
        ],
        "20250705_0753_8_3650": [
            0,
            218
        ],
        "20250705_0747_4_3665": [
            0,
            716
        ],
        "20250705_0743_8_3687": [
            0,
            698
        ],
        "20250705_0724_7_3701": [
            0,
            695
        ],
        "20250501_3735": [
            0,
            774
        ],
        "20250501_3764": [
            0,
            770
        ],
        "20250501_3820": [
            0,
            761
        ],
        "20250420_3838": [
            0,
            195
        ],
        "20250501_3860": [
            0,
            775
        ],
        "20250501_3879": [
            0,
            765
        ],
        "20250501_3886": [
            0,
            763
        ],
        "20250501_3900": [
            0,
            725
        ],
        "20250501_3930": [
            0,
            685
        ],
        "20250503_3970red": [
            0,
            708
        ],
        "20250503_4005": [
            0,
            717
        ],
        "20250528_4030": [
            0,
            705
        ],
        "20250528_4045": [
            0,
            692
        ],
        "20250528_4072": [
            0,
            705
        ],
        "20250420_4078": [
            0,
            80
        ],
        "20250528_4111": [
            0,
            705
        ],
        "20250601_0949_4150": [
            0,
            710
        ],
        "20250601_0928_4186": [
            0,
            710
        ],
        "20250531_1050_4227": [
            0,
            716
        ],
        "20250531_1052_4222": [
            0,
            713
        ],
        "20250601_0842_4250": [
            0,
            705
        ],
        "20250531_1039_4329": [
            0,
            717
        ],
        "20250531_1042_4308": [
            0,
            716
        ],
        "20250422_4308": [
            0,
            370
        ],
        "20250531_1034_4326": [
            0,
            711
        ],
        "20250501_4383": [
            0,
            420
        ],
        "20250601_0831_4370": [
            0,
            712
        ],
        "20250607_1045_4_4383": [
            0,
            719
        ],
        "20250607_1042_0_4404": [
            0,
            720
        ],
        "20250607_1037_8_4435": [
            0,
            719
        ],
        "20250607_1032_1_4454": [
            0,
            718
        ],
        "20250607_1025_0_4482": [
            0,
            724
        ],
        "20250607_1012_5_4528": [
            0,
            723
        ],
        "20250607_1019_6_4528": [
            0,
            721
        ],
        "20250502_4554": [
            0,
            715
        ],
        "20250607_1006_7_4549": [
            0,
            725
        ],
        "20250607_0950_4_4581": [
            0,
            724
        ],
        "20250607_0956_5_4581": [
            0,
            724
        ],
        "20250607_0942_1_4611": [
            0,
            721
        ],
        "20250607_0909_5_4654": [
            0,
            725
        ],
        "20250607_0919_4_4654": [
            0,
            719
        ],
        "20250607_0931_1_4654": [
            0,
            726
        ],
        "20250607_0902_9_4703": [
            0,
            727
        ],
        "20250607_0854_5_4714": [
            0,
            723
        ],
        "20250607_0842_7_4737": [
            0,
            726
        ],
        "20250607_0830_3_4783": [
            0,
            729
        ],
        "20250607_0802_0_4824": [
            0,
            723
        ],
        "20250607_0814_8_4824": [
            0,
            725
        ],
        "20250514_4861": [
            0,
            713
        ],
        "20250607_0752_8_4861": [
            0,
            726
        ],
        "20250614_0752_9_4892": [
            0,
            711
        ],
        "20250614_0814_0_4921": [
            0,
            709
        ],
        "20250614_0831_6_4958": [
            0,
            713
        ],
        "20250614_0845_9_4958": [
            0,
            460
        ],
        "20250614_0854_5018": [
            0,
            710
        ],
        "20250426_5000": [
            0,
            727
        ],
        "20250614_0908_5042": [
            0,
            710
        ],
        "20250614_0911_5099": [
            0,
            718
        ],
        "20250614_0929_5099": [
            0,
            565
        ],
        "20250704_0758_6_blue": [
            0,
            700
        ],
        "20250704_0755_4_blue": [
            0,
            714
        ],
        "20250426_5183": [
            0,
            700
        ],
        "20250704_0745_9_blue": [
            0,
            717
        ],
        "20250706_0804_8_Mgr": [
            0,
            731
        ],
        "20250706_0903_1_Mgr2": [
            0,
            748
        ],
        "20250421_5303": [
            0,
            243
        ],
        "20250719_0717_8_5328": [
            0,
            695
        ],
        "20250719_0816_5_5328": [
            0,
            730
        ],
        "20250706_0928_9_Mgr4": [
            0,
            742
        ],
        "20250706_0937_6_Mgr5": [
            0,
            747
        ],
        "20250802_0745_6_5510": [
            0,
            745
        ],
        "20250802_0758_1_5550": [
            0,
            740
        ],
        "20250802_0820_1_5610": [
            0,
            738
        ],
        "20250802_0847_9_5658": [
            0,
            721
        ],
        "20250802_0902_1_5710": [
            0,
            720
        ],
        "20250704_1059_2_b": [
            0,
            734
        ],
        "20250802_0908_8_5763": [
            0,
            730
        ],
        "20250704_1050_7_r": [
            0,
            725
        ],
        "20250421_5880": [
            0,
            200
        ],
        "20250704_0926_2_NaHe": [
            0,
            722
        ],
        "20250816_0751_0_NaHe": [
            0,
            730
        ],
        "20250816_0802_4_5985": [
            0,
            740
        ],
        "20250816_0820_0_6024": [
            0,
            737
        ],
        "20250816_0829_6_6103": [
            0,
            692
        ],
        "20250421_6140": [
            0,
            195
        ],
        "20250816_0849_1_6162": [
            0,
            730
        ],
        "20250816_0858_7_6178": [
            0,
            740
        ],
        "20250704_1012_0_6173": [
            0,
            684
        ],
        "20250816_0909_3_6230": [
            0,
            725
        ],
        "20250704_1030_3_6302": [
            0,
            724
        ],
        "20250816_0937_9_6301": [
            0,
            715
        ],
        "20250816_0946_5_6320": [
            0,
            706
        ],
        "20250421_6374": [
            0,
            203
        ],
        "20250816_1000_4_6385": [
            0,
            711
        ],
        "20250816_1014_0_6400": [
            0,
            731
        ],
        "20250816_1027_6_6462": [
            0,
            725
        ],
        "20250816_1038_3_6495": [
            0,
            710
        ],
        "20250704_0903_8_Ha": [
            0,
            729
        ],
        "20250816_1049_2_HaBw": [
            0,
            728
        ],
        "20250816_1103_8_HaRw": [
            0,
            703
        ]
    }
};

    CubeSlicesEnumerations_checkForExpectations(clientSideGenerated, localhostOnDevMachineGenerated);
}

function CubeSlicesEnumerations_checkForExpectations(clientSideGenerated, localhostOnDevMachineGenerated){
    var discrep = 0;
    Object.keys(localhostOnDevMachineGenerated.itemExtremes).forEach(function (ie){
        try {
            if (
                (clientSideGenerated.itemExtremes[ie][0] != 
                localhostOnDevMachineGenerated.itemExtremes[ie][0]) 
                ||  
                (clientSideGenerated.itemExtremes[ie][1] != 
                localhostOnDevMachineGenerated.itemExtremes[ie][1])
            ){
                    console.log({
                        "cube": ie,
                        "clientSide": clientSideGenerated.itemExtremes[ie],
                        "devMachine": localhostOnDevMachineGenerated.itemExtremes[ie],
                    });
                    discrep++;
            }        
        }catch(err){
            console.log(err);
            console.log({
                "cube": ie,
            });
        }
    });
    console.log("dev vs client discrep: "+discrep);
}

setTimeout(function (){
   CubeSlicesEnumerations_checkForContinuity();
}, 3000);
</script>
</body>
</html>


