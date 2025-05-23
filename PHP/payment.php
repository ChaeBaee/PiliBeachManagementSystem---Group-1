<?php
include_once 'elements/navbar.php';

$payment_method = $card_number = $expiry = $cvv = "";
$card_numberMsg = $expiryMsg = $cvvMsg = $paymentMethodMsg = $gcashProofMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST["payment_method"] ?? "";

    if (empty($payment_method)) {
        $paymentMethodMsg = "Please choose a payment method.";
    } else {
        if ($payment_method === "card") {
            $card_number = $_POST["card_number"] ?? "";
            $expiry = $_POST["expiry"] ?? "";
            $cvv = $_POST["cvv"] ?? "";

            if (empty($card_number)) {
                $card_numberMsg = "Please input card number.";
            }
            if (empty($expiry)) {
                $expiryMsg = "Please input card expiry.";
            }
            if (empty($cvv)) {
                $cvvMsg = "Please input CVV.";
            }
        } elseif ($payment_method === "gcash") {
            // Validate image upload
            if (!isset($_FILES['gcash_proof']) || $_FILES['gcash_proof']['error'] === UPLOAD_ERR_NO_FILE) {
                $gcashProofMsg = "Please upload proof of payment.";
            } else {
                $allowedTypes = ['jpg','jpeg','png'];
                $fileName = $_FILES['gcash_proof']['name'];
                $fileTmp = $_FILES['gcash_proof']['tmp_name'];
                $fileSize = $_FILES['gcash_proof']['size'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($fileExt, $allowedTypes)) {
                    $gcashProofMsg = "Only JPG, JPEG, PNG, GIF files allowed.";
                } elseif ($fileSize > 5*1024*1024) {
                    $gcashProofMsg = "File size must be less than 5MB.";
                }
            }
        }
    }

    // If no errors, process payment
    if (
        $paymentMethodMsg === "" &&
        $card_numberMsg === "" &&
        $expiryMsg === "" &&
        $cvvMsg === "" &&
        $gcashProofMsg === ""
    ) {
        if ($payment_method === "gcash") {
            // Save the uploaded image
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $uniqueName = uniqid() . "-" . basename($_FILES['gcash_proof']['name']);
            $targetFile = $uploadDir . $uniqueName;

            if (move_uploaded_file($_FILES['gcash_proof']['tmp_name'], $targetFile)) {
                $_SESSION['booking']['gcash_proof'] = $targetFile;
            } else {
                $gcashProofMsg = "Failed to upload image. Please try again.";
            }
        }

        if ($gcashProofMsg === "") {
            $_SESSION['booking']['payment_method'] = $payment_method;
            $_SESSION['booking']['card_number'] = $card_number;

            // Redirect to invoice or confirmation page
            header('Location: invoice.php');
            exit();
        }
    }
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking'])) {
    header("Location: /Proj/PHP/booking/front/2ndlogin.php");
    exit();
}

