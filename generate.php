<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Certificates</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-12">
        <div class="bg-white rounded-xl shadow-lg p-8 md:p-12 max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">Generation Complete</h1>
            <div class="text-center mb-8">
                 <a href="index.php" class="text-violet-600 hover:text-violet-800">&larr; Generate More Certificates</a>
            </div>

<?php

// --- CONFIGURATION ---
// IMPORTANT: Make these directories writable by the server (e.g., chmod 777)
$output_dir = 'certificates/';
$qr_dir = 'qrcodes/';
$verification_db_file = 'verification_db.csv';
$font_file = 'fonts/Inter-Bold.ttf'; // Using a real font file is highly recommended.
                                     // Create a 'fonts' directory and add a .ttf file.
                                     // If you can't, we'll fall back to a default GD font.

// --- DIRECTORY SETUP ---
if (!is_dir($output_dir)) mkdir($output_dir, 0777, true);
if (!is_dir($qr_dir)) mkdir($qr_dir, 0777, true);

// --- HELPER FUNCTION: Display Error Message ---
function showError($message) {
    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">';
    echo '<p class="font-bold">Error:</p>';
    echo '<p>' . htmlspecialchars($message) . '</p>';
    echo '</div></div></div></body></html>';
    exit();
}


// --- FILE UPLOAD VALIDATION ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['csv_file']) || !isset($_FILES['template_file'])) {
    showError("Invalid request. Please upload files through the form.");
}

$csv_file = $_FILES['csv_file'];
$template_file = $_FILES['template_file'];

// Validate CSV
$csv_mimetypes = ['text/csv', 'application/csv', 'text/plain'];
if ($csv_file['error'] !== UPLOAD_ERR_OK || !in_array($csv_file['type'], $csv_mimetypes)) {
    showError("Invalid CSV file uploaded. Please ensure it's a valid .csv file.");
}

// Validate Image
$image_mimetypes = ['image/jpeg', 'image/png'];
if ($template_file['error'] !== UPLOAD_ERR_OK || !in_array($template_file['type'], $image_mimetypes)) {
    showError("Invalid template file. Please upload a .jpg or .png image.");
}


// --- CSV PROCESSING ---
$participants = [];
if (($handle = fopen($csv_file['tmp_name'], "r")) !== FALSE) {
    // Skip header row
    fgetcsv($handle); 
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (isset($data[0]) && !empty(trim($data[0]))) {
            $participants[] = trim($data[0]);
        }
    }
    fclose($handle);
}

if (empty($participants)) {
    showError("No names found in the CSV file. Please check the file format.");
}


// --- CERTIFICATE GENERATION LOOP ---
echo '<div class="space-y-4">';
$db_handle = fopen($verification_db_file, 'a');

foreach ($participants as $name) {
    // 1. Generate Unique ID and Verification URL
    $unique_id = uniqid() . '-' . bin2hex(random_bytes(4));
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    $verify_url = "{$protocol}://{$host}{$path}/verify.php?id={$unique_id}";

    // 2. Generate QR Code
    $qr_code_file = $qr_dir . $unique_id . '.png';
    $qr_api_url = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($verify_url);
    file_put_contents($qr_code_file, file_get_contents($qr_api_url));

    // 3. Create Certificate Image
    $template_path = $template_file['tmp_name'];
    $image_type = exif_imagetype($template_path);

    $template_image = null;
    if ($image_type == IMAGETYPE_JPEG) {
        $template_image = imagecreatefromjpeg($template_path);
    } elseif ($image_type == IMAGETYPE_PNG) {
        $template_image = imagecreatefrompng($template_path);
    } else {
        continue; // Skip if not a valid image
    }

    $qr_image = imagecreatefrompng($qr_code_file);

    // --- TEXT & QR PLACEMENT ---
    // These coordinates may need adjustment based on your template.
    // (0,0) is the top-left corner.
    $template_width = imagesx($template_image);
    $template_height = imagesy($template_image);
    
    // Name Placement
    $text_color = imagecolorallocate($template_image, 19, 22, 31); // Dark Gray
    $font_size = 50; // Adjust font size
    $use_custom_font = file_exists($font_file);

    if($use_custom_font) {
        $text_box = imagettfbbox($font_size, 0, $font_file, $name);
        $text_width = $text_box[2] - $text_box[0];
        $x_name = ($template_width / 2) - ($text_width / 2);
        $y_name = ($template_height / 2) - 50; // Adjust vertical position
        imagettftext($template_image, $font_size, 0, $x_name, $y_name, $text_color, $font_file, $name);
    } else {
        // Fallback to basic GD font if TTF is not available
        $text_width = imagefontwidth(5) * strlen($name);
        $x_name = ($template_width / 2) - ($text_width / 2);
        $y_name = ($template_height / 2) - 50;
        imagestring($template_image, 5, $x_name, $y_name, $name, $text_color);
    }

    // QR Code Placement
    $qr_width = imagesx($qr_image);
    $qr_height = imagesy($qr_image);
    $x_qr = $template_width - $qr_width - 80; // 80px from right edge
    $y_qr = $template_height - $qr_height - 60; // 60px from bottom edge
    imagecopy($template_image, $qr_image, $x_qr, $y_qr, 0, 0, $qr_width, $qr_height);
    
    // 4. Save Final Certificate
    $output_file = $output_dir . 'Certificate-' . preg_replace('/[^a-z0-9]/i', '_', $name) . '-' . $unique_id . '.png';
    imagepng($template_image, $output_file);
    
    // 5. Store Verification Data
    fputcsv($db_handle, [$unique_id, $name]);

    // 6. Clean up memory
    imagedestroy($template_image);
    imagedestroy($qr_image);

    // 7. Display Link
    echo '<div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border">';
    echo '  <p class="text-gray-700">' . htmlspecialchars($name) . '</p>';
    echo '  <a href="' . $output_file . '" download class="px-4 py-2 bg-violet-600 text-white text-sm font-semibold rounded-md hover:bg-violet-700">Download</a>';
    echo '</div>';
}

fclose($db_handle);
echo '</div>'; // close space-y-4

?>
        </div>
    </div>
</body>
</html>
