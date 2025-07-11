<div id="help-texts" style="display:none">
    <div id="help-of-interesting_wavelengths">
        <div class="help-paragraph">
            The Sun's spectrum is not a continuous rainbow, but contains dark stripes: spectral lines. 
            The most obvious ones, named after the scientist that described them, are called <a href="https://en.wikipedia.org/wiki/Fraunhofer_lines">Fraunhofer lines.</a>
            Getting familiar with them is still the best introduction there is. The light that went missing from those wavelengths is absorbed by something in between our eyes and the Sun. Either on the Sun, 
            or here on Earth - telluric lines, of which water and Oxygen are known culprits.
            The Sun being made of Hydrogen and Helium and traces of metals*, it is expected to have interesting wavelengths of Hydrogen: alpha, beta etc, and for Helium: I D3.            
        </div> 
        <div class="help-paragraph">
            For Hydrogen Alpha, and Calcium H/K - and Sodium and Magnezium if we insist - there are cheap-ish etalons and telescope systems, available to the amateur astronomer. The rest of the spectral lines are spectroheliograph domain.
        </div>        
        <div class="help-paragraph">
            Beyond the obvious Fraunhofer lines, most of what we see are neutral metals, Iron most prominently. When saying Iron, every astronomer feels a little shiver: that's the element where nuclear fusion no longer releases but actually absorbs energy. Iron is where stars die. That is where stellar furnaces stop, where red giants shrink into white dwarfs, where supergiants collapse into supernovae and/or black holes. Remember that the next time you hold a hammer or something.
        </div>    
        <div class="help-paragraph">    
            One of the prominent Iron lines is the Fe (c) at 4957.61&Aring;, quite a bit like a "wide" Calcium image amateurs are so familiar with. 
            
            The same goes for the Sodium doublet at 5895.92 and 5889.95, and the Magnezium triplet at 5183.62, 5172.70, 5168.91 (Fe photobombing the picture) and 5167.33, 
            interesting, magnetic, etc, but ultimately active regions are just bright, faint prominences and with some luck, faint filaments on the disk.
        </div>    
        <div class="help-paragraph">
            But, we can see surprising stuff when looking into ionized metals. 
            On the disk, the Fe II signal at 5018.45&Aring; is similar to the Helium I D3 at 5875.6&Aring;, the active regions turn dark, but the signal is much stronger. 
            Prominences are also visible, albeit much fainter than the He proms. 
            
            Fun fact, there is a very faint He I line at 5015.7&Aring; also, not to mention the He I line at 4471.5&Aring;, which sits between the well observed D3 and this unknown-to-amateurs green Helium.
        </div>
        
        <div class="help-paragraph">
            *elements other than Hydrogen and Helium
        </div>
    </div>        


    <div id="help-of-spectral_cube">                
        <img src="?imgproxy=the-spectral-data-cube.jpg" style="display: inline-block; float:right; max-width:400px">
        <div class="help-paragraph">
          <div><strong>So, what is a picture?</strong></div>
          <div>It is a <em>plane</em>, a raster of pixels identified by their x-y coordinates. That is 2D.</div>
        </div>

        <div class="help-paragraph">
            <div><strong>What is a video?</strong></div>
            <div>It is a <em>sequence of images</em>, so on top of the x-y coordinates of the pixels, we now also have the frame number, <em><strong>i</strong></em> if you will for index, or <em><strong>t</strong></em> from time, or <em><strong>z</strong></em> to follow the x-y.</div>
            <div>How do we locate a pixel? We need to know its x-y coordinates and... in which frame/plane to look for it, ie  we are definitely in 3D now.</div>
            <div>Having now three axes, the most obvious object we can think about is: <strong>the cube</strong>.</div>
        </div>

        <div class="help-paragraph">
            <div><strong>What is a spectral cube?</strong></div>
            <div>It is a <em>sequence of images</em>, where the frame number is the &lambda; wavelength. We record, at once, the same image in several wavelengths that become the slices/layers in the cube.</div>
            <div>How do we locate a pixel? We need to know its x-y coordinates and... in which wavelength to look for it.</div>
            <div>How do we look at a solar flare? We look at some promising x-y positions, and check what those pixels show us in diffferent wavelengths.</div>
        </div>        

    </div>        

    <div id="help-of-spectral_cube_solex">                
        <div class="help-paragraph">
          <div>Solar disks displayed below are slices of spectral cubes recorded with a spectroheliograph, by scanning the Sun's surface. See above.</div>
        </div>
        <div class="help-paragraph">
          <div>The individual disks are as good as: </div>
            <ul>
                <li>the camera, pixel size, readout noise, curving and aliasing etc.</li>
                <li>the mechanical stability of the system, bendings, kinks and sagging etc</li>
                <li>the optics, is the projection significantly different from being rectilinear for the inspected domain etc etc</li>
                <li>the polynomial deduced from the central wavelength (on some spectra, marked with green)</li>
            </ul>    
            <div>Hence all kinds of artefacts and inaccuracies are possible, including but not limited to:</div>
            <ul>
                <li>"polar caps" ie brightening perpendicular to the scan axis</li>
                <li>"wobbly light" ie limb darkening / on-bandness that appears to wobble along the scan axis</li>
                <li>banding parallel the scan axis, due to slit issues and sunspots offsetting the reconstruction</li>
                <li>banding perpendicular to the scan axis, due to camera sensor readout artefacts and spectral line bending through the camera sensors's banding</li>
                <li>a slope on the average spectrum could be camera respons and/or the transmission profile of the bandpass filter, used as ERF, especially away from its CWL</li>
            </ul>    
        </div>
        <div class="help-paragraph">
          <div>The cost of a cube</div>  
          <div>Recording the raw light for such a cube, per se, if all else is configured, takes only a minute, one scan, which translates to about 15 GiB of raw data. However, several scans are needed, per wavelength area, to get lucky:</div>
            <ul>
                <li>seeing, turbulence: it only takes one or two seconds to completely ruin an otherwise perfect scan</li>
                <li>clouds, any kind of nebulosity, even birds: as with seeing, one cloud or even a bird can ruin the entire scan, as there is no returning to the ruined scanline, like with two dimensional lucky imaging</li>
                <li>storage speed: although some research went into the SSDs used, sometimes the stars don't align, and frames get dropped, losing the entire scan</li>
                <li>mechanical issues: anything other than a smooth glide ruins the scan</li>
                <li>often times the above become obvious only after reconstructing, so the more raw the better</li>
            </ul>    
        </div>
        <div class="help-paragraph">
            On top of the above, even with specialized software, generated scripts etc, it takes surprisingly long processing a .ser video. On the desktop computer I use, NVMe SSDs, i7 13700 CPU, 64 GB RAM, 45 minutes of reconstruction with JSol'Ex, and another say 15 -- to round it to an hour -- of typing the json and measuring pixel positions.
        </div>
        <div class="help-paragraph">
            Given the above, I have amassed about 60TB of high speed SSDs to act as a recording and storage buffer, given the high yield and long processing times.
        </div>  

    </div>        

    <div id="help-of-data-sources-ack">
        <div class="help-paragraph">
            <em>The work presented here is akin to the dwarf standing on the shoulders of giants.</em>
        </div>    
        <div class="help-paragraph">
            <div><strong>Sun Data</strong></div>
            <div class="help-sub-paragraph">
            The solar disks and spectral cubes presented here are the author's own work, 
            his own observations with his own instruments. Some of the gear was bought off the shelf, 
            some was built/hacked/repurposed.
            </div>

            <div class="help-sub-paragraph">
            The mount - an old SkyWatcher EQ3, for example retains only its mechanical components 
            and motors from its factory state. The controlling hardware and software control of it 
            has been implemented from the ground up, and thus it features a dedicated 
            spectroheliograph scanning mode too.
            </div>  

        </div>         
        <div class="help-paragraph">
            <div><strong>Instrument #1: stock Sol'Ex</strong></div>
            <div class="help-sub-paragraph">
            This setup is the main instrument of the Atlas. A stock Sol'Ex, mounted onto 
            a 62/400 refractor stopped down to ~42/400 by the 2" full aperture filters, 
            put in place to act as energy rejection filters, to protect the slit. 
            The camera is a ZWO ASI 678MM. To reduce mechanical issues,
            the setup features additional support structure.

            <div class="help-sub-paragraph">
                Thank you, Christian Buil, for making spectroheliography so accessible!
            </div>            
        </div>    
            <div class="help-sub-paragraph">
                &nbsp;
            </div>            
        <div class="help-paragraph">
            <div><strong>Instrument #2: ML Astro SHG 700</strong></div>
            <div class="help-sub-paragraph">
            This setup is the stock ML Astro SHG 700, "third batch", mounted onto 
            a 80/540 refractor, using in-cone, well in front of the focal plabe,
            2" narrow band filters, to act as energy rejection filters, protecting the slit. 
            The camera is a ZWO ASI 678MM. To reduce mechanical issues,
            the setup features additional support structure.
            </div>
        </div>
        <div class="help-paragraph">
            <div><strong>Software</strong></div>
            <ul>
                <li>
                    The author's own Ersatz-Obsi, from driving the telescope mount, power management etc. to handling the amassed data as a database/librarian.
                    Components include: C, Arduino, Pascal/Lazarus, nodejs, php, html/css/js
                </li>
                <li>
                    SharpCap, to capture the images.
                </li>    
                <li>
                    Scan reconstruction: mainly JSol'Ex and also INTI.
                    <div>Valerie Desnoux <a href="http://valerie.desnoux.free.fr/inti/">http://valerie.desnoux.free.fr/inti/</a></div>
                    <div>Cedric Champeau <a href="https://github.com/melix/astro4j">https://github.com/melix/astro4j</a></div>
                </li>
                <li>
                    Other software include: Registax, AutoStakkert4, PIPP (Planetary Image Pre-Processor)
                </li>        
            </ul>    

        </div>
        <div class="help-paragraph">
            <div><strong>Literature etc</strong></div>
            <ul>
                <li>
                    Christian Buil: <em>Sol'Ex</em>   <a href="https://solex.astrosurf.com/sol-ex-presentation-en.html">https://solex.astrosurf.com/sol-ex-presentation-en.html</a>
                </li>
                <li>
                    Charlotte E. Moore, M. G. J. MiNNAERT J. HOUTGAST: <em>THE SOLAR SPECTRUM 2935A to 8770A...</em>
                    <a href="https://nvlpubs.nist.gov/nistpubs/Legacy/MONO/nbsmonograph61.pdf">https://nvlpubs.nist.gov/nistpubs/Legacy/MONO/nbsmonograph61.pdf</a>
                </li>
                <li>
                    Resources on the Bass2000 website: <a href="https://bass2000.obspm.fr/home.php">https://bass2000.obspm.fr/home.php</a>                    
                </li>    
            </ul>
        </div>  
        <div class="help-paragraph">
            <div class="help-sub-paragraph">
                <center>
                P&aacute;l V&Aacute;RADI NAGY, 2025
                </center>
            </div>
        </div>    
    </div>    

