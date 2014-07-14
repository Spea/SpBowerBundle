Changelog
=========

### 0.11 (2014-07-14)

* Use PSR4 autoloader (thanks to @jrobeson)
* Update configuration reference (thanks to @stephanvierkant and @jrobeson)
* Add command to update bower dependencies (thanks to @amarisi)
* Make the bundle compatible to new symfony3 directory structure
* Update Scripthandler to support symfony3 (thanks to @sstok)

### 0.10 (2014-05-15)

* Add php 5.6 and hhvm-nightly for testing
* Remove composer.lock and add it to .gitignore
* Refactor event dispatching to fix a bug while clearing cache
* Reset working directory when resolving path for a dependency

### 0.9 (2014-01-15)

* Added new command to list all assetic resources (`sp:bower:list`)
* Fixed some typos
* Adjusted documentation
* Dependency cache warmer is now optional (so it will not be triggered on a "soft" cache warmup)
* Generate Assetic packages only when content for scripts and/or styles isn't empty
* Create cache directory in the components directory - this way the cache must not be recreated on every cache:warmup/cache:clear

### 0.8 (2013-09-03)

* Made bundle compatible with bower >= 1.0.0

### 0.7 (2013-06-20)

* Added support for ```scripts``` and ```styles``` properties in the component.json file. As used in node
* Throw an exception instead of deleting the cache when the dependency mapping returned from bower is invalid
* Added documentation on how to process images/fonts in css files
* Added new option `assetic.nest_dependencies` to configure the behavior of nested dependencies in the generated assetic resources
* Added new command for warming up cache
* Catch invalid mapping exception in cache warmer

### 0.6 (2013-04-06)

* When the retrieved mapping from the bower command ```bower list --map``` is invalid, the dependency cache will be deleted
* The dependency cache warmer no longer creates the cache twice when install_on_warmup is enabled
* An exception is now thrown when a required js/css file could not be found when retrieving the dependency mapping
* Added new interface for converting a package name to an assetic name
* Removed deprecated configuration option ```register_assets```
* Added error handling for command execution

### 0.5 (2013-03-14)

* Fixed a serializer issue when ```use_controller``` were enabled

### 0.4 (2013-01-22)

* Added possibility to add assetic filters to all (or some) bower packages
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
