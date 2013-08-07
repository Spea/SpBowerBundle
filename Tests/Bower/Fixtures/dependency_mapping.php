<?php
return array(
    "dependencies" => array(
        "other_package" => array(
            "canonicalDir" => "../components/other_package",
            "pkgMeta" => array(
                "main" => array(
                    "styles.css",
                    "script.js"
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
                    "pkgMeta" => array(
                        "main" => "package.js"
                    )
                )
            )
        ),
        "package" => array(
            "canonicalDir" => "../components/package",
            "pkgMeta" => array(
                "main" => "package.js"
            )
        ),
        "dependency_package" => array(
            "canonicalDir" => "../components/dependency_package",
            "pkgMeta" => array(
                "main" => array(
                    "script.js",
                    "utils.js",
                    "styles.css",
                    "navigation.css",
                ),
                "scripts" => array(
                    "script.js",
                    "utils.js",
                ),
                "styles" => array(
                    "styles.css",
                    "navigation.css"
                )
            ),
            "dependencies" => array(
                "package" => array(
                    "pkgMeta" => array(
                        "main" => "package.js",
                    ),
                ),
                "other_package" => array(
                    "pkgMeta" => array(
                        "main" => array(
                            "styles.css",
                            "script.js",
                        ),
                        "scripts" => array(
                            "main.js",
                            "customized.js"
                        ),
                        "styles" => array(
                            "main.css",
                            "customized.css"
                        )
                    ),
                )
            )
        ),
        "invalid-package_name" => array(),
        "boo_package" => array(
        ),
    )
);
