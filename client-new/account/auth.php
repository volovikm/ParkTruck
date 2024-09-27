<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client-new/forms.php");
$form = new Form();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
$account = new Account();

$account->checkRegAuthAllowed();
$form=$form->authForm();
?>

<html>
    <head>
        <script src="../scripts/jquery_ajax.js"></script>
        <script src="../scripts/request.js"></script>

        <link rel="stylesheet" href="../styles/auth.css">
        <link rel="stylesheet" href="../styles/main.css">
    </head>

    <body>

        <div class="main_form_div">

            <?php echo($form); ?>

        </div>
        
    </body>
    
    <script src="../scripts/main.js"></script>
</html>