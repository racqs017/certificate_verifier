<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-lg text-center">
        <?php
        $verification_db_file = 'verification_db.csv';
        $certificate_id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $is_valid = false;
        $participant_name = '';

        if (!empty($certificate_id) && file_exists($verification_db_file)) {
            // Sanitize ID to prevent directory traversal
            if (preg_match('/^[a-z0-9-]+$/', $certificate_id)) {
                if (($handle = fopen($verification_db_file, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if (isset($data[0]) && $data[0] === $certificate_id) {
                            $is_valid = true;
                            $participant_name = $data[1];
                            break;
                        }
                    }
                    fclose($handle);
                }
            }
        }

        if ($is_valid):
        ?>
            <!-- Valid Certificate -->
            <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                    <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Certificate Verified</h1>
                <p class="text-gray-600 mt-3">This is a valid certificate issued by our organization.</p>
                <div class="mt-8 text-left bg-gray-50 p-4 rounded-lg border">
                    <p class="text-sm text-gray-500">Issued To:</p>
                    <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($participant_name); ?></p>
                    <p class="text-sm text-gray-500 mt-2">Certificate ID:</p>
                    <p class="text-sm font-mono text-gray-700 break-all"><?php echo htmlspecialchars($certificate_id); ?></p>
                </div>
            </div>
        <?php else: ?>
            <!-- Invalid Certificate -->
            <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
                 <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-6">
                    <svg class="h-10 w-10 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Verification Failed</h1>
                <p class="text-gray-600 mt-3">The certificate ID is invalid or could not be found in our records.</p>
                <p class="text-sm text-gray-500 mt-4">Please check the ID or contact the issuing organization.</p>
            </div>
        <?php endif; ?>

        <p class="text-center text-sm text-gray-500 mt-8">Certificate Verification System</p>
    </div>

</body>
</html>
