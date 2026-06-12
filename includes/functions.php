<?php
// Génère un slug URL à partir d'un texte (ex: "Mon Article" → "mon-article")
function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text); // Supprime les accents
    $text = preg_replace('/[^a-z0-9]+/', '-', $text); // Remplace les caractères spéciaux
    return trim($text, '-');
}

// Tronque un texte à une longueur maximale
function truncate(string $text, int $length = 150): string {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

// Formate une date MySQL en format français
function formatDate(string $date): string {
    return date('d/m/Y', strtotime($date));
}