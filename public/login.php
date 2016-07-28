<?php require_once '../resources/config.php';?>
<?php include TEMPLATE_FRONT . DS . 'header.php';?>

  <!-- Page Content -->
    <div class="container">

      <header>
        <h1 class="text-center">Login</h1>
        <div class="col-sm-4 col-sm-offset-5">         
          <form class="" action="" method="post">
            <div class="form-group"><label for="">
              username<input type="text" name="username" class="form-control"></label>
            </div>
             <div class="form-group"><label for="user_password">
              Password<input type="text" name="user_password" class="form-control"></label>
            </div>

            <div class="form-group">
              <input type="submit" name="submit" class="btn btn-primary" >
            </div>
          </form>
        </div>  


      </header>


    </div>

  </div>
  <!-- /.container -->

  <?php include TEMPLATE_FRONT . DS . 'footer.php';?>  
