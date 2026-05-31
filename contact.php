
<?php
include "header.php";
include "connection.php"; 

// Initialize message variables
$success_message = '';
$error_message = '';

if(isset($_POST['submit'])) {
    $nm    = trim($_POST['txtnm']);
    $email = trim($_POST['txtemail']);
    $sub   = trim($_POST['txtsub']);
    $msg   = trim($_POST['txtmsg']);

    if (empty($nm) || empty($email) || empty($msg)) {
        $error_message = "Please fill in all required fields (Name, Email, Message).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // ✅ Simple Insert Logic (working one)
        $qry = "INSERT INTO contact(nm, email, sub, msg) VALUES ('$nm', '$email', '$sub', '$msg')";
        if (mysqli_query($conn, $qry)) {
            $success_message = "Thank you! Your message has been sent successfully.";
            $_POST = array(); // Clears form fields
        } else {
            $error_message = "Failed to send message. MySQL Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Aroma Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        .contact-gradient { background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); }
        .form-input:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
        .animate-fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);} }
    </style>
</head>
<body class="min-h-screen bg-gray-50">

<main>
    <!-- Breadcrumb -->
    <section class="bg-white py-4 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="index.php" class="text-gray-500 hover:text-orange-600">Home</a></li>
                    <li class="flex items-center">
                        <i data-lucide="chevron-right" class="h-4 w-4 text-gray-400 mx-2"></i>
                        <span class="text-orange-600 font-medium">Contact Us</span>
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Hero -->
    <section class="contact-gradient py-16 text-center text-white">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Get In Touch</h1>
            <p class="text-xl opacity-90">Have questions about our premium spices? We'd love to hear from you.</p>
        </div>
    </section>

    <!-- Messages -->
    <?php if (!empty($success_message)): ?>
        <div class="max-w-3xl mx-auto mt-8 px-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 animate-fade-in flex items-center">
                <i data-lucide="check-circle" class="h-5 w-5 text-green-500 mr-3"></i>
                <p class="text-green-800 font-medium"><?= htmlspecialchars($success_message); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="max-w-3xl mx-auto mt-8 px-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 animate-fade-in flex items-center">
                <i data-lucide="alert-circle" class="h-5 w-5 text-red-500 mr-3"></i>
                <p class="text-red-800 font-medium"><?= nl2br(htmlspecialchars($error_message)); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Contact Form Section -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- Info -->
            
                <div class="space-y-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">Contact Information</h2>
                        <p class="text-gray-600 text-lg mb-8">
                            Reach out to us for any inquiries about our premium spices, bulk orders, or general questions. Our team is here to help!
                        </p>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="flex items-start space-x-4">
                                <div class="bg-orange-100 p-3 rounded-lg">
                                    <i data-lucide="map-pin" class="h-6 w-6 text-orange-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Our Address</h3>
                                    <p class="text-gray-600">
                                        123 Main Road<br>
                                        Kattappana, Idukki<br>
                                        Kerala – INDIA
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="flex items-start space-x-4">
                                <div class="bg-orange-100 p-3 rounded-lg">
                                    <i data-lucide="phone" class="h-6 w-6 text-orange-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Phone Numbers</h3>
                                    <p class="text-gray-600">
                                        Mobile: <a href="tel:+919999990000" class="text-orange-600 hover:text-orange-700">(+91) 999 999 0000</a><br>
                                        Hotline: <a href="tel:1009678456" class="text-orange-600 hover:text-orange-700">1009 678 456</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="flex items-start space-x-4">
                                <div class="bg-orange-100 p-3 rounded-lg">
                                    <i data-lucide="mail" class="h-6 w-6 text-orange-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Email Addresses</h3>
                                    <p class="text-gray-600">
                                        General: <a href="mailto:info@aromahub.com" class="text-orange-600 hover:text-orange-700">info@aromahub.com</a><br>
                                        Support: <a href="mailto:support@aromahub.com" class="text-orange-600 hover:text-orange-700">support@aromahub.com</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="flex items-start space-x-4">
                                <div class="bg-orange-100 p-3 rounded-lg">
                                    <i data-lucide="clock" class="h-6 w-6 text-orange-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Business Hours</h3>
                                    <p class="text-gray-600">
                                        Monday - Friday: 9:00 AM - 6:00 PM<br>
                                        Saturday: 9:00 AM - 4:00 PM<br>
                                        Sunday: Closed
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            <!-- Form -->
            <div class="bg-white p-8 rounded-lg shadow-xl">
                <h2 class="text-2xl font-bold mb-6 text-gray-900">Send Us a Message</h2>
                <form method="POST" action="" class="space-y-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium">Your Name *</label>
                        <input type="text" name="txtnm" required value="<?= $_POST['txtnm'] ?? ''; ?>" 
                            class="form-input w-full px-4 py-3 border rounded-lg" placeholder="Enter your name">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium">Your Email *</label>
                        <input type="email" name="txtemail" required value="<?= $_POST['txtemail'] ?? ''; ?>" 
                            class="form-input w-full px-4 py-3 border rounded-lg" placeholder="Enter your email">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium">Subject</label>
                        <input type="text" name="txtsub" value="<?= $_POST['txtsub'] ?? ''; ?>" 
                            class="form-input w-full px-4 py-3 border rounded-lg" placeholder="Subject">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium">Message *</label>
                        <textarea name="txtmsg" rows="5" required class="form-input w-full px-4 py-3 border rounded-lg"><?= $_POST['txtmsg'] ?? ''; ?></textarea>
                    </div>
                    <button type="submit" name="submit" 
                        class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg flex items-center justify-center space-x-2">
                        <i data-lucide="send" class="h-4 w-4"></i><span>Send Message</span>
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include "footer.php"; ?>
<script>lucide.createIcons();</script>
</body>
</html>
```
