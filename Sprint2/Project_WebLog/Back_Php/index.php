<?php
    session_start();
    if(isset($_SESSION['userName']) == true){
        echo "<H1><center><dev>{$_SESSION['userName']} {$_SESSION['lastName']}</dev></center></H1>";
        include ("../Front_Html/index_login.html");
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            session_destroy();
            setcookie("blogEdit", "" , time() - 0 , '/');
            header("Location: http://localhost/araeghi/Project_WebLog/Back_Php/index.php");
        }
    }else{
        include ("../Front_Html/index.html");
    }
?>