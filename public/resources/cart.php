<?php require_once '../../resources/config.php';?>
<?php
  if(isset($_GET['add'])){
    
    
    $query = query("SELECT * FROM products WHERE id=" . escape_string($_GET['add']));
    confirm($query);
    
    while($row = fetch_array($query)){
      if($row['product_quantity'] != $_SESSION['product_' . $_GET['add']]){
        $_SESSION['product_' . $_GET['add']] += 1;
        redirect("../checkout.php");
      } else {
        set_message("We only have {$row['product_quantity']} {$row['product_title']} available");
        redirect("../checkout.php");
      }
    }
    
    //$_SESSION['product_' . $_GET['add']] += 1;
    //redirect("index.php");
  }
  
  if(isset($_GET['remove'])){
    $_SESSION['product_' . $_GET['remove']]--;
    if($_SESSION['product_' . $_GET['remove']] <= 0){
      $_SESSION['product_' . $_GET['remove']] = '0';
      $_SESSION['item_total'] = 0;
      $_SESSION['item_quantity'] = 0;
      redirect("../checkout.php");
    } else {
      redirect("../checkout.php");
    }
  }
  
  if(isset($_GET['delete'])){
    $_SESSION['product_' . $_GET['delete']] = '0';
    $_SESSION['item_total'] = 0;
    $_SESSION['item_quantity'] = 0;
    redirect("../checkout.php");
  }
  
function cart(){
  $total = 0;
  $item_quantity = 0;
  $item_name = 1;
  $item_number = 1;
  $amount = 1;
  $quantity = 1;
  foreach ($_SESSION as $name => $value){
    if($value > 0){
      if(substr($name, 0, 8) == "product_"){
        $length = strlen($name - 8);
        $id = substr($name, 8, $length);
        $query = query("SELECT * FROM products WHERE id=" . escape_string($id));
        confirm($query);

        while($row = fetch_array($query)){
          $sub = $row['product_price']*$value;
          $item_quantity += $value;
          $product_image = display_image($row['product_image']);
          $product = <<<DELIMITER
          <tr>
            <td>
              {$row['product_title']}<br>
              <img width='100' src="resources/{$product_image}"/>
            </td>
            <td>&#36;{$row['product_price']}</td>
            <td>{$value}</td>
            <td>{$sub}</td>
            <td>
              <a class='btn btn-warning' href="resources/cart.php?remove={$row['id']}"><span class='glyphicon glyphicon-minus'></span></a>
              <a class='btn btn-success' href="resources/cart.php?add={$row['id']}"><span class='glyphicon glyphicon-plus'></span></a>
              <a class='btn btn-danger' href="resources/cart.php?delete={$row['id']}"><span class='glyphicon glyphicon-remove'></span></a>
            </td> 
          </tr>
          <input type="hidden" name="item_name_{$item_name}" value="{$row['product_title']}">
          <input type="hidden" name="item_number_{$item_number}" value="{$row['id']}">
          <input type="hidden" name="amount_{$amount}" value="{$row['product_price']}">
          <input type="hidden" name="quantity_{$quantity}" value="{$value}">   
DELIMITER;
          echo $product;
          $item_name++;
          $item_number++;
          $amount++;
          $quantity++;
        }
        $_SESSION['item_total'] = $total += $sub;
        $_SESSION['item_quantity'] = $item_quantity;
      }  
    } 
  }
  
  
}

function show_paypal(){
  if(isset($_SESSION['item_quantity']) && $_SESSION['item_quantity']>=1){
    $paypal_button = <<<BUTTON
      <input type="image" name="upload" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif"
        alt="PayPal - The safer, easier way to pay online">        
BUTTON;
    return $paypal_button;
  }
}

function process_transaction(){
  if(isset($_GET['tx'])){
    $amount = $_GET['amt'];
    $currency = $_GET['cc'];
    $transaction = $_GET['tx'];
    $status = $_GET['st'];
    $count_product_sessions = 0;
    
    foreach ($_SESSION as $name => $value){
      if($value > 0){      
        if(substr($name, 0, 8) == "product_"){
          $count_product_sessions ++;
          if($count_product_sessions == 1){
            $send_order=query("INSERT INTO orders (order_amount, order_transaction, order_status, order_currency) "
              . "VALUES('{$amount}', '{$transaction}', '{$status}', '{$currency}')");
            confirm($send_order);
    
            $last_id = last_id();
          }
          $length = strlen($name - 8);
          $id = substr($name, 8, $length);
          $query = query("SELECT * FROM products WHERE id=" . escape_string($id));
          confirm($query);

          while($row = fetch_array($query)){
            $insert_report=query("INSERT INTO reports (product_id, order_id, product_title, product_price, product_quantity) "
              . "VALUES('{$id}', {$last_id}, '{$row['product_title']}', '{$row['product_price']}', '{$value}')");
            confirm($insert_report);
          }
          unset($_SESSION[$name]);
        } 
      } 
    }
  } else {
    redirect("index.php");
  }
  
}
?>