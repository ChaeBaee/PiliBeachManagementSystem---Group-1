<?php
session_start();
require_once '../../db.php';

$room_id = $_GET['room_id'] ?? null;
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/front/login.php");
    exit();
}

if (!$room_id) {
    echo '<div class="text-center text-red-500 mt-10">Room not specified.</div>';
    exit();
}

$stmt = $conn->prepare('SELECT * FROM rooms WHERE room_id = ?');
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo '<div class="text-center text-red-500 mt-10">Room not found.</div>';
    exit();
}

// Fetch multiple images for this room
try {
    $stmt = $conn->prepare("
        SELECT image_id, image_filename, image_title, image_description, is_primary, display_order
        FROM room_images 
        WHERE room_id = ? 
        ORDER BY is_primary DESC, display_order ASC, image_id ASC
    ");
    $stmt->execute([$room_id]);
    $room_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If no images found in room_images table, fall back to the main room image
    if (empty($room_images)) {
        $room_images = [[
            'image_id' => 0,
            'image_filename' => $room['room_image'],
            'image_title' => 'Room View',
            'image_description' => null,
            'is_primary' => 1,
            'display_order' => 1
        ]];
    }
} catch (PDOException $e) {
    // Fallback to single image if there's an error
    $room_images = [[
        'image_id' => 0,
        'image_filename' => $room['room_image'],
        'image_title' => 'Room View',
        'image_description' => null,
        'is_primary' => 1,
        'display_order' => 1
    ]];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Room Details - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gallery-img { transition: transform 0.3s; cursor: pointer; }
        .gallery-img:hover { transform: scale(1.05); }
        .main-gallery-img { transition: box-shadow 0.3s; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18); border: 3px solid #e0e7ff; }
        .glass-card { background: rgba(255,255,255,0.7); backdrop-filter: blur(16px); }
        .gallery-scroll { display: flex; overflow-x: auto; gap: 0.75rem; padding-bottom: 0.5rem; }
        .gallery-scroll::-webkit-scrollbar { height: 6px; background: #e0e7ff; border-radius: 8px; }
        .gallery-scroll::-webkit-scrollbar-thumb { background: #a5b4fc; border-radius: 8px; }
        
        /* Custom styles for date inputs */
        .date-input:disabled {
            background-color: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }
        
        .availability-status {
            transition: all 0.3s ease;
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        /* Enhanced gallery styles */
        .gallery-thumbnail {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .gallery-thumbnail:hover {
            border-color: #3b82f6;
            transform: scale(1.05);
        }
        
        .gallery-thumbnail.active {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .image-counter {
            position: absolute;
            bottom: 1rem;
            right: 1rem;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .gallery-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .gallery-nav-btn:hover {
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .gallery-nav-btn.prev {
            left: 1rem;
        }
        
        .gallery-nav-btn.next {
            right: 1rem;
        }
        
        .gallery-nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen flex flex-col">
    <?php include('../../elements/navbar.php'); ?>
    
    <!-- Availability Alert Modal -->
    <div id="availabilityModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center mb-4">
                <div id="modalIcon" class="w-8 h-8 rounded-full flex items-center justify-center mr-3">
                    <i id="modalIconClass" class="text-white"></i>
                </div>
                <h3 id="modalTitle" class="text-lg font-semibold"></h3>
            </div>
            <p id="modalMessage" class="text-gray-600 mb-6"></p>
            <div class="flex justify-end">
                <button onclick="closeAvailabilityModal()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    OK
                </button>
            </div>
        </div>
    </div>
    
    <!-- Hero Section with Blurred Background -->
    <div class="relative w-full h-[340px] md:h-[420px] flex items-center justify-center overflow-hidden">
        <img src="../../assets/<?= htmlspecialchars($room_images[0]['image_filename']) ?>" alt="Room Hero" class="absolute inset-0 w-full h-full object-cover object-center scale-110 blur-md brightness-75" />
        <div class="relative z-10 text-center">
            <span class="inline-block bg-gradient-to-r from-blue-600 to-blue-400 text-white text-xs md:text-sm font-semibold px-4 py-1 rounded-full shadow-lg mb-4 uppercase tracking-widest opacity-90">
                <?= htmlspecialchars($room['room_type']) ?>
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white drop-shadow-lg mb-2">
                Room <?= htmlspecialchars($room['room_number']) ?>
            </h1>
            <p class="text-lg md:text-xl text-blue-100 font-medium drop-shadow mb-2">
                Up to <?= htmlspecialchars($room['room_capacity']) ?> guests &bull; ₱<?= number_format($room['room_price']) ?>/night
            </p>
        </div>
    </div>
    
    <!-- Main Content Card -->
    <div class="container mx-auto px-4 -mt-24 mb-12 flex-1">
        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Gallery & Amenities -->
            <div class="lg:w-7/12 w-full">
                <div class="glass-card rounded-3xl shadow-2xl p-6 mb-8 border border-blue-100">
                    <div class="mb-4 relative">
                        <img src="../../assets/<?= htmlspecialchars($room_images[0]['image_filename']) ?>" 
                             alt="<?= htmlspecialchars($room_images[0]['image_title']) ?>" 
                             class="main-gallery-img w-full h-80 md:h-96 object-cover rounded-2xl shadow-md border border-gray-200 cursor-pointer" 
                             id="mainImage" 
                             onclick="openLightbox(this.src, '<?= htmlspecialchars($room_images[0]['image_title']) ?>')">
                        
                        <!-- Navigation buttons -->
                        <button class="gallery-nav-btn prev" id="prevBtn" onclick="previousImage()" style="display: none;">
                            <i class="fas fa-chevron-left text-gray-700"></i>
                        </button>
                        <button class="gallery-nav-btn next" id="nextBtn" onclick="nextImage()" style="display: none;">
                            <i class="fas fa-chevron-right text-gray-700"></i>
                        </button>
                        
                        <!-- Image counter -->
                        <div class="image-counter" id="imageCounter">
                            1 of <?= count($room_images) ?>
                        </div>
                        
                        <span class="absolute top-4 right-4 bg-white/80 text-blue-700 text-xs font-bold px-3 py-1 rounded-full shadow">
                            <?= count($room_images) ?> Photos
                        </span>
                    </div>
                    
                    <!-- Thumbnail gallery -->
                    <div class="gallery-scroll" id="thumbnailGallery">
                        <?php foreach ($room_images as $index => $img): ?>
                            <div class="gallery-thumbnail-container flex-shrink-0">
                                <img src="../../assets/<?= htmlspecialchars($img['image_filename']) ?>" 
                                     alt="<?= htmlspecialchars($img['image_title']) ?>" 
                                     class="gallery-thumbnail w-16 h-16 object-cover rounded-lg cursor-pointer <?= $index === 0 ? 'active' : '' ?>"
                                     data-index="<?= $index ?>"
                                     data-filename="<?= htmlspecialchars($img['image_filename']) ?>"
                                     data-title="<?= htmlspecialchars($img['image_title']) ?>"
                                     onclick="changeMainImage(<?= $index ?>)">
                                <div class="text-xs text-gray-600 mt-1 text-center truncate w-16">
                                    <?= htmlspecialchars($img['image_title']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Amenities Section -->
                <div class="glass-card rounded-3xl shadow-xl p-6 mb-8 border border-blue-100">
                    <h3 class="text-xl font-semibold mb-4 text-blue-700 flex items-center"><i class="fas fa-concierge-bell mr-2"></i> Room Amenities</h3>
                    <div class="flex flex-wrap gap-3">
                        <span class="inline-flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-full text-sm font-medium shadow-sm"><i class="fas fa-wifi mr-2"></i>Free WiFi</span>
                        <span class="inline-flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-full text-sm font-medium shadow-sm"><i class="fas fa-tv mr-2"></i>Flat-screen TV</span>
                        <span class="inline-flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-full text-sm font-medium shadow-sm"><i class="fas fa-snowflake mr-2"></i>Air Conditioning</span>
                        <span class="inline-flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-full text-sm font-medium shadow-sm"><i class="fas fa-bath mr-2"></i>Private Bathroom</span>
                        <span class="inline-flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-full text-sm font-medium shadow-sm"><i class="fas fa-mug-hot mr-2"></i>Complimentary Coffee</span>
                        <span class="inline-flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-full text-sm font-medium shadow-sm"><i class="fas fa-umbrella-beach mr-2"></i>Beach Access</span>
                    </div>
                </div>
                
                <!-- Guest Reviews Section (dynamic) -->
                <div class="glass-card rounded-3xl shadow-xl p-6 border border-blue-100 mt-8">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-comments text-blue-400 text-2xl"></i>
                        <span class="text-blue-700 font-bold text-lg">What Guests Are Saying</span>
                    </div>
                    <hr class="mb-4 border-blue-100">
                    <h3 class="text-xl font-semibold mb-4 text-blue-700 flex items-center"><i class="fas fa-star mr-2 text-yellow-400"></i> Guest Reviews</h3>
                    <div class="space-y-4">
                        <?php
                        try {
                            $stmt = $conn->prepare("
                                SELECT f.feedback_rating, f.feedback_comments, f.feedback_date_created, u.user_name
                                FROM feedback f
                                JOIN users u ON f.user_id = u.user_id
                                WHERE f.room_id = ? AND f.feedback_comments != ''
                                ORDER BY f.feedback_date_created DESC
                                LIMIT 6
                            ");
                            $stmt->execute([$room_id]);
                            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            $reviews = [];
                        }
                        if (count($reviews) > 0):
                            foreach ($reviews as $review):
                                $initial = strtoupper(substr($review['user_name'], 0, 1));
                        ?>
                        <div class="bg-white/80 rounded-xl p-4 shadow flex gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-lg border">
                                <?= htmlspecialchars($initial) ?>
                            </div>
                            <div>
                                <div class="font-semibold text-blue-800"><?= htmlspecialchars($review['user_name']) ?></div>
                                <div class="text-yellow-400 text-sm mb-1">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $review['feedback_rating'] ? '<i class=\'fas fa-star\'></i>' : '<i class=\'fas fa-star\' style=\'color:#e5e7eb\'></i>';
                                    }
                                    ?>
                                    <span class="text-gray-500 ml-2 text-xs"><?= date('M d, Y', strtotime($review['feedback_date_created'])) ?></span>
                                </div>
                                <div class="text-gray-700 text-sm"><?= htmlspecialchars($review['feedback_comments']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                            <p class="text-lg">No reviews yet. Be the first to share your experience!</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Why Book With Us Section -->
                <div class="glass-card rounded-3xl shadow-xl p-6 border border-blue-100 mt-8">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-umbrella-beach text-yellow-400 text-2xl"></i>
                        <span class="text-blue-700 font-bold text-lg">Why Book With Us?</span>
                    </div>
                    <hr class="mb-4 border-blue-100">
                    <h3 class="text-xl font-semibold mb-4 text-blue-700 flex items-center"><i class="fas fa-heart mr-2 text-red-400"></i> Exclusive Benefits</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white/80 rounded-xl p-4 shadow flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-semibold text-lg border">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-blue-800">Best Price Guarantee</div>
                                <div class="text-gray-700 text-sm">We guarantee the best rates for your stay with us</div>
                            </div>
                        </div>
                        <div class="bg-white/80 rounded-xl p-4 shadow flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-lg border">
                                <i class="fas fa-water"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-blue-800">Beachfront Location</div>
                                <div class="text-gray-700 text-sm">Direct access to pristine beaches and ocean views</div>
                            </div>
                        </div>
                        <div class="bg-white/80 rounded-xl p-4 shadow flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-semibold text-lg border">
                                <i class="fas fa-glass-martini"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-blue-800">Free Welcome Drink</div>
                                <div class="text-gray-700 text-sm">Complimentary tropical drink upon arrival</div>
                            </div>
                        </div>
                        <div class="bg-white/80 rounded-xl p-4 shadow flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-semibold text-lg border">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-blue-800">24/7 Customer Support</div>
                                <div class="text-gray-700 text-sm">Round-the-clock assistance for all your needs</div>
                            </div>
                        </div>
                        <div class="bg-white/80 rounded-xl p-4 shadow flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-semibold text-lg border">
                                <i class="fas fa-undo"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-blue-800">Flexible Cancellation</div>
                                <div class="text-gray-700 text-sm">Easy cancellation policy for peace of mind</div>
                            </div>
                        </div>
                        <div class="bg-white/80 rounded-xl p-4 shadow flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 font-semibold text-lg border">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-blue-800">Secure Booking</div>
                                <div class="text-gray-700 text-sm">Safe and secure payment processing</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Details & Booking Sidebar -->
            <div class="lg:w-5/12 w-full">
                <div class="glass-card rounded-3xl shadow-2xl p-8 sticky top-32 flex flex-col gap-6 border border-blue-100">
                    <div>
                        <h2 class="text-3xl font-extrabold mb-2 text-blue-800 leading-tight flex items-center gap-2">
                            <i class="fas fa-bed text-blue-400"></i> <?= htmlspecialchars($room['room_type']) ?>
                        </h2>
                        <div class="flex items-center text-gray-600 mb-2">
                            <i class="fas fa-door-open text-blue-500 mr-2"></i>
                            <span class="font-medium">Room <?= htmlspecialchars($room['room_number']) ?></span>
                        </div>
                        <div class="flex items-center text-gray-600 mb-2">
                            <i class="fas fa-users text-blue-500 mr-2"></i>
                            <span>Up to <?= htmlspecialchars($room['room_capacity']) ?> guests</span>
                        </div>
                        <div class="flex items-center text-gray-600 mb-4">
                            <i class="fas fa-ruler-combined text-blue-500 mr-2"></i>
                            <span>Spacious & comfortable</span>
                        </div>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-blue-600">₱<?= number_format($room['room_price']) ?></span>
                            <span class="text-gray-500 text-lg">/ night</span>
                        </div>
                        <p class="text-gray-700 mb-6 leading-relaxed text-lg"><?= nl2br(htmlspecialchars($room['room_description'])) ?></p>
                    </div>
                    
                    <!-- Availability Status -->
                    <div id="availabilityStatus" class="availability-status bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
                        <div class="flex items-center">
                            <i id="statusIcon" class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <span id="statusText" class="text-blue-700 font-medium"></span>
                        </div>
                    </div>
                    
                    <!-- Booking Form -->
                    <form id="bookingForm" action="/Proj/PHP/booking/front/booking-confirmation.php" method="GET" class="space-y-4">
                        <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['room_id']) ?>">
                        
                        <div>
                            <label for="check_in" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-check mr-2 text-blue-500"></i>Check-in Date
                            </label>
                            <input type="date" id="check_in" name="check_in" required
                                min="<?= date('Y-m-d') ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition date-input"
                                value="<?= htmlspecialchars($check_in) ?>">
                        </div>
                        
                        <div>
                            <label for="checkin_time" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clock mr-2 text-blue-500"></i>Check-in Time (PH Time)
                            </label>
                            <select id="checkin_time" name="checkin_time" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="14:00">2:00 PM</option>
                                <option value="14:30">2:30 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="15:30">3:30 PM</option>
                                <option value="16:00">4:00 PM</option>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Standard check-in time is between 2:00 PM - 4:00 PM</p>
                        </div>
                        
                        <div>
                            <label for="check_out" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-times mr-2 text-blue-500"></i>Check-out Date
                            </label>
                            <input type="date" id="check_out" name="check_out" required
                                min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition date-input"
                                value="<?= htmlspecialchars($check_out) ?>">
                            <p class="text-sm text-gray-500 mt-1">Standard check-out time is 12:00 PM</p>
                        </div>
                        
                        <div class="bg-blue-50 p-4 rounded-xl">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Estimated Total:</span>
                                <span class="text-lg font-bold text-blue-600" id="totalPrice">₱<?= number_format($room['room_price']) ?></span>
                            </div>
                        </div>
                        
                        <button type="submit" id="bookButton"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-400 hover:from-blue-700 hover:to-blue-600 text-white py-4 rounded-xl font-semibold text-xl shadow-lg transition">
                            <i class="fas fa-calendar-check mr-2"></i>Book Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Lightbox Modal (JS) -->
    <div id="lightboxModal" class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center">
        <span class="absolute top-8 right-8 text-white text-3xl cursor-pointer" onclick="closeLightbox()">&times;</span>
        <img id="lightboxImg" src="" class="max-w-3xl max-h-[80vh] rounded-2xl shadow-2xl border-4 border-white" alt="Enlarged Room Image">
        <div id="lightboxTitle" class="absolute bottom-8 left-8 text-white text-lg font-semibold bg-black/50 px-4 py-2 rounded-lg"></div>
    </div>
    
    <script>
        // Global variables
        const roomId = <?= $room['room_id'] ?>;
        const roomPrice = <?= $room['room_price'] ?>;
        const roomImages = <?= json_encode($room_images) ?>;
        let currentImageIndex = 0;
        let bookedDates = [];
        let isCheckingAvailability = false;
        
        // Gallery functions
        function changeMainImage(index) {
            if (index < 0 || index >= roomImages.length) return;
            
            currentImageIndex = index;
            const image = roomImages[index];
            const mainImage = document.getElementById('mainImage');
            const imageCounter = document.getElementById('imageCounter');
            
            mainImage.src = '../../assets/' + image.image_filename;
            mainImage.alt = image.image_title;
            imageCounter.textContent = (index + 1) + ' of ' + roomImages.length;
            
            // Update thumbnail active state
            document.querySelectorAll('.gallery-thumbnail').forEach((thumb, i) => {
                thumb.classList.toggle('active', i === index);
            });
            
            // Show/hide navigation buttons
            updateNavigationButtons();
        }
        
        function previousImage() {
            if (currentImageIndex > 0) {
                changeMainImage(currentImageIndex - 1);
            }
        }
        
        function nextImage() {
            if (currentImageIndex < roomImages.length - 1) {
                changeMainImage(currentImageIndex + 1);
            }
        }
        
        function updateNavigationButtons() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            prevBtn.style.display = currentImageIndex > 0 ? 'flex' : 'none';
            nextBtn.style.display = currentImageIndex < roomImages.length - 1 ? 'flex' : 'none';
        }
        
        // Lightbox functions
        function openLightbox(src, title) {
            document.getElementById('lightboxImg').src = src;
            document.getElementById('lightboxTitle').textContent = title || '';
            document.getElementById('lightboxModal').style.display = 'flex';
        }
        
        function closeLightbox() {
            document.getElementById('lightboxModal').style.display = 'none';
        }
        
        // Keyboard navigation for gallery
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                previousImage();
            } else if (e.key === 'ArrowRight') {
                nextImage();
            } else if (e.key === 'Escape') {
                closeLightbox();
            }
        });
        
        // Availability modal functions
        function showAvailabilityModal(title, message, isError = false) {
            const modal = document.getElementById('availabilityModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalIcon = document.getElementById('modalIcon');
            const modalIconClass = document.getElementById('modalIconClass');
            
            modalTitle.textContent = title;
            modalMessage.textContent = message;
            
            if (isError) {
                modalIcon.style.backgroundColor = '#ef4444';
                modalIconClass.className = 'fas fa-exclamation-triangle text-white';
            } else {
                modalIcon.style.backgroundColor = '#10b981';
                modalIconClass.className = 'fas fa-check text-white';
            }
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function closeAvailabilityModal() {
            document.getElementById('availabilityModal').classList.add('hidden');
            document.getElementById('availabilityModal').classList.remove('flex');
        }
        
        // Load booked dates for this room
        async function loadBookedDates() {
            try {
                const response = await fetch(`../back/check_availability.php?room_id=${roomId}`);
                const data = await response.json();
                
                if (data.booked_dates) {
                    bookedDates = data.booked_dates;
                    console.log('Booked dates loaded:', bookedDates);
                }
            } catch (error) {
                console.error('Error loading booked dates:', error);
            }
        }
        
        // Check availability for specific dates
        async function checkAvailability(checkIn, checkOut) {
            if (!checkIn || !checkOut) return;
            
            isCheckingAvailability = true;
            document.getElementById('bookingForm').classList.add('loading');
            
            try {
                const checkinTime = document.getElementById('checkin_time').value;
                const response = await fetch(`../back/check_availability.php?room_id=${roomId}&check_in=${checkIn}&check_out=${checkOut}&checkin_time=${checkinTime}`);
                const data = await response.json();
                
                const statusDiv = document.getElementById('availabilityStatus');
                const statusIcon = document.getElementById('statusIcon');
                const statusText = document.getElementById('statusText');
                const bookButton = document.getElementById('bookButton');
                
                if (data.available) {
                    statusDiv.className = 'availability-status bg-green-50 border border-green-200 rounded-lg p-4';
                    statusIcon.className = 'fas fa-check-circle text-green-500 mr-2';
                    statusText.textContent = data.message || 'Room is available for selected dates';
                    statusText.className = 'text-green-700 font-medium';
                    bookButton.disabled = false;
                    bookButton.className = 'w-full bg-gradient-to-r from-blue-600 to-blue-400 hover:from-blue-700 hover:to-blue-600 text-white py-4 rounded-xl font-semibold text-xl shadow-lg transition';
                } else {
                    statusDiv.className = 'availability-status bg-red-50 border border-red-200 rounded-lg p-4';
                    statusIcon.className = 'fas fa-times-circle text-red-500 mr-2';
                    statusText.textContent = data.message || 'Room is not available for selected dates';
                    statusText.className = 'text-red-700 font-medium';
                    bookButton.disabled = true;
                    bookButton.className = 'w-full bg-gray-400 text-white py-4 rounded-xl font-semibold text-xl shadow-lg cursor-not-allowed';
                }
                
                statusDiv.classList.remove('hidden');
                
            } catch (error) {
                console.error('Error checking availability:', error);
                showAvailabilityModal('Error', 'Unable to check room availability. Please try again.', true);
            } finally {
                isCheckingAvailability = false;
                document.getElementById('bookingForm').classList.remove('loading');
            }
        }
        
        // Disable booked dates in date inputs
        function disableBookedDates() {
            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');
            
            // Create a function to check if a date is booked
            function isDateBooked(dateStr) {
                return bookedDates.includes(dateStr);
            }
            
            // Add event listeners to prevent selecting booked dates
            checkInInput.addEventListener('input', function() {
                if (isDateBooked(this.value)) {
                    showAvailabilityModal('Date Not Available', 'This date is already booked. Please select a different date.', true);
                    this.value = '';
                    return;
                }
                
                // Update check-out minimum date
                const checkInDate = new Date(this.value);
                const nextDay = new Date(checkInDate);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOutInput.min = nextDay.toISOString().split('T')[0];
                
                // If check-out date is before or equal to check-in date, update it
                if (checkOutInput.value && new Date(checkOutInput.value) <= checkInDate) {
                    checkOutInput.value = nextDay.toISOString().split('T')[0];
                }
                
                calculateTotal();
                
                // Check availability if both dates are set
                if (checkOutInput.value) {
                    checkAvailability(this.value, checkOutInput.value);
                }
            });
            
            checkOutInput.addEventListener('input', function() {
                if (isDateBooked(this.value)) {
                    showAvailabilityModal('Date Not Available', 'This date is already booked. Please select a different date.', true);
                    this.value = '';
                    return;
                }
                
                const checkInDate = new Date(checkInInput.value);
                const checkOutDate = new Date(this.value);
                
                if (checkOutDate <= checkInDate) {
                    const nextDay = new Date(checkInDate);
                    nextDay.setDate(nextDay.getDate() + 1);
                    this.value = nextDay.toISOString().split('T')[0];
                }
                
                calculateTotal();
                
                // Check availability if both dates are set
                if (checkInInput.value) {
                    checkAvailability(checkInInput.value, this.value);
                }
            });
        }
        
        // Calculate total price
        function calculateTotal() {
            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');
            const totalPriceElement = document.getElementById('totalPrice');
            
            if (checkInInput.value && checkOutInput.value) {
                const checkIn = new Date(checkInInput.value);
                const checkOut = new Date(checkOutInput.value);
                const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                
                if (nights > 0) {
                    const total = nights * roomPrice;
                    totalPriceElement.textContent = '₱' + total.toLocaleString();
                } else {
                    totalPriceElement.textContent = '₱' + roomPrice.toLocaleString();
                }
            } else {
                totalPriceElement.textContent = '₱' + roomPrice.toLocaleString();
            }
        }
        
        // Form submission handler
        document.getElementById('bookingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;
            
            if (!checkIn || !checkOut) {
                showAvailabilityModal('Missing Information', 'Please select both check-in and check-out dates.', true);
                return;
            }
            
            // Final availability check before submission
            try {
                const checkinTime = document.getElementById('checkin_time').value;
                const response = await fetch(`../back/check_availability.php?room_id=${roomId}&check_in=${checkIn}&check_out=${checkOut}&checkin_time=${checkinTime}`);
                const data = await response.json();
                
                if (!data.available) {
                    showAvailabilityModal('Booking Unavailable', data.message || 'This room is no longer available for the selected dates. Please choose different dates.', true);
                    return;
                }
                
                // If available, proceed with booking
                this.submit();
                
            } catch (error) {
                console.error('Error during final availability check:', error);
                showAvailabilityModal('Error', 'Unable to verify room availability. Please try again.', true);
            }
        });
        
        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadBookedDates().then(() => {
                disableBookedDates();
                calculateTotal();
            });
            
            // Initialize gallery navigation
            updateNavigationButtons();
        });
    </script>
    
    <?php include('../../elements/footer.php'); ?>
</body>
</html> 