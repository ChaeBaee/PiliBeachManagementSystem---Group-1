<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: /Proj/Admin/Dashboard/front/index.php");
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

<?php include '../../Elements/navbar.php'; ?>

    <div class="flex flex-col md:flex-row">

        <?php include '../../Elements/sidebar.php'; ?>

        <div class="flex-1 p-4 md:p-6 pt-28 md:pt-24 md:ml-64 transition-all duration-300">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-700">User Management</h2>
                <button onclick="openUserModal(false)"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Add New User
                </button>
            </div>

            <!-- Search and Filter Section -->
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" id="searchInput" placeholder="Search users by name, email, or phone..." 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex gap-2">
                        <select id="roleFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Roles</option>
                            <option value="Admin">Admin</option>
                            <option value="Guest">Guest</option>
                        </select>
                        <button onclick="clearFilters()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="text-gray-600 border-b bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 font-semibold">ID</th>
                                <th class="px-6 py-3 font-semibold">Name</th>
                                <th class="px-6 py-3 font-semibold">Email</th>
                                <th class="px-6 py-3 font-semibold">Phone</th>
                                <th class="px-6 py-3 font-semibold">Role</th>
                                <th class="px-6 py-3 font-semibold">Date Created</th>
                                <th class="px-6 py-3 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <!-- Users will be loaded here -->
                        </tbody>
                    </table>
                </div>
                <div id="noUsersMessage" class="hidden text-center py-8 text-gray-500">
                    <i class="fas fa-users text-4xl mb-4"></i>
                    <p>No users found matching your criteria.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div id="userModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="userModalTitle" class="text-xl font-bold text-gray-800">Add User</h3>
                    <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="userForm" onsubmit="handleUserSubmit(event)">
                    <input type="hidden" id="userId" name="userId">
                    <input type="hidden" id="isEdit" name="isEdit" value="false">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="csrf_token" id="csrfToken">

                    <div class="space-y-4">
                        <div>
                            <label class="block mb-2 font-medium text-sm text-gray-700">Full Name *</label>
                            <input type="text" id="userName" name="userName" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                placeholder="Enter full name">
                        </div>

                        <div>
                            <label class="block mb-2 font-medium text-sm text-gray-700">Email *</label>
                            <input type="email" id="userEmail" name="userEmail" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                placeholder="Enter email address">
                        </div>

                        <div>
                            <label class="block mb-2 font-medium text-sm text-gray-700">Phone *</label>
                            <input type="tel" id="userPhone" name="userPhone" required pattern="09[0-9]{9}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                placeholder="09XXXXXXXXX">
                            <p class="text-xs text-gray-500 mt-1">Format: 09XXXXXXXXX (11 digits)</p>
                        </div>

                        <div>
                            <label class="block mb-2 font-medium text-sm text-gray-700">Role *</label>
                            <select id="userRole" name="userRole" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                <option value="">Select role</option>
                                <option value="Guest">Guest</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>

                        <div id="passwordField">
                            <label class="block mb-2 font-medium text-sm text-gray-700">Password *</label>
                            <div class="relative">
                                <input type="password" id="userPassword" name="userPassword" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                    placeholder="Enter password">
                                <button type="button" onclick="togglePassword('userPassword')" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="userPasswordIcon"></i>
                                </button>
                            </div>
                            <div id="passwordStrength" class="mt-2">
                                <div class="flex space-x-1">
                                    <div class="flex-1 h-2 bg-gray-200 rounded"></div>
                                    <div class="flex-1 h-2 bg-gray-200 rounded"></div>
                                    <div class="flex-1 h-2 bg-gray-200 rounded"></div>
                                    <div class="flex-1 h-2 bg-gray-200 rounded"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Password must be at least 8 characters with uppercase, lowercase, and number</p>
                            </div>
                        </div>

                        <div id="confirmPasswordField">
                            <label class="block mb-2 font-medium text-sm text-gray-700">Confirm Password *</label>
                            <div class="relative">
                                <input type="password" id="confirmPassword" name="confirmPassword" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                    placeholder="Confirm password">
                                <button type="button" onclick="togglePassword('confirmPassword')" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div id="dateCreatedField" class="hidden">
                            <label class="block mb-2 font-medium text-sm text-gray-700">Date Created</label>
                            <input type="text" id="userDateCreated" disabled
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                        <button type="button" onclick="closeUserModal()"
                            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition-colors duration-200">Cancel</button>
                        <button type="submit" id="submitBtn"
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <span id="submitText">Save</span>
                            <i class="fas fa-spinner fa-spin hidden ml-2" id="submitSpinner"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div id="notificationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="notificationTitle" class="text-xl font-semibold text-gray-800"></h3>
                    <button onclick="closeNotificationModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <p id="notificationMessage" class="text-gray-600 text-base leading-relaxed mb-6"></p>
                <div class="flex justify-end">
                    <button onclick="closeNotificationModal()"
                        class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let allUsers = [];
        let csrfToken = '';

        // Load CSRF token and users on page load
        document.addEventListener('DOMContentLoaded', async function() {
            await loadCSRFToken();
            await loadUsers();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Search functionality
            document.getElementById('searchInput').addEventListener('input', filterUsers);
            document.getElementById('roleFilter').addEventListener('change', filterUsers);
        }

        async function loadCSRFToken() {
            try {
                const response = await fetch('/Proj/Admin/Users/back/users_backend.php?action=getCSRFToken');
                const data = await response.json();
                csrfToken = data.csrf_token;
                document.getElementById('csrfToken').value = csrfToken;
            } catch (error) {
                console.error('Error loading CSRF token:', error);
            }
        }

        async function loadUsers() {
            try {
                const response = await fetch('/Proj/Admin/Users/back/users_backend.php?action=getUsers');
                const result = await response.json();
                
                if (Array.isArray(result)) {
                    allUsers = result;
                    displayUsers(allUsers);
                } else if (result.success === false) {
                    console.error('Error loading users:', result.message);
                    showNotification('Error', 'Failed to load users: ' + result.message);
                } else {
                    console.error('Unexpected response format:', result);
                    showNotification('Error', 'Failed to load users. Unexpected response format.');
                }
            } catch (error) {
                console.error('Error loading users:', error);
                showNotification('Error', 'Failed to load users. Please try again.');
            }
        }

        function displayUsers(users) {
            const tbody = document.getElementById('usersTableBody');
            const noUsersMessage = document.getElementById('noUsersMessage');
            
            if (users.length === 0) {
                tbody.innerHTML = '';
                noUsersMessage.classList.remove('hidden');
                return;
            }

            noUsersMessage.classList.add('hidden');
            tbody.innerHTML = '';

            users.forEach(user => {
                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-gray-50 transition-colors duration-200';
                row.innerHTML = `
                    <td class="px-6 py-4 font-medium">${user.user_id}</td>
                    <td class="px-6 py-4">${escapeHtml(user.user_name)}</td>
                    <td class="px-6 py-4">${escapeHtml(user.user_email)}</td>
                    <td class="px-6 py-4">${escapeHtml(user.user_phone)}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium ${user.role_class}">
                            ${user.user_role}
                        </span>
                    </td>
                    <td class="px-6 py-4">${formatDate(user.user_date_created)}</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                onclick="openUserModal(true, ${JSON.stringify(user).replace(/"/g, '&quot;')})"
                                title="Edit user">
                                <i class="fas fa-edit text-lg"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-800 transition-colors duration-200" 
                                onclick="confirmDelete(${user.user_id}, '${user.user_role}')"
                                title="Delete user">
                                <i class="fas fa-trash text-lg"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function filterUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value;
            
            const filteredUsers = allUsers.filter(user => {
                const matchesSearch = user.user_name.toLowerCase().includes(searchTerm) ||
                                    user.user_email.toLowerCase().includes(searchTerm) ||
                                    user.user_phone.includes(searchTerm);
                const matchesRole = !roleFilter || user.user_role === roleFilter;
                
                return matchesSearch && matchesRole;
            });
            
            displayUsers(filteredUsers);
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('roleFilter').value = '';
            displayUsers(allUsers);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + 'Icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function validatePasswordStrength(password) {
            const strengthBars = document.querySelectorAll('#passwordStrength .flex-1');
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            
            strengthBars.forEach((bar, index) => {
                if (index < strength) {
                    bar.className = 'flex-1 h-2 rounded transition-colors duration-200 ' + 
                        (strength <= 2 ? 'bg-red-500' : strength <= 3 ? 'bg-yellow-500' : 'bg-green-500');
                } else {
                    bar.className = 'flex-1 h-2 bg-gray-200 rounded transition-colors duration-200';
                }
            });
            
            return strength >= 3;
        }

        function showNotification(title, message) {
            document.getElementById('notificationTitle').textContent = title;
            document.getElementById('notificationMessage').textContent = message;
            document.getElementById('notificationModal').classList.remove('hidden');
        }

        function closeNotificationModal() {
            document.getElementById('notificationModal').classList.add('hidden');
        }

        function openUserModal(isEdit = false, userData = null) {
            const modal = document.getElementById('userModal');
            const title = document.getElementById('userModalTitle');
            const passwordField = document.getElementById('passwordField');
            const confirmPasswordField = document.getElementById('confirmPasswordField');
            const dateCreatedField = document.getElementById('dateCreatedField');
            const form = document.getElementById('userForm');
            const emailField = document.getElementById('userEmail');
            const roleField = document.getElementById('userRole');

            title.textContent = isEdit ? "Edit User" : "Add User";
            document.getElementById('isEdit').value = isEdit;
            document.getElementById('formAction').value = isEdit ? 'edit' : 'add';

            // Show/hide fields based on mode
            passwordField.style.display = isEdit ? 'none' : 'block';
            confirmPasswordField.style.display = isEdit ? 'none' : 'block';
            dateCreatedField.style.display = isEdit ? 'block' : 'none';

            // Disable fields in edit mode
            emailField.disabled = isEdit;
            roleField.disabled = isEdit;

            if (isEdit && userData) {
                document.getElementById('userId').value = userData.user_id;
                document.getElementById('userName').value = userData.user_name;
                document.getElementById('userEmail').value = userData.user_email;
                document.getElementById('userPhone').value = userData.user_phone;
                document.getElementById('userRole').value = userData.user_role;
                document.getElementById('userDateCreated').value = formatDate(userData.user_date_created);
            } else {
                form.reset();
                document.getElementById('userDateCreated').value = '';
            }

            modal.classList.remove('hidden');
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
            document.getElementById('userForm').reset();
            document.getElementById('passwordStrength').querySelectorAll('.flex-1').forEach(bar => {
                bar.className = 'flex-1 h-2 bg-gray-200 rounded';
            });
        }

        async function handleUserSubmit(event) {
            event.preventDefault();
            
            const form = document.getElementById('userForm');
            const formData = new FormData(form);
            const isEdit = formData.get('isEdit') === 'true';
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');

            // Validate password strength for new users
            if (!isEdit) {
                const password = formData.get('userPassword');
                const confirmPassword = formData.get('confirmPassword');

                if (password !== confirmPassword) {
                    showNotification('Error', 'Passwords do not match!');
                    return;
                }

                if (!validatePasswordStrength(password)) {
                    showNotification('Error', 'Password is too weak. Please use a stronger password.');
                    return;
                }
            }

            // Show loading state
            submitBtn.disabled = true;
            submitText.textContent = 'Saving...';
            submitSpinner.classList.remove('hidden');

            try {
                const response = await fetch('/Proj/Admin/Users/back/users_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    closeUserModal();
                    await loadUsers();
                    showNotification('Success', data.message);
                } else {
                    showNotification('Error', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error', 'An error occurred while processing your request.');
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                submitText.textContent = 'Save';
                submitSpinner.classList.add('hidden');
            }
        }

        function confirmDelete(userId, userRole) {
            const confirmModal = document.createElement('div');
            confirmModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
            confirmModal.innerHTML = `
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-semibold text-gray-800">Confirm Delete</h3>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">Are you sure you want to delete this user? This action cannot be undone.</p>
                        <div class="flex justify-end gap-3">
                            <button onclick="this.closest('.fixed').remove()" 
                                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                                Cancel
                            </button>
                            <button onclick="deleteUser(${userId}, this)" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(confirmModal);
        }

        async function deleteUser(userId, button) {
            const modal = button.closest('.fixed');
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('userId', userId);
            formData.append('csrf_token', csrfToken);

            try {
                const response = await fetch('/Proj/Admin/Users/back/users_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                modal.remove();

                if (data.success) {
                    await loadUsers();
                    showNotification('Success', data.message);
                } else {
                    showNotification('Error', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                modal.remove();
                showNotification('Error', 'An error occurred while deleting the user.');
            }
        }

        // Password strength validation
        document.getElementById('userPassword').addEventListener('input', function() {
            validatePasswordStrength(this.value);
        });
    </script>

</body>

</html>