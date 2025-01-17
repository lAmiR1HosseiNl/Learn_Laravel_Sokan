<?php
// class setup
class SignUp{
        var $firstName ;
        var $lastName ;
        var $email ;
        var $password  ;
    //init 
    function __construct($firstName , $lastName ,$email , $password )
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password  = $password;
    }
    //add users to csv
    function registerAccounts(){
        try{
        // Create connection
        $connection = new mysqli("localhost", "root" , "");
        // Check connection
        if ($connection->connect_error) {
        die("اتصال با دیتابیس به مشکل خورده است" . $connection->connect_error);
        }
        //Create DB
        $sql = "CREATE DATABASE IF NOT EXISTS Blogs";
        if ($connection->query($sql) === TRUE) {
            } else {
            echo "مشکل در ساخت دیتابیس" . $connection->error;
            }
        //Use the DB
        $sql = "USE Blogs";
        if ($connection->query($sql) === TRUE) {
        }
        //Create Table User
        $sql = "CREATE TABLE IF NOT EXISTS Users(
                UserId INT AUTO_INCREMENT,
                Email  VARCHAR(50) NOT NULL UNIQUE ,
                FirstName   VARCHAR(50) NOT NULL,
                LastName    VARCHAR(50) NOT NULL,
                Pass        VARCHAR(255) NOT NULL,
                PRIMARY KEY(UserId)
                );";
        if ($connection->query($sql) === TRUE) {
        } else {
            echo "مشکل در ساخت جدول" . $connection->error;
        }
        // Create connection
        $connection = new mysqli("localhost", "root" , "");
        // Check connection
        if ($connection->connect_error) {
            die("اتصال با دیتابیس به مشکل خورده است" . $connection->connect_error);
        }
        //Use the DB
        $sql = "USE Blogs";
        if ($connection->query($sql) === TRUE) {
        }
        //Insert into Users Table
        $sql = "INSERT INTO Users(Email,FirstName,LastName,Pass)
            VALUES('{$this->email}','{$this->firstName}','{$this->lastName}','{$this->password}')
        ";
        if ($connection->query($sql) === TRUE) {
        } else {
            echo "مشکل در وارد کردن در جدول" . $connection->error;
        }
        $connection->close();
        
    }catch(Exception $e){
        echo "<center style='margin-top:50px;'>ثبت اکانت ناموفق بود</center>";
        }
    }
    //check for uniqueness fileOpenRead email in csv
    function isEmailUnique(){
        try{
        // Create connection
        $connection = new mysqli("localhost", "root" , "" , "Blogs");
        // Check connection
        if ($connection->connect_error) {
            die("اتصال با دیتابیس به مشکل خورده است" . $connection->connect_error);
            return false;
        }else{
        //Search for repeated emails
        $sql  = "SELECT * FROM Users
                WHERE Email = '$this->email'";
        $retriveData = $connection->query($sql);
        if ($retriveData->num_rows>0){
            return true;
        } else {
            return false;
        }
        $connection->close();
            }
        }catch(Exception $e){
            return false;
        }
    }
}
?>
<?php 
    //start session
    session_start();
    if ($_SESSION['userName'] == ''){
        include("../Front_Html/Signup.html");
        if($_SERVER["REQUEST_METHOD"] == 'POST'){
            $password = password_hash($_POST['password'].''.$_POST['email'] , PASSWORD_DEFAULT); 
            $accountSignUp = new Signup($_POST['firstName'] , $_POST['lastName'] , $_POST['email'] , $password);
            $uniqueEmail = $accountSignUp->isEmailUnique();
            //rules and restricted
            $notEmpty = (empty($_POST["firstName"]) == true || empty($_POST["lastName"]) == true || empty($_POST["email"]) == true || empty($_POST["password"]) == true || empty($_POST["repassword"]) == true);
            $notNumInName = (!ctype_alpha(str_replace(' ', '', $_POST["firstName"])) == true || !ctype_alpha(str_replace(' ', '', $_POST["lastName"])) == true);
            $notSamePass = ($_POST['password']!== $_POST['repassword']);
            $notValidMail = (!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL));
            $notEnoughLenName = (strlen($_POST['firstName']) < 6 || strlen($_POST['lastName']) < 6);
            $notEnoughLenPass = (strlen($_POST['password']) < 8) ;
            $notUniqueEmail =($uniqueEmail === true);
            if(($notEmpty)){
                echo "<center style='margin-top:50px;'>فیلد ها نباید خالی باشند </center>";
            }if($notNumInName){
                echo "<center style='margin-top:50px;'>نام و نام خانوادگی نباید شامل اعداد شود</center>";
            }if($notSamePass){
                echo "<center style='margin-top:50px;'> پسورد ها با هم برابر نیستند</center>";
            }if($notValidMail){
                echo "<center style='margin-top:50px;'> ایمیل به فرم درست وارد نشده است</center>";
            }if($notEnoughLenName){
                echo "<center style='margin-top:50px;'>نام و نام خانوادگی نمی تواند کم تر از ۶ حرف باشد </center>";
            }if($notEnoughLenPass){
                echo "<center style='margin-top:50px;'>رمز نمی تواند کم تر از ۸ حرف باشد </center>";
            }if($notUniqueEmail){
                echo "<center style='margin-top:50px;'>ایمیل قبلا استفاده شده است</center>";
            }
            if(($notEmpty || $notNumInName || $notSamePass || $notValidMail || $notEnoughLenName || $notEnoughLenPass || $notUniqueEmail) === false){
                $accountSignUp -> registerAccounts();
                $_SESSION["userName"] = $_POST['firstName'];
                $_SESSION['lastName'] = $_POST['lastName'];
                $_SESSION['email'] = $_POST['email'];
                header("Location: http://localhost/araeghi/public/index.php");
            }
        }
    }else{
        header("Location: http://localhost/araeghi/public/Page404");
    }
?>
