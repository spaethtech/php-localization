<?php
/** @noinspection SpellCheckingInspection Disable due to numerous translations! */

declare(strict_types=1);
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/bootstrap.php";

use MVQN\Localization\Locale;
use MVQN\Localization\Translator;

// IMPORTANT: Supported locales are configured as constants in the Locale class and can be passed directly to
// Translator::setCurrentLocale().
$locale = Locale::SPANISH_SPAIN;
Translator::setCurrentLocale($locale);

// IMPORTANT: Any other locale supported by the Google Translation API can be used, but must be forced in the call to
// Translator::setCurrentLocale(..., true).
//
// NOTE: A local dictionary will be built on first page load and could be slow, depending upon the number of unique
// translations needed.
$locale = "de-DE";
Translator::setCurrentLocale($locale, true);

// NOTE: Specific translations can be taught to the local dictionary before loading pages, to avoid any or all online
// look-ups!  See documentation for additional features.
Translator::teach("Welcome", "Herzlich willkommen");
Translator::teach("Thank You!", "Vielen Dank!");


// Generate and return the HTML from the Twig template!
echo $twig->render("home.html.twig", [ "locale" => $locale ]);
