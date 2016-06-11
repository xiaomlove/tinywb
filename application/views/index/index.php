<div class="container">
    <div class="jumbotron">
      <h1 class="display-3">Hello, world!</h1>
      <p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
      <hr class="m-y-md">
      <p><strong><?php echo $info?>.</strong></p>
      <p><strong>layout: <?php echo $this->context->getLayoutFile()?>.</strong></p>
      <p><strong>view: <?php echo $this->context->getViewFile()?>.</strong></p>
      <p class="lead">
        <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>
      </p>
    </div>
</div>