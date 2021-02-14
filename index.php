<?php

define('APP_ACCESS', true);
require_once './suggest.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php echo SITE_NAME; ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="dnw-gd-suggest.css">
</head>

<body>
    <div class="container-fluid text-center">

        <h1><?php echo SITE_NAME; ?></h1>

        <?php echo $msg; ?>

        <form action="" method="post" id="searchform">
            <h3>Enter Keywords Only Separated By Space</h3>
            <input class="form-control" type="text" placeholder="example: ac repair" name="domain" value="<?php echo @rtrim($_POST['domain']); ?>" style="margin: 5px 0px;">
            <input type="submit" name="submit" value="Search" class="btn btn-lg btn-primary btn-block">
        </form>

        <?php
            if($searchDNAvailable){
                echo printDomainResults($searchdn,'search');
            }
            $domainsAvailable = 1;
        ?>
    </div>
</body>
</html>