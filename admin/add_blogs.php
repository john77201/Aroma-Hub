<?php
include "../connection.php";
include "header.php";

// Define custom colors for consistent styling
// This should ideally be in a shared config, but is repeated here for completeness
?>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#f97316', // Orange
                    secondary: '#dc2626', // Red
                }
            }
        }
    }
</script>
<style>
    .hero-gradient {
        /* Consistent orange/red gradient for icons/headers */
        background: linear-gradient(135deg, #f97316 0%, #dc2626 50%, #b91c1c 100%);
    }
    .file-upload-area {
        border: 2px dashed #d1d5db; /* gray-300 */
        transition: all 0.2s ease;
    }
    .file-upload-area:hover, .dragover {
        border-color: #f97316; /* primary color */
        background-color: #fff7ed; /* orange-50 */
    }
</style>
<?php

// --- PHP LOGIC: BLOG ADDITION ---
if(isset($_POST['save']))
{
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    
    $blogimage = $_FILES['image']['name']; 
    $tmp = $_FILES['image']['tmp_name'];
    $folder = "../uploads/blogs/".$blogimage;
    
    // Attempt to move uploaded file
    if (move_uploaded_file($tmp, $folder)) {
        // Status 0: Draft/Pending, 1: Published (assuming 0 is default/draft status)
        $sql = "INSERT INTO blog(title, content, image, author, status) 
                VALUES ('$title', '$content', '$blogimage', '$author', 0)";
        $result = mysqli_query($conn, $sql);

        if($result){  
            echo "<script>alert('Blog added Successfully.'); window.location.href='view_blogs.php';</script>"; // Redirect after success
        } else {
            echo "<script>alert('Failed to insert into database: " . mysqli_error($conn) . "');</script>";  
        }
    } else {
        echo "<script>alert('Failed to upload image. Please check directory permissions.');</script>"; 
    }
}
// --- END PHP LOGIC ---
?>

<div class="page-wrapper bg-gray-50 min-h-screen">
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary">
                            <i class="fas fa-home mr-2"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Blogs</span>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="ml-1 text-sm font-medium text-primary md:ml-2">Add Blog</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 hero-gradient rounded-xl flex items-center justify-center">
                    <i class="fas fa-feather-alt text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Add New Blog Post</h1>
                    <p class="text-gray-600 mt-1">Create and publish new content for your website.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <form method="post" enctype="multipart/form-data" class="space-y-6">
                        <div class="p-6">
                            <h4 class="text-xl font-semibold text-gray-900 mb-6 border-b pb-3">Blog Details</h4>
                            
                            <div class="mb-6">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Blog Title</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-150" id="title" name="title" placeholder="Enter Blog Title" required>
                            </div>

                            <div class="mb-6">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                                <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-150" id="content" name="content" placeholder="Enter Blog Content" rows="8" required></textarea>
                            </div>

                            <div class="mb-6">
                                <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-150" id="author" name="author" placeholder="Enter Author Name" value="Admin">
                            </div>

                            <div class="mb-6">
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Blog Image (Featured)</label>
                                <div class="file-upload-area p-6 text-center rounded-lg cursor-pointer">
                                    <input type="file" id="image" name="image" class="hidden" required onchange="document.getElementById('file-name-display').textContent = this.files.length > 0 ? this.files[0].name : 'Choose a file'">
                                    <label for="image" class="text-gray-500 block">
                                        <i class="fas fa-cloud-upload-alt text-4xl mb-3 text-gray-400"></i>
                                        <p class="font-semibold text-gray-900">Drag & drop image here, or <span class="text-primary font-bold">click to browse</span></p>
                                        <p class="text-xs mt-1" id="file-name-display">JPEG, PNG, or GIF (Max 5MB)</p>
                                    </label>
                                </div>
                            </div>

                        </div>

                        <div class="border-t border-gray-200 p-6 bg-gray-50 rounded-b-xl">
                            <button type="submit" name="save" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Publish Blog Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="lg:col-span-1 hidden lg:block">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 space-y-4">
                    <h5 class="text-lg font-semibold text-gray-900 mb-3">Blog Checklist</h5>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Compelling Blog Title (SEO optimized).</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Rich, detailed Content (min 500 words).</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>High-quality Featured Image uploaded.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Author name is correct.</span>
                        </li>
                    </ul>
                    <div class="pt-4 border-t mt-4">
                         <a href="view_blogs.php" class="text-primary hover:text-secondary text-sm font-medium flex items-center">
                            <i class="fas fa-list-alt mr-2"></i>
                            View All Existing Blogs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Ensure the footer is included outside the form/container-fluid div
include "footer.php";
?>