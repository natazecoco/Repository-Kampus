<?php
putenv("APP_ENV=testing");
putenv("DB_CONNECTION=sqlite");
putenv("DB_DATABASE=:memory:");
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/register', 'POST', ['name'=>'A','email'=>'a@example.com','npm'=>'12345678','password'=>'password123','password_confirmation'=>'password123']);
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content:\n" . $response->getContent() . "\n";
