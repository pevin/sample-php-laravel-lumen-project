<?php
passthru("php artisan migrate");
require __DIR__ . '/app.php';
