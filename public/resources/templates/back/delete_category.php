<?php

require_once '../../../../resources/config.php';

if (isset($_GET['id'])){
  $query = query("DELETE FROM categories WHERE id=" . escape_string($_GET['id']));
  confirm($query);
  
  set_message("Category Deleted");
  redirect("../../../admin/index.php?categories");
} else {
  redirect("../../../admin/index.php?categories");
}