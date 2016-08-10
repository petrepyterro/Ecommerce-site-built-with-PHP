<?php
$uploads_directory = "uploads";
//helper functions

function redirect($location){
  header("Location: $location");
}

function query($sql){
  global $connection;
  
  return mysqli_query($connection, $sql);
}

function confirm($result){
  global $connection;
  
  if(!$result){
    die("QUERY FAILED. " . mysqli_error($connection));
  }
}

function escape_string($string){
  global $connection;
  
  return mysqli_real_escape_string($connection, $string);
}

function fetch_array($result){
  return mysqli_fetch_array($result);
}

function set_message($msg){
  if(!empty($msg)){
    $_SESSION['message'] = $msg;
  } else {
    $msg = "";
  }
}

function display_message(){
  if(isset($_SESSION['message'])){
    echo $_SESSION['message'];
    unset($_SESSION['message']);
  }
}

/*********************FRONT END FUNCTIONS*****************/

//get products
function get_products(){
  $query = query("SELECT * FROM products");
  confirm($query);
  
  while($row = fetch_array($query)){
    $product_image = display_image($row['product_image']);
    $products = <<<EOD
    <div class="col-sm-4 col-lg-4 col-md-4">
      <div class="thumbnail">
        <a href="item.php?id={$row['id']}"><img src="../resources/{$product_image}" alt=""></a>
        <div class="caption">
          <h4 class="pull-right">&#36;{$row['product_price']}</h4>
          <h4><a href="item.php?id={$row['id']}">{$row['product_title']}</a></h4>
          <p>See more snippets like this online store item at <a target="_blank" href="http://www.bootsnipp.com">Bootsnipp - http://bootsnipp.com</a>.</p>
          <a class="btn btn-primary" target="_blank" href="../resources/cart.php?add={$row['id']}">Add to cart</a>
        </div>
      </div>
    </div>
EOD;
    echo $products;
  }
}

function get_categories(){
  $query = query("SELECT * FROM categories");
  confirm($query);

  while($row = fetch_array($query)){
    $categories_links = <<<DELIMETER
      <a href='category.php?id={$row['id']}' class='list-group-item'>{$row['cat_title']}</a>
DELIMETER;
    echo $categories_links;  
  }
}

function get_products_by_category(){
  $query = query("SELECT * FROM products WHERE product_category_id=" . escape_string($_GET['id']));
  confirm($query);
  
  while($row = fetch_array($query)){
    $product_image = display_image($row['product_image']);
    $products = <<<EOD
    <div class="col-md-3 col-sm-6 hero-feature">
      <div class="thumbnail">
        <img width='100' src="../resources/{$product_image}" alt="">
        <div class="caption">
          <h3>{$row['product_title']}</h3>
          <p>Lorem Ipsum</p>
          <p>
            <a href="../resources/cart.php?add={$row['id']}" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$row['id']}" class="btn btn-default">More Info</a>
          </p>
        </div>
      </div>
    </div>
EOD;
    echo $products;
  }
}

function get_products_in_shop_page(){
  $query = query("SELECT * FROM products ");
  confirm($query);
  
  while($row = fetch_array($query)){
    $product_image = display_image($row['product_image']);
    $products = <<<EOD
    <div class="col-md-3 col-sm-6 hero-feature">
      <div class="thumbnail">
        <img width='100' src="../resources/{$product_image}" alt="">
        <div class="caption">
          <h3>{$row['product_title']}</h3>
          <p>
            <a href="../resources/cart.php?add={$row['id']}" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$row['id']}" class="btn btn-default">More Info</a>
          </p>
        </div>
      </div>
    </div>
EOD;
    echo $products;
  }
}

function get_category_name_in_category_page(){
  $query = query("SELECT cat_title FROM categories WHERE id=" . escape_string($_GET['id']));
  confirm($query);
  
  while($row = fetch_array($query)){
    $cat_title = ucfirst($row['cat_title']);
    echo $cat_title;
  }
}

function login_user(){
  if(isset($_POST['submit'])){
    $username = escape_string($_POST['username']);
    $password = escape_string($_POST['user_password']);
    
    $query = query("SELECT * FROM users WHERE username='$username' AND user_password='$password'");
    confirm($query);
    
    if(mysqli_num_rows($query) == 0){
      set_message("Your password or username are wrong");
      redirect("login.php");
    } else {
      $_SESSION['username'] = $username;
      set_message("Welcome to Admin $username");
      redirect("admin");
    }
  }
}

