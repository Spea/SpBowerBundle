Installation
============

Prerequisites
-------------

This bundle requires Symfony 2.1+

### Bower (required)

In order to use this bundle you have to install bower

[Bower Documentation](http://twitter.github.com/bower/)

Installation
------------

1. Download SpBowerBundle using composer
2. Enable the Bundle
3. Configure the bundle
4. Installing bower dependencies
5. Use the installed assets in your templates

### Step 1: Download SpBowerBundle using composer

Add SpBowerBundle in your composer.json:

```js
{
    "require": {
        "sp/bower-bundle": "*"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update sp/bower-bundle
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
    paths:
        YourBundle: ~
```

### Step 4: Installing bower dependencies

Place your ```component.json``` in the config directory, the default value for the config dir is ```$yourBundle/Resources/config/bower```.

#### Example:
```json
{
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
# AcmeDemoBundle/Resources/config/bower/component.json
{
    "dependencies": {
        "bootstrap": "latest"
    }
}
```

then you can use them in your templates (or wherever you need them) like this

```twig
{% javascripts
    "@bootstrap_js" %}
    <link rel="stylesheet" href="{{ asset_url }}" />
{% endjavascripts %}
{% stylesheets
    "@bootstrap_css" %}
    <link rel="stylesheet" href="{{ asset_url }}" />
{% endstylesheets %}
```

**Note**:
> If you don't want this bundle to automatically register the assets, you can disable this behavior by setting
> ```register_assets``` to false

**Warning**:
> Your site may be slowed down if you enabled ```use_controller``` in the assetic bundle and
> ```register_assets``` in this bundle.

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

### Next Steps

- [Configuration Reference](configuration_reference.md)
