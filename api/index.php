<?php
// Forward Vercel requests to normal index.php
$path = dirname(__DIR__);
chdir($path);
require $path . '/public/index.php';