Changelog
=========

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
