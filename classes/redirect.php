<?php

class Redirect 
{
    public $index="ParkTruck/index.php";

    public function redirectTo($page) 
    {
        header("Location: /".$page);
    }
}

?>