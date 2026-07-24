<?php
$files = glob('c:/Users/vohun/Kiosk_Rental_System/kiosk_rental/app/Models/*.php');
$models = ['Contract.php', 'Kiosk.php', 'Customer.php', 'RentalRequest.php', 'BookingRequest.php', 'Payment.php'];
foreach ($files as $file) {
    if (in_array(basename($file), $models)) {
        $content = file_get_contents($file);
        if (strpos($content, 'use App\Traits\Auditable;') === false) {
            $content = preg_replace('/(use Illuminate\\\\Database\\\\Eloquent\\\\Model;)/', "$1\nuse App\\Traits\\Auditable;", $content);
            $content = preg_replace('/(use HasFactory;)/', "$1\n    use Auditable;", $content);
            file_put_contents($file, $content);
            echo "Added Auditable to " . basename($file) . "\n";
        }
    }
}