$booking = $_SESSION['booking'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Secure Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#faebe1] min-h-screen flex flex-col">

<main class="flex-grow p-6 pt-28 min-h-screen overflow-y-auto">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-2xl mx-auto relative">

        <a href="/Proj/PHP/booking/front/booking-confirmation.php" class="absolute top-4 left-4 text-gray-600 hover:text-black text-sm flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>

        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Complete Your Payment</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Booking Summary -->
            <div class="bg-gray-100 p-4 rounded-xl shadow-sm">
                <h3 class="text-lg font-semibold mb-3">Booking Summary</h3>
                <p class="text-sm text-gray-700">Room: <strong><?= htmlspecialchars($booking['room_type']) ?></strong></p>
                <p class="text-sm text-gray-700">Guests: <strong><?= htmlspecialchars($booking['guests']) ?></strong></p>
                <p class="text-sm text-gray-700">Check-in: <strong><?= htmlspecialchars($booking['checkin']) ?></strong></p>
                <p class="text-sm text-gray-700">Check-out: <strong><?= htmlspecialchars($booking['checkout']) ?></strong></p>
                <hr class="my-3">
                <p class="text-sm">Subtotal: <span class="float-right">₱3,000</span></p>
                <p class="text-sm">Service Fee: <span class="float-right">₱200</span></p>
                <p class="text-sm font-bold">Total: <span class="float-right">₱3,200</span></p>
                <p class="text-xs text-gray-500 mt-4">Need help? <a href="/contact.php" class="text-blue-600 underline">Contact support</a></p>
            </div>

            <!-- Payment Form -->
            <form method="POST" action="" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Payment Method</label>
                    <select name="payment_method" id="payment_method" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">-- Choose Method --</option>
                        <option value="gcash" <?= $payment_method === 'gcash' ? 'selected' : '' ?>>Gcash</option>
                        <option value="card" <?= $payment_method === 'card' ? 'selected' : '' ?>>Credit / Debit Card</option>
                        <option value="cod" <?= $payment_method === 'cod' ? 'selected' : '' ?>>Cash on Arrival</option>
                    </select>
                    <span class="text-red-600 text-sm"><?= $paymentMethodMsg ?></span>
                </div>

                <!-- GCash Fields -->
                <div id="gcash_fields" class="<?= $payment_method === 'gcash' ? '' : 'hidden' ?> space-y-3">
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                        <h4 class="font-semibold text-sm text-blue-800 mb-2">How to Pay via GCash</h4>
                        <ol class="text-sm text-blue-700 list-decimal pl-5 space-y-1">
                            <li>Open your GCash app.</li>
                            <li>Tap “Pay QR”.</li>
                            <li>Scan the QR code below.</li>
                            <li>Enter amount and proceed to pay.</li>
                            <li>Upload proof of payment below.</li>
                        </ol>
                    </div>

                    <div class="mt-3 text-center">
                        <img src="../PHP/assets/qr.jpg" alt="GCash QR Code" class="mx-auto w-48 h-48 border rounded-lg shadow-md">
                        <p class="text-xs text-gray-500 mt-2">Scan to Pay with GCash</p>
                    </div>

                    <label class="block text-sm font-medium text-gray-700">Upload Proof of Payment</label>
                    <input type="file" name="gcash_proof" accept="image/*" class="w-full border rounded-lg px-3 py-2" />
                    <span class="text-red-600 text-sm"><?= $gcashProofMsg ?></span>
                </div>

                <!-- Card Fields -->
                <div id="card_fields" class="<?= $payment_method === 'card' ? '' : 'hidden' ?> space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Card Number</label>
                    <input type="text" name="card_number" value="<?= htmlspecialchars($card_number) ?>" class="w-full px-3 py-2 border rounded-lg" placeholder="1234 5678 9012 3456">
                    <span class="text-red-600 text-sm"><?= $card_numberMsg ?></span>

                    <div class="flex gap-2">
                        <input type="text" name="expiry" value="<?= htmlspecialchars($expiry) ?>" placeholder="MM/YY" class="w-1/2 px-3 py-2 border rounded-lg">
                        <input type="text" name="cvv" value="<?= htmlspecialchars($cvv) ?>" placeholder="CVV" class="w-1/2 px-3 py-2 border rounded-lg">
                    </div>
                    <span class="text-red-600 text-sm"><?= $expiryMsg ?></span><br>
                    <span class="text-red-600 text-sm"><?= $cvvMsg ?></span>
                </div>

                <p class="text-xs text-gray-500 mt-2">
                    🔒 All payments are secured with end-to-end encryption.
                </p>

                <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">
                    Pay ₱3,200
                </button>
            </form>
        </div>
    </div>
</main>

<?php include('elements/footer.php'); ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const methodSelect = document.getElementById('payment_method');
        const gcashFields = document.getElementById('gcash_fields');
        const cardFields = document.getElementById('card_fields');

        function toggleFields() {
            gcashFields.classList.add('hidden');
            cardFields.classList.add('hidden');

            if (methodSelect.value === 'gcash') {
                gcashFields.classList.remove('hidden');
            } else if (methodSelect.value === 'card') {
                cardFields.classList.remove('hidden');
            }
        }

        methodSelect.addEventListener('change', toggleFields);
        toggleFields(); // initial call
    });
</script>

</body>
</html>
