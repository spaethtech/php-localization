<?php
declare(strict_types=1);
require_once __DIR__ . "/../../vendor/autoload.php";

use MVQN\Localization\Translator;

/**
 * bootstrap.php
 *
 * A common configuration and initialization file.
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */

// IMPORTANT: Set the dictionary directory and "default" locale before using any other functions of the Translator!
Translator::setDictionaryDirectory(__DIR__."/../translations/");
Translator::setCurrentLocale("en-US");

// Configure the Twig template environment and pass it along in the global namespace as this is used often.
$twig = new Twig_Environment(
    new Twig_Loader_Filesystem(__DIR__ . "/twig/"),
    [
        // TODO: Make sure cached templates honor the translations!
        //"cache" => __DIR__."/twig/.cache/", // Can optionally be enabled after development is complete!
    ]
);

// IMPORTANT: Add the "translate" filter to the Twig environment, otherwise the |translate filter will not function!
$twig->addFilter(Translator::getTwigFilterTranslate());