<?php
include "header.php"; // Assumed to contain opening <html>, <head>, and the main navigation
include "connection.php";

// --- PAGINATION LOGIC START ---
// 1. Configuration
$limit = 9; // Number of blogs to display per page (Set to 9 for 3 columns)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// 2. Get total number of records
$total_query = "SELECT COUNT(*) FROM blog";
$total_result = mysqli_query($conn, $total_query);
$total_rows = mysqli_fetch_array($total_result)[0];
$total_pages = ceil($total_rows / $limit);

// Ensure the page number is valid
if ($page < 1) {
    $page = 1;
} elseif ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
    $start = ($page - 1) * $limit;
}

// 3. Main Query with LIMIT and OFFSET
$query = "SELECT * FROM blog 
          ORDER BY created_at DESC 
          LIMIT $limit OFFSET $start";
$result = mysqli_query($conn, $query);
// --- PAGINATION LOGIC END ---
?>

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#f97316',
                    secondary: '#dc2626',
                }
            }
        }
    }
</script>
<style>
    /* Custom Styles from BLOGS.HTML */
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
    .featured-badge {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    .category-tag {
        background: linear-gradient(135deg, #f97316 0%, #dc2626 100%);
    }
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        z-index: 1000;
    }
    .toast.show {
        transform: translateX(0);
    }
    .toast.info {
        background: #3b82f6;
    }
    .search-highlight {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
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
</style>

<div id="toast-container"></div>

<section class="hero-gradient text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">Spice Stories & Culinary Tales</h1>
        <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
            Discover the rich history, health benefits, and culinary secrets of the world's finest spices and seasonings
        </p>
        
        <div class="max-w-2xl mx-auto">
            <div class="relative">
                <input 
                    type="text" 
                    id="blog-search"
                    placeholder="Search articles, recipes, spice guides..." 
                    class="w-full px-6 py-4 pl-14 text-gray-900 bg-white rounded-2xl shadow-lg focus:ring-4 focus:ring-white/20 focus:outline-none text-lg"
                >
                <i class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                <button class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-xl hover:shadow-lg transition-all">
                    Search
                </button>
            </div>
        </div>
    </div>
</section>

<div class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-wrap items-center justify-center gap-4">
            <button class="category-filter active category-tag text-white px-4 py-2 rounded-full transition-all" data-category="all">
                All Articles
            </button>
            <button class="category-filter border border-gray-300 text-gray-700 px-4 py-2 rounded-full hover:border-primary hover:text-primary transition-all" data-category="recipes">
                <i class="fas fa-utensils mr-2"></i>Recipes
            </button>
            <button class="category-filter border border-gray-300 text-gray-700 px-4 py-2 rounded-full hover:border-primary hover:text-primary transition-all" data-category="health">
                <i class="fas fa-heart mr-2"></i>Health & Wellness
            </button>
            <button class="category-filter border border-gray-300 text-gray-700 px-4 py-2 rounded-full hover:border-primary hover:text-primary transition-all" data-category="guides">
                <i class="fas fa-book mr-2"></i>Spice Guides
            </button>
            <button class="category-filter border border-gray-300 text-gray-700 px-4 py-2 rounded-full hover:border-primary hover:text-primary transition-all" data-category="culture">
                <i class="fas fa-globe mr-2"></i>Culture & History
            </button>
            <button class="category-filter border border-gray-300 text-gray-700 px-4 py-2 rounded-full hover:border-primary hover:text-primary transition-all" data-category="tips">
                <i class="fas fa-lightbulb mr-2"></i>Cooking Tips
            </button>
        </div>
    </div>
</div>


<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
   

    <section>
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-3xl font-bold text-gray-900">Latest Articles (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)</h3>
            <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option>Most Recent</option>
                <option>Most Popular</option>
                <option>Oldest First</option>
                <option>A-Z</option>
            </select>
        </div>

        <div id="blog-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php 
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){ 
                    // Sanitize and prepare data
                    $title = htmlspecialchars($row['title']);
                    $content_snippet = htmlspecialchars(substr($row['content'], 0, 120)) . "...";
                    $image_src = "uploads/blogs/" . htmlspecialchars($row['image']);
                    $blog_id = htmlspecialchars($row['blog_id']);
                    $created_date = date("F j, Y", strtotime($row['created_at']));
                    
                    // Placeholders for dynamic category data (needs proper DB fields to be fully dynamic)
                    $category_name = "Spice Guides"; 
                    $category_color = "bg-blue-500"; 
                    $read_time = rand(3, 10);
            ?>
            <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-lg" data-category="<?php echo strtolower($category_name); ?>" data-article-id="<?php echo $blog_id; ?>">
                <div class="relative">
                    <img src="<?php echo $image_src; ?>" alt="<?php echo $title; ?>" class="w-full h-48 object-cover">
                    <div class="absolute top-4 right-4">
                        <span class="<?php echo $category_color; ?> text-white px-3 py-1 rounded-full text-sm font-medium"><?php echo $category_name; ?></span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 mb-3">
                        <span class="read-time px-3 py-1 rounded-full text-sm font-medium"><?php echo $read_time; ?> min read</span>
                        <span class="text-sm text-gray-500"><?php echo $created_date; ?></span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-primary transition-colors cursor-pointer">
                        <a href="blogdetails.php?id=<?php echo $blog_id; ?>"><?php echo $title; ?></a>
                    </h3>
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        <?php echo $content_snippet; ?>
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="author-avatar rounded-full flex items-center justify-center text-white text-sm font-medium">AD</div>
                            <span class="text-sm text-gray-700 font-medium">Admin</span>
                        </div>
                        <a href="blogdetails.php?id=<?php echo $blog_id; ?>" class="read-article text-primary hover:text-secondary transition-colors font-medium">
                            Read More <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </article>
            <?php 
                }
            } else {
                echo "<div class='col-span-full'><p class='text-center text-xl text-gray-600'>No blogs found on this page.</p></div>";
            }
            ?>
        </div>
    </section>

    <?php if ($total_pages > 1) { ?>
    <div class="flex items-center justify-center mt-12 space-x-2">
        <a href="blogs.php?page=<?php echo $page - 1; ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors <?php echo ($page <= 1) ? 'opacity-50 pointer-events-none' : ''; ?>">
            <i class="fas fa-chevron-left mr-2"></i>Previous
        </a>
        
        <?php
        // Display a range of links centered around the current page
        $start_range = max(1, $page - 2);
        $end_range = min($total_pages, $page + 2);

        if ($start_range > 1) { echo '<span class="px-2 text-gray-500">...</span>'; }
        
        for ($i = $start_range; $i <= $end_range; $i++) {
            $active_class = ($i == $page) ? 'bg-gradient-to-r from-primary to-secondary text-white border-primary' : 'border border-gray-300 text-gray-700 hover:bg-gray-50';
            echo "<a href='blogs.php?page={$i}' class='px-4 py-2 {$active_class} rounded-lg transition-colors'>{$i}</a>";
        }

        if ($end_range < $total_pages) { echo '<span class="px-2 text-gray-500">...</span>'; }
        ?>
        
        <a href="blogs.php?page=<?php echo $page + 1; ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors <?php echo ($page >= $total_pages) ? 'opacity-50 pointer-events-none' : ''; ?>">
            Next<i class="fas fa-chevron-right ml-2"></i>
        </a>
    </div>
    <?php } ?>
