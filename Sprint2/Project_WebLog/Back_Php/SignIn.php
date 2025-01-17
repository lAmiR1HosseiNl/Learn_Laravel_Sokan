<?php
//class set up
class Login{
    var $email;
    var $password;        
    public $firstName;
    public $lastName;
    function __construct($email , $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
    function isAccountRegister()
    {   
        // Create connection
        $connection = new mysqli("localhost","root","");
        //Use the DB
        // Check connection
        if ($connection->connect_error) {
            die("اتصال با دیتابیس به مشکل خورده است" . $connection->connect_error);
            }
        //Use the DB
        try{
            $sql = "USE Blogs";
            if ($connection->query($sql) === TRUE){}
            //Search for Accounts
            $sql  = "   SELECT * FROM Users
            WHERE Email = '$this->email'";
            $retriveData = $connection->query($sql);
            if ($retriveData->num_rows>0){
                while($row = $retriveData->fetch_assoc()){
                    $passwordQuery = $row['Pass'];
                    $this->firstName = $row['FirstName'];
                    $this->lastName  = $row['LastName'];
                    $resultPasswordMatch = password_verify($this->password ,$passwordQuery);
                    return $resultPasswordMatch;
                    }
                }else{
                    return false;
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
    if(isset($_SESSION['userName']) == false){
        include ("../Front_Html/SignIn.html");
        if($_SERVER["REQUEST_METHOD"] == 'POST'){
            $email = $_POST['email'];
            $password = $_POST['password'].''.$_POST['email']; 
            $loginUser = new Login($email , $password);
            $isSamePerson = $loginUser -> isAccountRegister();
            if(empty($_POST["email"]) == true || empty($_POST["password"]) == true){
                    echo "<center style='margin-top:50px;'>فیلد ها نباید خالی باشند </center>";
            }if(!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){
                        echo "<center style='margin-top:50px;'> ایمیل به فرم درست وارد نشده است</center>";
            }if(strlen($_POST['password']) < 8 ){
                echo "<center style='margin-top:50px;'>رمز نمی تواند کم تر از ۸ حرف باشد </center>";
                        
            }elseif($isSamePerson === true){
                $_SESSION["userName"] = $loginUser->firstName;
                $_SESSION['lastName'] = $loginUser->lastName;
                $_SESSION['email'] = $email;
                header("Location: http://localhost/araeghi/Project_WebLog/Back_Php/index.php");
        }else{
            echo "<center style='margin-top:50px;'>نام یا رمز موجود درست نمی باشد</center>";
        }
            }   
        }else{
            header("Location: http://localhost/araeghi/Project_WebLog/Back_Php/index.php");
        }
?>