<?php
session_start();

$payment_method = $card_number = $expiry = $cvv = "";
$card_numberMsg = $expiryMsg = $cvvMsg = $paymentMethodMsg = $gcashProofMsg = "";

// Redirect if no user or booking info
if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking'])) {
    header("Location: PHP/login/front/login.php");
    exit();
}

$booking = $_SESSION['booking'];

// Validate and process form submission
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
            if (!isset($_FILES['gcash_proof']) || $_FILES['gcash_proof']['error'] === UPLOAD_ERR_NO_FILE) {
                $gcashProofMsg = "Please upload proof of payment.";
            } else {
                $allowedTypes = ['jpg', 'jpeg', 'png'];
                $fileName = $_FILES['gcash_proof']['name'];
                $fileTmp = $_FILES['gcash_proof']['tmp_name'];
                $fileSize = $_FILES['gcash_proof']['size'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($fileExt, $allowedTypes)) {
                    $gcashProofMsg = "Only JPG, JPEG, PNG files allowed.";
                } elseif ($fileSize > 5 * 1024 * 1024) {
                    $gcashProofMsg = "File size must be less than 5MB.";
                }
            }
        }
    }

    // If no errors, process
    if (
        $paymentMethodMsg === "" &&
        $card_numberMsg === "" &&
        $expiryMsg === "" &&
        $cvvMsg === "" &&
        $gcashProofMsg === ""
    ) {
        if ($payment_method === "gcash") {
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0755, true);

            $uniqueName = uniqid() . "-" . basename($_FILES['gcash_proof']['name']);
            $targetFile = $uploadDir . $uniqueName;

            if (move_uploaded_file($_FILES['gcash_proof']['tmp_name'], $targetFile)) {
                $_SESSION['booking']['gcash_proof'] = $targetFile;
            } else {
                $gcashProofMsg = "Failed to upload image.";
            }
        }

        if ($gcashProofMsg === "") {
            $_SESSION['booking']['payment_method'] = $payment_method;
            $_SESSION['booking']['card_number'] = $card_number;

            header('Location: /Proj/PHP/Invoice/Front/invoice.php');
            exit();
        }
    }
}

// Default to 0 if not set
$total = $_SESSION['booking']['total_amount'] ?? 0.00;
?>