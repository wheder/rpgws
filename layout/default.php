<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title></title>
     </head>
    <body>
    	<div id="menu">
	<ul>
            <?php
                foreach($this->menu as $name => $link)
		{
		    echo "<li><a href=\"$link\">$name</a></li>\n";
		}
	    ?>
	</ul>
	</div>
	<div id="content">
            <?php echo $content ?>
	</div>
    </body>
</html>


