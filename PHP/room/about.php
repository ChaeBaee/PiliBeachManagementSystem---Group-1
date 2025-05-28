<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Inter', sans-serif;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect width="1" height="1" fill="rgba(255,255,255,0.05)"/></svg>');
            opacity: 0.1;
        }

        .hover-scale {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-scale:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .premium-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .gold-accent {
            color: #D4AF37;
        }

        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
            margin: 4rem 0;
        }

        .team-card {
            position: relative;
            overflow: hidden;
        }

        .team-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .team-card:hover::after {
            opacity: 1;
        }

        .team-info {
            position: relative;
            z-index: 1;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: white;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="h-16"></div>
    <?php include('../elements/navbar.php'); ?>

    <!-- Hero Section -->
    <div class="hero-gradient text-white py-32">
        <div class="container mx-auto px-4 sm:px-8 lg:px-16">
            <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
                <h1 class="text-5xl md:text-6xl font-bold mb-8 leading-tight">Welcome to Pili Resort</h1>
                <p class="text-2xl text-blue-100 leading-relaxed font-light">
                    Where luxury meets nature, creating unforgettable moments in paradise
                </p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-8 lg:px-16 py-20">
        <div class="max-w-6xl mx-auto mb-32" data-aos="fade-up">
            <h2 class="text-5xl font-bold text-blue-900 mb-16 text-center">Our Story</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="premium-card rounded-2xl shadow-xl p-10 hover-scale">
                    <div class="space-y-8">
                        <div class="flex items-center space-x-6 mb-8">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-3xl font-semibold text-blue-900">Our Vision</h3>
                        </div>
                        <p class="text-xl text-gray-700 leading-relaxed">
                            Welcome to <span class="font-semibold text-blue-700">Pili Resort</span>, where relaxation
                            meets nature. Nestled in a scenic location, our resort offers comfort, serenity, and warm
                            local hospitality.
                        </p>
                    </div>
                </div>

                <div class="premium-card rounded-2xl shadow-xl p-10 hover-scale">
                    <div class="space-y-8">
                        <div class="flex items-center space-x-6 mb-8">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                            </div>
                            <h3 class="text-3xl font-semibold text-blue-900">Our Location</h3>
                        </div>
                        <p class="text-xl text-gray-700 leading-relaxed">
                            Pili Beach Resort, nestled on the serene shores of Tablas Island, Romblon, offers a tranquil
                            escape for discerning travelers seeking refuge from the bustling crowds of Boracay.
                        </p>
                    </div>
                </div>

                <div class="premium-card rounded-2xl shadow-xl p-10 hover-scale lg:col-span-2">
                    <div class="space-y-8">
                        <div class="flex items-center space-x-6 mb-8">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                            <h3 class="text-3xl font-semibold text-blue-900">Our Experience</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-8 shadow-lg">
                                <h4 class="text-2xl font-semibold text-blue-900 mb-4">Luxury Accommodations</h4>
                                <p class="text-gray-700 text-lg">Experience the perfect blend of comfort and elegance in
                                    our
                                    carefully designed rooms and suites.</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-8 shadow-lg">
                                <h4 class="text-2xl font-semibold text-blue-900 mb-4">Natural Beauty</h4>
                                <p class="text-gray-700 text-lg">Immerse yourself in the pristine white sands and
                                    crystalline
                                    waters of our private beach.</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-8 shadow-lg">
                                <h4 class="text-2xl font-semibold text-blue-900 mb-4">Local Hospitality</h4>
                                <p class="text-gray-700 text-lg">Enjoy warm Filipino hospitality and personalized
                                    service
                                    throughout your stay.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <div class="mb-32" data-aos="fade-up">
            <h3 class="text-4xl font-bold text-blue-900 mb-16 text-center">Meet Our Team</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10">
                <div class="team-card bg-white rounded-xl shadow-xl overflow-hidden hover-scale">
                    <img src="../assets/5.png" alt="James" class="w-full h-96 object-cover">
                    <div class="p-8 team-info">
                        <h4 class="text-2xl font-semibold mb-2">James Andrew Ocuaman</h4>
                        <p class="text-blue-200">Lead Developer</p>
                    </div>
                </div>
                <div class="team-card bg-white rounded-xl shadow-xl overflow-hidden hover-scale">
                    <img src="../assets/Mayor.png" alt="Luis" class="w-full h-96 object-cover">
                    <div class="p-8 team-info">
                        <h4 class="text-2xl font-semibold mb-2">Luis Gabriel Ronatay</h4>
                        <p class="text-blue-200">Senior Developer</p>
                    </div>
                </div>
                <div class="team-card bg-white rounded-xl shadow-xl overflow-hidden hover-scale">
                    <img src="../assets/2.png" alt="Paul Vincent" class="w-full h-96 object-cover">
                    <div class="p-8 team-info">
                        <h4 class="text-2xl font-semibold mb-2">Paul Vincent Lunaspe</h4>
                        <p class="text-blue-200">Full Stack Developer</p>
                    </div>
                </div>
                <div class="team-card bg-white rounded-xl shadow-xl overflow-hidden hover-scale">
                    <img src="../assets/3.png" alt="Paul Castor" class="w-full h-96 object-cover">
                    <div class="p-8 team-info">
                        <h4 class="text-2xl font-semibold mb-2">Paul Castor Janobas</h4>
                        <p class="text-blue-200">Backend Developer</p>
                    </div>
                </div>
                <div class="team-card bg-white rounded-xl shadow-xl overflow-hidden hover-scale">
                    <img src="../assets/4.png" alt="John Wesly" class="w-full h-96 object-cover">
                    <div class="p-8 team-info">
                        <h4 class="text-2xl font-semibold mb-2">John Wesly Caburog</h4>
                        <p class="text-blue-200">Frontend Developer</p>
                    </div>
                </div>
                <div class="team-card bg-white rounded-xl shadow-xl overflow-hidden hover-scale">
                    <img src="../assets/logo.png" alt="Logo" class="w-full h-96 object-cover">
                    <div class="p-8 team-info">
                        <h4 class="text-2xl font-semibold mb-2">Pili Beach Resort Agmanic</h4>
                        <p class="text-blue-200">Your Paradise Destination</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <div class="max-w-4xl mx-auto" data-aos="fade-up">
            <h3 class="text-4xl font-bold text-blue-900 mb-12 text-center">Find Us</h3>
            <div class="premium-card rounded-2xl shadow-xl p-10 mb-12">
                <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                    We are located at Sitio Ilaya, Brgy. Agmanic, Santa Fe, Romblon, Philippines, 5508 — easily
                    accessible and surrounded by breathtaking nature.
                </p>
                <div class="w-full h-[500px] rounded-xl overflow-hidden shadow-xl">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3900.98027165368!2d122.00487937482626!3d12.113502288128512!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a51f6855555669%3A0x90089acb04c38f1c!2sPili%20Beach%20Resort%20Agmanic!5e0!3m2!1sen!2sph!4v1747390893802!5m2!1sen!2sph"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    <?php include('../elements/footer.php'); ?>

    <script>
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
    </script>
</body>

</html>