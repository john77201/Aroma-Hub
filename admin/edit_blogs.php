<?php
include "../connection.php";
include "header.php";

// --- 1. INITIAL FETCH AND ID CHECK ---
// Assuming 'blog_id' is passed in the URL, as set in view_blogs.php
$id = isset($_GET['blog_id']) ? $_GET['blog_id'] : (isset($_GET['product_id']) ? $_GET['product_id'] : null);

if (!$id) {
    echo "<script>alert('Error: Blog ID is missing.'); window.location.href='view_blogs.php';</script>";
    exit;
}
$id = mysqli_real_escape_string($conn, $id);

$qry="SELECT * FROM blog WHERE blog_id='$id'";
$answer=mysqli_query($conn,$qry);
// Check if the blog post exists
if (mysqli_num_rows($answer) == 0) {
    echo "<script>alert('Error: Blog post not found.'); window.location.href='view_blogs.php';</script>";
    exit;
}
$row=mysqli_fetch_assoc($answer);

// --- 2. HANDLE FORM SUBMISSION (UPDATE) ---
if(isset($_POST['save']))
{
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $oldimage = mysqli_real_escape_string($conn, $_POST['imagee']);
    $new_image_name = $_FILES['image']['name']; 
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    
    $image_to_save = $oldimage;

    // Check if a new image was uploaded
    if(!empty($new_image_name)) 
    {
        $tmp = $_FILES['image']['tmp_name'];
        // Correcting folder path to be consistent with add_blogs.php
        $folder = "../uploads/blogs/".$new_image_name; 
        
        if(move_uploaded_file($tmp, $folder)) {
            $image_to_save = $new_image_name;
        } else {
            // Handle file upload error
            echo "<script>alert('Warning: Failed to upload new image. Keeping old image.');</script>"; 
        }
    }
    
    $sql="UPDATE blog SET title='$title', content='$content', image='$image_to_save', author='$author' WHERE blog_id='$id'";
    $result=(mysqli_query($conn,$sql));
    
    if($result)
      {  
        echo "<script>alert('Blog Updated Successfully.');window.location.href='view_blogs.php';</script>";
      }
      else
      {
       echo "<script>alert('Failed to update blog: " . mysqli_error($conn) . "');</script>"; 
      }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog - <?php echo htmlspecialchars($row['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .current-image-preview {
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

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
                            <a href="view_blogs.php" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary md:ml-2">Blogs</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="ml-1 text-sm font-medium text-primary md:ml-2">Edit Post</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 hero-gradient rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Blog Post: <span class="text-primary"><?php echo htmlspecialchars(substr($row['title'], 0, 50)) . (strlen($row['title']) > 50 ? '...' : ''); ?></span></h1>
                    <p class="text-gray-600 mt-1">Make changes to the blog content and featured image.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1">
            <div class="col-span-1">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <form method="post" enctype="multipart/form-data" class="space-y-6">
                        <div class="p-6">
                            <h4 class="text-xl font-semibold text-gray-900 mb-6 border-b pb-3">Blog Content</h4>
                            
                            <div class="mb-6">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Blog Title</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-150" id="title" name="title" value="<?php echo htmlspecialchars($row ['title']); ?>" required>
                            </div>

                            <div class="mb-6">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                                <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-150" id="content" name="content" rows="10" required><?php echo htmlspecialchars($row['content']); ?></textarea>
                            </div>

                            <div class="mb-6">
                                <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-150" id="author" name="author" value="<?php echo htmlspecialchars($row ['author']); ?>" placeholder="Enter Author Name">
                            </div>

                            <div class="mb-6 border-t pt-6 border-gray-100">
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-4">Blog Image (Featured)</label>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                                    <div class="col-span-1">
                                        <p class="text-xs font-semibold text-gray-600 mb-2">Current Image:</p>
                                        <?php if (!empty($row['image'])): ?>
                                            <div class="current-image-preview p-3 bg-white rounded-lg">
                                                <img src="../uploads/blogs/<?php echo htmlspecialchars($row['image']); ?>" 
                                                     alt="Current Blog Image" 
                                                     class="max-w-full h-auto object-cover rounded-md" style="max-height: 150px;">
                                                <input type="hidden" value="<?php echo htmlspecialchars($row['image']) ?>" name="imagee">
                                            </div>
                                        <?php else: ?>
                                            <p class="text-sm text-gray-500">No current image.</p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-span-2">
                                        <p class="text-xs font-semibold text-gray-600 mb-2">Upload New Image (Optional):</p>
                                        <div class="file-upload-area p-6 text-center rounded-lg cursor-pointer">
                                            <input type="file" id="image" name="image" class="hidden" onchange="document.getElementById('file-name-display').textContent = this.files.length > 0 ? this.files[0].name : 'Choose a file to replace the current image'">
                                            <label for="image" class="text-gray-500 block">
                                                <i class="fas fa-cloud-upload-alt text-4xl mb-3 text-gray-400"></i>
                                                <p class="font-semibold text-gray-900">Drag & drop image here, or <span class="text-primary font-bold">click to browse</span></p>
                                                <p class="text-xs mt-1" id="file-name-display">Choose a file to replace the current image</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 p-6 bg-gray-50 rounded-b-xl">
                            <button type="submit" name="save" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Update Blog Post
                            </button>
                            <a href="view_blogs.php" class="ml-4 inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php
include "footer.php";
?>