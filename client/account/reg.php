<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client/forms.php");

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
$account = new Account();

$account->checkRegAuthAllowed();
$form = new Form();
$form=$form->regForm();
?>

<html>
    <head>
        <script src="../scripts/jquery_ajax.js"></script>
        <script src="../scripts/request.js"></script>

        <link rel="stylesheet" href="../styles/reg.css">
        <link rel="stylesheet" href="../styles/main.css">
    </head>

    <body class="body">

        <div class="main_form_div">

            <?php echo($form); ?>

        </div>
        
    </body>
    
    <script src="../scripts/main.js"></script>
    <script src="../scripts/reg.js"></script>
</html>