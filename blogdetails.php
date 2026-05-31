<?php
include "header.php"; // Assumed to contain opening <html>, <head>, and the main navigation
include "connection.php";

// --- PHP LOGIC: FETCH SINGLE BLOG POST ---

// 1. Get the blog ID from the URL and sanitize it
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: blogs.php");
    exit();
}
$blog_id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. Query to fetch the specific blog post
$query = "SELECT * FROM blog WHERE blog_id = '$blog_id'";
$result = mysqli_query($conn, $query);
$blog = mysqli_fetch_assoc($result);

// 3. Handle case where blog post is not found
if (!$blog) {
    header("Location: blogs.php");
    exit();
}

// Prepare blog data for display
$title = htmlspecialchars($blog['title']);
$content = $blog['content']; 
$image_src = "uploads/blogs/" . htmlspecialchars($blog['image']);
$created_date = date("F j, Y", strtotime($blog['created_at']));

// Placeholder data
$read_time = rand(4, 12); 
$category_name = "Spice Guides"; 

// --- DYNAMIC DATA FOR RELATED ARTICLES ---
$related_query = "SELECT blog_id, title, image, created_at FROM blog
                  WHERE blog_id != '$blog_id'
                  ORDER BY created_at DESC
                  LIMIT 3";
$related_result = mysqli_query($conn, $related_query);
?>

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#f97316',
                    secondary: '#dc2626',
                    // Brand colors
                    'facebook-blue': '#1877F2',
                    'whatsapp-green': '#25D366',
                    'twitter-dark': '#000000', 
                }
            }
        }
    }
