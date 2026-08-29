<?php

include 'config.php';
session_start();

if(!isset($_SESSION['user_id'])){
   header('location:login.php');
   exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_GET['logout'])){
   session_unset();
   session_destroy();
   header('location:login.php');
   exit();
}

// ডাটাবেজ ক্যোয়ারি (PDO)
try {
    $select = $conn->prepare("SELECT * FROM `user_form` WHERE id = ?");
    $select->execute([$user_id]);
    $fetch = $select->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $fetch = false;
}

// ডাটা না পাওয়া গেলে টেস্ট ভ্যালু
if(!$fetch){
    $fetch = [
        'id' => '123',
        'name' => 'মোঃ রহিম মিয়া',
        'dob' => '03/01/2020',
        'blood_group' => 'B+',
        'number' => '01700000000',
        'nid_number' => 'BD-1234567890',
        'card_number' => 'BD-1234567890',
        'image' => ''
    ];
}

// --- ডাইনামিক QR কোড তৈরি ---
$domain = "http://ehealthbd.rf.gd"; 
$login_url = $domain . "/qr_login.php?number=" . urlencode($fetch['number'] ?? '') . "&token=" . urlencode($fetch['id'] ?? '');
$qr_code_api = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($login_url);

?>

<!DOCTYPE html>
<html lang="bn">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>জাতীয় ই-হেলথ কার্ড</title>

   <!-- html2canvas Library -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

   <style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

      *{
         font-family: 'Poppins', sans-serif;
         margin: 0; padding: 0;
         box-sizing: border-box;
      }

      body {
         background-color: #eef2f5;
         display: flex;
         justify-content: center;
         align-items: center;
         min-height: 100vh;
         padding: 15px;
      }

      .main-wrapper {
         display: flex;
         flex-direction: column;
         align-items: center;
         width: 100%;
      }

      /* ইমেজ যেন কোনো অবস্থাতেই স্ক্রিনের বাইরে না যায় */
      img {
         max-width: 100%;
         height: auto;
         display: block;
      }

      /* কার্ড কন্টেইনার */
      .health-card {
         width: 100%;
         max-width: 420px;
         background: #ffffff;
         border-radius: 12px;
         box-shadow: 0 8px 20px rgba(0,0,0,0.15);
         overflow: hidden;
         border: 1px solid #dcdcdc;
         position: relative;
      }

      /* হেডার অংশ */
      .card-header {
         background: linear-gradient(135deg, #14a05f 0%, #14a05f 58%, #0076a3 58%, #0076a3 100%);
         color: #ffffff;
         padding: 10px 12px;
         display: flex;
         justify-content: space-between;
         align-items: center;
      }

      .gov-info {
         display: flex;
         align-items: center;
         gap: 8px;
      }

      .gov-logo {
         width: 38px !important;
         height: 38px !important;
         background-color: #ffffff;
         border-radius: 50%; 
      }

      .gov-text {
         display: flex;
         flex-direction: column;
         font-size: 11px;
         line-height: 1.2;
      }

      .gov-text .bn { font-weight: 600; }
      .gov-text .en { font-size: 8px; opacity: 0.9; }

      .card-title {
         font-size: 13px;
         font-weight: 600;
         text-align: right;
         white-space: nowrap;
      }

      /* বডি অংশ */
      .card-body {
         padding: 12px;
         display: flex;
         justify-content: space-between;
         position: relative;
         background-color: #f8fafc;
      }

      .left-section {
         width: 68%;
      }

      .profile-image {
         width: 85px !important;
         height: 95px !important;
         object-fit: cover;
         border-radius: 6px;
         border: 2px solid #14a05f;
         margin-bottom: 8px;
      }

      .service-title {
         font-size: 18px;
         font-weight: 700;
         color: #0076a3;
         margin-bottom: 6px;
      }

      .info-row {
         font-size: 11px;
         color: #333;
         margin-bottom: 3px;
         display: flex;
      }

      .info-row .label {
         font-weight: 600;
         color: #444;
         width: 85px;
         flex-shrink: 0;
      }

      .info-row .value {
         font-weight: 500;
         color: #000;
      }

      /* ডান পাশ (আইকন ও কিউআর) */
      .right-section {
         width: 28%;
         display: flex;
         flex-direction: column;
         justify-content: space-between;
         align-items: flex-end;
      }

      .hex-icons {
         display: flex;
         flex-direction: column;
         gap: 5px;
      }

      .hex-icon {
         width: 26px;
         height: 26px;
         background: #ffffff;
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         border: 1px solid #14a05f;
         color: #14a05f;
         font-size: 12px;
      }

      .qr-code {
         width: 70px !important;
         height: 70px !important;
         border: 1px solid #ddd;
         border-radius: 4px;
         background: #fff;
         padding: 2px;
      }

      /* ফুটার অংশ */
      .card-footer {
         height: 10px;
         background: #14a05f;
      }

      /* ডাউনলোড বাটন স্টাইল */
      .download-btn {
         margin-top: 20px;
         padding: 10px 24px;
         background-color: #14a05f;
         color: #ffffff;
         border: none;
         border-radius: 6px;
         font-size: 14px;
         font-weight: 600;
         cursor: pointer;
         box-shadow: 0 4px 10px rgba(0,0,0,0.1);
         transition: background-color 0.2s ease, transform 0.1s ease;
         display: flex;
         align-items: center;
         gap: 8px;
      }

      .download-btn:hover {
         background-color: #0e7a48;
      }

      .download-btn:active {
         transform: scale(0.98);
      }
   </style>
</head>
<body>

   <div class="main-wrapper">
      
      <!-- কার্ড -->
      <div class="health-card" id="healthCard">
         
         <!-- হেডার -->
         <div class="card-header">
            <div class="gov-info">
               <img src="images/Bangladesh-Govt-Logo.png" alt="Logo" class="gov-logo" style="width:38px; height:38px;">
               <div class="gov-text">
                  <span class="bn">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</span>
                  <span class="en">Government of the People's Republic of Bangladesh</span>
               </div>
            </div>
            <div class="card-title">জাতীয় ই-হেলথ কার্ড</div>
         </div>

         <!-- বডি -->
         <div class="card-body">
            <div class="left-section">
               <?php if(empty($fetch['image'])): ?>
                  <img src="images/default-avatar.png" class="profile-image" style="width:85px; height:95px;">
               <?php else: ?>
                  <img src="uploaded_img/<?php echo htmlspecialchars($fetch['image']); ?>" class="profile-image" style="width:85px; height:95px;">
               <?php endif; ?>

               <div class="service-title">স্বাস্থ্যসেবা কার্ড</div>
               
               <div class="info-row"><span class="label">কার্ড নম্বর:</span><span class="value"><?php echo htmlspecialchars($fetch['card_number'] ?? 'BD-1234567890'); ?></span></div>
               <div class="info-row"><span class="label">এনআইডি নম্বর:</span><span class="value"><?php echo htmlspecialchars($fetch['nid_number'] ?? ''); ?></span></div>
               <div class="info-row"><span class="label">নাম:</span><span class="value"><?php echo htmlspecialchars($fetch['name'] ?? ''); ?></span></div>
               <div class="info-row"><span class="label">জন্ম তারিখ:</span><span class="value"><?php echo htmlspecialchars($fetch['dob'] ?? ''); ?></span></div>
               <div class="info-row"><span class="label">রক্তের গ্রুপ:</span><span class="value"><?php echo htmlspecialchars($fetch['blood_group'] ?? ''); ?></span></div>
               <div class="info-row"><span class="label">বৈধতা:</span><span class="value">চালু হচ্ছে</span></div>
            </div>

            <div class="right-section">
               <div class="hex-icons">
                  <div class="hex-icon">❤</div>
                  <div class="hex-icon">💊</div>
                  <div class="hex-icon">🖥</div>
               </div>
               <!-- ডাইনামিক QR কোড ইমেজে `crossorigin="anonymous"` যোগ করা হয়েছে যাতে ইমেজে ডাউনলোড সহজ হয় -->
               <img src="<?php echo $qr_code_api; ?>" alt="QR Code" class="qr-code" style="width:70px; height:70px;" crossorigin="anonymous">
            </div>
         </div>

         <!-- ফুটার -->
         <div class="card-footer"></div>

      </div>

      <!-- ডাউনলোড বাটন -->
      <button class="download-btn" id="downloadBtn">
         📥 কার্ড ডাউনলোড করুন
      </button>

   </div>

   <script>
      document.getElementById('downloadBtn').addEventListener('click', function() {
         const card = document.getElementById('healthCard');
         
         const originalText = this.innerHTML;
         this.innerHTML = 'প্রসেস হচ্ছে...';
         this.disabled = true;

         html2canvas(card, {
            scale: 3,
            useCORS: true, // API থেকে ডাইনামিক QR ইমেজ ডাউনলোডের জন্য প্রয়োজনীয়
            logging: false
         }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'Health_Card_<?php echo htmlspecialchars($fetch['card_number'] ?? 'BD'); ?>.png';
            link.href = canvas.toDataURL('image/png', 1.0);
            link.click();

            this.innerHTML = originalText;
            this.disabled = false;
         }).catch(err => {
            console.error(err);
            alert('ডাউনলোড করতে সমস্যা হয়েছে!');
            this.innerHTML = originalText;
            this.disabled = false;
         });
      });
   </script>

</body>
</html>