function sent_message(){
  if(isset($_POST['submit'])){
    $to         = "someEmailAddress@gmail.com";
    $from_name  = $_POST['name'];
    $email      = $_POST['email'];
    $subject    = $_POST['subject'];
    $message    = $_POST['message'];
    
    $headers = "From: {$from_name} {$email}";
    
    $result = mail($to, $subject, $message, $headers);
    
    if(!$result){
      set_message("Sorry we could not send your email");
      redirect("contact.php");
    } else {
      sent_message("Your message has been sent");
    }
  }
}

function last_id(){
  global $connection;
  return mysqli_insert_id($connection);
}



/*********************BACK END FUNCTIONS*****************/
function display_orders(){
  $query = query("SELECT * FROM orders");
  confirm($query);
  
  while($row = fetch_array($query)){
    $orders = <<<ORDERS
      <tr>
        <td>{$row['id']}</td>
        <td>{$row['order_amount']}</td>
        <td>{$row['order_transaction']}</td>
        <td>{$row['order_currency']}</td>
        <td>{$row['order_status']}</td>
        <td><a class="btn btn-danger" href="../../resources/templates/back/delete_order.php?id={$row['id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
      </tr>        
ORDERS;
    echo $orders;    
  }
}

/*************************************ADMIN PRODUCTS PAGE*********************************/
function display_image($picture){
  global $uploads_directory;
  return $uploads_directory . DS . $picture;
}

function get_products_in_admin(){
  $query = query("SELECT * FROM products");
  confirm($query);
  
  while($row = fetch_array($query)){
    $cat_title = !empty($row['product_category_id']) ? show_product_category_title($row['product_category_id']) : "";
    $product_image = display_image($row['product_image']);
    $products = <<<PRODUCTS
      <tr>
        <td>{$row['id']}</td>
        <td>{$row['product_title']}<br>
          <a href="index.php?edit_product&id={$row['id']}"><img width='100' src="../../resources/{$product_image}" alt=""></a>
        </td>
        <td>{$cat_title}</td>
        <td>{$row['product_price']}</td>
        <td>{$row['product_quantity']}</td>
        <td><a class="btn btn-danger" href="../../resources/templates/back/delete_product.php?id={$row['id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
      </tr>    
PRODUCTS;
    echo $products;    
  }
}

function show_product_category_title($product_category_id){
  $query = query("SELECT cat_title FROM categories WHERE id={$product_category_id}");
  confirm($query);
  
  while($row =  fetch_array($query)){
    return $row['cat_title'];
  }
}

/*************************************AD PRODUCTS IN ADMIN*********************************/
function add_product(){
  if(isset($_POST['publish'])){
    $product_title          = escape_string($_POST['product_title']);
    $product_category_id    = !empty($row['product_category_id']) ? escape_string($row['product_category_id']) : NULL;
    $product_description    = escape_string($_POST['product_description']);
    $product_short_desc     = escape_string($_POST['product_short_desc']);
    $product_price          = escape_string($_POST['product_price']);
    $product_quantity       = escape_string($_POST['product_quantity']);
    $product_image          = escape_string($_FILES['file']['name']);
    $image_temp_location    = escape_string($_FILES['file']['tmp_name']);
    
    move_uploaded_file($image_temp_location, UPLOAD_DIRECTORY . DS . $product_image);
    if($product_category_id){
      $query = query("INSERT INTO products(product_title, product_category_id, product_description, product_short_desc, product_price, product_quantity, product_image) VALUES('{$product_title}', {$product_category_id}, '{$product_description}', '{$product_short_desc}', '{$product_price}', '{$product_quantity}', '{$product_image}')");
    } else {
      $query = query("INSERT INTO products(product_title, product_description, product_short_desc, product_price, product_quantity, product_image) VALUES('{$product_title}', '{$product_description}', '{$product_short_desc}', '{$product_price}', '{$product_quantity}', '{$product_image}')");
    }
    confirm($query);
    $last_id = last_id();
    set_message("New Product with id {$last_id} was Added");
    redirect("index.php?products");
    
  }
}

