<?php

use App\Models\Language;

if (!function_exists('translate')) {
    function translate($key, $locale = null)
    {
        // Use the default locale if none is provided
        $locale = $locale ?? app()->getLocale();

        // Retrieve translation from the database
        $translation = Language::where('key', $key)
            ->where('locale', $locale)
            ->value('value');

        // Return the translation if found, otherwise return the key itself
        return $translation ?? $key;
    }
}
