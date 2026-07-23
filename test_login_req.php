<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Disable CSRF globally for testing
$app->instance(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, new class($app, $app['Illuminate\Contracts\Encryption\Encrypter']) extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken {
    protected $except = ['*'];
});

$req = Illuminate\Http\Request::create('http://admin.kiosk.localhost:8000/login', 'POST', [
    'email' => 'employee@huebus.com',
    'password' => 'password',
]);

$response = $kernel->handle($req);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Location: " . $response->headers->get('Location') . "\n";
$sessionId = null;
foreach ($response->headers->getCookies() as $cookie) {
    echo "Cookie: " . $cookie->getName() . "=" . substr($cookie->getValue(), 0, 10) . "...; \n";
    if ($cookie->getName() === 'laravel-session' || $cookie->getName() === 'laravel_session' || config('session.cookie') === $cookie->getName()) {
        $sessionId = $cookie->getValue();
    }
}

if ($sessionId) {
    echo "Session ID from cookie: " . substr($sessionId, 0, 10) . "...\n";
    // Check DB
    $count = \Illuminate\Support\Facades\DB::table('sessions')->where('id', $sessionId)->count();
    echo "Session in DB? " . ($count > 0 ? "YES" : "NO") . "\n";
} else {
    echo "No session cookie found.\n";
}
