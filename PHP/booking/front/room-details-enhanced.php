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
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-blue-600">₱<?= number_format($room['room_price']) ?></span>
                            <span class="text-gray-500 text-lg">/ night</span>
                        </div>
                        <p class="text-gray-700 mb-6 leading-relaxed text-lg"><?= nl2br(htmlspecialchars($room['room_description'])) ?></p>
                    </div>
                    
                    <!-- Booking Form -->
                    <form action="/Proj/PHP/booking/front/booking-confirmation.php" method="GET" class="space-y-4">
                        <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['room_id']) ?>">
                        
                        <div>
                            <label for="check_in" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-check mr-2 text-blue-500"></i>Check-in Date
                            </label>
                            <input type="date" id="check_in" name="check_in" required
                                min="<?= date('Y-m-d') ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                value="<?= htmlspecialchars($check_in) ?>">
                        </div>
                        
                        <div>
                            <label for="check_out" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-times mr-2 text-blue-500"></i>Check-out Date
                            </label>
                            <input type="date" id="check_out" name="check_out" required
                                min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                value="<?= htmlspecialchars($check_out) ?>">
                        </div>
                        
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-400 hover:from-blue-700 hover:to-blue-600 text-white py-4 rounded-xl font-semibold text-xl shadow-lg transition">
                            <i class="fas fa-calendar-check mr-2"></i>Book Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Lightbox Modal -->
    <div id="lightboxModal" class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center">
        <span class="absolute top-8 right-8 text-white text-3xl cursor-pointer" onclick="closeLightbox()">&times;</span>
        <img id="lightboxImg" src="" class="max-w-3xl max-h-[80vh] rounded-2xl shadow-2xl border-4 border-white" alt="Enlarged Room Image">
        <div id="lightboxTitle" class="absolute bottom-8 left-8 text-white text-lg font-semibold bg-black/50 px-4 py-2 rounded-lg"></div>
    </div>
    
    <script>
        // Global variables
        const roomImages = <?= json_encode($room_images) ?>;
        let currentImageIndex = 0;
        
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
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                previousImage();
            } else if (e.key === 'ArrowRight') {
                nextImage();
            } else if (e.key === 'Escape') {
                closeLightbox();
            }
        });
        
        // Initialize gallery navigation
        document.addEventListener('DOMContentLoaded', function() {
            updateNavigationButtons();
        });
    </script>
    
    <?php include('../../elements/footer.php'); ?>
</body>
</html> 