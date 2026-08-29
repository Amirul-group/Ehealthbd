<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){

   $number = $_POST['number'];
   $pass = md5($_POST['password']);

   // PDO Prepared Statement ব্যবহার করা হয়েছে
   $stmt = $conn->prepare("SELECT * FROM `user_form` WHERE number = :number AND password = :pass");
   $stmt->execute([
       ':number' => $number,
       ':pass'   => $pass
   ]);

   $user = $stmt->fetch();

   if($user){
      $_SESSION['user_id'] = $user['id'];
      header('location:index.php');
      exit();
   }else{
      $message[] = 'incorrect number or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="I=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>login now</h3>
      <?php
      if(isset($message)){
         foreach($message as $msg){
            echo '<div class="message">'.$msg.'</div>';
         }
      }
      ?>
      <input type="number" name="number" placeholder="enter number" class="box" required>
      <input type="password" name="password" placeholder="enter password" class="box" required>
      <input type="submit" name="submit" value="login now" class="btn">
      <p>don't have an account? <a href="register.php">register now</a></p>
   </form>

</div>

</body>
</html>