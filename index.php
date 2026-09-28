<?php
 // fuck microsoft visual studio, just fuck it, it is not <div?php, it is fucking <?php


require_once("debug_time.php"); 
require_once("helpers.php");
require_once("proxy.php");
require_once("atlas_hajonaplo.php");
require_once("html_head.php");
Debug_logMoment("Done: html head php");
require_once("main_scripts.php");
Debug_logMoment("Done: main scripts php");

echo get_svg_gamma_items();



?>
<master-body><div align="center">
<div class="section-item">
    <div style="display:none" class="section-title">UV-visible-NIR spectrum</div>
    <div>&nbsp;</div>
    <div class="section-body">
    <?php require_once("section_main_spectrum.php"); ?>
    <?php Debug_logMoment("Done: section main spectrum"); ?>
    </div>    
</div>

<div class="section-item">
    <div class="section-title">Interesting Wavelengths <span class="help-q-mark" data-help-for="interesting_wavelengths">[?]</span><span id="wavelength-selector-wrapper"></span></div>
    <div class="section-body">
    <?php require_once("section_interesting_wavelengths.php"); ?>
    <?php Debug_logMoment("Done: section interesting wavelengths 1"); ?>
    <?php require_once("section_interesting_wavelengths_2.php"); ?>
    <?php Debug_logMoment("Done: section interesting wavelengths 2"); ?>
    </div>    
</div>

<div class="section-item" id="section-interesting-playlists" style="display:none">
    <div class="section-title">Interesting Playlists</div>
    <div class="section-body">
        
    </div>    
</div>

<div class="section-item">
    <div class="section-title">Spectral Cubes <span class="help-q-mark" data-help-for="spectral_cube">[?]</span> 
        <button class="extend-more-button" onclick="SpectralCubeList_toggleVisibility(this, 'spectral-cubes-list-section-body')">[...]</button></div>
    <div class="section-body" data-min-height="40" 
        style="position: relative; max-height:40px; overflow: hidden;" id="spectral-cubes-list-section-body">
        <?php require_once("section_spectral_cubes_list.php"); ?>        
        <?php Debug_logMoment("Done: cubes list"); ?>
        <div onclick="SpectralCubeList_toggleVisibility(this, 'spectral-cubes-list-section-body')" class='box-shadow' style="box-shadow: inset 0px 0px 10px 10px #FFFFFF; height:100%; width:100%; position: absolute; left:0; top:0;"></div>
    </div>
    <div align="center">
        <div>&nbsp;</div>
        <button class="extend-more-button" onclick="SpectralCubeList_toggleVisibility(this, 'spectral-cubes-list-section-body')">[...]</button>
    </div>        
</div>

