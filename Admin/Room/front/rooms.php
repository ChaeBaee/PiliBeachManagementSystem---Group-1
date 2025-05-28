<?php
include_once '../../Room/back/room-back.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">

    <?php include '../../Elements/navbar.php'; ?>

    <div class="flex flex-col md:flex-row">

        <?php include '../../Elements/sidebar.php'; ?>

        <div class="flex-1 p-4 md:p-6 pt-28 md:pt-24 md:ml-64 transition-all duration-300">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-700">Room Management</h2>
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow"
                    onclick="openAddModal()">Add Room</button>
            </div>

            <div class="bg-white shadow rounded-xl overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="text-gray-600 border-b bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Room ID</th>
                            <th class="px-6 py-3">Room Number</th>
                            <th class="px-6 py-3">Room Type</th>
                            <th class="px-6 py-3">Price</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Image</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once '../../../PHP/db.php';
                        $stmt = $conn->query("SELECT * FROM rooms ORDER BY room_id");
                        while ($room = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $image_path = $room['room_image'] ? '/Proj/PHP/assets/' . $room['room_image'] : '/Proj/PHP/assets/logo.jpg';


                            $status_color = '';
                            switch (strtolower($room['room_status'])) {
                                case 'available':
                                    $status_color = 'text-emerald-600 bg-emerald-50 border border-emerald-200';
                                    break;
                                case 'booked':
                                    $status_color = 'text-rose-600 bg-rose-50 border border-rose-200';
                                    break;
                                case 'maintenance':
                                    $status_color = 'text-amber-600 bg-amber-50 border border-amber-200';
                                    break;
                                default:
                                    $status_color = 'text-gray-600 bg-gray-50 border border-gray-200';
                            }

                            echo "<tr class='border-b hover:bg-gray-50'>";
                            echo "<td class='px-6 py-4'>{$room['room_id']}</td>";
                            echo "<td class='px-6 py-4'>{$room['room_number']}</td>";
                            echo "<td class='px-6 py-4'>{$room['room_type']}</td>";
                            echo "<td class='px-6 py-4'>₱" . number_format($room['room_price'], 2) . "</td>";
                            echo "<td class='px-6 py-4'>
                                    <span class='inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {$status_color}'>
                                        " . ucfirst($room['room_status']) . "
                                    </span>
                                  </td>";
                            echo "<td class='px-6 py-4'><img src='{$image_path}' alt='Room {$room['room_number']}' class='w-16 h-16 object-cover rounded'></td>";
                            echo "<td class='px-6 py-4'>
                                    <button class='text-blue-600 hover:text-blue-800 mr-3' onclick='openEditModal({$room['room_id']})'>
                                        <i class='fas fa-edit text-lg'></i>
                                    </button>
                                    <button class='text-red-600 hover:text-red-800' onclick='confirmDelete({$room['room_id']})'>
                                        <i class='fas fa-trash text-lg'></i>
                                    </button>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="roomModal"
        class="<?= $hasErrors ? '' : 'hidden' ?> fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold mb-4"><?= $isEdit ? "Edit Room" : "Add Room" ?></h3>
            <form id="roomForm" enctype="multipart/form-data" method="POST" action="../back/update_room.php">
                <input type="hidden" id="room_id" name="room_id" value="<?= htmlspecialchars($room_id) ?>">

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Room Number</label>
                    <input type="text" id="room_number" name="room_number" value="<?= htmlspecialchars($room_number) ?>"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500">
                    <span class="text-red-600 text-sm"><?= $room_numberMsg ?></span>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Room Type</label>
                    <select id="room_type" name="room_type"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500">
                        <option value="" disabled <?= empty($room_type) ? 'selected' : '' ?>>Select room type</option>
                        <option value="Deluxe Room" <?= $room_type === 'Deluxe Room' ? 'selected' : '' ?>>Deluxe Room
                        </option>
                        <option value="Standard Room" <?= $room_type === 'Standard Room' ? 'selected' : '' ?>>Standard Room
                        </option>
                        <option value="Single Room" <?= $room_type === 'Single Room' ? 'selected' : '' ?>>Single Room
                        </option>
                        <option value="Double Room" <?= $room_type === 'Double Room' ? 'selected' : '' ?>>Double Room
                        </option>
                    </select>
                    <span class="text-red-600 text-sm"><?= $room_typeMsg ?></span>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Price</label>
                    <input type="number" id="room_price" name="room_price" value="<?= htmlspecialchars($room_price) ?>"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500">
                    <span class="text-red-600 text-sm"><?= $room_priceMsg ?></span>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Room Status</label>
                    <select id="room_status" name="room_status"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500">
                        <option value="" disabled <?= empty($room_status) ? 'selected' : '' ?>>Select room status
                        </option>
                        <option value="available" <?= $room_status === 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="booked" <?= $room_status === 'booked' ? 'selected' : '' ?>>Booked</option>
                        <option value="maintenance" <?= $room_status === 'maintenance' ? 'selected' : '' ?>>Maintenance
                        </option>
                    </select>
                    <span class="text-red-600 text-sm"><?= $room_statusMsg ?></span>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Room Image</label>
                    <div class="mt-2">
                        <img id="currentImage" src="/Proj/PHP/assets/logo.jpg" alt="Current room image"
                            class="w-32 h-32 object-cover rounded mb-2">
                        <input type="file" id="room_image" name="room_image" accept="image/jpeg,image/png,image/webp"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500"
                            onchange="previewImage(this)">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" id="saveButton"
                        class="px-4 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("roomModal");
        const title = document.getElementById("modalTitle");
        const form = document.getElementById("roomForm");
        const currentImage = document.getElementById("currentImage");
        const saveButton = document.getElementById("saveButton");

        function openAddModal() {
            title.textContent = "Add Room";
            form.reset();
            currentImage.src = "/Proj/PHP/assets/logo.jpg";
            document.getElementById("room_id").value = "";
            document.getElementById("room_status").value = "available";
            modal.classList.remove("hidden");
        }

        function openEditModal(roomId) {
            title.textContent = "Edit Room";

            fetch(`../back/get_room.php?id=${roomId}`)
                .then(response => response.json())
                .then(room => {
                    document.getElementById("room_id").value = room.room_id;
                    document.getElementById("room_number").value = room.room_number;
                    document.getElementById("room_type").value = room.room_type;
                    document.getElementById("room_price").value = room.room_price;
                    document.getElementById("room_status").value = room.room_status;
                    currentImage.src = room.room_image ? `/Proj/PHP/assets/${room.room_image}` : "/Proj/PHP/assets/logo.png";
                    modal.classList.remove("hidden");
                });
        }

        function closeModal() {
            modal.classList.add("hidden");
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    currentImage.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function confirmDelete(roomId) {
            if (confirm("Are you sure you want to delete this room?")) {
                window.location.href = `/Proj/Admin/Room/back/delete_room.php?id=${roomId}`;
            }
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            form.querySelectorAll('.text-red-600').forEach(el => el.textContent = '');

            const roomNumber = document.getElementById('room_number').value.trim();
            const roomType = document.getElementById('room_type').value.trim();
            const roomPrice = document.getElementById('room_price').value.trim();
            const roomStatus = document.getElementById('room_status').value.trim();

            let hasErrors = false;

            if (!roomNumber) {
                document.getElementById('room_number').nextElementSibling.textContent = 'Room Number is required.';
                hasErrors = true;
            } else if (!/^[A-Za-z0-9-]+$/.test(roomNumber)) {
                document.getElementById('room_number').nextElementSibling.textContent = 'Room Number can only contain letters, numbers, and hyphens.';
                hasErrors = true;
            }

            if (!roomType) {
                document.getElementById('room_type').nextElementSibling.textContent = 'Room Type is required.';
                hasErrors = true;
            } else if (!['Deluxe Room', 'Standard Room', 'Single Room', 'Double Room'].includes(roomType)) {
                document.getElementById('room_type').nextElementSibling.textContent = 'Invalid room type selected.';
                hasErrors = true;
            }

            if (!roomPrice) {
                document.getElementById('room_price').nextElementSibling.textContent = 'Room Price is required.';
                hasErrors = true;
            } else if (isNaN(roomPrice)) {
                document.getElementById('room_price').nextElementSibling.textContent = 'Room Price must be a number.';
                hasErrors = true;
            } else if (parseFloat(roomPrice) <= 0) {
                document.getElementById('room_price').nextElementSibling.textContent = 'Room Price must be greater than 0.';
                hasErrors = true;
            } else if (parseFloat(roomPrice) > 1000000) {
                document.getElementById('room_price').nextElementSibling.textContent = 'Room Price cannot exceed ₱1,000,000.';
                hasErrors = true;
            }

            if (!roomStatus) {
                document.getElementById('room_status').nextElementSibling.textContent = 'Room Status is required.';
                hasErrors = true;
            } else if (!['available', 'booked', 'maintenance'].includes(roomStatus.toLowerCase())) {
                document.getElementById('room_status').nextElementSibling.textContent = 'Invalid room status selected.';
                hasErrors = true;
            }

            if (hasErrors) {
                return;
            }

            const formData = new FormData(form);

            fetch('../back/update_room.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message || 'An error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the room');
                });
        });
    </script>

</body>

</html>