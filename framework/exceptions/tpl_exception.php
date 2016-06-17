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
    <div style="margin: 15px;padding: 10px;border: 1px solid #ddd">
    	<h2><?php echo sprintf('[%s] %s in file: <code>%s</code> at line: <code>%s</code>', $errcode, $errtype, $errfile, $errline)?></h2>
    	<br/>
    	<h1><?php echo $errMessage?></h1>
    	<br/>
    	<?php if (!empty($errSql)):?>
    	<p><strong>SQL：</strong><?php echo $errSql?></p>
    	<p><strong>Binds: </strong><?php print_r($errBinds)?></p>
    	<?php endif;?>
    	<pre class="prettyprint lang-php">
    		<ol start="<?php echo $errStartLine?>" class="code-ol">
    		<?php foreach ($errSourceCode as $key => $value):?>
    		  <li class="line-<?php echo $errStartLine + $key?>"><?php echo $value?></li>
    		<?php endforeach?>
    		</ol>
    	</pre>
    	
    	<h2>Call Stack</h2>
    	<p>
    		<?php echo $errStack?>
    	</p>
    	
    	<?php include 'tpl_common_running_info.php'?>
    </div>
	</div>
    <!-- jQuery first, then Bootstrap JS. -->
    <script src="//cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/tether/1.3.2/js/tether.min.js"></script>
    <script src="//cdn.bootcss.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    var line = '<?php echo $errline?>';
    $('.line-' + line).addClass('highlight');
    </script>
  </body>
</html>