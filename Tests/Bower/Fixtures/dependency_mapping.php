<?php
return array(
    "foo_package" => array(
        "source" => array(
            "main" => array(
                "../components/foo_package/foo.css",
                "../components/foo_package/barfoo.js"
            ),
            "scripts" => array(
                "main.js",
                "customized.js",
            ),
            "styles" => array(
                "main.css",
                "customized.css",
            ),
        ),
        "dependencies" => array(
            "package" => array(
                "source" => array(
                    "main" => "../components/package/package.js"
                )
            )
        )
    ),
    "package" => array(
        "source" => array(
            "main" => "../components/package/package.js"
        )
    ),
    "invalid-package_name" => array(),
    "boo_package" => array(
    ),
);
