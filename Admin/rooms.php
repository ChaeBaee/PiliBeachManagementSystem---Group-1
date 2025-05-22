<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">


    <?php include 'navbar.php'; ?>

    <div class="flex flex-col md:flex-row">

        <?php include 'sidebar.php'; ?>


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
                            <th class="px-6 py-3">Room Name</th>
                            <th class="px-6 py-3">Room Type</th>
                            <th class="px-6 py-3">Price</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4">Room 101</td>
                            <td class="px-6 py-4">Deluxe</td>
                            <td class="px-6 py-4">₱2,500</td>
                            <td class="px-6 py-4 text-green-600">Available</td>
                            <td class="px-6 py-4">
                                <button class="text-blue-600 hover:underline mr-3"
                                    onclick="openEditModal()">Edit</button>
                                <button class="text-red-600 hover:underline" onclick="confirmDelete()">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div id="roomModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Add Room</h3>
            <form>
                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Room Name</label>
                    <input type="text"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500"
                        required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Room Type</label>
                    <input type="text"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500"
                        required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Price</label>
                    <input type="number"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500"
                        required>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("roomModal");
        const title = document.getElementById("modalTitle");

        function openAddModal() {
            title.textContent = "Add Room";
            modal.classList.remove("hidden");
        }

        function openEditModal() {
            title.textContent = "Edit Room";
            modal.classList.remove("hidden");
        }

        function closeModal() {
            modal.classList.add("hidden");
        }

        function confirmDelete() {
            if (confirm("Are you sure you want to delete this room?")) {
                alert("Room deleted (logic not yet implemented)");
            }
        }
    </script>

</body>

</html>