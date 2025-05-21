<?php
include_once 'elements/navbar.php';

$gcash = $card_number = $expiry = $cvv = $payment_method = "";
$gcashMsg = $card_numberMsg = $expiryMsg = $cvvMsg = $paymentMethodMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST["payment_method"] ?? "";

    if (empty($payment_method)) {
        $paymentMethodMsg = "Please choose a payment method.";
    } else {
        if ($payment_method === "gcash") {
            $gcash = $_POST["gcash_ref"] ?? "";
            if (empty($gcash)) {
                $gcashMsg = "Please input GCash reference number.";
            }
        } elseif ($payment_method === "card") {
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
        }
    }

    if (
        $paymentMethodMsg === "" &&
        $gcashMsg === "" &&
        $card_numberMsg === "" &&
        $expiryMsg === "" &&
        $cvvMsg === ""
    ) {
        $_SESSION['booking']['payment_method'] = $payment_method;
        $_SESSION['booking']['gcash'] = $gcash;
        $_SESSION['booking']['card_number'] = $card_number;
        header('Location: invoice.php');
        exit();
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
            <form method="POST" action="" class="space-y-4">
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
                <div id="gcash_fields" class="<?= $payment_method === 'gcash' ? '' : 'hidden' ?>">
                    <label class="block text-sm font-medium text-gray-700 mb-1">GCash Reference Number</label>
                    <input type="text" name="gcash_ref" value="<?= htmlspecialchars($gcash) ?>" class="w-full px-3 py-2 border rounded-lg" placeholder="e.g., 1234XXXX5678">
                    <span class="text-red-600 text-sm"><?= $gcashMsg ?></span>
                </div>

                <!-- Card Fields -->
                <div id="card_fields" class="<?= $payment_method === 'card' ? '' : 'hidden' ?> space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
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
    const methodSelect = document.getElementById('payment_method');
    const gcashFields = document.getElementById('gcash_fields');
    const cardFields = document.getElementById('card_fields');

    methodSelect.addEventListener('change', function () {
        gcashFields.classList.add('hidden');
        cardFields.classList.add('hidden');

        if (this.value === 'gcash') {
            gcashFields.classList.remove('hidden');
        } else if (this.value === 'card') {
            cardFields.classList.remove('hidden');
        }
    });
</script>

</body>
</html>
