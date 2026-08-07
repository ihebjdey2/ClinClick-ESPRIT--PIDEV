<?php

declare(strict_types=1);

// Router for PHP's built-in development server. Existing files must be
// returned directly so CSS, JavaScript, fonts and images bypass Symfony.
if (PHP_SAPI === 'cli-server') {
    $requestPath = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
    $publicFile = __DIR__.str_replace('/', DIRECTORY_SEPARATOR, $requestPath);

    if (is_file($publicFile)) {
        return false;
    }

    // Symfony Runtime must see the real front controller as the entry script.
    $_SERVER['SCRIPT_FILENAME'] = __DIR__.'/index.php';
}

require __DIR__.'/index.php';
