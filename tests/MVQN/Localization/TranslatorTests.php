<?php
/** @noinspection SpellCheckingInspection - Disable due to numerous translations! */

use rspaeth\Localization\Locale;
use rspaeth\Localization\Translator;
use rspaeth\Localization\Exceptions\TranslatorException;

/**
 * Class TranslatorTests
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class TranslatorTests extends PHPUnit\Framework\TestCase
{
    // =================================================================================================================
    // SETUP
    // =================================================================================================================

    protected function setUp()
    {
        Translator::setDictionaryDirectory(__DIR__."/../../translations/");
    }

    // =================================================================================================================
    // HELPERS
    // =================================================================================================================

    protected function uninitialize()
    {
        $reflected = new ReflectionClass(Translator::class);
        $dictionaryDirectory = $reflected->getProperty("dictionaryDirectory");
        $dictionaryDirectory->setAccessible(true);
        $dictionaryDirectory->setValue(null);

        $supportedLocales = $reflected->getProperty("supportedLocales");
        $supportedLocales->setAccessible(true);
        $supportedLocales->setValue(null);

        $currentLocale = $reflected->getProperty("currentLocale");
        $currentLocale->setAccessible(true);
        $currentLocale->setValue(null);
    }

    // =================================================================================================================
    // PATHS
    // =================================================================================================================

    public function testGetDictionaryDirectory()
    {
        $directory = Translator::getDictionaryDirectory();
        echo $directory."\n";
        $this->assertEquals(realpath(__DIR__."/../../translations/"), $directory);
    }

    public function testGetDictionaryDirectoryUninitialized()
    {
        $this->uninitialize();

        $this->expectException(TranslatorException::class);

        $directory = Translator::getDictionaryDirectory();
        echo $directory."\n";
        $this->assertEquals(realpath(__DIR__."/../../translations/"), $directory);
    }

    // =================================================================================================================
    // LOCALES
    // =================================================================================================================

    public function testGetSupportedLocales()
    {
        $locales = Translator::getSupportedLocales();
        print_r($locales);
        $this->assertGreaterThan(1, count($locales));
    }

    public function testIsSupportedLocale()
    {
        $supported = Translator::isSupportedLocale(Locale::SPANISH_SPAIN);
        echo ($supported ? "YES" : "NO")."\n";
        $this->assertTrue($supported);

        $supported = Translator::isSupportedLocale("ar-SA");
        echo ($supported ? "YES" : "NO")."\n";
        $this->assertFalse($supported);
    }

    public function testIsTeachingLocale()
    {
        $teaching = Translator::isTeachingLocale(Locale::ENGLISH_UNITED_STATES);
        echo ($teaching ? "YES" : "NO")."\n";
        $this->assertTrue($teaching);
    }

    public function testGetCurrentLocale()
    {
        Translator::setCurrentLocale(Locale::SPANISH_SPAIN);
        $current = Translator::getCurrentLocale();
        echo $current."\n";
        $this->assertEquals(Locale::SPANISH_SPAIN, $current);
    }

    public function testGetCurrentLocaleUninitialized()
    {
        $this->uninitialize();
        $this->setUp();

        $this->expectException(TranslatorException::class);

        $current = Translator::getCurrentLocale();
        echo $current."\n";
        $this->assertEquals(Locale::ENGLISH_UNITED_STATES, $current);
    }

    public function testSetCurrentLocale()
    {
        Translator::setCurrentLocale(Locale::SPANISH_SPAIN);
        $current = Translator::getCurrentLocale();
        echo $current."\n";
        $this->assertEquals(Locale::SPANISH_SPAIN, $current);
    }

    // =================================================================================================================
    // FILES
    // =================================================================================================================

    public function testGetTranslationFilename()
    {
        $filename = Translator::getDictionaryFilename(Locale::SPANISH_SPAIN);
        echo $filename."\n";
        $this->assertEquals(realpath(Translator::getDictionaryDirectory()."/es-ES.json") , $filename);
    }

    public function testLoadDictionary()
    {
        $dictionary = Translator::loadDictionary(Locale::SPANISH_SPAIN);
        print_r($dictionary);
        $this->assertNotEmpty($dictionary);
    }

    public function testSaveDictionary()
    {
        $dictionary = Translator::loadDictionary(Locale::SPANISH_SPAIN);
        $dictionary["This is a PHPUnit test added phrase!"] = "Esta es una frase agregada de prueba de PHPUnit!";
        $dictionary = Translator::saveDictionary($dictionary, Locale::SPANISH_SPAIN);
        print_r($dictionary);
        $this->assertArrayHasKey("This is a PHPUnit test added phrase!", $dictionary);

        unset($dictionary["This is a PHPUnit test added phrase!"]);
        $dictionary = Translator::saveDictionary($dictionary, Locale::SPANISH_SPAIN);
        print_r($dictionary);
        $this->assertArrayNotHasKey("This is a PHPUnit test added phrase!", $dictionary);
    }

    // =================================================================================================================
    // INQUIRIES
    // =================================================================================================================

    public function testKnows()
    {
        Translator::setCurrentLocale(Locale::SPANISH_SPAIN);

        $test = Translator::knows("Hello");
        $this->assertTrue($test);
        $test = Translator::knows("Goodbye", Locale::SPANISH_SPAIN);
        $this->assertTrue($test);
        $test = Translator::knows("mother", "de-DE");
        $this->assertTrue($test);

        $test = Translator::knows("car");
        $this->assertFalse($test);
    }

    public function testAsk()
    {
        Translator::setCurrentLocale(Locale::SPANISH_SPAIN);

        $test = Translator::ask("Hello");
        $this->assertEquals("Hola", $test);

        $test = Translator::ask("mother", Locale::SPANISH_SPAIN);
        $this->assertEquals("madre", $test);

        $test = Translator::ask("Goodbye", "de-DE");
        $this->assertEquals("Auf Wiedersehen", $test);

        $test = Translator::ask("car", "de-DE");
        $this->assertEquals("", $test);
    }

    // =================================================================================================================
    // LEARNING
    // =================================================================================================================

    public function testLearnAndForget()
    {
        Translator::setCurrentLocale(Locale::SPANISH_SPAIN);

        // Local lookup!
        $test = Translator::learn("Hello");
        $this->assertTrue(Translator::knows("Hello"));
        $this->assertEquals("Hola", $test);

        // Online lookup!
        Translator::forget("Hello");
        $test = Translator::learn("Hello");
        $this->assertTrue(Translator::knows("Hello"));
        $this->assertEquals("Hola", $test);
    }

    public function testIntroduceAndWithdrawAndIsFamiliar()
    {
        Translator::setCurrentLocale(Locale::SPANISH_SPAIN);

        Translator::introduce("warrior");
        $this->assertTrue(Translator::isFamiliar("warrior"));

        Translator::withdraw("warrior");
        $this->assertFalse(Translator::isFamiliar("warrior"));
    }

    // =================================================================================================================
    // TEACHING
    // =================================================================================================================

    public function testTeach()
    {
        Translator::setCurrentLocale(Locale::SPANISH_SPAIN);

        Translator::teach("morning", "mañana");
        $this->assertTrue(Translator::knows("morning"));
        Translator::teach("afternoon", "tarde");
        $this->assertTrue(Translator::knows("afternoon"));
        Translator::teach("evening", "noche");
        $this->assertTrue(Translator::knows("evening"));

        Translator::teach("evening", "abend", "de-DE");
        $this->assertTrue(Translator::knows("evening", "de-DE"));
    }

    public function testShare()
    {
        Translator::setCurrentLocale(Locale::SPANISH_SPAIN);

        $teachingCount = count(Translator::loadDictionary(Translator::getTeachingLocale()));

        Translator::share([ "de-DE", "ar-SA" ]);

        $deCount = count(Translator::loadDictionary("de-DE"));
        $this->assertEquals($deCount, $teachingCount);

        $arCount = count(Translator::loadDictionary("ar-SA"));
        $this->assertEquals($arCount, $teachingCount);
    }

}