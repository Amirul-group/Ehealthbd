<?php
include 'config.php';
session_start();

// QR স্ক্যান থেকে নাম্বার ও টোকেন/আইডি গ্রহণ
if(isset($_GET['number']) && isset($_GET['token'])){
   $number = $_GET['number'];
   $user_id = $_GET['token'];

   try {
      // ডাটাবেজে ইউজার সঠিক আছে কি না তা যাচাই
      $select = $conn->prepare("SELECT * FROM `user_form` WHERE number = ? AND id = ?");
      $select->execute([$number, $user_id]);
      $fetch = $select->fetch(PDO::FETCH_ASSOC);

      if($fetch){
         // তথ্য সঠিক হলে সেশন শুরু হবে এবং অটো লগইন হয়ে যাবে
         $_SESSION['user_id'] = $fetch['id'];
         header('location: index.php');
         exit();
      } else {
         echo "<script>alert('অবৈধ QR কোড!'); window.location.href='login.php';</script>";
      }
   } catch (PDOException $e) {
      header('location: login.php');
      exit();
   }
} else {
   header('location: login.php');
   exit();
}
?>
