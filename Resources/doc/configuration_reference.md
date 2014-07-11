SpBowerBundle Configuration Reference
=====================================

All available configuration options are listed below with their default values.

``` yaml
# app/config/config.yml
sp_bower:
    install_on_warmup: false # Optional
    keep_bowerrc: false # Optional
    bin: /usr/bin/bower # Optional
    offline: false # Optional. Set true in situations where you do not have internet capabilities, for example on a plane or your local pub.
    allow_root: false # optional
    # assetic: ~
    assetic:
        enabled: true
        nest_dependencies: true
        filters:
            css:
                - ?yui_css
            js:
                - ?uglifyjs
            packages:
                bootstrap:
                    css:
                        - cssrewrite
                    js:
                        - ?yui_js
    bundles:
        # DemoBundle: ~
        DemoBundle:
            config_dir: Resources/config/bower # Can be relative to the bundles root directory, absolute or a bundle notation
            asset_dir: ../../public/components # Can be relative to the config_dir directory, absolute or a bundle notation
            json_file: bower.json
            endpoint: https://bower.herokuapp.com
            # The cache to use for storing the dependencies (optional)
            cache:
                # The id of the cache service - It must implement the interface \Doctrine\Common\Cache\Cache
                id: ~
                directory: ../../public/components/cache
            # cache: /path/to/cache/directory
```
