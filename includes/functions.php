<?php
function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function truncate(string $text, int $length = 150): string {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

function formatDate(string $date): string {
    return date('d/m/Y', strtotime($date));
}