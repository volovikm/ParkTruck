<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client/forms.php");
$form = new Form();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
$account = new Account();

$user_data=$account->checkAuth();
$account->checkRequiredRegConfirm();
$form=$form->regConfirmForm($user_data['telephone']);
?>

<html>
    <head>
        <script src="../scripts/jquery_ajax.js"></script>
        <script src="../scripts/request.js"></script>
        <script src="../scripts/main.js"></script>

        <link rel="stylesheet" href="../styles/reg_confirm.css">
        <link rel="stylesheet" href="../styles/main.css">
    </head>

    <body class="body">

        <div class="main_form_div">

            <?php echo($form); ?>

        </div>
        
    </body>
    
</html>