function show_categories_add_product(){
  $query = query("SELECT * FROM categories");
  confirm($query);

  while($row = fetch_array($query)){
    $categories_options = <<<CATEGORIES
      <option value="{$row['id']}">{$row['cat_title']}</option>
CATEGORIES;
    echo $categories_options;  
  }
}

/*************************UPDATING PRODUCT CODE**************************/
function update_product(){
  if(isset($_POST['update'])){
    $product_title          = escape_string($_POST['product_title']);
    $product_category_id    = !empty($_POST['product_category_id']) ? escape_string($_POST['product_category_id']) : NULL;
    $product_description    = escape_string($_POST['product_description']);
    $product_short_desc     = escape_string($_POST['product_short_desc']);
    $product_price          = escape_string($_POST['product_price']);
    $product_quantity       = escape_string($_POST['product_quantity']);
    $product_image          = escape_string($_FILES['file']['name']);
    $image_temp_location    = escape_string($_FILES['file']['tmp_name']);
    
    if(empty($product_image)){
      $get_pic = query("SELECT product_image FROM products WHERE id=" . escape_string($_GET['id'])); 
      confirm($get_pic);
      
      while($row=fetch_array($get_pic)){
        $product_image = $row['product_image'];
      }
    }
    
    move_uploaded_file($image_temp_location, UPLOAD_DIRECTORY . DS . $product_image);
    if($product_category_id){
      $query = "UPDATE products SET ";
      $query .= "product_title        = '{$product_title}', ";
      $query .= "product_category_id  = '{$product_category_id}', ";
      $query .= "product_description  = '{$product_description}', ";
      $query .= "product_short_desc   = '{$product_short_desc}', ";
      $query .= "product_price        = {$product_price}, ";
      $query .= "product_quantity     = {$product_quantity}, ";
      $query .= "product_image        = '{$product_image}' ";
      $query .= "WHERE id=" . escape_string($_GET['id']);
      $query = query($query);
    } else {
      $query = "UPDATE products SET ";
      $query .= "product_title          = '{$product_title}', ";
      $query .= "product_description    = '{$product_description}', ";
      $query .= "product_short_desc = '{$product_short_desc}', ";
      $query .= "product_price = {$product_price}, ";
      $query .= "product_quantity = {$product_quantity}, ";
      $query .= "product_image = '{$product_image}' ";
      $query .= "WHERE id=" . escape_string($_GET['id']);
      $query = query($query);
    }
    confirm($query);
    
    set_message("Product has been updated");
    redirect("index.php?products");
    
  }
}
/*************************CATEGORIES IN ADMIN**************************/

function show_categories_in_admin(){
  $query = query("SELECT * FROM categories");
  confirm($query);
  
  while($row = fetch_array($query)){
    $cat_id = $row['id'];
    $cat_title = $row['cat_title'];
    
    $categories = <<<CATEGORIES
      <tr>
        <td>{$cat_id}</td>
        <td>{$cat_title}</td>
        <td><a class="btn btn-danger" href="../../resources/templates/back/delete_category.php?id={$row['id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
      </tr>        
CATEGORIES;
    echo $categories;    
  }
  
}

function add_category_in_admin(){
  if(isset($_POST['add_category'])){
    $cat_title = escape_string($_POST['cat_title']);
    if(!empty(trim($cat_title))){
      $query = query("INSERT INTO categories(cat_title) VALUES('{$cat_title}')");
      confirm($query);
      set_message("Category Created");
      redirect("index.php?categories");
    } else {
      echo "<p class='bg-danger'>THIS CANNOT BE EMPTY</p>";
    }
  }
}

/*************************ADMIN USERS**************************/
function show_users_in_admin(){
  $query = query("SELECT * FROM users");
  confirm($query);
  
  while($row = fetch_array($query)){
    $user_id = $row['id'];
    $username = $row['username'];
    $user_email = $row['user_email'];
    
    $users = <<<USERS
      <tr>
        <td>{$user_id}</td>
        <td>{$username}</td>
        <td>{$user_email}</td>
        <td><a class="btn btn-danger" href="../../resources/templates/back/delete_user.php?id={$row['id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
      </tr>        
USERS;
    echo $users;    
  }
}