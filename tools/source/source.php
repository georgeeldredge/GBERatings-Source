<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta name="google-site-verification" content="lSGP4VGCjCsSuYeRRi_8Gx_Dgw6NI0VpIwHKOlyLZYA" />

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!--<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />-->

<link rel="shortcut icon" href="/image/site/favicon.ico" type="image/jpg" />

<link href="/tools/style.css" rel="stylesheet" type="text/css" title="main" />

<script type="text/javascript" src="/tools/jscript/jquery.js"></script>

<?php
if (strstr($_SERVER['HTTP_USER_AGENT'],"MSIE 6")) { // GIVE IT THE SCREWY IE6 CSS
?>
	<link href="/tools/style.ie6.css" rel="stylesheet" type="text/css" title="main" />
    
	<script type="text/javascript" src="/tools/jscript/jquery.pngFix.pack.js"></script>
	<script type="text/javascript">
    	$(document).ready(function(){
        	$(document).pngFix();
	    });
	</script> 
<?php
}
?>

<title><?=$siteTitle?></title>

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-26144699-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>

</head>

<body>

<div id="container">

	<div id="header">
    	
        <h1><a href="/">GBE College Football Ratings</a></h1>
        
    </div>
    
    <div id="main">
    
    	<div id="content">
			<?=$source?>
		</div>
        
        <?php echo navMenu($pageActive); ?>
        
        <div class="clear"></div>            
        
    </div>
    
    <div id="footer">
    
    	<div id="contact">
			<a href="mailto:gbe@gberatings.com">Contact</a>
        </div>
        
        <div id="copyright">
			&copy; 2007-<?=date('Y');?> GBEWeb
        </div>
        
        <div class="clear"></div>
    
    </div>

</div>

</body>

</html>

<?php

require_once("tools/hitcounter.php");

?>