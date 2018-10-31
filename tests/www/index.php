<?php
declare(strict_types=1);
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/bootstrap.php";

use MVQN\Localization\Locale;
use MVQN\Localization\Translator;

$locale = Locale::SPANISH_SPAIN;
Translator::setCurrentLocale($locale);

// Generate and return the HTML from the Twig template!
echo $twig->render("home.html.twig", [ "locale" => $locale ]);
