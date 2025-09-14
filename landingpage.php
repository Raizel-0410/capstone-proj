<?php
session_start();
require 'db_connect.php'; 

function generateRandomToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

if (!isset($_SESSION['user_token'])) {
    $token = generateRandomToken(64);
    $_SESSION['user_token'] = $token;

    $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));

    $stmt = $pdo->prepare("INSERT INTO visitor_sessions (user_token, expires_at) VALUES (?, ?)");
    $stmt->execute([$token, $expiry]);
} else {
    $token = $_SESSION['user_token'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="icon" type="image/png" href=".\images\logo\5thFighterWing-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./stylesheet/style.css">
    <title>5th Fighter Wing</title>
</head>
<body>
     

    <div id="top-bar" class="top-bar"></div>

    <!-- Header -->

  <div class="header">
  <div class="left-group">
    <div class="basa-logo"><img src=".\images\logo\5thFighterWing-logo.png" alt="BASA Logo"></div>
    <h1>5TH FIGHTER WING</h1>
  </div>

  <div class="right-group">
    <div class="transparency-logo"><img src=".\images\logo\Transparency Seal - img.png" alt="Transparency Logo"></div>
    <div class="phaf-logo"><img src=".\images\logo\Philippine Airforce - Logo.png" alt="PHAF Logo"></div>
    <div class="bagongpilipinas-logo"><img src=".\images\logo\Bagong Pilipinas - Logo.png" alt="Bagong Pilipinas Logo"></div>
  </div>
</div>

    <div class="breadcrumb">
        <div class="nav-links">
            <ul>
                <li><a href="#top-bar">Home</a></li>
                <li><a href="#AboutUs">About Us</a></li>
                <li><a href="#News">News & Announcements</a></li>
                <li><a href="#ScheduleVisit">Schedule a Visit</a></li>
                <li><a href="#ContactUs">Contact Us</a></li>
            </ul>
        </div>
    </div>

    <!-- Header Carousel -->

<section id="slider">
  <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <?php
      require 'db_connect.php';
      $stmt = $pdo->query("SELECT * FROM landing_carousel ORDER BY sort_order ASC");
      $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $isActive = true;
      foreach ($slides as $slide):
      ?>
        <div class="carousel-item <?= $isActive ? 'active' : '' ?>">
          <img src="<?= htmlspecialchars($slide['image_path']) ?>" class="d-block w-100" alt="Carousel Slide">
          <?php if (!empty($slide['caption_title']) || !empty($slide['caption_text'])): ?>
            <div class="carousel-caption d-none d-md-block">
              <h5><?= htmlspecialchars($slide['caption_title']) ?></h5>
              <p><?= htmlspecialchars($slide['caption_text']) ?></p>
            </div>
          <?php endif; ?>
        </div>
      <?php
        $isActive = false;
      endforeach;
      ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</section>

    <!-- About Us -->
   <div id="AboutUs" class="AboutUs">
    <?php
   
    $stmt = $pdo->query("SELECT * FROM landing_about_us WHERE id = 1 LIMIT 1");
    $about = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    
    <h1 class="aboutush1"><?= htmlspecialchars($about['title']) ?></h1>
    <div class="hr-about"></div>
    <p><?= nl2br(htmlspecialchars($about['content'])) ?></p>
</div>


    <!-- Vision & Mission Card -->

    <div class="vision-mission">
    <?php
    
    $stmt = $pdo->query("SELECT * FROM landing_vision_mission");
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($cards as $card): ?>
        <div class="card" style="width: 35rem;">
            <img src="<?= htmlspecialchars($card['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($card['title']) ?>-img">
            <div class="card-body">
                <div class="bottom">
                    <h4><?= htmlspecialchars($card['title']) ?></h4> 
                    <div class="bottom-right">
                        <a href="<?= htmlspecialchars($card['link_url']) ?>">View More</a>
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


    <!-- News & Announcement -->

   <section id="News" class="news-section">
  <h1 class="section-title">NEWS & ANNOUNCEMENTS</h1>

  <div class="news-layout">
    
    <!-- LEFT COLUMN -->
    <div class="news-left">

      <!-- Carousel -->
      <div id="news-carouselExampleAutoplaying" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php
          require 'db_connect.php';
          $carousel = $pdo->query("SELECT * FROM news_carousel ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
          $active = "active";
          foreach ($carousel as $item): ?>
            <div class="carousel-item <?= $active ?>">
              <img src="<?= htmlspecialchars($item['image_path']) ?>" class="d-block w-100" alt="News Carousel">
            </div>
          <?php $active = ""; endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#news-carouselExampleAutoplaying" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#news-carouselExampleAutoplaying" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>

      <!-- Headlines -->
      <div class="news-list">
        <?php
        $headlines = $pdo->query("SELECT * FROM news_headlines ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($headlines as $news): ?>
          <div class="news-item">
            <img src="<?= htmlspecialchars($news['image_path']) ?>" alt="<?= htmlspecialchars($news['title']) ?>">
            <div class="news-text">
              <h2><?= htmlspecialchars($news['title']) ?></h2>
              <p><?= htmlspecialchars($news['description']) ?></p>
            </div>
          </div>
          <hr class="line-break">
        <?php endforeach; ?>
      </div>
    </div>

    <!-- RIGHT SIDEBAR -->
    <aside class="news-sidebar">
      <div id="search-container" class="sidebar-box">
        <div class="search-box">
          <input type="text" placeholder="Search..."> <i class="fa-solid fa-search"></i>
        </div>
      </div>
      <!-- You can add widgets here later -->
    </aside>

  </div>
</section>




    <h1 id="ScheduleVisit" class="visith1">VISITATION REQUEST FORM</h1>

<form class="visitation-request-section" action="visitation_submit.php" method="POST" enctype="multipart/form-data">

  <!-- Visitor Info -->
  <h3>Visitor Information: </h3>
  <div class="visitor-information-section">
    <div class="visitor-info-column">
      <label>Visitor Name:
        <input type="text" name="visitor_name" placeholder="Enter full name" required>
      </label>
      <label>Home Address:
        <input type="text" name="home_address" placeholder="Enter home address" required>
      </label>
      <label>Valid ID:
        <input type="file" name="valid_id" accept="image/*" required>
      </label>
    </div>

    <div class="visitor-info-column">
      <label>Contact Number:
        <input type="text" name="contact_number" placeholder="e.g. 09xxxxxxxxx" required>
      </label>
      <label>Email Address:
        <input type="email" name="email" placeholder="example@email.com" required>
      </label>
      <label>Selfie Photo:
        <input type="file" name="selfie_photo" accept="image/*" required>
      </label>
    </div>
  </div>

  <!-- Vehicle Info -->
  <h3>Vehicle Information: </h3>
  <div class="vehicle-information-section">
    <div class="vehicle-info-column">
      <label>Vehicle Owner:
        <input type="text" name="vehicle_owner" placeholder="Owner name">
      </label>
      <label>Vehicle Brand:
        <input type="text" name="vehicle_brand" placeholder="e.g. Toyota, Honda">
      </label>
      <label>Plate Number:
        <input type="text" name="plate_number" placeholder="ABC-1234">
      </label>
    </div>

    <div class="vehicle-info-column">
      <label>Vehicle Color:
        <input type="text" name="vehicle_color" placeholder="e.g. Red, Black">
      </label>
      <label>Vehicle Model:
        <input type="text" name="vehicle_model" placeholder="e.g. Vios, Civic">
      </label>
      <label>Vehicle Photo:
        <input type="file" name="vehicle_photo" accept="image/*">
      </label>
    </div>
  </div>

  <!-- Schedule -->
  <h3>Schedule Request: </h3>
  <div class="schedule-request-section">
    <div class="schedule-req-div">
      <label>Reason for Visitation:
        <input type="text" name="reason" placeholder="Enter reason" required>
      </label>
      <label>Personnel Related to:
        <input type="text" name="personnel_related" placeholder="Who will be visited">
      </label>
    </div>

    <label id="datetime">Date & Time:
      <input type="date" name="visit_date" required>
      <input type="time" name="visit_time" required>
    </label>

    <button type="submit" class="submit">Submit</button>
  </div>

</form>



    <div id="ContactUs" class="contact-us-section">
      <h1>CONTACT US</h1>
        <div class="contact-us-container">
            <h2>Basa Air Base, 5th Fighter Wing</h2>

                <div class="contact-info">
                    <div class="info-item">
                         <i class="fa-solid fa-envelope"></i>
                         <h4>5thfighterwing@mil.ph</h4>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <h4>Basa Air Base, Floridablanca, Pampanga</h4>
                    </div>
                </div>
                <br>
                <br>

            <div class="contact-message">
                 <h2>Report an Issue</h2>
                <input type="text" placeholder="Message" class="message"><br>
            </div>
         

            <div class="contact-visitor">
                <input type="text" placeholder="Name" class="name"><br>
                <input type="text" placeholder="Email Address (optional)" class="email-add"><br>
                
            </div>
            <button class="send-message">Send Message</button>
            
        </div>
    </div>

  <div class="visitor-counter">
  <span>Total Visitation :</span>
  <div class="digits" id="counter">
    <span>0</span>
    <span>0</span>
    <span>0</span>
    <span>0</span>
    <span>0</span>
    <span>0</span>
  </div>
</div>


  <div class="footer-columns">
    <div class="col">
      <div class="col-img">
         <img src=".\images\logo\5thFighterWing-logo.png" alt="Basa Logo">
         <img src=".\images\logo\Bagong Pilipinas - Logo.png" alt="Bagong Pilipinas Logo">
         <img src=".\images\logo\Philippine Airforce - Logo.png" alt="Philippine Air Force Logo">
      </div>
      <br>
      <h5>Copyright © Basa Air Base 5th Fighter Wing. All Rights Reserved</h5>
    </div>
    <div class="col">
      <h4 class="middle-panel-text">Follow our Socials:</h4>
      <i class="fa-brands fa-facebook"></i>
      <i class="fa-brands fa-instagram"></i>
      <i class="fa-brands fa-twitter"></i>
      <i class="fa-brands fa-youtube"></i>
    </div>

    <div id="last-col" class="col">
      <h4 class="right-panel-text">Developed By:</h4>

      <div id="left-col-img" class="col-img">
        <img src=".\images\logo\pamsu - logo.png" alt="Pampanga State Univertisty Logo">
        <img src=".\images\logo\ccs - log.png" alt="College of Computing Studies">
        <h4 class="right-panel-text2">CCS Students of Pampanga State University</h4>
      </div>
     
    </div>
  </div>
     <script src="landingpage.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
     </body>
</html>