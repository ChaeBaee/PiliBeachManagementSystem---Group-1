<!-- Create a responsive resort booking form with Tailwind CSS. The form should have fields for full name, email, check-in date, check-out date, number of guests, and a submit button. -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort Booking Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
        }
        .form-container {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-header {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 1rem;
            border-radius: 0.5rem 0.5rem 0 0;
        }
        .form-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .form-header p {
            font-size: 0.875rem;
            font-weight: 400;
        }
        .form-body {
            padding: 2rem;
        }
        .form-body input, .form-body select {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            padding: 0.5rem;
            width: 100%;
            margin-bottom: 1rem;
        }
        .form-body input:focus, .form-body select:focus {
            border-color: #4f46e5;
            outline: none;
        }
        .form-body button {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        .form-body button:hover {
            background-color: #4338ca;
        }
        @media (max-width: 640px) {
            .form-container {
                padding: 1rem;
            }
            .form-body {
                padding: 1rem;
            }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">
    <div class="form-container w-full max-w-md mx-auto mt-10">
        <div class="form-header text-center">
            <h2>Book Your Stay</h2>
            <p>Fill in the details below to reserve your spot.</p>
        </div>
        <div class="form-body">
            <form action="#" method="POST">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="date" name="check_in_date" required>
                <input type="date" name="check_out_date" required>
                <select name="number_of_guests" required>
                    <option value="" disabled selected>Select Number of Guests</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
                <button type="submit">Submit Booking</button>
            </form>
        </div>
    </div>
</body>
</html>