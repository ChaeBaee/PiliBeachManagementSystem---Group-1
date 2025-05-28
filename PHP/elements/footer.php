<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body>
    <footer class="bg-white text-gray-700 border-t border-gray-200 py-6">
        <div class="container mx-auto text-center">
            <h2 class="text-lg font-bold text-blue-600 mb-4">Pili Beach Resort</h2>

            <div class="mb-4 space-x-4">
                <a href="/Proj/PHP/home.php" class="hover:text-blue-600 transition">Home</a>
                <a href="/Proj/PHP/room/about.php" class="hover:text-blue-600 transition">About Us</a>
                <a href="/Proj/PHP/room/about.php" class="hover:text-blue-600 transition">Contact</a>
                <a href="/Proj/PHP/home.php" class="hover:text-blue-600 transition">Services</a>
            </div>

            <div class="flex justify-center space-x-4 mb-4 text-gray-500">
                <a href="https://www.facebook.com/pilibeach.agmanic.resort" class="hover:text-blue-600 transition">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.facebook.com/pilibeach.agmanic.resort" class="hover:text-blue-600 transition">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.facebook.com/pilibeach.agmanic.resort" class="hover:text-blue-600 transition">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.facebook.com/pilibeach.agmanic.resort" class="hover:text-blue-600 transition">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>

            <p class="text-sm text-gray-400">&copy; <?php echo date('Y'); ?> Pili Beach Resort. All rights reserved.</p>
        </div>
    </footer>