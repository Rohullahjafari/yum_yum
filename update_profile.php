<?php
include 'components/connect.php';
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $number = trim($_POST['number'] ?? '');
    $old_pass = $_POST['old_pass'] ?? '';
    $new_pass = $_POST['new_pass'] ?? '';
    $confirm_pass = $_POST['confirm_pass'] ?? '';

    if ($name === '' || $email === '' || $number === '') {
        $message[] = 'Name, email and phone number are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'The email entered is not valid.';
    }

    if (!preg_match('/^\+?\d{7,15}$/', $number)) {
        $message[] = 'The phone number is not valid.';
    }

    if (!empty($message)) {
    } else {
        try {
            if ($name !== '') {
                $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
                $stmt->execute([$name, $user_id]);
            }

            if ($email !== '') {
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && $row['id'] != $user_id) {
                    $message[] = 'The email has already been used.';
                } else {
                    $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
                    $stmt->execute([$email, $user_id]);
                }
            }

            if ($number !== '') {
                $stmt = $conn->prepare("SELECT id FROM users WHERE number = ?");
                $stmt->execute([$number]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && $row['id'] != $user_id) {
                    $message[] = 'The phone number has already been used.';
                } else {
                    $stmt = $conn->prepare("UPDATE users SET number = ? WHERE id = ?");
                    $stmt->execute([$number, $user_id]);
                }
            }

            if ($old_pass !== '' || $new_pass !== '' || $confirm_pass !== '') {
                if ($old_pass === '' || $new_pass === '' || $confirm_pass === '') {
                    $message[] = 'Please fill in all three fields to change your password.';
                } elseif ($new_pass !== $confirm_pass) {
                    $message[] = 'The new password and its confirmation do not match.';
                } elseif (strlen($new_pass) < 8) {
                    $message[] = 'The new password must be at least 8 characters long.';
                } else {
                    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $currentPass = $row['password'] ?? '';

                    if ($old_pass !== $currentPass) {
                        $message[] = 'The current password is incorrect.';
                    } else {
                        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                        $stmt->execute([$new_pass, $user_id]);
                        $message[] = 'The password has been successfully updated.';
                    }
                }
            }

        } catch (PDOException $e) {
            error_log("Database error in profile_update: " . $e->getMessage());
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
   <title>yumly</title>

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

<section class="form-container update-form">

   <form action="" method="post">
      <h3>update profile</h3>
      <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" class="box" maxlength="50">
      <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="number" name="number" placeholder="<?= $fetch_profile['number']; ?>" class="box" min="0" max="9999999999" maxlength="10">
      <input type="password" name="old_pass" placeholder="enter your old password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="enter your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" placeholder="confirm your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" name="submit" class="btn">
   </form>

</section>










<?php include 'components/footer.php'; ?>






<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>