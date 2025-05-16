<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Page</title>
 
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
 
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    main {
      flex: 1; 
      padding-top: 200px;
    }
  </style>
</head>
<body class="bg-[#faebe1]">
  <?php include('../elements/navbar.php'); ?>

  <main>
    <section class="py-10 px-4">
      <div class="max-w-5xl mx-auto">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Room Options</h2>

        <form class="bg-[#fdf4ee] rounded-full shadow-md px-6 py-4 flex flex-col md:flex-row items-center gap-4 md:gap-6">
          
          
          <div class="flex-1">
            <label class="text-sm text-gray-700 block">Room Type (Optional)</label>
            <input type="text" placeholder="Which room do you prefer?" class="w-full bg-transparent outline-none text-sm placeholder-gray-500" />
          </div>

          
          <div class="hidden md:block h-10 w-px bg-gray-300"></div>

          
          <div class="flex-1">
            <label class="text-sm text-gray-700 block">Check In</label>
            <input type="date" class="w-full bg-transparent outline-none text-sm text-gray-700" />
          </div>

       
          <div class="hidden md:block h-10 w-px bg-gray-300"></div>

          
          <div class="flex-1">
            <label class="text-sm text-gray-700 block">Check Out</label>
            <input type="date" class="w-full bg-transparent outline-none text-sm text-gray-700" />
          </div>

          
          <div class="hidden md:block h-10 w-px bg-gray-300"></div>

        
          <div class="flex-1">
            <label class="text-sm text-gray-700 block">Guests</label>
            <input type="number" min="1" placeholder="Add Guests" class="w-full bg-transparent outline-none text-sm placeholder-gray-500" />
          </div>

          
          <button type="submit" class="w-12 h-12 flex items-center justify-center bg-green-200 hover:bg-green-300 rounded-full text-black transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </button>

        </form>
      </div>
    </section>
  </main>

  <?php include('../elements/footer.php'); ?>
</body>
</html>
