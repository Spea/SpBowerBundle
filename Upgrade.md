Upgrade instruction
===================

This document describes the changes needed when upgrading because of a BC
break. For the full list of changes, please look at the Changelog file.

## 0.7 to 0.8

### Configuration

The default name for the dependency file from bower has changed from `compoment.json` to `bower.json`.
So if you still want to use the old name, you must adjust the configuration for this bundle

Before:

```
sp_bower:
    bundles:
        AcmeDemoBundle: ~
```

After:

```
sp_bower:
    bundles:
        AcmeDemoBundle:
            json_file: component.json
```

## 0.5 to 0.6

### Exceptions

The exception class ```Sp\BowerBundle\Bower\Exception``` was removed.
If you used this class to catch or throw new exceptions, replace it with one of the new ones:

* [Sp\BowerBundle\Bower\Exception\ExceptionInterface](Bower/Exception/ExceptionInterface.php)
* [Sp\BowerBundle\Bower\Exception\FileNotFoundException](Bower/Exception/FileNotFoundException.php)
* [Sp\BowerBundle\Bower\Exception\RuntimeException](Bower/Exception/RuntimeException.php)

## 0.3 to 0.4

### Configuration

#### Deprecations

The configuration option ```register_assets``` is deprecated and will be removed in 0.6.
You should now use the ```assetic``` option

Before:

```yml
sp_bower:
    register_assets: true
```

After:

```yml
sp_bower:
    assetic: ~
```

## 0.1 to 0.2

### Configuration

The configuration for this bundle slightly changed to make it more clear.

Before:

```yml
# app/config/config.yml
sp_bower:
    paths:
        DemoBundle: ~
```

After:

```yml
# app/config/config.yml
sp_bower:
    bundles:
        DemoBundle: ~
```
