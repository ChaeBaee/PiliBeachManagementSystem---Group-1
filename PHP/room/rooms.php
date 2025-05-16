<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="h-16"></div>
<?php include('../elements/navbar.php'); ?>

    <div class="container mx-auto py-16 px-4">
        <h1 class="text-3xl font-bold text-center mb-8">Our Rooms</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <img src="room1.jpg" alt="Room 1" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">Deluxe Room</h2>
                    <p class="text-gray-700 mb-4">$120 per night</p>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Book Now</button>
                </div>
            </div>
            
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <img src="room2.jpg" alt="Room 2" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">Suite Room</h2>
                    <p class="text-gray-700 mb-4">$200 per night</p>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Book Now</button>
                </div>
            </div>
       
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <img src="room3.jpg" alt="Room 3" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">Standard Room</h2>
                    <p class="text-gray-700 mb-4">$80 per night</p>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Book Now</button>
                </div>
            </div>
          
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <img src="room4.jpg" alt="Room 4" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">Family Room</h2>
                    <p class="text-gray-700 mb-4">$150 per night</p>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Book Now</button>
                </div>
            </div>
       
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <img src="room5.jpg" alt="Room 5" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">Luxury Suite</h2>
                    <p class="text-gray-700 mb-4">$300 per night</p>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Book Now</button>
                </div>
            </div>
   
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <img src="room6.jpg" alt="Room 6" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">Economy Room</h2>
                    <p class="text-gray-700 mb-4">$60 per night</p>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Book Now</button>
                </div>
            </div>
         
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <img src="room7.jpg" alt="Room 7" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">Presidential Suite</h2>
                    <p class="text-gray-700 mb-4">$500 per night</p>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Book Now</button>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <img src="room8.jpg" alt="Room 8" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">Single Room</h2>
                    <p class="text-gray-700 mb-4">$50 per night</p>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Book Now</button>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <img src="room9.jpg" alt="Room 9" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">Penthouse Suite</h2>
                    <p class="text-gray-700 mb-4">$700 per night</p>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Book Now</button>
                </div>
            </div>
        </div>
    </div>

    <?php include('../elements/footer.php'); ?>
    
</body>
</html>