<?php
// Adapter ces paramètres à votre installation MySQL locale.
const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'stageconnect';
const DB_USER = 'root';
const DB_PASS = '';
// Détection automatique du chemin HTTP, même si le dossier est renommé.
// Exemple : C:/xampp/htdocs/monprojet -> /monprojet
$projectRoot = realpath(dirname(__DIR__));
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: false;
$basePath = '/stageconnect'; // Repli en mode CLI / Apache personnalisé.
if ($projectRoot && $documentRoot) {
    $rootNormalized = str_replace('\\', '/', $projectRoot);
    $docNormalized = rtrim(str_replace('\\', '/', $documentRoot), '/');
    if (strtolower($rootNormalized) === strtolower($docNormalized)) {
        $basePath = '';
    } elseif (str_starts_with(strtolower($rootNormalized), strtolower($docNormalized . '/'))) {
        $basePath = substr($rootNormalized, strlen($docNormalized));
    }
}
define('BASE_URL', $basePath);
const MAX_CV_BYTES = 2 * 1024 * 1024;
