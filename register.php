<?php
include 'components/connect.php';
session_start();

if (!empty($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $number = trim($_POST['number'] ?? '');
    $pass = $_POST['pass'] ?? '';
    $cpass = $_POST['cpass'] ?? '';

    $message = [];

    if ($name === '' || $email === '' || $number === '' || $pass === '' || $cpass === '') {
        $message[] = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'The email entered is not valid.';
    } elseif (!preg_match('/^\+?\d{7,15}$/', $number)) {
        $message[] = 'The phone number entered is not valid.';
    } elseif (strlen($pass) < 8) {
        $message[] = 'The password must be at least 8 characters long.';
    } elseif ($pass !== $cpass) {
        $message[] = 'The password confirmation does not match.';
    } else {
        try {
            $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR number = ?");
            $check->execute([$email, $number]);

            if ($check->fetch()) {
                $message[] = 'The email or phone number has already been registered.';
            } else {
                $plainPass = $pass;
                $insert = $conn->prepare("INSERT INTO users (name, email, number, password, address) VALUES (?, ?, ?, ?, ?)");
                $insert->execute([$name, $email, $number, $plainPass, '']);

                $userId = $conn->lastInsertId();

                session_regenerate_id(true);
                $_SESSION['user_id'] = $userId;

                header('Location: home.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log("Database error in register.php: " . $e->getMessage());
            $message[] = 'Server error. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>yumly-register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/favicon.png" type="image/x-icon">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container">

   <form action="" method="post">
      <h3>register now</h3>
      <input type="text" name="name" required placeholder="enter your name" class="box" maxlength="50">
      <input type="email" name="email" required placeholder="enter your email" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="number" name="number" required placeholder="enter your number" class="box" min="0" max="9999999999" maxlength="10">
      <input type="password" name="pass" required placeholder="enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="confirm your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="register now" name="submit" class="btn">
      <p>already have an account? <a href="login.php">login now</a></p>
   </form>

</section>











<?php include 'components/footer.php'; ?>







<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>