</main>



<script>
    // Toast notification system
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                container.removeChild(toast);
            }, 300);
        }, 3000);
    }

    // Category filtering (Filters results on the current page)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.category-filter')) {
            const button = e.target.closest('.category-filter');
            const category = button.dataset.category;
            
            // Update active state
            document.querySelectorAll('.category-filter').forEach(btn => {
                btn.classList.remove('active', 'category-tag', 'text-white');
                btn.classList.add('border', 'border-gray-300', 'text-gray-700');
            });
            
            button.classList.add('active', 'category-tag', 'text-white');
            button.classList.remove('border', 'border-gray-300', 'text-gray-700');
            
            // Filter articles
            const articles = document.querySelectorAll('#blog-grid article');
            articles.forEach(article => {
                if (category === 'all' || article.dataset.category === category) {
                    article.style.display = 'block';
                } else {
                    article.style.display = 'none';
                }
            });
            
            showToast(`Showing ${category === 'all' ? 'all' : category} articles`, 'info');
        }
    });

    // Read article functionality - handles static button links
    document.addEventListener('click', function(e) {
        if (e.target.closest('.read-article')) {
            const button = e.target.closest('.read-article');
            const articleId = button.dataset.articleId;
            
            if (articleId) {
                showToast('Loading article...', 'info');
                setTimeout(() => {
                    window.location.href = `blogdetails.php?id=${articleId}`;
                }, 1000);
            }
        }
    });

    // Search functionality (Searches results on the current page)
    document.getElementById('blog-search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const articles = document.querySelectorAll('#blog-grid article');
        
        articles.forEach(article => {
            const title = article.querySelector('h3').textContent.toLowerCase();
            const content = article.querySelector('p').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || content.includes(searchTerm)) {
                article.style.display = 'block';
                
                // Highlight search terms
                if (searchTerm) {
                    article.querySelector('h3').classList.add('search-highlight');
                } else {
                    article.querySelector('h3').classList.remove('search-highlight');
                }
            } else {
                article.style.display = 'none';
            }
        });
    });

    // Newsletter subscription
    document.querySelector('section[class*="hero-gradient"] button').addEventListener('click', function() {
        const emailInput = this.previousElementSibling;
        const email = emailInput.value;
        if (email) {
            showToast('Successfully subscribed to our newsletter!', 'success');
            emailInput.value = '';
        } else {
            showToast('Please enter a valid email address', 'info');
        }
    });
</script>

<?php include "footer.php"; ?>