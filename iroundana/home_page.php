<?php

include 'connect.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard - IRO UNDANA</title>
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/png">
    <link rel="apple-touch-icon" href="style/image/Logo_Undana.png">
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/x-icon">

    <meta name="theme-color" content="#ffffff">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="phone\build\css\intlTelInput.css">
    <script src="phone\build\js\intlTelInputWithUtils.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.css">

    <link rel="stylesheet" href="user/fonts/icomoon/style.css" />

    <link rel="stylesheet" href="user/css/bootstrap.min.css" />

    <link rel="stylesheet" href="user/css/style.css" />
    <link rel="stylesheet" href="user/css/mystyle.css">
    <link rel="stylesheet" href="user/css/steps.css">

    <script src="user/js/jquery-3.3.1.min.js"></script>

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
        <div class="site-navbar site-navbar-target js-sticky-header">
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
                                        <a href="#home-section" class="nav-link">Home</a>
                                    </li>
                                    <!-- VRF -->
                                    <li>
                                        <a href="#vrf-section" class="nav-link">VRF</a>
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
    <div class="modal fade" id="logoutModal" style="z-index: 9999;" tabindex="-1" role="dialog"
        aria-labelledby="logoutModalLabel" aria-hidden="true">
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
    <!-- Home Section -->
    <div id="home-section">
        <div id="heroCarousel" class="carousel slide" data-bs-pause="false" data-bs-ride="carousel"
            data-bs-interval="10000">
            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active"
                    style="background-image: url('user/images/japan.jpg'); background-size: cover; background-position: center; height: 100vh;">
                    <div class="d-flex align-items-center justify-content-center text-center h-100 text-white">
                        <div>
                            <h1 class="fw-bold text-uppercase" style="font-size: 1.5rem;">
                                <?php
                                if (isset($_SESSION['nama_user'])) {
                                    echo "Welcome To IRO UNDANA, " . htmlspecialchars($_SESSION['nama_user']);
                                }
                                ?>
                            </h1>
                            <p class="mt-3 text-uppercase text-white" style="text-shadow: 2px 2px 3px #000000;">
                                <?php
                                // Tampilkan pesan yang sesuai
                                if (isset($_SESSION['nama_user'])) {
                                    echo "\"Come as a Guest, Leave as a Friend\"";
                                }
                                ?>
                            </p>
                            <a href="#vrf-section" class="btn btn-primary btn-lg mt-4 px-4">REQUEST NOW</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="carousel-item"
                    style="background-image: url('user/images/image2.jpg'); background-size: cover; background-position: center; height: 100vh;">
                    <div class="d-flex align-items-center justify-content-center text-center h-100 text-white">
                        <div>
                            <h1 class="fw-bold text-uppercase" style="font-size: 1.5rem;">Global Connections</h1>
                            <p class="mt-3 text-uppercase text-white" style="text-shadow: 2px 2px 3px #000000;">"Join us
                                in
                                building bridges across the globe."
                            </p>
                            <a href="#vrf-section" class="btn btn-primary btn-lg mt-4 px-4">REQUEST NOW</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="carousel-item"
                    style="background-image: url('user/images/image3.jpg'); background-size: cover; background-position: center; height: 100vh;">
                    <div class="d-flex align-items-center justify-content-center text-center h-100 text-white">
                        <div>
                            <h1 class="fw-bold text-uppercase" style="font-size: 1.5rem;">Your Journey Begins</h1>
                            <p class="mt-3 text-uppercase text-white" style="text-shadow: 2px 2px 3px #000000;">
                                "Discover opportunities that transform lives."</p>
                            <a href="#vrf-section" class="btn btn-primary btn-lg mt-4 px-4">REQUEST NOW</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Navigation Arrows -->
            <button class="carousel-control-prev d-none d-lg-block" type="button" data-bs-target="#heroCarousel"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next d-none d-lg-block" type="button" data-bs-target="#heroCarousel"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
    </div>
    <!-- End of Home Section -->

    <div id="vrf-section"></div>

    <!-- VRF Section -->
    <div class="container" style="margin-top: 100px">
        <h2 class="text-center mb-5" style="font-weight: bold;">VISIT REQUEST FORM</h2>
        <div class="wizard-form">
            <iframe name="response_frame" id="response_frame" style="display:none;"></iframe>
            <form class="form-register" id="form-vrf" action="" method="POST" target="response_frame">
                <div id="vrf-form">
                    <!-- Step 1 -->
                    <h2>1</h2>
                    <section>
                        <h3 style="font-family: 'Poppins';">Visit Details</h3>
                        <div class="divider2"></div>
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-12 col-md-6">
                                <!-- Date and Time of Proposed Visit -->
                                <div class="mb-3 row">
                                    <div class="col-12 col-md-6">
                                        <label for="visit-date" class="form-label">Date of Proposed Visit</label>
                                        <input type="date" class="form-control" id="visit-date" name="visit_date"
                                            required />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="visit-time" class="form-label">Time of Proposed Visit</label>
                                        <input type="time" class="form-control" id="visit-time" name="visit_time"
                                            required />
                                    </div>
                                </div>

                                <!-- Duration of Visit -->
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration of Visit (Hour)</label>
                                    <input type="number" class="form-control" id="duration" name="visit_duration"
                                        placeholder="Enter duration of visit" min="0" required />
                                </div>

                                <!-- Person Making the Visit Request -->
                                <div class="mb-3">
                                    <label class="form-label">Person Making the Visit Request</label>
                                    <div class="row">
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <input type="text" class="form-control" placeholder="Title"
                                                name="request_person_title" required />
                                        </div>
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <input type="text" class="form-control" placeholder="First Name"
                                                name="request_person_first_name" required />
                                        </div>
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <input type="text" class="form-control" placeholder="Last Name"
                                                name="request_person_last_name" required />
                                        </div>
                                    </div>
                                </div>

                                <!-- Position -->
                                <div class="mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" class="form-control" id="position" name="request_person_position"
                                        placeholder="Enter position" required />
                                </div>

                                <!-- Institution/Organization -->
                                <div class="mb-3">
                                    <label for="institution" class="form-label">Institution/Organization</label>
                                    <input type="text" class="form-control" id="institution"
                                        name="request_person_institution" placeholder="Enter institution/organization"
                                        required />
                                </div>

                                <!-- Institution Website -->
                                <div class="mb-3">
                                    <label for="website" class="form-label">Institution Website</label>
                                    <input type="url" class="form-control" id="website" name="request_person_website"
                                        placeholder="Enter institution website" required />
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-12 col-md-6">
                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="request_person_email"
                                        placeholder="Enter email" required />
                                </div>

                                <!-- Phone/Mobile Phone -->
                                <div class="mb-3 d-flex flex-column">
                                    <label for="phone" class="form-label">Phone/Mobile Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="request_person_phone"
                                        value="" required />
                                </div>

                                <!-- Facsimile -->
                                <div class="mb-3">
                                    <label for="fax" class="form-label">Facsimile</label>
                                    <input type="text" class="form-control" id="fax" name="request_person_fax"
                                        placeholder="Enter fax number" required />
                                </div>

                                <!-- Overview of the Institution/Organization -->
                                <div class="mb-3">
                                    <label for="overview" class="form-label">Overview of the
                                        Institution/Organization</label>
                                    <textarea class="form-control" id="overview" name="institution_overview" rows="4"
                                        placeholder="Enter overview" required></textarea>
                                </div>

                                <!-- Purpose of Visit -->
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose of Visit</label>
                                    <textarea class="form-control" id="purpose" name="visit_purpose" rows="4"
                                        placeholder="Enter purpose of visit" required></textarea>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Step 2 -->
                    <h2>2</h2>
                    <section>
                        <h3 style="font-family: 'Poppins';">Additional Information</h3>
                        <div class="divider2"></div>

                        <!-- Person(s) You Would Like To Meet -->
                        <div class="mb-3" id="meet-persons">
                            <label for="meet-person" class="form-label">Person(s) You Would Like To Meet</label>
                            <div class="input-group mb-3 meet-person-item">
                                <input type="text" class="form-control" name="meet_person[]"
                                    placeholder="Enter person's name" required />
                            </div>
                        </div>
                        <div class="action-btn">
                            <a href="#" class="btn-add" id="add-meet-person">Add Person</a>
                            <a href="#" class="btn-delete" id="remove-meet-person">Remove</a>
                        </div>

                        <!-- Specific Areas/Topics of Interest for Discussion -->
                        <div class="mb-3">
                            <label for="topics" class="form-label">Specific Areas/Topics of Interest for
                                Discussion</label>
                            <textarea class="form-control" id="topics" name="discussion_topics" rows="4"
                                placeholder="Enter topics of interest" required></textarea>
                        </div>

                        <!-- Contact Person at Universitas Nusa Cendana (Optional) -->
                        <div class="mb-3" id="contact-persons">
                            <label for="contact-person" class="form-label">Contact Person at Universitas Nusa
                                Cendana
                                (Optional)</label>
                            <div class="row g-2">
                                <div class="col-12 col-md-3 pb-2">
                                    <input type="text" class="form-control" name="contact_person_title[]"
                                        placeholder="Title" />
                                </div>
                                <div class="col-12 col-md-3 pb-2">
                                    <input type="text" class="form-control" name="contact_person_first_name[]"
                                        placeholder="First Name" />
                                </div>
                                <div class="col-12 col-md-3 pb-2">
                                    <input type="text" class="form-control" name="contact_person_last_name[]"
                                        placeholder="Last Name" />
                                </div>
                                <div class="col-12 col-md-3 pb-2">
                                    <input type="text" class="form-control" name="contact_person_position[]"
                                        placeholder="Position" />
                                </div>
                            </div>
                        </div>
                        <div class="action-btn">
                            <a href="#" class="btn-add" id="add-contact-person">Add Contact</a>
                            <a href="#" class="btn-delete" id="remove-contact-person">Remove</a>
                        </div>

                        <!-- Names of Delegation/Visitors -->
                        <div class="mb-3" id="delegation">
                            <label for="delegation" class="form-label">Names of Delegation/Visitors</label>
                            <div class="row g-2">
                                <div class="col-12 col-md-3 pb-2">
                                    <input type="text" class="form-control" name="delegation_title[]"
                                        placeholder="Title" required />
                                </div>
                                <div class="col-12 col-md-3 pb-2">
                                    <input type="text" class="form-control" name="delegation_first_name[]"
                                        placeholder="First Name" required />
                                </div>
                                <div class="col-12 col-md-3 pb-2">
                                    <input type="text" class="form-control" name="delegation_last_name[]"
                                        placeholder="Last Name" required />
                                </div>
                                <div class="col-12 col-md-3 pb-2">
                                    <input type="text" class="form-control" name="delegation_position[]"
                                        placeholder="Position" required />
                                </div>
                            </div>
                        </div>
                        <div class="action-btn">
                            <a href="#" class="btn-add" id="add-delegation">Add Delegation</a>
                            <a href="#" class="btn-delete" id="remove-delegation">Remove</a>
                        </div>

                        <!-- Interpreter (Yes/No) -->
                        <div class="btn-interpreter mb-3">
                            <label class="form-label">For your delegation to gain maximum benefit from the visit, they
                                should either have a working knowledge of English or be accompanied by an interpreter.
                                Do you require an interpreter?</label>
                            <label for="interpreter" class="form-label">
                                <input type="radio" name="interpreter" value="yes" required /> Yes
                            </label for="interpreter" class="form-label">
                            <label>
                                <input type="radio" name="interpreter" value="no" required /> No
                            </label>
                        </div>
                        <script>
                            // Generalized functions to add/remove inputs
                            function addInput(containerId, template, removeButtonId) {
                                const container = document.getElementById(containerId);
                                const newItem = document.createElement("div");
                                newItem.classList.add("mb-3");
                                newItem.innerHTML = template;
                                container.appendChild(newItem);

                                // Enable Remove button
                                const removeBtn = document.getElementById(removeButtonId);
                                removeBtn.style.opacity = "1";
                                removeBtn.style.pointerEvents = "auto";
                            }

                            function removeInput(containerId, removeButtonId) {
                                const container = document.getElementById(containerId);
                                if (container.children.length > 2) {
                                    container.removeChild(container.lastElementChild);
                                }

                                // Disable Remove button if only 1 input remains
                                const removeBtn = document.getElementById(removeButtonId);
                                if (container.children.length <= 2) {
                                    removeBtn.style.opacity = "0.5";
                                    removeBtn.style.pointerEvents = "none";
                                }
                            }

                            // Add/Remove Meet Person
                            document.getElementById("add-meet-person").addEventListener("click", function (e) {
                                e.preventDefault();
                                addInput("meet-persons", '<input type="text" class="form-control" name="meet_person[]" placeholder="Enter person\'s name" required />', "remove-meet-person");
                            });

                            document.getElementById("remove-meet-person").addEventListener("click", function (e) {
                                e.preventDefault();
                                removeInput("meet-persons", "remove-meet-person");
                            });

                            // Add/Remove Contact Person
                            document.getElementById("add-contact-person").addEventListener("click", function (e) {
                                e.preventDefault();
                                const template = `
                                <div class="row g-2">
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="contact_person_title[]" placeholder="Title" /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="contact_person_first_name[]" placeholder="First Name" /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="contact_person_last_name[]" placeholder="Last Name" /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="contact_person_position[]" placeholder="Position" /></div>
                                </div>`;
                                addInput("contact-persons", template, "remove-contact-person");
                            });

                            document.getElementById("remove-contact-person").addEventListener("click", function (e) {
                                e.preventDefault();
                                removeInput("contact-persons", "remove-contact-person");
                            });

                            // Add/Remove Delegation
                            document.getElementById("add-delegation").addEventListener("click", function (e) {
                                e.preventDefault();
                                const template = `
                                <div class="row g-2">
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="delegation_title[]" placeholder="Title" required /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="delegation_first_name[]" placeholder="First Name" required /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="delegation_last_name[]" placeholder="Last Name" required /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="delegation_position[]" placeholder="Position" required /></div>
                                </div>`;
                                addInput("delegation", template, "remove-delegation");
                            });

                            document.getElementById("remove-delegation").addEventListener("click", function (e) {
                                e.preventDefault();
                                removeInput("delegation", "remove-delegation");
                            });

                            // Initial State: Set Remove buttons to disabled on load
                            document.addEventListener("DOMContentLoaded", function () {
                                document.getElementById("remove-meet-person").style.opacity = "0.5";
                                document.getElementById("remove-meet-person").style.pointerEvents = "none";

                                document.getElementById("remove-contact-person").style.opacity = "0.5";
                                document.getElementById("remove-contact-person").style.pointerEvents = "none";

                                document.getElementById("remove-delegation").style.opacity = "0.5";
                                document.getElementById("remove-delegation").style.pointerEvents = "none";
                            });

                        </script>

                    </section>

                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" style="border: none; padding: 10px 20px;"
                        id="btn-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <!-- End of VRF Section -->

    <footer class="bg-body-tertiary text-center text-lg-start">
        <div class="text-center p-3 mt-3" style="font-size: 0.6rem; background-color: #151c24; color: white;">
            Copyright © Computer Science UNDANA 2024
        </div>
    </footer>

    <!-- Success Modal -->
    <div class="modal fade" style="z-index: 9999;" id="successModal" tabindex="-1" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-custom text-gray" style="">
                    <h5 class="modal-title" id="successModalLabel">Submission Successful</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Your submission has been successfully recorded. Please wait for further confirmation. You can
                        check the progress and approval status of your submission on the <strong>Status</strong> page.
                    </p>
                </div>
                <div class="modal-footer">
                    <a href="status_page.php" class="btn btn-primary">Go to Status Page</a>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" style="display:none; position: fixed; top: 10px; right: 10px;
    background-color: #28a745; color: white; padding: 10px 20px;
    border-radius: 5px; z-index: 1000;">
        Form successfully submitted!
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="user/js/bootstrap.min.js"></script>
    <script src="user/js/jquery.sticky.js"></script>
    <script src="user/js/jquery.steps.js"></script>
    <script src="user/js/main.js"></script>

</body>

</html>