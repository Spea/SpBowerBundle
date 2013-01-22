Changelog
=========

### 0.4 (2013-01-22)

* Added possiblity to add assetic filters to all (or some) bower packages
* Deprecated the configuration option ```register_assets```. More on that can be found in the [upgrade instructions](Upgrade.md)
* Added bundle notation support for the ```config_dir``` and ```asset_dir``` option
* Fixed a bug where asset paths were not resolved correctly when using a temporary .bowerrc file
* Install command now shows the directory where the assets will be installed to

### 0.3 (2013-01-08)

* Fixed an error when the package didn't defined any source files
* Added a new option ```install_on_warmup``` which defines whether or not to install bower dependencies on the cache warmup event
* Fixed an error when the bower package name included the character ```-```
* Added event system
* Added new option ```keep_bowerrc``` which defines whether or not to keep the bower configuration file ```.bowerrc``` in the bundle

### 0.2 (2012-11-21)

* Added composer script handler (thanks to @Fran6co)
* Changed ```paths``` to ```bundles```
* Command will now show for which bundle the bower dependencies will be installed
* Moved config directory from bower to the configuration class
