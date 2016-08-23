<?php

require_once '../../resources/config.php';

if (isset($_GET['delete_slide_id'])){
  $image_name_query = query("SELECT slide_image FROM slides WHERE id=" . escape_string($_GET['delete_slide_id']) . " LIMIT 1");
  confirm($image_name_query);
  $row = fetch_array($image_name_query);
  $target_path = UPLOAD_DIRECTORY . DS . $row['slide_image'];
  unlink($target_path);
  
  
  $query = query("DELETE FROM slides WHERE id=" . escape_string($_GET['delete_slide_id']));
  confirm($query);
  
  
  set_message("Slide Deleted");
  redirect("../../../public/admin/index.php?slides");
} else {
  redirect("../../../public/admin/index.php?slides");
}