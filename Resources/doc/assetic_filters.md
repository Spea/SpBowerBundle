SpBowerBundle Assetic Filters
=============================

SpBowerBundle offers the possibility to add assetic filters to all (or some) of your bower packages.

### Adding assetic filters to all bower packages

```yml
sp_bower:
    assetic:
        enabled: true
        filters:
            css:
                - cssrewrite
            js:
                - ?uglifyjs
```

This configuration will add the ```cssrewrite``` filter to **all** css files and the
```uglifyjs``` filter to **all** js files from the installed bower packages.

### Adding assetic filters to specific bower packages

```yml
sp_bower:
    assetic:
        enabled: true
        filters:
            packages:
                myPackage:
                    css:
                        - cssrewrite
                    js:
                        - ?uglifyjs
```

This configuration will add the ```cssrewrite``` and the ```uglifyjs``` filter only to the package **myPackage**.

**Note:**
> Package filters and global filters will be merged together.
