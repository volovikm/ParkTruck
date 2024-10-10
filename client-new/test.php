<?php
function myFunc()
{
static $id =
1;
$id++;
echo $id;
}
myFunc();
myFunc();
myFunc();