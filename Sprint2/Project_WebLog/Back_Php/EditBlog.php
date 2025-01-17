<?php
class EditBlog{
    var $topic;
    var $body ;
    function __construct($topic , $body)
    {
        $this->topic = $topic;
        $this->body  = $body;
    }
    function isTopicUnique(){
        try{
        // Create connection
        $connection = new mysqli("localhost","root","");
        //Use the DB
        // Check connection
        if ($connection->connect_error) {
            die("اتصال با دیتابیس به مشکل خورده است" . $connection->connect_error);
            }
        //Use the DB
        $sql = "USE Blogs";
        if ($connection->query($sql) === TRUE){}
        //Search from BlogId in DB
        $sql = "SELECT * FROM WebLogs
                WHERE Topic = '$this->topic';"; 
        $retriveData = $connection->query($sql);
        if ($retriveData->num_rows>0){
            return true;
        }else{
            return False;
        }
        }catch(Exception){
            echo 'مشکل در باز کردن DB';
        }
    }
    function setInputBoxes($cookline){
        try{
        // Create connection
        $connection = new mysqli("localhost","root","");
        //Use the DB
        // Check connection
        if ($connection->connect_error) {
            die("اتصال با دیتابیس به مشکل خورده است" . $connection->connect_error);
            }
        //Use the DB
        $sql = "USE Blogs";
        if ($connection->query($sql) === TRUE){}
        //Search from BlogId in DB
        $sql = "SELECT * FROM WebLogs
                WHERE BlogId = $cookline;"; 
        $retriveData = $connection->query($sql);
        if ($retriveData->num_rows>0){
            while($row = $retriveData->fetch_assoc()){
                return $row;
                }
            }
        }catch(EXCEPTION){
    }
}
    function setEditDB($rowInEdit){
        try{
            // Create connection
            $connection = new mysqli("localhost","root","");
            //Use the DB
            // Check connection
            if ($connection->connect_error) {
                die("اتصال با دیتابیس به مشکل خورده است" . $connection->connect_error);
                }
            //Use the DB
            $sql = "USE Blogs";
            if ($connection->query($sql) === TRUE){}
                //Edit Row
                $sql = "UPDATE WebLogs
                SET Topic = '$this->topic', Body='$this->body'
                WHERE BlogId = $rowInEdit;
                ";
                if ($connection->query($sql) === TRUE){}
                header("Location: http://localhost/araeghi/Project_WebLog/Back_Php/BlogShow.php");
            }catch(EXCEPTION){
                echo "مشکل در تغییر و ویرایش";
        }
    }
}
?>
<?php
    //start session
    session_start();
    if(isset($_SESSION['userName']) && isset($_COOKIE['blogEdit']) === true){
    $setEditDB = new EditBlog($topic , $body);
    $fillInputs = $setEditDB->setInputBoxes($_COOKIE['blogEdit']);
    echo "<H1><center><dev>{$_SESSION['userName']} {$_SESSION['lastName']}</dev></center></H1>";
    echo
    '<form action="../Back_Php/BlogShow.php" , method="post">
    <center><input type="submit" style="width: 100px;" value="خروج از اکانت" name="leaveAccount"></input></center>
    </form>';
    echo   "<!DOCTYPE html>
    <html lang='fa'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Blog Edit</title>
                    <H1><center><dev>به صفحه ادیت بلاگ خوش آمدید</dev></center></H1>
                    <H3><center><dev><a href='../Back_Php/index.php' style='margin: 0 0 0 0;'>خانه</a> </dev></center></H3>
        </head>
            <body>
                <div style='margin-top:5px; direction: rtl; display: flex; justify-content: center; align-items: center; list-style: none;'>
                    <form action='../Back_Php/EditBlog.php' method='post' >
                        <h2>
                            <p>
                                عنوان:
                                </br>
                                <input 
                                    style='margin-bottom: 50px;' type='text' name='topic' value='{$fillInputs['Topic']}'>
                                </input>
                            </p>
                        </h2>
                        <h2>
                            <p>
                                بدنه:
                                    </br>
                                <textarea style='height: 200px; width: 200px; margin-bottom: 50px;' name='body'>{$fillInputs['Body']}</textarea>
                            </p>
                        </h2>
                        <center><input type='submit' style='width: 100px;' value='ادیت بلاگ' name='Weblogcreation'></input></center>
                    </form>
                </div>
            </body>
    </html>";
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['leaveAccount'])){
        session_destroy();
        setcookie("blogEdit", "" , time() - 0 , '/');
        header("Location: http://localhost/araeghi/Project_WebLog/Back_Php/index.php");
    }elseif($_SERVER["REQUEST_METHOD"]=== "POST"){
        $topic = $_POST['topic'];
        $body = $_POST['body'];
        if(isset($topic)){
        }else{
            $topic = $fillInputs['Topic'];
        }
        if(isset($body)){
        }else{
            $body = $fillInputs['Body'];
        }
        $editBlog = new EditBlog($topic , $body);
        $uniqeTopic = !$editBlog->isTopicUnique();
        $isInputsFill = true;
        $isTopicHaveNumber = true;
        $isTopicRepeated = true;
        if(empty($topic)  || empty($body)){
            echo "<center style='margin-top:50px;'>فیلد ها نباید خالی باشند</center>";
            $isInputsFill = false;
        }if(!ctype_alpha(str_replace(' ', '', $topic)) == true)
        {
            $isTopicHaveNumber = false;
            echo "<center style='margin-top:50px;'>موضوع نباید شامل اعداد شود</center>";
        }
        if(($fillInputs['Topic'] !== $_POST['topic'] && $uniqeTopic === false)){
            $isTopicRepeated = false;
            echo "<center style='margin-top:50px;'>موضوع شما قبلا انتخاب شده است</center>";
        }
        elseif($isInputsFill && $isTopicHaveNumber && $isTopicRepeated){
            $editBlog->setEditDB($_COOKIE['blogEdit']);
        }else{
            echo "<center style='margin-top:50px;'>ادیت وبلاگ ناموفق بود مجددا تلاش کنید</center>";
        }
    }
    }else{
        header('Location: http://localhost/araeghi/Project_WebLog/Back_Php/index.php');
    }
?>