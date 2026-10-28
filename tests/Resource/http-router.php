<?php

declare(strict_types=1);

# Router for the PHP built-in web server used in ResourceFactoryTest

if ('/redirect' === $_SERVER['REQUEST_URI']) {
    header('Location: /file.json', true, 302);
    header('Content-Type: text/html');
    header('Content-Length: 11');
    echo 'redirecting';

    return;
}

header('Content-Type: application/json; charset=utf-8');
header('Content-Length: 12');
echo "{\"abc\":123}\n";