</script>
<style>
    .hero-gradient {
        background: linear-gradient(135deg, #f97316 0%, #dc2626 50%, #b91c1c 100%);
    }
    .blog-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .blog-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .category-tag {
        background: linear-gradient(135deg, #f97316 0%, #dc2626 100%);
    }
    .author-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #f97316 0%, #dc2626 100%);
    }
    .read-time {
        background: rgba(249, 115, 22, 0.1);
        color: #f97316;
    }
    /* Content styling */
    .blog-content h2, .blog-content h3 {
        color: #1f2937;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
    }
    .blog-content p, .blog-content ul, .blog-content ol, .blog-content blockquote {
        color: #4b5563;
        line-height: 1.75;
        margin-bottom: 1.5rem;
    }
    /* Specific style for social icons */
    .social-icon-btn {
        padding: 0.75rem; /* Equivalent to p-3 */
        border-radius: 9999px; /* Equivalent to rounded-full */
        font-size: 1.25rem; /* Equivalent to text-xl */
        transition: opacity 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .social-icon-btn:hover {
        opacity: 0.9;
    }
</style>

<section>
    <div class="relative w-full h-96 lg:h-[600px] overflow-hidden">
        <img src="<?php echo $image_src; ?>" alt="<?php echo $title; ?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/40"></div>
        
        <div class="absolute inset-x-0 bottom-0 text-white p-6 md:p-12 lg:p-20 bg-gradient-to-t from-black/80 to-transparent">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center space-x-4 mb-4">
                    <span class="category-tag text-white px-3 py-1 rounded-full text-sm font-medium"><?php echo $category_name; ?></span>
                    <span class="read-time bg-white/20 text-white px-3 py-1 rounded-full text-sm font-medium"><?php echo $read_time; ?> min read</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-extrabold mb-4 leading-tight">
                    <?php echo $title; ?>
                </h1>
                <div class="flex items-center space-x-4 text-white/90">
                    <div class="flex items-center space-x-3">
                        <div class="author-avatar rounded-full flex items-center justify-center text-white font-medium text-sm">AD</div>
                        <span class="font-medium">Admin</span>
                    </div>
                    <span>&middot;</span>
                    <span class="text-sm"><?php echo $created_date; ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<main class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <article class="blog-content text-lg text-gray-700 leading-relaxed">
            <?php echo $content; ?>
        </article>

        <div class="mt-16 pt-8 border-t border-gray-200">
            <div class="flex items-center space-x-5 p-6 bg-gray-50 rounded-xl shadow-inner">
                <div class="author-avatar w-16 h-16 flex-shrink-0 rounded-full flex items-center justify-center text-xl text-white font-bold">AD</div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900">Written by Admin</h4>
                    <p class="text-sm text-gray-600">
                        The culinary experts at Aroma Hub share their knowledge on spices, recipes, and global cuisine traditions.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-gray-200 flex flex-col md:flex-row items-center justify-between">
            
            <a href="blogs.php" class="text-primary hover:text-secondary font-semibold flex items-center transition-colors mb-6 md:mb-0 order-2 md:order-1">
                <i class="fas fa-arrow-left mr-2"></i> Back to All Articles
            </a>
            
        <div class="flex items-center space-x-4 order-1 md:order-2">
    <span class="text-gray-700 font-bold text-lg">Share This Story:</span>
    
    <!-- Facebook -->
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://yourwebsite.com/blogdetails.php?id=' . $blog_id); ?>" 
       target="_blank" 
       title="Share on Facebook" 
       class="text-white bg-[#1877F2] hover:bg-[#145dbf] social-icon-btn rounded-full p-3 transition">
        <i class="fab fa-facebook-f"></i>
    </a>
    
    <!-- X (Twitter) -->
    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://yourwebsite.com/blogdetails.php?id=' . $blog_id); ?>&text=<?php echo urlencode($title); ?>" 
       target="_blank" 
       title="Share on X (Twitter)" 
       class="text-white bg-black hover:bg-gray-800 social-icon-btn rounded-full p-3 transition">
        <i class="fab fa-x-twitter"></i> <!-- correct X logo -->
    </a>
    
    <!-- WhatsApp -->
    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($title . ' - http://yourwebsite.com/blogdetails.php?id=' . $blog_id); ?>" 
       target="_blank" 
       title="Share on WhatsApp" 
       class="text-white bg-[#25D366] hover:bg-[#1DA851] social-icon-btn rounded-full p-3 transition">
        <i class="fab fa-whatsapp"></i>
    </a>
</div>


        </div>

    </div>
</main>

---

<section class="bg-gray-50 py-16 border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">You Might Also Like</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php 
            if (mysqli_num_rows($related_result) > 0) {
                while ($related_row = mysqli_fetch_assoc($related_result)) {
            ?>
            <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl">
                <a href="blogdetails.php?id=<?php echo $related_row['blog_id']; ?>">
                    <img src="uploads/blogs/<?php echo htmlspecialchars($related_row['image']); ?>" 
                        alt="<?php echo htmlspecialchars($related_row['title']); ?>" 
                        class="w-full h-48 object-cover">
                </a>
                <div class="p-5">
                    <p class="text-sm text-gray-500 mb-2"><?php echo date("F j, Y", strtotime($related_row['created_at'])); ?></p>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-primary transition-colors line-clamp-2">
                        <a href="blogdetails.php?id=<?php echo $related_row['blog_id']; ?>">
                            <?php echo htmlspecialchars($related_row['title']); ?>
                        </a>
                    </h3>
                    <a href="blogdetails.php?id=<?php echo $related_row['blog_id']; ?>" class="text-primary hover:text-secondary font-medium text-sm">
                        Read Article <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </article>
            <?php
                }
            } else {
                echo '<div class="col-span-3"><p class="text-center text-lg text-gray-600">No related articles found.</p></div>';
            }
            ?>
        </div>
    </div>
</section>

---

<section class="hero-gradient text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-3xl md:text-4xl font-bold mb-4">Stay Updated with Spice Stories</h3>
        <p class="text-xl text-white/90 mb-8">
            Get the latest recipes, spice guides, and culinary tips delivered to your inbox
        </p>
        <div class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto">
            <input 
                type="email" 
                placeholder="Enter your email address" 
                class="flex-1 px-6 py-4 text-gray-900 rounded-xl focus:ring-4 focus:ring-white/20 focus:outline-none"
            >
            <button class="bg-white text-primary px-8 py-4 rounded-xl hover:bg-gray-100 transition-colors font-medium">
                Subscribe
            </button>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>