<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-xl bg-white rounded-xl shadow-lg p-8 md:p-12">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Certificate Generator</h1>
            <p class="text-gray-500 mt-2">Upload a CSV of names and a certificate template to begin.</p>
        </div>

        <!-- Instructions -->
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-md mb-8" role="alert">
            <p class="font-bold">Instructions:</p>
            <ol class="list-decimal list-inside mt-2 text-sm">
                <li>Create a CSV file with a single column header: <strong>Name</strong>. List all participant names under it.</li>
                <li>Design your certificate in Canva and download it as a <strong>PNG</strong> or <strong>JPG</strong> file.</li>
                <li>Leave space on your template for the participant's name and a QR code.</li>
                <li>Upload both files below and click "Generate".</li>
            </ol>
        </div>

        <!-- Form -->
        <form action="generate.php" method="post" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">1. Participant List (.csv file)</label>
                <input type="file" name="csv_file" id="csv_file" required
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 transition-colors duration-200">
            </div>

            <div>
                <label for="template_file" class="block text-sm font-medium text-gray-700 mb-1">2. Certificate Template (.png or .jpg)</label>
                <input type="file" name="template_file" id="template_file" required accept="image/png, image/jpeg"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 transition-colors duration-200">
            </div>

            <div>
                <button type="submit"
                        class="w-full bg-violet-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-transform transform hover:scale-105 duration-300">
                    Generate Certificates
                </button>
            </div>
        </form>
    </div>

</body>
</html>
