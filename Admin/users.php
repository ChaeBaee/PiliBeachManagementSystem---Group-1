<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">


    <?php include 'navbar.php'; ?>

    <div class="flex flex-col md:flex-row">

        <?php include 'sidebar.php'; ?>


        <div class="flex-1 p-4 md:p-6 pt-28 md:pt-24 md:ml-64 transition-all duration-300">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-700">User Management</h2>
                <button onclick="openUserModal(false)"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Add New User
                </button>
            </div>


            <div class="bg-white shadow rounded-xl overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="text-gray-600 border-b bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Phone</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Date Created</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div id="userModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
            <h3 id="userModalTitle" class="text-xl font-bold mb-4">Add User</h3>
            <form id="userForm" onsubmit="handleUserSubmit(event)">
                <input type="hidden" id="userId" name="userId">
                <input type="hidden" id="isEdit" name="isEdit" value="false">
                <input type="hidden" name="action" id="formAction" value="add">

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Full Name</label>
                    <input type="text" id="userName" name="userName" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Email</label>
                    <input type="email" id="userEmail" name="userEmail" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Phone</label>
                    <input type="tel" id="userPhone" name="userPhone" required pattern="[0-9]{11}"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Role</label>
                    <select id="userRole" name="userRole"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500">
                        <option value="Guest">Guest</option>
                        <option value="Admin">Admin</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>

                <div id="passwordField" class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Password</label>
                    <input type="password" id="userPassword" name="userPassword"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <div id="confirmPasswordField" class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <div id="dateCreatedField" class="mb-4" style="display: none;">
                    <label class="block mb-1 font-medium text-sm">Date Created</label>
                    <input type="text" id="userDateCreated" disabled
                        class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeUserModal()"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="notificationModal"
        class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md transform transition-all">
            <div class="flex items-center justify-between mb-6">
                <h3 id="notificationTitle" class="text-2xl font-semibold text-gray-800 tracking-tight"></h3>
                <button onclick="closeNotificationModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <p id="notificationMessage" class="text-gray-600 text-base leading-relaxed mb-8"></p>
            <div class="flex justify-end">
                <button onclick="closeNotificationModal()"
                    class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    OK
                </button>
            </div>
        </div>
    </div>

    <script>
        const userModal = document.getElementById("userModal");
        const userModalTitle = document.getElementById("userModalTitle");
        const passwordField = document.getElementById("passwordField");
        const userForm = document.getElementById("userForm");
        const notificationModal = document.getElementById("notificationModal");
        const notificationTitle = document.getElementById("notificationTitle");
        const notificationMessage = document.getElementById("notificationMessage");

        function showNotification(title, message) {
            notificationTitle.textContent = title;
            notificationMessage.textContent = message;
            notificationModal.classList.remove("hidden");
        }

        function closeNotificationModal() {
            notificationModal.classList.add("hidden");
        }

        // Load users on page load
        document.addEventListener('DOMContentLoaded', loadUsers);

        function loadUsers() {
            fetch('users_backend.php?action=getUsers')
                .then(response => response.json())
                .then(users => {
                    const tbody = document.getElementById('usersTableBody');
                    tbody.innerHTML = '';

                    users.forEach(user => {
                        tbody.innerHTML += `
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4">${user.user_id}</td>
                                <td class="px-6 py-4">${user.user_name}</td>
                                <td class="px-6 py-4">${user.user_email}</td>
                                <td class="px-6 py-4">${user.user_phone}</td>
                                <td class="px-6 py-4">${user.user_role}</td>
                                <td class="px-6 py-4">${user.user_date_created}</td>
                                <td class="px-6 py-4">
                                    <button class="text-blue-600 hover:text-blue-800 mr-3"
                                        onclick="openUserModal(true, ${JSON.stringify(user).replace(/"/g, '&quot;')})">
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800" 
                                        onclick="confirmDelete(${user.user_id}, '${user.user_role}')">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    showNotification('Error', 'Failed to load users. Please try again.');
                });
        }

        function openUserModal(isEdit = false, userData = null) {
            userModalTitle.textContent = isEdit ? "Edit User" : "Add User";
            document.getElementById('isEdit').value = isEdit;
            document.getElementById('formAction').value = isEdit ? 'edit' : 'add';

            passwordField.style.display = isEdit ? 'none' : 'block';
            document.getElementById('confirmPasswordField').style.display = isEdit ? 'none' : 'block';
            dateCreatedField.style.display = isEdit ? 'block' : 'none';

            document.getElementById('userEmail').disabled = isEdit;
            document.getElementById('userRole').disabled = isEdit;

            if (isEdit && userData) {
                document.getElementById('userId').value = userData.user_id;
                document.getElementById('userName').value = userData.user_name;
                document.getElementById('userEmail').value = userData.user_email;
                document.getElementById('userPhone').value = userData.user_phone;
                document.getElementById('userRole').value = userData.user_role;
                document.getElementById('userDateCreated').value = userData.user_date_created;
            } else {
                userForm.reset();
                document.getElementById('userDateCreated').value = '';
            }

            userModal.classList.remove("hidden");
        }

        function closeUserModal() {
            userModal.classList.add("hidden");
            userForm.reset();
        }

        function handleUserSubmit(event) {
            event.preventDefault();
            const formData = new FormData(userForm);
            const isEdit = formData.get('isEdit') === 'true';

            if (!isEdit) {
                const password = formData.get('userPassword');
                const confirmPassword = formData.get('confirmPassword');

                if (password !== confirmPassword) {
                    showNotification('Error', 'Passwords do not match!');
                    return;
                }
            }

            if (isEdit) {
                formData.delete('userPassword');
                formData.delete('confirmPassword');
            }

            fetch('users_backend.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeUserModal();
                        loadUsers();
                        showNotification('Success', data.message);
                    } else {
                        showNotification('Error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error', 'An error occurred while processing your request.');
                });
        }

        function confirmDelete(userId, userRole) {
            const confirmModal = document.createElement('div');
            confirmModal.className = 'fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50';
            confirmModal.innerHTML = `
                <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Confirm Delete</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to delete this user?</p>
                    <div class="flex justify-end gap-3">
                        <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                            Cancel
                        </button>
                        <button onclick="deleteUser(${userId}, this)" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Delete
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(confirmModal);
        }

        function deleteUser(userId, button) {
            const modal = button.closest('.fixed');
            fetch('users_backend.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&userId=${userId}`
            })
                .then(response => response.json())
                .then(data => {
                    modal.remove();
                    if (data.success) {
                        loadUsers();
                        showNotification('Success', data.message);
                    } else {
                        showNotification('Error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modal.remove();
                    showNotification('Error', 'An error occurred while deleting the user.');
                });
        }
    </script>

</body>

</html>