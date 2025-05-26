<?php include 'back/room-data/room-data.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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

<body class="bg-gray-100">

    <?php include('../elements/navbar.php'); ?>

    <?php
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login/front/login.php");
        exit();
    }
    ?>

    <main>
        <section class="py-10 px-4">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">Room Options</h2>

                <form method="POST"
                    class="bg-white rounded-full border border-gray-200 shadow-sm px-6 py-6 flex flex-col md:flex-row items-center gap-4 md:gap-6 mb-8">
                    <div class="flex-1">
                        <label class="text-sm text-gray-700 block mb-1">Room Type (Optional)</label>
                        <select name="room_type"
                            class="w-full bg-white border border-gray-300 rounded-full px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                            <option value="" <?= $roomType == "" ? "selected" : "" ?>>All Room Types</option>
                            <option value="Single Room" <?= $roomType == "Single Room" ? "selected" : "" ?>>Single Room
                            </option>
                            <option value="Standard Room" <?= $roomType == "Standard Room" ? "selected" : "" ?>>Standard
                                Room</option>
                            <option value="Double Room" <?= $roomType == "Double Room" ? "selected" : "" ?>>Double Room
                            </option>
                            <option value="Deluxe Room" <?= $roomType == "Deluxe Room" ? "selected" : "" ?>>Deluxe Room
                            </option>
                        </select>
                    </div>

                    <div class="hidden md:block h-10 w-px bg-gray-200"></div>

                    <div class="flex-1">
                        <label class="text-sm text-gray-700 block mb-1">Check In</label>
                        <input type="date" name="checkin" min="<?= $today ?>" value="<?= htmlspecialchars($checkIn) ?>"
                            class="w-full bg-white border border-gray-300 rounded-full px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
                        <span style="color: red"><?= $checkInMsg ?></span>
                    </div>

                    <div class="hidden md:block h-10 w-px bg-gray-200"></div>

                    <div class="flex-1">
                        <label class="text-sm text-gray-700 block mb-1">Check Out</label>
                        <input type="date" name="checkout" min="<?= $today ?>"
                            value="<?= htmlspecialchars($checkOut) ?>"
                            class="w-full bg-white border border-gray-300 rounded-full px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
                        <span style="color: red"><?= $checkOutMsg ?></span>
                    </div>

                    <div class="hidden md:block h-10 w-px bg-gray-200"></div>

                    <div class="flex-1">
                        <label class="text-sm text-gray-700 block mb-1">Guests</label>
                        <input type="number" name="guests" min="1" value="<?= htmlspecialchars($guest) ?>"
                            placeholder="Add Guests"
                            class="w-full bg-white border border-gray-300 rounded-full px-3 py-2 text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-300" />
                        <span style="color: red"><?= $guestMsg ?></span>
                    </div>

                    <button type="submit"
                        class="w-12 h-12 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>

                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !$hasError && !empty($available_rooms)): ?>
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-700">Available Rooms</h3>

                        <?php foreach ($available_rooms as $room): ?>
                            <div
                                class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 flex flex-col md:flex-row gap-4">
                                <img src="<?= htmlspecialchars($room['room_image']) ?>" alt="Room"
                                    class="w-full md:w-48 h-32 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-md font-semibold text-gray-800"><?= htmlspecialchars($room['room_type']) ?>
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($room['room_description']) ?></p>
                                    <p class="text-blue-600 font-semibold text-sm">$<?= htmlspecialchars($room['room_price']) ?>
                                        / night</p>
                                </div>
                                <div class="flex items-center justify-end md:flex-col md:justify-between gap-2">
                                    <span
                                        class="text-sm text-green-600 font-medium"><?= htmlspecialchars($room['room_status']) ?></span>
                                    <form method="POST" action="bookroom/front/bookRoom.php">
                                        <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">
                                        <input type="hidden" name="checkin" value="<?= htmlspecialchars($checkIn) ?>">
                                        <input type="hidden" name="checkout" value="<?= htmlspecialchars($checkOut) ?>">
                                        <input type="hidden" name="guests" value="<?= htmlspecialchars($guest) ?>">
                                        <button type="submit"
                                            class="bg-blue-500 text-white text-sm px-4 py-2 rounded-full hover:bg-blue-600">
                                            Book Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !$hasError): ?>
                    <p class="text-center text-gray-500">No rooms available matching your criteria.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include('../elements/footer.php'); ?>
</body>

</html>