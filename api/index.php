<?php

try {
    require __DIR__.'/../public/index.php';
} catch (Throwable $e) {
    error_log('[budget-kit] Fatal bootstrap: '.$e->getMessage());
    error_log($e->getFile().':'.$e->getLine());
    error_log($e->getTraceAsString());

    http_response_code(500);

    if (getenv('APP_DEBUG') === 'true') {
        header('Content-Type: text/plain; charset=utf-8');
        echo $e->getMessage()."\n\n".$e->getFile().':'.$e->getLine()."\n\n".$e->getTraceAsString();
    }
}
