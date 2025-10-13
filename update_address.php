<?php
include 'components/connect.php';
session_start();

if(empty($_SESSION['user_id'])){
    header('Location: home.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $fields = ['flat','building','area','town','city','state','country','pin_code'];
    $parts = [];

    foreach($fields as $f){
        if(!empty($_POST[$f])){
            $parts[] = htmlspecialchars(trim($_POST[$f]), ENT_QUOTES, 'UTF-8');
        }
    }

    $address = implode(', ', $parts);

    try {
        $stmt = $conn->prepare("UPDATE users SET address = ? WHERE id = ?");
        if($stmt->execute([$address, $user_id])){
            header('Location: profile.php');
            exit;
        } else {
            $message[] = 'Could not save address. Please try again.';
        }
    } catch(PDOException $e){
        error_log("Database error in update_address: ".$e->getMessage());
        $message[] = 'Server error. Please try later.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>yumly</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <link rel="icon" href="images/favicon.png" type="image/x-icon">

</head>
<body>
   
<?php include 'components/user_header.php' ?>

<section class="form-container">

   <form action="" method="post">
      <h3>your address</h3>
      <input type="text" class="box" placeholder="flat no." required maxlength="50" name="flat">
      <input type="text" class="box" placeholder="building no." required maxlength="50" name="building">
      <input type="text" class="box" placeholder="area name" required maxlength="50" name="area">
      <input type="text" class="box" placeholder="town name" required maxlength="50" name="town">
      <input type="text" class="box" placeholder="city name" required maxlength="50" name="city">
      <input type="text" class="box" placeholder="state name" required maxlength="50" name="state">
      <input type="text" class="box" placeholder="country name" required maxlength="50" name="country">
      <input type="number" class="box" placeholder="pin code" required max="999999" min="0" maxlength="6" name="pin_code">
      <input type="submit" value="save address" name="submit" class="btn">
   </form>

</section>










<?php include 'components/footer.php' ?>







<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>