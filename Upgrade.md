Upgrade instruction
===================

This document describes the changes needed when upgrading because of a BC
break. For the full list of changes, please look at the Changelog file.

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
