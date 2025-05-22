<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Access Logs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex">


    <?php include 'sidebar.php'; ?>

    <div class="flex flex-col flex-1">

        <?php include 'navbar.php'; ?>

        <main class="p-6">
            <h1 class="text-2xl font-semibold mb-4">Access Logs</h1>

            <div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">ID</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Table</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Action</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Record ID</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Old Data</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">New Data</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Changed By</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Changed At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm text-gray-800">


                        <tr>
                            <td class="px-4 py-2">1</td>
                            <td class="px-4 py-2">users</td>
                            <td class="px-4 py-2 text-green-600 font-semibold">INSERT</td>
                            <td class="px-4 py-2">9</td>
                            <td class="px-4 py-2 text-gray-500">NULL</td>
                            <td class="px-4 py-2">{...}</td>
                            <td class="px-4 py-2">9</td>
                            <td class="px-4 py-2">2025-05-17 15:05:27</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>