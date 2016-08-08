<?php

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
    $products = <<<EOD
    <div class="col-sm-4 col-lg-4 col-md-4">
      <div class="thumbnail">
        <a href="item.php?id={$row['id']}"><img src="{$row['product_image']}" alt=""></a>
        <div class="caption">
          <h4 class="pull-right">&#36;{$row['product_price']}</h4>
          <h4><a href="item.php?id={$row['id']}">{$row['product_title']}</a>
          </h4>
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
    $products = <<<EOD
    <div class="col-md-3 col-sm-6 hero-feature">
      <div class="thumbnail">
        <img src="{$row['product_image']}" alt="">
        <div class="caption">
          <h3>{$row['product_title']}</h3>
          <p>Lorem Ipsum</p>
          <p>
            <a href="#" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$row['id']}" class="btn btn-default">More Info</a>
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
    $products = <<<EOD
    <div class="col-md-3 col-sm-6 hero-feature">
      <div class="thumbnail">
        <img src="{$row['product_image']}" alt="">
        <div class="caption">
          <h3>{$row['product_title']}</h3>
          <p>Lorem Ipsum</p>
          <p>
            <a href="#" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$row['id']}" class="btn btn-default">More Info</a>
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

/*************************************ADMIN PRODUCTS*********************************/
function get_products_in_admin(){
  $query = query("SELECT * FROM products");
  confirm($query);
  
  while($row = fetch_array($query)){
    $products = <<<PRODUCTS
      <tr>
        <td>{$row['id']}</td>
        <td>{$row['product_title']}<br>
          <a href="index.php?edit_product&id={$row['id']}"><img src="{$row['product_image']}" alt=""></a>
        </td>
        <td>{$row['product_category_id']}</td>
        <td>{$row['product_price']}</td>
        <td>{$row['product_quantity']}</td>
        <td><a class="btn btn-danger" href="../../resources/templates/back/delete_product.php?id={$row['id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
      </tr>    
PRODUCTS;
    echo $products;    
  }
}