<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            max-width: 100%;
            overflow-x: hidden;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include '../../elements/navbar.php'; ?>

    <!-- Hero Section -->
    <div class="pt-32 pb-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Contact Us</h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Have questions or need assistance? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                </p>
            </div>
        </div>
    </div>

    <!-- Contact Form Section -->
    <div class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid md:grid-cols-2 gap-16">
                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-6">Get in Touch</h2>
                            <p class="text-gray-600 mb-8">
                                We're here to help and answer any questions you might have. We look forward to hearing from you.
                            </p>
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Address</h3>
                                    <p class="text-gray-600">Pili Beach Resort<br>Barangay, Sitio Ilaya, Santa Fe, 5508 Romblon<br>Philippines</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-phone text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Phone</h3>
                                    <p class="text-gray-600">0939-990-4522</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-envelope text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Email</h3>
                                    <p class="text-gray-600">pili.beach.resort.agmanic@gmail.com</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Business Hours</h3>
                                    <p class="text-gray-600">Monday - Sunday<br>8:00 AM - 10:00 PM</p>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Links -->
                        <div class="pt-6">
                            <h3 class="font-semibold text-gray-800 mb-4">Follow Us</h3>
                            <div class="flex space-x-4">
                                <a href="https://www.facebook.com/pilibeach.agmanic.resort" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white hover:bg-blue-700 transition duration-300">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://pilibeach.com" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center text-white hover:bg-indigo-700 transition duration-300">
                                    <i class="fas fa-globe"></i>
                                </a>
                                <a href="https://www.facebook.com/pilibeach.agmanic.resort" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-pink-600 rounded-lg flex items-center justify-center text-white hover:bg-pink-700 transition duration-300">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-white rounded-2xl shadow-xl p-10">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Send us a Message</h2>
                        
                        <!-- Formspree Integration Notice -->
                        <div class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Formspree Integration:</strong> This form uses Formspree for reliable email delivery to luisgabrielronatay@gmail.com
                        </div>

                        <!-- IMPORTANT: Replace YOUR_FORMSPREE_ID with your actual Formspree form ID -->
                        <form action="https://formspree.io/f/YOUR_FORMSPREE_ID" method="POST" class="space-y-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300"
                                        placeholder="Enter your first name">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                                    <input type="text" id="last_name" name="last_name" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300"
                                        placeholder="Enter your last name">
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300"
                                    placeholder="Enter your email address">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" id="phone" name="phone"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300"
                                    placeholder="Enter your phone number">
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                                <select id="subject" name="subject" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300">
                                    <option value="">Select a subject</option>
                                    <option value="General Inquiry">General Inquiry</option>
                                    <option value="Booking Question">Booking Question</option>
                                    <option value="Room Information">Room Information</option>
                                    <option value="Pricing Inquiry">Pricing Inquiry</option>
                                    <option value="Special Request">Special Request</option>
                                    <option value="Complaint">Complaint</option>
                                    <option value="Feedback">Feedback</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                                <textarea id="message" name="message" rows="6" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 resize-none"
                                    placeholder="Tell us how we can help you..."></textarea>
                            </div>

                            <div class="flex items-start space-x-3">
                                <input type="checkbox" id="privacy" name="privacy" required
                                    class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="privacy" class="text-sm text-gray-600">
                                    I agree to the <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a> and consent to being contacted regarding my inquiry.
                                </label>
                            </div>

                            <button type="submit"
                                class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Send Message
                            </button>
                        </form>

                        <!-- Setup Instructions -->
                        <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <h3 class="font-semibold text-yellow-800 mb-2">Setup Instructions:</h3>
                            <ol class="text-sm text-yellow-700 space-y-1">
                                <li>1. Go to <a href="https://formspree.io" target="_blank" class="underline">Formspree.io</a> and create a free account</li>
                                <li>2. Create a new form</li>
                                <li>3. Copy your form ID (looks like: xrgjqjqr)</li>
                                <li>4. Replace "YOUR_FORMSPREE_ID" in the form action above</li>
                                <li>5. Set your email (luisgabrielronatay@gmail.com) as the recipient</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../elements/footer.php'; ?>

    <script>
        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const messageTextarea = document.getElementById('message');
            
            // Auto-resize textarea
            messageTextarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });

            // Form submission enhancement
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
                submitBtn.disabled = true;
                
                // Re-enable after 5 seconds if no response
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });
        });
    </script>
</body>
</html> 