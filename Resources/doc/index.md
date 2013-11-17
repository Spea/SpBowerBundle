Installation
============

Prerequisites
-------------

This bundle requires Symfony 2.1+

### Bower (required)

In order to use this bundle you have to install bower

[Bower Documentation](http://bower.io/)

Installation
------------

1. Download SpBowerBundle using composer
2. Enable the Bundle
3. Configure the bundle
4. Installing bower dependencies
5. Use the installed assets in your templates
6. Add composer scripts for automatic update of dependencies
7. Installing dependencies on every cache warmup

### Step 1: Download SpBowerBundle using composer

Ask composer to add SpBowerBundle in your composer.json
and to download it by running the command:

``` bash
$ php composer.phar require sp/bower-bundle
```

Composer will install the bundle to your project's `vendor/sp` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Sp\BowerBundle\SpBowerBundle(),
    );
}
```

### Step 3: Configure the SpBowerBundle

Enable bower dependency management for your bundles in the ```config.yml``` file.

```yml
# app/config/config.yml
sp_bower:
    bundles:
        YourBundle: ~
```

### Step 4: Installing bower dependencies

Place your ```bower.json``` in the config directory, the default value for the config dir is ```$yourBundle/Resources/config/bower```.

#### Example:
```json
{
    "name": "your-bundle-name",
    "dependencies": {
        "jquery": "~1.8.2"
    }
}
```

Now run the command ```app/console sp:bower:install``` to install all the necessary
dependencies in the asset directory ```$yourBundle/Resources/public```.

### Step 5: Use the installed assets in your templates

This bundle registers all installed bower assets automatically for you.

Lets say you have the following dependencies defined

```json
# AcmeDemoBundle/Resources/config/bower/bower.json
{
    "name": "acme-demo-bundle",
    "dependencies": {
        "bootstrap": "latest"
    }
}
```

then you can use them in your templates (or wherever you need them) like this

```twig
{% javascripts
    "@bootstrap_js" %}
    <script src="{{ asset_url }}"></script>
{% endjavascripts %}
{% stylesheets
    "@bootstrap_css" %}
    <link rel="stylesheet" href="{{ asset_url }}" />
{% endstylesheets %}
```

**Note:**
> Since asset names in the AsseticLibrary can not contain ```.``` or ```-``` characters, they will be
> converted to a ```_```. So the css files from "font-awesome" must be referenced with "font_awesome_css".

**Note**:
> If you don't want this bundle to automatically register the assets, you can disable this behavior by setting
> ```assetic``` to false

**Warning**:
> Your site may be slowed down if you enabled ```use_controller``` in the assetic bundle and
> ```assetic``` in this bundle.

### Step 6: Add composer scripts for automatic update of dependencies

```json
{
   "scripts": {
       "post-install-cmd": [
           "Sp\\BowerBundle\\Composer\\ScriptHandler::bowerInstall"
       ],
       "post-update-cmd": [
           "Sp\\BowerBundle\\Composer\\ScriptHandler::bowerInstall"
       ]
   }
}
```

### Step 7: Installing dependencies on every cache warmup

If you want to install/update all bower dependencies during the warmup, you must set ```install_on_warmup``` to true

```yml
# app/config/config.yml
sp_bower:
    install_on_warmup: true
```

### Next Steps

- [Configuration Reference](configuration_reference.md)
- [Assetic Filters](assetic_filters.md)
- [What to do when the images/fonts from a css file are not displayed?](image_font_processing.md)
- [Configure the behavior of nested dependencies](nested_dependencies.md)
