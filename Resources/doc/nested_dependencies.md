SpBowerBundle Nested Dependencies
=================================

By default all automatically registered asset resources use deep nesting for dependencies, this means that
if you have a package `my_package` which requires the package `jquery`, and you use the assetic resource
`@my_package_js`, `jquery_js` will be included aswell.

Now imagine that you also have a package `awesome_package` which requires `jquery` too. As soon as you use both
resources in one of your templates, `jquery` will be included twice (once in `my_package` and once in `awesome_package`).
Of course this is kinda bad, and it coud lead to unexpected output or even errors.

Happily you are able to disable this behavior for **all** packages ...

```yml
# app/config.yml
sp_bower:
    assetic:
        nest_dependencies: false
```

... or just for **some** packages ...

```yml
# app/config.yml
sp_bower:
    assetic:
        nest_dependencies:
            package: false
```
