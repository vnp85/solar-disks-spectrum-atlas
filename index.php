<?php
 // fuck microsoft visual studio, just fuck it, it is not <div?php, it is fucking <?php
require_once("helpers.php");
require_once("proxy.php");
require_once("atlas_hajonaplo.php");
require_once("html_head.php");
require_once("main_scripts.php");

?>
<master-body><div align="center">
<div class="section-item">
    <div class="section-title">UV-visible-NIR spectrum</div>
    <div class="section-body">
    <?php require_once("section_main_spectrum.php"); ?>
    </div>    
</div>

<div class="section-item">
    <div class="section-title">Interesting Wavelengths <span class="help-q-mark" data-help-for="interesting_wavelengths">[?]</span><span id="wavelength-selector-wrapper"></span></div>
    <div class="section-body">
    <?php require_once("section_interesting_wavelengths.php"); ?>
    <?php require_once("section_interesting_wavelengths_2.php"); ?>
    </div>    
</div>

<div class="section-item">
    <div class="section-title">Spectral Cubes <span class="help-q-mark" data-help-for="spectral_cube">[?]</span></div>
    <div class="section-body">
        <?php require_once("section_spectral_cubes_list.php"); ?>
    </div>    
</div>

<div class="section-item">
    <div class="section-title">Cube Slices <span class="help-q-mark" data-help-for="spectral_cube_solex">[?]</span></div>
    <div class="section-body">
        <?php require_once("section_spectral_cube_slices.php"); ?>
        <div style="spectral-cube-slice-wrapper" align="center">
            <div>
                <table style="width:800px" border="0">
                    <tr>
                        <td>
                            <span id="current-slice-subtitle"></span> 
                        </td>    
                        <td align="right">
                            <span style="font-size:75%">
                                <span style="cursor:pointer" onclick="cubeSlice_gammaSet(this, true, 0)">brightness</span>  
                                <button onclick="cubeSlice_gammaSet(this, false, -1)">-</button>
                                <button onclick="cubeSlice_gammaSet(this, true,   0)">*</button>
                                <button onclick="cubeSlice_gammaSet(this, false, +1)">+</button>
                            </span>
                        </td>    
                    </tr>    
                </table>
            </div>
            <div>
                <img id="current-slice" style="width:800px">
            </div>
        </div>   
    </div>    
</div>

<div class="section-item">
    <div class="section-title">Sources, Acknowledgements etc.<span class="help-q-mark" data-help-for="data-sources-ack">[ view ]</span></div>
    <div class="section-body">
        <?php require_once("section_sources_ack.php"); ?>
    </div>    
</div>

</div></master-body>

 
<?php require_once("html_foot.php"); ?>