</div>    

<script>
    var Help = {
        init: function (){
          var l = document.getElementsByClassName("help-q-mark");
          for (var i=0; i<l.length; i++){
			  this.transform(l[i]);
          }
        },
        transform: function (elem){
			var that = this;
		    var uid = 'uid'+Date.now()+Math.random();
		    uid = uid.split('.').join('-');
		    elem.setAttribute("data-help-wrapper-id", uid);
			var div = document.createElement("div");
			div.id = uid;
			div.className = "help-for-wrapper";
			div.style.display = "none";			
			elem.parentNode.insertBefore(div, elem.nextSibling);			
			var that = this;
            elem.addEventListener("click", function (e){
				that.showHelpFor(elem);
			});			
        },
		close: function (elem){
			while (elem){
				if ((elem.className+'').indexOf("help-for-wrapper") > -1){
					elem.style.display = "none";
					return ;
				}
				elem = elem.parentNode;
			} 
		},
		showHelpFor: function (elem){
			var divid = elem.getAttribute("data-help-wrapper-id");
            var div = document.getElementById(divid);			
			if (div.style.display != "none"){
				// already open
				this.close(div);
				return ;
			}
			var textid = elem.getAttribute("data-help-for");
			var s = this.getHelpTextFromTextId(textid);
			var ih = '<div align="center"><div style="width:800px;border:1px solid silver; border-radius: 15px; padding:12px;" align="left"><div align="right"><button onclick="Help.close(this)" >X</button></div><hr><div>%text%</div></div></div>';
			div.innerHTML = ih.replace("%text%", s);
			div.style.display = "";			
		},
		getHelpTextFromTextId: function (textid, defaultText = "help under construction"){
			var id = 'help-of-'+textid;
			var e = document.getElementById(id);
			if (e){
				return e.innerHTML;
			}
			return defaultText;
		}
    };
	Help.init();
</script>    
