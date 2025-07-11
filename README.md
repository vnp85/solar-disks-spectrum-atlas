# Solar Disks Spectrum Atlas

A database of spectroheliograms and a web-based viewer for the recorded data.

## Description

This project consists of two parts:

* a database of spectroheliograms: Using a combination of off-the-shelf and custom made, mainly amateur instruments, refractors and spectroheliographs (one stock Sol'Ex, and one ML Astro SHG 700), scans were obtained of the Sun during the solar cycle's approximate peak, in 2025. The observations continuously cover a significant part of the near UV and visible spectrum, with patches outside the continuously covered interval. The raw data collected and processed was on the order of terabytes. It got selected and downsampled into about 5GB of jpeg files.
* a web-browser based viewer implementation, to allow for an interactive vieweing of the data.

## Getting Started

### Dependencies

* **disk space**: at this moment, about `5 GB of free disk space` is needed, as there are 55k files in the database
* **a webserver** like `xampp`, of which the php part is used, mysql for example is not
* in **php, GD** (graphics, images) must be enabled

### Installing, Executing

#### With XAMPP, for example:

* clone this repo into the `htdocs` folder
* http://localhost/%the-repo-folder-name%

#### With your own viewer, using the datasets:

* check the `cubes-info/*.json` files
* check the `cubes/*.*` folder contents referenced by the JSON files above


## Author

observation data and viewer: [Pal VARADI NAGY](https://csillagtura.ro)


## Version History

    * Initial Release UT 2025-07-11 11:32

## License

The observation data (cubes) published here, and the viewing software are licensed under [CC-BY 4.0](https://creativecommons.org/licenses/by/4.0/)

## Acknowledgments

* A.G.M. Pietrow: solar astronomy advice 
* Christian Buil: [Sol'Ex (Solar Explorer)](https://solex.astrosurf.com/sol-ex-presentation-en.html)
* Cedric Champeau: [JSol'Ex](https://github.com/melix/astro4j)
