<?php
class CreateBlog{
    var $topic;
    var $body;
    var $author;
    var $accountCreated;
    function __construct($topic , $body , $author , $accountCreated)
    {
        $this->topic = $topic;
        $this->body = $body;
        $this->author = $author;
        $this->accountCreated = $accountCreated;
    }
    function saveBlogs($tags){
    //fputcsv($fileOpenWrite , ["Topic" , "Body" , "Author","Account"],";");
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
        //Check for current User Id
        //Create Table
        $sql = 
            "CREATE TABLE IF NOT EXISTS WebLogs(
            BlogId INT AUTO_INCREMENT,
            Topic  VARCHAR(50) NOT NULL UNIQUE ,
            Body   VARCHAR(50) Not Null,
            UserId INT,
            PRIMARY KEY (BlogId),
            FOREIGN KEY (UserId) REFERENCES Users(UserId)
            );";
        if ($connection->query($sql) === TRUE) {}
        // Create Tags Table
        $sql = 
            "CREATE TABLE IF NOT EXISTS tags(
                tag_id INT AUTO_INCREMENT,
                tag_name VARCHAR(50) UNIQUE,
                PRIMARY KEY (tag_id)
                );";
        if ($connection->query($sql) === TRUE) {}
        // Create weblogs_tags
        $sql = 
        "CREATE TABLE IF NOT EXISTS weblogs_tags(
            tag_id INT ,
            BlogId INT,
            FOREIGN KEY (tag_id) REFERENCES tags(tag_id),
            FOREIGN KEY (BlogId) REFERENCES WebLogs(BlogId)
            ON DELETE CASCADE
            );";
        if ($connection->query($sql) === TRUE) {}
        //Find User Id of the person that logged in
        $email = $_SESSION['email'];
        $sql  = " SELECT * FROM Users
        WHERE Email = '$email'";
        $retriveData = $connection->query($sql);
        if ($retriveData->num_rows>0){
            while($row = $retriveData->fetch_assoc()){
                $UserId = $row['UserId'];
            }
        }
        //Insert into Users Table
        $sql = "INSERT INTO WebLogs(Topic,Body,UserId)
        VALUES('{$this->topic}','{$this->body}','{$UserId}')";
            if ($connection->query($sql) === TRUE) {
            } else {
                echo "مشکل در وارد کردن در جدول" . $connection->error;
            }
        //Insert Into tags table and weblogs_tags table
        $tags = explode(',',$tags);
        //echo count($tags);
        try{
            // INSERT into tags table
            for ($i=0;$i < count($tags);$i++){
                $sql = "INSERT IGNORE INTO tags(tag_name)
                        VALUES ('{$tags[$i]}')
                ;";
                if ($connection->query($sql) === TRUE) {}
                // find my blog id of current blog 
                $sql = "SELECT BlogId FROM WebLogs
                        WHERE  Topic = '$this->topic'
                ;";
                if ($connection->query($sql) === TRUE) {}
                $retriveDataBlogId = $connection->query($sql);
                if ($retriveDataBlogId->num_rows>0){
                    while($row = $retriveDataBlogId->fetch_assoc()){
                        $blogId = $row['BlogId'];
                    }
                }
                // find my current tag id 
                $sql = " SELECT tag_id FROM tags
                         WHERE  tag_name = '{$tags[$i]}'
                        ;";
                $retriveDataTagId = $connection->query($sql);
                if ($retriveDataTagId->num_rows>0){
                    while($row = $retriveDataTagId->fetch_assoc()){
                        $tagId = $row['tag_id'];
                    }
                }
                $sql = "INSERT IGNORE INTO weblogs_tags(tag_id,BlogId)
                        VALUES ('{$tagId}','{$blogId}')
                ;";
                if ($connection->query($sql) === TRUE) {}
            }
        }catch(Exception $e){
            echo $e;
        }
        $connection->close();    
        }catch(Exception $e){
            return "<center style='margin-top:50px;'>ثبت وبلاگ ناموفق بود مجددا تلاش کنید</center>";
        }
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
                //Search for Accounts
                $sql  = " SELECT * FROM WebLogs
                WHERE Topic = '$this->topic'";
                $retriveData = $connection->query($sql);
                if ($retriveData->num_rows>0){
                    return true;
                }else{
                    return false;
                }   
        }catch(Exception $e){
            return false;
        }
        $connection->close(); 
    }
}   
?>
<?php
    session_start();
    if(isset($_SESSION['userName']) == true){
    echo "<H1><center><dev>{$_SESSION['userName']} {$_SESSION['lastName']}</dev></center></H1>";
    echo
    '<form action="../Back_Php/BlogCreation.php" , method="post">
    <center><input type="submit" style="width: 100px;" value="خروج از اکانت" name="leaveAccount"></input></center>
    </form>';
    include ("../Front_Html/BlogCreation.html");
        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['leaveAccount'])){
            session_destroy();
            setcookie("blogEdit", "" , time() - 0 , '/');
            header("Location: http://localhost/araeghi/Project_WebLog/Back_Php/index.php");
        }
        elseif($_SERVER["REQUEST_METHOD"] == "POST"){
            $tags = $_POST['tags'];
            $topic = $_POST["topic"];
            $body = $_POST["body"];
            $author = $_SESSION['userName'] . " " . $_SESSION['lastName'];
            $accountCreated = $_SESSION['email'];
            $weblog = new CreateBlog($topic , $body , $author , $accountCreated);
            $isTopicUnique =  $weblog->isTopicUnique();
            if(empty($_POST["topic"]) == true || empty($_POST["body"]) == true){
                echo "<center style='margin-top:50px;'>فیلد ها نباید خالی باشند </center>";
            }
            if(!ctype_alpha(str_replace(' ', '', $_POST["topic"])) == true){
                echo "<center style='margin-top:50px;'>موضوع نباید شامل اعداد شود</center>";
            }
            if (!preg_match('/^(?:[a-zA-Z]+(?:,[a-zA-Z]+)*)?$/', $tags)) {
                echo "<center style='margin-top:50px;'> firstTag,SecondTag : تگ ها باید به فرم درست وارد شود به طور مثال </center>";
            }
            if($isTopicUnique === true){
                echo "<center style='margin-top:50px;'>موضوع شما قبلا انتخاب شده است</center>";
            }
            elseif(ctype_alpha(str_replace(' ', '', $_POST["topic"])) == true){
            $weblog -> saveBlogs($tags);
            echo "<center style='margin-top:50px;'>وبلاگ شما با موقیت ثبت شد</center>";
            }else{
                echo "<center style='margin-top:50px;'>ثبت وبلاگ ناموفق بود مجددا تلاش کنید</center>";
            }}}else{
                header("Location: http://localhost/araeghi/Project_WebLog/Back_Php/index.php");
            }
?>
        