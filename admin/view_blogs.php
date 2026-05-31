<?php
include "header.php";
include "../connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Blogs - Aroma Hub Admin</title>
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
            /* Consistent orange/red gradient */
            background: linear-gradient(135deg, #f97316 0%, #dc2626 50%, #b91c1c 100%);
        }
        .table-header {
            /* Consistent light orange/red gradient for table header */
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
        }
        /* Custom class for content truncation */
        .content-truncate {
            display: -webkit-box;
            -webkit-line-clamp: 2; /* Limit to 2 lines */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
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
                            <span class="ml-1 text-sm font-medium text-primary md:ml-2">Drafts/Pending</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 hero-gradient rounded-xl flex items-center justify-center">
                        <i class="fas fa-pen-nib text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">View Pending Blogs</h1>
                        <p class="text-gray-600 mt-1">Review, edit, or publish blog posts that are currently in draft status.</p>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="add_blogs.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Add New Blog
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="table-header px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Draft Blog Posts (Status: 0)</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                #
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Image
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Content Snippet
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Author
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        // Query for Draft/Pending blogs (status='0')
                        $sql="select * from blog where status='0' ORDER BY blog_id DESC";
                        $result=mysqli_query($conn,$sql);
                        $i=1;

                        if ($result && mysqli_num_rows($result) > 0) {
                            while($row=mysqli_fetch_assoc($result))
                            {
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo $i++; ?>
                                <span class="block text-xs text-gray-500 mt-1">ID: <?php echo $row['blog_id'] ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="../uploads/blogs/<?php echo htmlspecialchars($row['image']); ?>" 
                                     alt="Blog Image" 
                                     class="w-16 h-16 object-cover rounded-md shadow">
                            </td>
                            <td class="px-6 py-4 max-w-xs text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($row['title']) ?>
                            </td>
                            <td class="px-6 py-4 max-w-sm text-sm text-gray-500">
                                <p class="content-truncate"><?php echo htmlspecialchars($row['content']) ?></p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <?php echo htmlspecialchars($row['author']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-col space-y-2">
                                    <a href="edit_blogs.php?blog_id=<?php echo $row['blog_id'];?>" 
                                       class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-xs leading-4 font-medium rounded-lg text-white bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200"
                                       title="Edit Blog">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    
                                    <a href="publish_blog.php?blog_id=<?php echo $row['blog_id'];?>" 
                                       onclick="return confirm('Are you sure you want to PUBLISH this blog? It will be live on the website.');"
                                       class="inline-flex items-center justify-center px-3 py-2 border border-green-500 text-xs leading-4 font-medium rounded-lg text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200"
                                       title="Publish Blog">
                                        <i class="fas fa-upload mr-1"></i> Publish
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-check-double text-green-500 text-2xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">No Draft Blogs</h3>
                                    <p class="text-sm text-gray-500">All blogs are either published or none have been created yet.</p>
                                </div>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Blog Management Links</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="add_blogs.php" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-plus-circle mr-2 text-primary"></i>
                    Create New Post
                </a>
                <a href="published_blogs.php" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-book-open mr-2 text-blue-600"></i>
                    View Published Blogs
                </a>
                <a href="deleted_blogs.php" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-trash-alt mr-2 text-secondary"></i>
                    View Deleted/Archived
                </a>
            </div>
        </div>
    </div>
</body>
</html>

<?php
include "footer.php";
?>