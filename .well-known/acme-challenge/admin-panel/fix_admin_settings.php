<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Fixing Admin Settings ===\n\n";

// Get or create admin settings
$settings = \App\Models\AdminSetting::first();

if ($settings) {
    echo "Found existing settings:\n";
    echo "Company: " . $settings->company_name . "\n";
    echo "Email: " . $settings->company_email . "\n\n";
    
    // Update to FMCG
    $settings->update([
        'company_name' => 'FMCG Sales Panel',
        'company_email' => 'info@fmcg.com',
        'company_phone' => '+91-9876543210',
        'company_address' => 'Mumbai, India',
        'gst_number' => 'GST123456789',
        'currency' => 'INR',
        'timezone' => 'Asia/Kolkata'
    ]);
    
    echo "✓ Updated settings to:\n";
    echo "Company: " . $settings->company_name . "\n";
    echo "Email: " . $settings->company_email . "\n";
} else {
    echo "Creating new admin settings...\n";
    \App\Models\AdminSetting::create([
        'company_name' => 'FMCG Sales Panel',
        'company_email' => 'info@fmcg.com',
        'company_phone' => '+91-9876543210',
        'company_address' => 'Mumbai, India',
        'gst_number' => 'GST123456789',
        'currency' => 'INR',
        'timezone' => 'Asia/Kolkata'
    ]);
    echo "✓ Created new settings\n";
}

echo "\n=== Done ===\n";
