<?php 
 if(isset($_GET['id'])){
   $query = query("SELECT * FROM users WHERE id=" . escape_string($_GET['id']));
   confirm($query);
   
   while($row = fetch_array($query)){
    $username               = escape_string($row['username']);
    $user_email             = escape_string($row['user_email']);
    $user_password          = escape_string($row['user_password']);
    $user_photo             = display_image(escape_string($row['user_photo']));
   }   
 } else {
   redirect("index.php?users");
 }
 
 
?>
<h1 class="page-header">
    Edit User
    <small>Edwin</small>
    <?php update_user(); ?>
</h1>

<div class="col-md-6 user_image_box">
                          
  <a href="#" data-toggle="modal" data-target="#photo-library"><img class="img-responsive" src="" alt=""></a>

</div>


<form action="" method="post" enctype="multipart/form-data">
  <div class="col-md-6">

    <div class="form-group">

      <input type="file" name="file">
      <img width="200" src="../../resources/<?php echo $user_photo; ?>" alt=""/>

    </div>


     <div class="form-group">
      <label for="username">Username</label>
      <input type="text" name="username" class="form-control"  value="<?php echo $username; ?>">
     </div>


    <div class="form-group">
      <label for="user_email">Email</label>
      <input type="text" name="user_email" class="form-control"  value="<?php echo $user_email; ?>">
    </div>


    <div class="form-group">
      <label for="user_password">Password</label>
      <input type="user_password" name="user_password" class="form-control">

    </div>

    <div class="form-group">

      <a id="user-id" class="btn btn-danger" href="">Delete</a>

      <input type="submit" name="update_user" class="btn btn-primary pull-right" value="Update" >

    </div>

  </div>

</form>





    