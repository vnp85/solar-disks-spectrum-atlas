# Solar Disks Spectrum Atlas

A database of spectroheliograms and a web-based viewer for the recorded data.

## Description

This project consists of two parts:

* a database of spectroheliograms: Using a combination of off-the-shelf and custom made, mainly amateur instruments, refractors and spectroheliographs (one stock Sol'Ex, and one ML Astro SHG 700 and others), scans were obtained of the Sun during the solar cycle's approximate peak, in 2025 and then in 2026. The 2025 data covers most of the UV and visible, and the 2026 data extends from the deep red till beyond the infrared helium. The observations continuously cover most of what a CMOS sensor can register, albeit with degraded quality in the UV extreme, and in the IR half. The raw data collected and processed was on the order of a petabyte. The raws were selected and downsampled into about 13 GB of jpeg files.
* a web-browser based viewer implementation, to allow for an interactive vieweing of the data.

## Getting Started

### Dependencies

* **disk space**: at this moment, about `15 GB of free disk space` is needed, as there are 154k files in the database
* **a webserver** like `xampp`, of which the php part is used, mysql for example is not
* **inodes**: web servers and/or virtual machines may impose limits on the number of files on the file system (thus "hypercube" files)
* in **php, GD** (graphics, images) must be enabled

### Installing, Executing

#### With XAMPP, for example:

* clone this repo into the `htdocs` folder
* http://localhost/%the-repo-folder-name%

#### With your own viewer, using the datasets:

* check the `cubes-info/*.json` files
* check the `cubes/*.*` folder contents referenced by the JSON files above

In a `cubes/foo-bar/` folder there will be an `average.jpg` or png file, which has a green line. The green line denotes pixel-offset zero:

* the `rawLum` image P000 (plus-zero) is extracted from that pixel-offset
* in the filename, the `P123` denotes a plus 123 pixels offset, from the green line, towards the red, which is bottom or right
* in the filename, the `M456` denotes a minus 456 pixels offset, from the green line, towards the blue, which is top or left
* in the filename, the `C789` is a counter, to guarantee the files' order

The JSol'Ex ImageMath script was generated, and contained lines similar to this snippet:

```
lineOffset = 0

[outputs]

rot_rawLum_img_C000_M328 = rotate_rad(img(lineOffset -328), angleP)
rot_rawLum_img_C001_M327 = rotate_rad(img(lineOffset -327), angleP)

rot_rawLum_img_C008_M320 = rotate_rad(img(lineOffset -320), angleP)

rot_rawLum_img_C326_M002 = rotate_rad(img(lineOffset -2), angleP)
rot_rawLum_img_C327_M001 = rotate_rad(img(lineOffset -1), angleP)
rot_rawLum_img_C328_P000 = rotate_rad(img(lineOffset +0), angleP)
rot_rawLum_img_C329_P001 = rotate_rad(img(lineOffset +1), angleP)
rot_rawLum_img_C330_P002 = rotate_rad(img(lineOffset +2), angleP)

rot_rawLum_img_C715_P387 = rotate_rad(img(lineOffset +387), angleP)
rot_rawLum_img_C716_P388 = rotate_rad(img(lineOffset +388), angleP)
```

## Author

observation data and viewer: [Pal VARADI NAGY](https://csillagtura.ro)


## Version History

* Version 3.00 (UT 2026-09-28)
  * new data, continuous coverage ~ 355-1100 nm
  * the Gallery Chapter, to showcase disks with enhanced signal (with some artistic license)
  * the playback feature to shift blue-red around given sections
* Version 2.00 (new data, bugfixes in the code) UT 2025-08-18 12:02
* Initial Release UT 2025-07-11 11:32

## License

The observation data (cubes) published here, and the viewing software are licensed under [CC-BY 4.0](https://creativecommons.org/licenses/by/4.0/)

## Acknowledgments

* A.G.M. Pietrow: solar astronomy advice 
* Christian Buil: [Sol'Ex (Solar Explorer)](https://solex.astrosurf.com/sol-ex-presentation-en.html)
* Cedric Champeau: [JSol'Ex](https://github.com/melix/astro4j)
