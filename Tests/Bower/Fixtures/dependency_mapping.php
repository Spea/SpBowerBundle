<?php
return array(
    "other_package" => array(
        "source" => array(
            "main" => array(
                "../components/other_package/styles.css",
                "../components/other_package/script.js"
            ),
            "scripts" => array(
                "../components/other_package/main.js",
                "../components/other_package/customized.js",
            ),
            "styles" => array(
                "../components/other_package/main.css",
                "../components/other_package/customized.css",
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
    "dependency_package" => array(
        "source" => array(
            "main" => array(
                "../components/dependency_package/script.js",
                "../components/dependency_package/utils.js",
                "../components/dependency_package/styles.css",
                "../components/dependency_package/navigation.css",
            ),
            "scripts" => array(
                "../components/dependency_package/script.js",
                "../components/dependency_package/utils.js",
            ),
            "styles" => array(
                "../components/dependency_package/styles.css",
                "../components/dependency_package/navigation.css"
            )
        ),
        "dependencies" => array(
            "package" => array(
                "source" => array(
                    "main" => "../components/package/package.js",
                ),
            ),
            "other_package" => array(
                "source" => array(
                    "main" => array(
                        "../components/other_package/styles.css",
                        "../components/other_package/script.js",
                    ),
                    "scripts" => array(
                        "../components/other_package/main.js",
                        "../components/other_package/customized.js"
                    ),
                    "styles" => array(
                        "../components/other_package/main.css",
                        "../components/other_package/customized.css"
                    )
                ),
            )
        )
    ),
    "invalid-package_name" => array(),
    "boo_package" => array(
    ),
);
