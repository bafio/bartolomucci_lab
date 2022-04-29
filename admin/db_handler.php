<?php
function slugify($text) {
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '_', $text);
    // trim
    $text = trim($text, '_');
    // transliterate
    if (function_exists('iconv')) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
    // lowercase
    $text = strtolower($text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    if (empty($text)) {
        return 'n_a';
    }
    return $text;
}


class DB extends SQLite3 {
    function __construct($prefix = '') {
        $this->open($prefix.'sqlitedb.db');
    }
}
?>