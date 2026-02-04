<?php

namespace Constpb\AmoPlaceholder\Locale;

class LocaleService
{
    private static $locale = 'ru';
    private static $translations = [];

    public static function setLocale(string $locale): void
    {
        self::$locale = $locale;
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }

    public static function trans(string $key): string
    {
        $translation = self::getTranslation($key);

        return $translation;
    }

    private static function getTranslation(string $key): string
    {
        if (empty(self::$translations[self::$locale])) {
            self::loadTranslations(self::$locale);
        }

        if (isset(self::$translations[self::$locale][$key])) {
            return self::$translations[self::$locale][$key];
        }

        return $key;
    }

    protected static function loadTranslations(string $locale): void
    {
        $file = __DIR__ . "/{$locale}/messages.php";

        if (file_exists($file)) {
            self::$translations[$locale] = require $file;
        } else {
            self::$translations[$locale] = [];
        }
    }
}
