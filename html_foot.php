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
</div>
<?php require_once("section_cubes_continuity.php"); ?>

<div class="just-some-spacer">
    <div style="height:64px">&nbsp;</div>
</div>        
</body>
</html>


