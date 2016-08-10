<?php

require_once '../../config.php';

if (isset($_GET['id'])){
  $query = query("DELETE FROM users WHERE id=" . escape_string($_GET['id']));
  confirm($query);
  
  set_message("Product Deleted");
  redirect("../../../public/admin/index.php?users");
} else {
  redirect("../../../public/admin/index.php?users");
}

