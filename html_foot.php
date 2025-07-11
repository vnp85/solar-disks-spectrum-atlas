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
    
</body>
</html>