<div class="section-item">
    <div class="section-title">Cube Slices <span class="help-q-mark" data-help-for="spectral_cube_solex">[?]</span></div>
    <div class="section-body">
        <?php require_once("section_spectral_cube_slices.php"); ?>
        <?php Debug_logMoment("Done: section cube slices"); ?>
        <div style="spectral-cube-slice-wrapper" align="center">
            <div>
                <table style="width:800px" border="0">
                    <tr>
                        <td>
                            <span id="current-slice-subtitle"></span> 
                        </td>    
                        <td align="right">
                            <div class="brightness-and-gamma-display-wrapper">
                            <span style="font-size:75%; display: none">
                                <span style="cursor:pointer" onclick="cubeSlice_contrastSet(this, true, 0)">contrast</span>  
                                <button onclick="cubeSlice_contrastSet(this, false, -1)">-</button>
                                <button onclick="cubeSlice_contrastSet(this, true,   0)"title="set to default">*</button>
                                <button onclick="cubeSlice_contrastSet(this, false, +1)">+</button>
                                &nbsp;&nbsp;&nbsp;
                            </span>
                            <span style="font-size:75%; " >
                                <span style="cursor:pointer" onclick="cubeSlice_brightnessSet(this, true, 0)">brightness</span>  
                                <button onclick="cubeSlice_brightnessSet(this, false, -1)">-</button>
                                <button onclick="cubeSlice_brightnessSet(this, true,   0)" title="set to default">*</button>
                                <button onclick="cubeSlice_brightnessSet(this, false, +1)">+</button>
                                &nbsp;&nbsp;&nbsp;
                            </span>
                            <span style="font-size:75%">
                                <span style="cursor:pointer" onclick="cubeSlice_svgGammaSet(this, true, 0)">gamma</span>  
                                <button onclick="cubeSlice_svgGammaSet(this, false, -1)">-</button>
                                <button onclick="cubeSlice_svgGammaSet(this, true,   0)" title="set to default">*</button>
                                <button onclick="cubeSlice_svgGammaSet(this, false, +1)">+</button>
                                &nbsp;&nbsp;&nbsp;
                            </span>
                            <span style="font-size:75%; ">
                                <input type="checkbox" id="auto-apply-recommended-lumina" title="auto-apply recommended" onclick="cubeSlice_autoApplyCheckboxClick()">
                                <button onclick="cubeSlice_everythingSet(this, 'recommended', 0)">recommended</button>
                                <button onclick="cubeSlice_everythingSet(this, 'default', 0)">default</button>
                            </span>   
                            &nbsp;&nbsp;
                            </div> 
                            <div class="add-to-playlist-wrapper" id="add-to-playlist-buttons-wrapper">
                            </div>
                        </td>    
                    </tr>    
                </table>
            </div>
            <table border="0">
                <tr>
                    <td valign="top">
                        <div style="position:relative" id="holder-of-the-current-slice">
                            <img id="current-slice" style="width:800px">
                        </div>
                        <div class="display-notes-of-active-cube" id="current-slice-display-notes">
                            
                        </div>    
                    </td>
                    <td valign="top">
                        <div id="playlist-wrapper" style="width:440px; display:none">
                            <div class="playlist-titlebar">
                            <table width="100%"><tr><td><div align="center" style="font-size: 150%">Playlist</div></td>
                            <td align="right">
                                <button onclick="Playlist_close()">X</button>
                            </td></tr></table>
                            </div>
                            <div align="center" id="playlists-proposals-wrapper">Proposed playlists: </div>
                            <div style="width:100%; overflow-y: scroll; height: 300px">
                              <table id="playlist-table" border="0" width="90%"></table> 
                            </div>   
                            <div style="margin: 5px;" align="center">
                                <button class="playlist-action-button playlist-action-button-narrow" onclick="Playlist_prev()">&#128896;</button>
                                <button class="playlist-action-button playlist-action-button-narrow" onclick="Playlist_next()">&#128898;</button>
                                <span style="border:1px solid grey; padding: 5px; margin-right: 5px">
                                    <input type="text" id="playlist-interval" value="xxx" style="width:50px"> ms
                                    &nbsp;
                                    <button onclick="Playlist_multiplyInterval(2)">x 2</button>
                                    <button onclick="Playlist_multiplyInterval(1/2)">/ 2</button>
                                </span>
                                <button class="playlist-action-button playlist-action-button-wide" onclick="Playlist_play()">&#9654;</button>
                                <button class="playlist-action-button playlist-action-button-wide" onclick="Playlist_stop()">&#x25FC;</button>
                            </div>
                            <div>
                                <textarea id="playlist-stringified" style="width:100%; height: 300px">                                    
                                </textarea>
                            </div>    
                        </div>    
                    </td>
                </tr>    
            </table>    
        </div>   
    </div>    
</div>
<?php require_once("playlist.php"); ?>

<div class="section-item">
    <div class="section-title">Citing</div>
    <div class="section-body">
       If you have used this Atlas in your research, 
       then we would appreciate it if you could cite 
       the following reference to acknowledge the work done 
       https://iopscience.iop.org/article/10.3847/2515-5172/adef50
    </div>
</div>    

<div class="section-item">
    <div class="section-title">Sources, Acknowledgements, Licence etc.<span class="help-q-mark" data-help-for="data-sources-ack">[ view ]</span></div>
    <div class="section-body">
        <?php require_once("section_sources_ack.php"); ?>
    </div>    
</div>

</div></master-body>

<div style="display:none">
<pre>
    <?php Debug_printMoments(); ?>
</pre>    
</div>
 
<?php require_once("html_foot.php"); ?>