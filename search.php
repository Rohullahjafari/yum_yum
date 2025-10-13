<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

include 'components/add_cart.php';

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

<!-- search form section starts  -->

<section class="search-form">
   <form method="post" action="">
      <input type="text" name="search_box" placeholder="search here..." class="box" value="<?= isset($_POST['search_box']) ? htmlspecialchars($_POST['search_box'], ENT_QUOTES, 'UTF-8') : '' ?>">
      <button type="submit" name="search_btn" class="fas fa-search"></button>
   </form>
</section>

<!-- search form section ends -->


<section class="products" style="min-height: 100vh; padding-top:0;">

<div class="box-container">

      <?php
         if((isset($_POST['search_box']) && $_SERVER['REQUEST_METHOD'] === 'POST') || isset($_POST['search_btn'])){

            $search_box = trim($_POST['search_box'] ?? '');

            if($search_box === ''){
               echo '<p class="empty">please enter a search term!</p>';
            } else {
               try {
                  $search_param = "%{$search_box}%";

                  $select_products = $conn->prepare("SELECT * FROM `products` WHERE `name` LIKE ? OR `category` LIKE ? LIMIT 100");
                  $select_products->execute([$search_param, $search_param]);

                  $found = false;
                  while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
                     $found = true;

                     $pid = htmlspecialchars($fetch_products['id'], ENT_QUOTES, 'UTF-8');
                     $pname = htmlspecialchars($fetch_products['name'], ENT_QUOTES, 'UTF-8');
                     $pprice = htmlspecialchars($fetch_products['price'], ENT_QUOTES, 'UTF-8');
                     $pimage = htmlspecialchars($fetch_products['image'], ENT_QUOTES, 'UTF-8');
                     $pcat = htmlspecialchars($fetch_products['category'], ENT_QUOTES, 'UTF-8');

      ?>
      <form action="" method="post" class="box">
         <input type="hidden" name="pid" value="<?= $pid; ?>">
         <input type="hidden" name="name" value="<?= $pname; ?>">
         <input type="hidden" name="price" value="<?= $pprice; ?>">
         <input type="hidden" name="image" value="<?= $pimage; ?>">
         <a href="quick_view.php?pid=<?= $pid; ?>" class="fas fa-eye"></a>
         <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
         <img src="uploaded_img/<?= $pimage; ?>" alt="<?= $pname; ?>">
         <a href="category.php?category=<?= $pcat; ?>" class="cat"><?= $pcat; ?></a>
         <div class="name"><?= $pname; ?></div>
         <div class="flex">
            <div class="price"><span>$</span><?= $pprice; ?></div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
         </div>
      </form>
      <?php
                  } // end while

                  if(!$found){
                     echo '<p class="empty">no products found!</p>';
                  }

               } catch(PDOException $e){
                  error_log("Database error in search page: " . $e->getMessage());
                  echo '<p class="empty">Server error. Please try again later.</p>';
               }
            }
         }
      ?>

   </div>

</section>

<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
