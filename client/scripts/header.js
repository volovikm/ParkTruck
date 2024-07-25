var auth_button = document.getElementById("auth_button");
var reg_button = document.getElementById("reg_button");
var logout_button = document.getElementById("logout_button");

if(auth_button!==null)
{
    redirectToButton(auth_button,'account/auth.php');
}

if(reg_button!==null)
{
    redirectToButton(reg_button,'account/reg.php');
}

if(logout_button!==null)
{
    redirectToButton(logout_button,'account/logout.php');
}