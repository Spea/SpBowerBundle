<?php
return array(
    "foo_package" => array(
        "source" => array(
            "main" => array(
                "../components/foo_package/foo.css",
                "../components/foo_package/barfoo.js"
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
    "boo_package" => array(
    ),
);
