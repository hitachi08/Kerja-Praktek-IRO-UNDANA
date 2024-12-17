<?php

include 'connect.php';

$nama_user = $_SESSION['nama_user'];
$email_user = $_SESSION['email_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $kategori = $_POST['category'];
    $rating = $_POST['rating'];
    $ulasan = $_POST['comments'];
    $tanggal_review = date('Y-m-d');
    try {

        $query_user = "SELECT id_user FROM user WHERE nama_user = ? AND email_user = ?";
        $stmt_user = $conn->prepare($query_user);
        $stmt_user->bind_param("ss", $nama_user, $email_user);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        if ($result_user->num_rows > 0) {
            $row = $result_user->fetch_assoc();
            $id_user = $row['id_user'];

            $query_review = "INSERT INTO review (kategori, rating, ulasan, tanggal_review, id_user) 
                             VALUES (?, ?, ?, ?, ?)";
            $stmt_review = $conn->prepare($query_review);
            $stmt_review->bind_param("ssssi", $kategori, $rating, $ulasan, $tanggal_review, $id_user);
            if ($stmt_review->execute()) {
                echo "<script>alert('Review berhasil disimpan!');</script>";
            } else {
                echo "<script>alert('Gagal menyimpan review: " . $stmt_review->error . "');</script>";
            }

            echo "<script>alert('Review berhasil disimpan!');</script>";
        } else {
            echo "<script>alert('User tidak ditemukan!');</script>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Page - IRO UNDANA</title>
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/png">
    <link rel="apple-touch-icon" href="style/image/Logo_Undana.png">
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/x-icon">

    <meta name="theme-color" content="#ffffff">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.css">

    <link rel="stylesheet" href="user/fonts/icomoon/style.css" />

    <link rel="stylesheet" href="user/css/bootstrap.min.css" />

    <link rel="stylesheet" href="user/css/style.css" />
    <link rel="stylesheet" href="user/css/mystyle.css">
    <link rel="stylesheet" href="user/css/steps.css">

    <script src="user/js/jquery-3.3.1.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .rating-emote {
            font-size: 30px;
            cursor: pointer;
            transition: 0.2s;
        }

        .rating-emote:hover {
            transform: scale(1.5);
        }

        .rating-emote:active {
            transform: scale(1.2);
        }

        .rating-emote.selected {
            transform: scale(1.5);
        }

        .form-section {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .modal-content {
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }
    </style>

</head>

<body>

    <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
            <div class="site-mobile-menu-close mt-3">
                <span class="icon-close2 js-menu-toggle"></span>
            </div>
        </div>
        <div class="site-mobile-menu-body"></div>
    </div>

    <div class="site-navbar-wrap">
        <div class="site-navbar site-navbar-target js-sticky-header" style="background-color: #151c24;">
            <div class="container">
                <div class="row align-items-center">

                    <div class="nav-left col-4">
                        <img src="user/images/Logo_Undana.png" alt="Logo Undana" width="40rem" class="me-3" />
                        <div class="divider d-none d-lg-block"
                            style="border-left: 2px solid #805f03; height: 2.7rem; margin: 0px 1rem;"></div>
                        <div>
                            <div class="d-none d-lg-block" style="color: white;">
                                <span style="font-size: 1rem; font-weight: 500">International Relations</span><br>
                                <span style="font-size: 0.8rem;">Office UNDANA</span>
                            </div>
                            <div class="d-block d-lg-none">
                                <h1 class="my-0 site-logo"
                                    style="padding: 0.5rem 0px 0px 0.5rem; color: white; font-size: 1.2rem; font-weight: 600;">
                                    IROUNDANA
                                </h1>
                            </div>
                        </div>
                    </div>

                    <div class="col-8">
                        <nav class="site-navigation text-right" role="navigation">
                            <div class="container">
                                <div class="d-inline-block d-lg-none ml-md-0 mr-auto py-3">
                                    <a href="#" class="site-menu-toggle js-menu-toggle text-white"><span
                                            class="icon-menu h3"></span></a>
                                </div>

                                <ul class="site-menu main-menu js-clone-nav d-none d-lg-block">
                                    <!-- Home -->
                                    <li class="<?php echo ($page == 'home') ? 'active' : ''; ?>">
                                        <a href="index.php?page=home" class="nav-link">Home</a>
                                    </li>
                                    <!-- VRF -->
                                    <li>
                                        <a href="index.php?page=home#vrf-section" class="nav-link">VRF</a>
                                    </li>
                                    <!-- Status -->
                                    <li class="<?php echo ($page == 'status') ? 'active' : ''; ?>">
                                        <a href="index.php?page=status" class="nav-link">Status</a>
                                    </li>
                                    <!-- Review -->
                                    <li class="<?php echo ($page == 'review') ? 'active' : ''; ?>">
                                        <a href="index.php?page=review" class="nav-link">Review</a>
                                    </li>
                                    <!-- Log out -->
                                    <li>
                                        <a href="#" class="nav-link" data-bs-toggle="modal"
                                            data-bs-target="#logoutModal">Log Out
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout_user.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-sm" style="margin-top: 80px">
                    <div class="card-header bg-light text-black text-center">
                        <h4>Submit Your Review</h4>
                    </div>
                    <div class="card-body">
                        <form id="reviewForm" method="POST" action="review_page.php">
                            <!-- Name -->
                            <div class="form-group">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo $nama_user; ?>" readonly>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo $email_user; ?>" readonly>
                            </div>

                            <!-- Review Category -->
                            <div class="form-group">
                                <label for="category" class="form-label">Review Category</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="" selected disabled>Choose a category</option>
                                    <option value="visit">Visit</option>
                                    <option value="website">Website</option>
                                </select>
                            </div>

                            <!-- Rating -->
                            <div class="form-group">
                                <label class="form-label">Rating</label>
                                <div class="star-rating d-flex justify-content-start">
                                    <span class="rating-emote" data-value="😡 Angry">😡</span>
                                    <span class="rating-emote" data-value="😠 Slightly Angry">😠</span>
                                    <span class="rating-emote" data-value="😐 Neutral">😐</span>
                                    <span class="rating-emote" data-value="🙂 Happy">🙂</span>
                                    <span class="rating-emote" data-value="😄 Very Happy">😄</span>
                                </div>
                                <input type="hidden" id="rating" name="rating" required>
                            </div>

                            <!-- Comments -->
                            <div class="form-group">
                                <label for="comments" class="form-label">Your Comments</label>
                                <textarea class="form-control" id="comments" name="comments" rows="4"
                                    placeholder="Write your comments here..." required></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-primary mr-3" id="previewBtn">Preview Your
                                    Review
                                </button>
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Modal -->
                <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="previewModalLabel">Review Preview</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Name:</strong> <span id="previewName"></span></p>
                                <p><strong>Email:</strong> <span id="previewEmail"></span></p>
                                <p><strong>Category:</strong> <span id="previewCategory"></span></p>
                                <p><strong>Rating:</strong> <span id="previewRating"></span></p>
                                <p><strong>Comments:</strong></p>
                                <p id="previewComments"></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="bg-body-tertiary text-center text-lg-start">
        <div class="text-center p-3 mt-3" style="font-size: 0.6rem; background-color: #151c24; color: white;">
            Copyright © Computer Science UNDANA 2024
        </div>
    </footer>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="user/js/bootstrap.min.js"></script>
    <script src="user/js/jquery.sticky.js"></script>
    <script src="user/js/main.js"></script>

    <script src="user/js/jquery.steps.js"></script>
    <script src="js/steps.js"></script>
    <script src="js/review_page.js"></script>

</body>

</html>