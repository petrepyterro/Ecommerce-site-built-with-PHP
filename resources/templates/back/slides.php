<div class="row">

  <h3 class="bg-success"><?php display_message(); ?></h3>

  <div class="col-xs-3">

    <form action="" method="post" enctype="multipart/form-data">
      <?php add_slides(); ?>
      <div class="form-group">

        <input type="file" name="file">

      </div>

      <div class="form-group">
        <label for="slide_title">Slide Title</label>
        <input type="text" name="slide_title" class="form-control">

      </div>

      <div class="form-group">

        <input type="submit" name="add_slide">

      </div>

    </form>

  </div>


  <div class="col-xs-8">

    <img src="http://placehold.it/800x300" alt="" />


  </div>

</div><!-- ROW-->

<hr>

<h1>Slides Available</h1>

<div class="row">
  




</div>


