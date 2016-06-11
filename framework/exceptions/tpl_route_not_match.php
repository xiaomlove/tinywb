<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <!-- Bootstrap CSS -->
    <link href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/tether/1.3.2/css/tether.min.css" rel="stylesheet">
    <style>
      .lang-php {margin: 0;border: none !important;}
      .code-ol {font-size: 0; background-color: blanchedalmond;margin: 0;}
      .code-ol li {font-size: 14px;background-color:gainsboro}
      li.highlight {background-color: lightcoral}
    </style>
  </head>
  <body>
    <div class="container-fluid">
    
    <?php if ($statusCode === 404):?>
    <h1 style="margin-top: 20%;text-align: center">404 NOT FOUND!</h1>
    <?php else:?>
    <div style="margin: 15px;padding: 10px;border: 1px solid #ddd">
    <h1><?php echo $message?></h1>
    <p><?php echo $trace?></p>
    <h2>
     the full URL is: <?php echo $fullUrl?>
     <?php if (!empty($toMatchPath)):?>
     , need to match path: <?php echo $toMatchPath?>
     <?php endif?>
    </h2>
    
    <?php if (!empty($maps)):?>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>index</th>
                <th>URL</th>
                <th>controller@action</th>
                <th>options</th>
                <th>reason</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($maps as $kk => $map):?>
            <tr>
                <td><?php echo $kk + 1?></td>
                <td><?php echo $map['url']?></td>
                <td><?php echo sprintf("%s@%s", $map['controller'], $map['action'])?></td>
                <td><?php print_r($map['options'])?></td>
                <td><?php echo $map['reason']?></td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <?php endif?>
    
	<?php include 'tpl_common_running_info.php'?>
	</div>
	<?php endif?>
	</div>
    <!-- jQuery first, then Bootstrap JS. -->
    <script src="//cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/tether/1.3.2/js/tether.min.js"></script>
    <script src="//cdn.bootcss.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js"></script>
  </body>
</html>