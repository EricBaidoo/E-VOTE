<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function i18n_available_languages() {
    return ['en' => 'English', 'fr' => 'Français', 'es' => 'Español', 'ar' => 'العربية'];
}

function i18n_is_rtl($lang) {
    return in_array($lang, ['ar', 'he', 'fa']);
}

function i18n_get_lang() {
    if (isset($_GET['lang']) && $_GET['lang']) {
        $_SESSION['lang'] = strtolower($_GET['lang']);
    }
    $lang = $_SESSION['lang'] ?? 'en';
    $langs = i18n_available_languages();
    return array_key_exists($lang, $langs) ? $lang : 'en';
}

function i18n_load_translations($lang) {
    $base = __DIR__ . '/../data/lang/' . $lang . '.json';
    if (file_exists($base)) {
        $raw = file_get_contents($base);
        $data = json_decode($raw, true);
        if (is_array($data)) return $data;
    }
    return [];
}

function __($key, $fallback = null) {
    static $cache = null;
    $lang = i18n_get_lang();
    if ($cache === null || ($cache['_lang'] ?? '') !== $lang) {
        $cache = i18n_load_translations($lang);
        $cache['_lang'] = $lang;
    }
    $val = $cache[$key] ?? null;
    if (!$val && $fallback) return $fallback;
    return $val ?: $key;
}

?>
