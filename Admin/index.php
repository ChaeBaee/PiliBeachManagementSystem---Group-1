<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {

  header("Location: login.php");
  exit;
}

function getDashboardData()
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST'] . "/Proj/Admin/dashboard_data.php");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close($ch);

  if ($response === false) {
    return null;
  }

  $data = json_decode($response, true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    return null;
  }

  return $data;
}

$dashboardData = getDashboardData();

if ($dashboardData === null) {
  $error = "Failed to load dashboard data. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pili Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 font-sans">

  <?php include 'navbar.php'; ?>

  <div class="flex flex-col md:flex-row">
    <?php include 'sidebar.php'; ?>

    <div class="flex-1 p-4 md:p-6 pt-28 md:pt-24 md:ml-64 transition-all duration-300">
      <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
          <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        </div>
      <?php else: ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

          <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-700">Total Bookings</h3>
                <p class="text-3xl mt-2 text-blue-600 font-bold">
                  <?php echo number_format($dashboardData['totalBookings']); ?>
                </p>
              </div>
              <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-700">Total Revenue</h3>
                <p class="text-3xl mt-2 text-green-600 font-bold">
                  ₱<?php echo number_format($dashboardData['totalPayments'], 2); ?></p>
              </div>
              <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-700">Pending Bookings</h3>
                <p class="text-3xl mt-2 text-yellow-600 font-bold">
                  <?php echo number_format($dashboardData['pendingBookings']); ?>
                </p>
              </div>
              <div class="bg-yellow-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-700">Total Invoices</h3>
                <p class="text-3xl mt-2 text-purple-600 font-bold">
                  <?php echo number_format($dashboardData['totalInvoices']); ?>
                </p>
              </div>
              <div class="bg-purple-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

          <div class="bg-white rounded-xl shadow p-5">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Monthly Revenue</h3>
            <canvas id="revenueChart" height="300"></canvas>
          </div>

          <div class="bg-white rounded-xl shadow p-5">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Revenue by Room Type</h3>
            <canvas id="roomTypeChart" height="300"></canvas>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Bookings</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="text-left text-gray-600 border-b">
                  <th class="py-2 pr-4">Guest</th>
                  <th class="py-2 pr-4">Room</th>
                  <th class="py-2 pr-4">Check In</th>
                  <th class="py-2 pr-4">Check Out</th>
                  <th class="py-2 pr-4">Amount</th>
                  <th class="py-2">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($dashboardData['recentBookings'] as $booking): ?>
                  <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 pr-4"><?php echo htmlspecialchars($booking['user_name']); ?></td>
                    <td class="py-2 pr-4"><?php echo htmlspecialchars($booking['room_number']); ?></td>
                    <td class="py-2 pr-4"><?php echo date('M d, Y', strtotime($booking['booking_check_in'])); ?></td>
                    <td class="py-2 pr-4"><?php echo date('M d, Y', strtotime($booking['booking_check_out'])); ?></td>
                    <td class="py-2 pr-4">₱<?php echo number_format($booking['booking_total_price'], 2); ?></td>
                    <td class="py-2">
                      <span class="px-2 py-1 rounded-full text-xs font-semibold
                      <?php
                      switch ($booking['booking_status']) {
                        case 'Confirmed':
                          echo 'bg-green-100 text-green-800';
                          break;
                        case 'Pending':
                          echo 'bg-yellow-100 text-yellow-800';
                          break;
                        case 'Checked-in':
                          echo 'bg-blue-100 text-blue-800';
                          break;
                        case 'Checked-out':
                          echo 'bg-gray-100 text-gray-800';
                          break;
                        default:
                          echo 'bg-red-100 text-red-800';
                      }
                      ?>">
                        <?php echo $booking['booking_status']; ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if (!isset($error)): ?>
    <script>
      // Monthly Revenue Chart
      const revenueCtx = document.getElementById('revenueChart').getContext('2d');
      new Chart(revenueCtx, {
        type: 'line',
        data: {
          labels: <?php echo json_encode(array_column($dashboardData['monthlyRevenue'], 'month')); ?>,
          datasets: [{
            label: 'Monthly Revenue',
            data: <?php echo json_encode(array_column($dashboardData['monthlyRevenue'], 'revenue')); ?>,
            borderColor: 'rgb(59, 130, 246)',
            tension: 0.1,
            fill: false
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return '₱' + value.toLocaleString();
                }
              }
            }
          }
        }
      });

      // Room Type Revenue Chart
      const roomTypeCtx = document.getElementById('roomTypeChart').getContext('2d');
      new Chart(roomTypeCtx, {
        type: 'doughnut',
        data: {
          labels: <?php echo json_encode(array_column($dashboardData['revenueByRoomType'], 'room_type')); ?>,
          datasets: [{
            data: <?php echo json_encode(array_column($dashboardData['revenueByRoomType'], 'total_revenue')); ?>,
            backgroundColor: [
              'rgb(59, 130, 246)',
              'rgb(16, 185, 129)',
              'rgb(245, 158, 11)',
              'rgb(139, 92, 246)'
            ]
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    </script>
  <?php endif; ?>
</body>

</html>