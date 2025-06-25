<?php

if (!$user) {
    http_response_code(404);
    require_once APP_PATH . '/pages/errors/404.php';
    exit;
} 