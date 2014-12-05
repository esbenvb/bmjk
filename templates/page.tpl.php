<!DOCTYPE html>
<html lang="da">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="images/favicon.ico">

    <title><?php if (isset($title)) print $title . ' - '; ?>Glædelig Jul / Merry Christmas - Berlingske Media</title>

    
    <link href="css/bootstrap.css" rel="stylesheet">
   
    <!--[if lte IE 9]>
      <link href="css/ie.css?ver3" rel="stylesheet">
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!--[if gte IE 9]>
    <style type="text/css">
      .gradient {
         filter: none;
      }
    </style>
    <![endif]-->
  </head>


<body class="<?php if (isset($body_classes)) print $body_classes; ?>">
<?php if (isset($top_menu)) print $top_menu; ?>
  


<div class="gradient"></div>
<div class="star">
    <div class="star-1"></div>
    <div class="star-2"></div>
    <div class="star-3"></div>
    <div class="star-4"></div>
    <div class="star-5"></div>
</div>

  
    <div class="container">
    <div class="module">
	    <div class="row">
		    <div class="col-md-12 greeting-container">
              <div class="greeting greeting-left">Glædelig jul<br>Godt nytår</div>
              <div class="greeting greeting-right relative">Merry Christmas<br>Happy New year</div>
    			</div>
	    </div>
    </div>


<?php if(isset($status)): ?>
  <div class="row status">
    <div class="col-md-12 text-center status-content">
      <div class="status-wrapper <?php if(isset($status_class)) print $status_class; ?>"><?php print $status; ?></div>  
    </div>
  </div>
<?php endif; ?>


    <div class="row main">
        
        
<div class="col-md-4 col-md-push-8 text-center tree">
      <img src="images/tree.png" class="img-responsive">
</div>
<div class="col-md-8 col-md-pull-4 relative card-content">        
    	<?php print $content; ?>
    </div>

</div>    

    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-push-8">
                   <div class="bem-logo"><a href="http://www.berlingskemedia.dk"></a></div>
                </div>
            </div>
        </div>

    </div>

    
    
<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'></script>
<script>window.jQuery || document.write('<script src="/dist/js/jquery-1.11.0.min.js"><\/script>')</script> 

<?php if(isset($footer)) print $footer; ?>
    
  </body>
</html>
