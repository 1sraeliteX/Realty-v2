<?php

// Supabase configuration
$supabaseUrl = "https://ducwcodegciekralkrqd.supabase.co";
$serviceKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImR1Y3djb2RlZ2NpZWtyYWxrcnFkIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3MjgzNjczNiwiZXhwIjoyMDg4NDEyNzM2fQ.VKZUKgEtkrJWhE1UlHzHNm_fIZe4gdrOGYfFyHlQ22Y";

// Admin data to insert
$admins = [
    [
        'name' => 'Super Admin',
        'email' => 'superadmin@cornerstone.com',
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'role' => 'super_admin'
    ],
    [
        'name' => 'Test Admin',
        'email' => 'admin@cornerstone.com',
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'role' => 'admin'
    ]
];

foreach ($admins as $admin) {
    $data = [
        'name' => $admin['name'],
        'email' => $admin['email'],
        'password' => $admin['password'],
        'role' => $admin['role'],
        'created_at' => date('c'),
        'updated_at' => date('c')
    ];
    
    $jsonData = json_encode($data);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $supabaseUrl . "/rest/v1/admins");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . $serviceKey,
        'Authorization: Bearer ' . $serviceKey,
        'Content-Type: application/json',
        'Prefer: return=minimal'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Creating admin: {$admin['email']}\n";
    echo "HTTP Status: $httpCode\n";
    echo "Response: $response\n\n";
}

echo "Admin creation completed!\n";

// Verify created admins
echo "\n=== Verifying Created Admins ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $supabaseUrl . "/rest/v1/admins?select=id,name,email,role,created_at&deleted_at=is.null&order=created_at.desc");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $serviceKey,
    'Authorization: Bearer ' . $serviceKey
]);

$response = curl_exec($ch);
curl_close($ch);

$admins = json_decode($response, true);
if ($admins) {
    foreach ($admins as $admin) {
        echo "ID: {$admin['id']}, Name: {$admin['name']}, Email: {$admin['email']}, Role: {$admin['role']}\n";
    }
} else {
    echo "No admins found or error occurred.\n";
}
?>
