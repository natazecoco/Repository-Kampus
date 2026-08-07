<?php
$projectRoot = dirname(__DIR__);
require $projectRoot . '/vendor/autoload.php';
$app = require $projectRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/register', 'POST', [
    'name' => 'Mahasiswa Debug',
    'email' => 'debug@example.com',
    'npm' => '12345678',
    'password' => 'password123',
    'password_confirmation' => 'password123',
]);
$response = $kernel->handle($request);
if (! is_dir($projectRoot . '/storage')) {
    mkdir($projectRoot . '/storage', 0777, true);
}
file_put_contents($projectRoot . '/storage/debug_post_register_status.txt', (string) $response->getStatusCode());
file_put_contents($projectRoot . '/storage/debug_post_register_body.html', $response->getContent());
$kernel->terminate($request, $response);
echo "Status: " . $response->getStatusCode() . PHP_EOL;
