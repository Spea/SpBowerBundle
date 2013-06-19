SpBowerBundle Image/Font Processing
===================================

Some packages include images or fonts in their css files. Due to the nature of assetic, the path to
the images/fonts will not be resolved correctly when using the generated assetic resources from this bundle.

But there are trhee workarounds for this issue:

1. Use the cssembed filter from assetic
2. Use the cssrewrite filter with an own resource
3. Use the FkrCssURLRewriteBundle

For all examples we will use the following `component.json` with a default `sp_bower` configuration.

```json
{
    "dependencies": {
        "bootstrap": "2.3.1",
        "font-awesome": "3.1.1"
    }
}
```

### 1. Use the cssembed filter from assetic

This workaround uses the cssembed filter to integrate the images directly into the css file.
First we have to download the [cssembed binary](https://github.com/nzakas/cssembed/downloads)
to the directory  `app/Resources/java`.

Afterwards we have to tell assetic where this binary can be found:

```yml
# app/config.yml
assetic:
    filters:
        cssembed:
            jar: %kernel.root_dir%/Resources/java/cssembed.jar
```

Now all we have to do, is to add the cssembed filter to the bootstrap package.

```yml
# app/config.yml
sp_bower:
    assetic:
        enabled: true
        filters:
            packages:
                bootstrap:
                    css:
                        - cssembed
```

### 2. Use the cssrewrite filter with an own resource

The first workaound only works for images, but if there are fonts linked in the css file, the cssrewrite filter
is the only approach which will work.

First we have to define our own assetic resource

```yml
# app/config.yml
assetic:
    filters:
        cssrewrite: ~
    assets:
        font_awesome:
            inputs:
                - "bundles/acmedemo/components/font-awesome/build/assets/font-awesome/css/font-awesome.min.css"
            filters:
                - cssrewrite
```

**Note:** Don't forget to run the command `app/console assets:install`.

Since we now have a valid assetic resource, we can use it in our twig template:

```twig
{% stylesheets
    "@bootstrap_css"
    "@font_awesome"
%}
    <link rel="stylesheet" href="{{ asset_url }}" />
{% endstylesheets %}
```

### 3. Use the FkrCssURLRewriteBundle

The [FkrCssURLRewriteBundle](https://github.com/fkrauthan/FkrCssURLRewriteBundle) resolves the issue by adding
new filter which can be configured in your config. Just follow the installation instructions and take a look at the usage.
