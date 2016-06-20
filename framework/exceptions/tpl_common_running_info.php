<br/>
<h5>Matched Route: <?php print_r(app('route')->getMatchedRoute())?></h5>
<br/>
<h2>Total Cost Time: <?php echo number_format(microtime(true) - APP_START_TIME, 4)?> seconds. Memory Used: <?php echo number_format(memory_get_peak_usage(true)/1024, 2)?> KB</h2>
<br/>
<ul class="nav nav-tabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" href="#server" role="tab" data-toggle="tab">$_SERVER</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#include-file" role="tab" data-toggle="tab">included files</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#constants" role="tab" data-toggle="tab">contants</a>
  </li>
  
  <li class="nav-item">
    <a class="nav-link" href="#request" role="tab" data-toggle="tab">$_REQUEST</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#get" role="tab" data-toggle="tab">$_GET</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#post" role="tab" data-toggle="tab">$_POST</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#session" role="tab" data-toggle="tab">$_SESSION</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#cookie" role="tab" data-toggle="tab">$_COOKIE</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#files" role="tab" data-toggle="tab">$_FILES</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#env" role="tab" data-toggle="tab">$_ENV</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#sqls" role="tab" data-toggle="tab">SQLS</a>
  </li>
</ul>

<!-- Tab panes -->
<br/>
<div class="tab-content">
  <div role="tabpanel" class="tab-pane fade in active" id="server">
    <table>
        <tbody>
            <?php foreach ($_SERVER as $key => $value):?>
            <tr><td  width="350px"><?php echo $key?></td><td><?php echo $value?></td></tr>
            <?php endforeach;?>
        </tbody>
    </table>
  </div>
  
  <div role="tabpanel" class="tab-pane fade" id="include-file">
    <ol>
        <?php foreach (get_included_files() as $file):?>
        <li><?php echo $file?></li>
        <?php endforeach?>
    </ol>
  </div>
  
  <div role="tabpanel" class="tab-pane fade" id="constants">
    <table>
        <tbody>
            <?php foreach (get_defined_constants(true)['user'] as $key => $value):?>
            <tr><td  width="200px"><?php echo $key?></td><td><?php echo $value?></td></tr>
            <?php endforeach;?>
        </tbody>
    </table>
  </div>
  
  <div role="tabpanel" class="tab-pane fade" id="request">
    <table>
        <tbody>
            <?php foreach ($_REQUEST as $key => $value):?>
            <tr><td width="100px"><?php echo $key?></td><td><?php echo $value?></td></tr>
            <?php endforeach;?>
        </tbody>
    </table>
  </div>
  
  <div role="tabpanel" class="tab-pane fade" id="get">
    <table>
        <tbody>
            <?php foreach ($_GET as $key => $value):?>
            <tr><td width="100px"><?php echo $key?></td><td><?php echo $value?></td></tr>
            <?php endforeach;?>
        </tbody>
    </table>
  </div>
  
  <div role="tabpanel" class="tab-pane fade" id="post">
    <table>
        <tbody>
            <?php foreach ($_POST as $key => $value):?>
            <tr><td width="100px"><?php echo $key?></td><td><?php echo $value?></td></tr>
            <?php endforeach;?>
        </tbody>
    </table>
  </div>
  
  
  <div role="tabpanel" class="tab-pane fade" id="session">
  <?php if (isset($_SESSION)):?>
    <table>
        <tbody>
            <?php foreach ($_SESSION as $key => $value):?>
            <tr><td width="100px"><?php echo $key?></td><td><?php print_r($value)?></td></tr>
            <?php endforeach;?>
        </tbody>
    </table>
  <?php endif?>
  </div>
  
  <div role="tabpanel" class="tab-pane fade" id="cookie">
    <table>
        <tbody>
            <?php foreach ($_COOKIE as $key => $value):?>
            <tr><td width="100px"><?php echo $key?></td><td><?php echo $value?></td></tr>
            <?php endforeach;?>
        </tbody>
    </table>
  </div>
  
  <div role="tabpanel" class="tab-pane fade" id="files">
    <table>
        <tbody>
            <?php foreach ($_FILES as $key => $value):?>
            <tr><td width="100px"><?php echo $key?></td><td><?php print_r($value)?></td></tr>
            <?php endforeach;?>
        </tbody>
    </table>
  </div>
  
  <div role="tabpanel" class="tab-pane fade" id="env">
    <table>
        <tbody>
            <?php foreach ($_ENV as $key => $value):?>
            <tr><td width="100px"><?php echo $key?></td><td><?php echo ($value)?></td></tr>
            <?php endforeach;?>
        </tbody>
    </table>
  </div>
  
  
  
</div>
