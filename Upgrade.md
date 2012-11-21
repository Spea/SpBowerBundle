Upgrade instruction
===================

This document describes the changes needed when upgrading because of a BC
break. For the full list of changes, please look at the Changelog file.

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
