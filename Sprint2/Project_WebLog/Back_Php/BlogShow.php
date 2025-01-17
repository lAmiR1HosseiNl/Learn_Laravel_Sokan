<?php
class Blogs{
    function showBlogs($searchTopic = '%%' , $searchBody = "%%" , $searchAuthuor = "%%" , $searchOnThree = false){
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
            //Check if the table is table is empty or not
            $sql ="SELECT * FROM WebLogs;";
            $retriveData = $connection->query($sql);
            if($retriveData->num_rows>0){
                if($searchOnThree === true){
                    //Join WebLogs(Topic Body) and Users(firstName lastName Email)
                    $sql = "SELECT WebLogs.BlogId,WebLogs.Topic,WebLogs.Body,Users.Email,Users.FirstName,Users.LastName 
                            FROM WebLogs
                            INNER JOIN Users ON WebLogs.UserId=Users.UserId
                            WHERE Topic LIKE '$searchTopic' 
                            OR Body LIKE '$searchBody'
                            OR CONCAT(Users.FirstName , ' ' , Users.LastName) LIKE'$searchAuthuor'
                            ;";
                }else{
                    //Join WebLogs(Topic Body) and Users(firstName lastName Email)
                    $sql = "SELECT WebLogs.BlogId,WebLogs.Topic,WebLogs.Body,Users.Email,Users.FirstName,Users.LastName 
                            FROM WebLogs
                            INNER JOIN Users ON WebLogs.UserId=Users.UserId
                            WHERE Topic LIKE '$searchTopic' 
                            AND Body LIKE '$searchBody'
                            AND CONCAT(Users.FirstName , ' ' , Users.LastName) LIKE'$searchAuthuor'
                            ;";
                }
                $retriveNewTable = $connection->query($sql);
                if ($retriveNewTable->num_rows>0){
                    while($row = $retriveNewTable->fetch_assoc()){
                        try{
                            $connectionCount = new mysqli("localhost","root","");
                            $sqlLikeCount = "USE Blogs";
                            if ($connectionCount->query($sqlLikeCount) === TRUE){}                     
                            $sqlLikeCount = "SELECT * FROM likes
                                             WHERE blog_id = '{$row["BlogId"]}'
                                             ;"; 
                            $retrivCount = $connectionCount->query($sqlLikeCount);
                            $numRows = mysqli_num_rows($retrivCount);
                        }catch(Exception){
                            $numRows = 0;
                        }

                        echo "<div style='margin: 100px; border: 10px solid darkorange;'>The Topic : {$row['Topic']} <br> The Body : {$row['Body']} <br> The auth : {$row['FirstName']} {$row['LastName']} <br> Likes : {$numRows}</div>";
                        if(isset($_SESSION['userName']) == true){
                            echo "<form action='../Back_Php/BlogShow.php' method='post'><center><input type='submit' style='width: 100px;' value='🩷' name='Like{$row['BlogId']}'></input></center></form>";   
                        }
                        if($row['Email'] === $_SESSION['email']){
                            echo "<form action='../Back_Php/BlogShow.php' method='post'><center><input type='submit' style='width: 100px;' value='حذف' name='del{$row['BlogId']}'></input></center></form>";
                            echo "<form action='../Back_Php/BlogShow.php' method='post'><center><input type='submit' style='width: 100px;' value='ویرایش' name='edi{$row['BlogId']}'></input></center></form>";
                        }
                        echo "<br>";
                    }
                }else{
                    echo  "<h2><center style='margin-top:50px;'>هنوز بلاگی یافت نشده است</center></h2>";
                }
            }else{
                echo  "<h2><center style='margin-top:50px;'>هنوز بلاگی ثبت نشده است</center></h2>";
            }
        }
        catch(Exception $e){
            //echo $e;
            echo "<h2><center style='margin-top:50px;'>هنوز بلاگی ثبت نشده است</center></h2>";
        }
    }
    function deleteBlog($rowToDelete){ 
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
            //Delete the spesefic row
            $sql = "DELETE FROM WebLogs WHERE BlogId=$rowToDelete;";
            if ($connection->query($sql) === TRUE){}
            header('Location: '.$_SERVER['REQUEST_URI']);
        }catch(Exception $e){
            //echo $e;
            echo "<h2><center style='margin-top:50px;'>حذف بلاگ ناموفق </center></h2>";
        }
    }
    function likeBlog($rowToLike){
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
            // Get my current UserId
            $sql = "SELECT UserId FROM Users
                    WHERE Email = '{$_SESSION['email']}' 
                    ;";
            $retriveData = $connection->query($sql);
            if($retriveData->num_rows>0){
                while($row = $retriveData->fetch_assoc()){
                    $personWhoLike = $row['UserId'];
                }
            }else{
                echo "How are you here without email????";
            }
            // Create a Likes DB
            $sql = "CREATE TABLE IF NOT EXISTS likes(
                like_id INT AUTO_INCREMENT,
                user_id  INT,
                blog_id  INT,
                PRIMARY KEY(like_id),
                FOREIGN KEY(user_id) REFERENCES Users(UserId),
                FOREIGN KEY(blog_id) REFERENCES WebLogs(BlogId) 
                ON DELETE CASCADE
                );";
            if ($connection->query($sql) === TRUE) {
            } else {
                echo "مشکل در ساخت جدول" . $connection->error;
            }
            // Insert Into sql or delete from sql on click
            $sql = "SELECT * FROM likes
                    WHERE user_id = '$personWhoLike' and blog_id = '$rowToLike'  
                    ;";
            $retriveData = $connection->query($sql);
            if ($retriveData->num_rows>0){
                $sql = "DELETE FROM likes 
                        WHERE user_id = '$personWhoLike' and blog_id = '$rowToLike'  
                        ;";
                if ($connection->query($sql) === TRUE){} 
            }else{
                $sql = "INSERT INTO likes(user_id , blog_id)
                VALUES ('$personWhoLike' , '$rowToLike')
                ;";
                if ($connection->query($sql) === TRUE){} 
            }
        }
        catch(Exception $e){
            //echo $e;
        }     
    }
}
?>
<?php
    session_start();
    if(isset($_SESSION['userName']) == true){
        echo "<H1><center><dev>{$_SESSION['userName']} {$_SESSION['lastName']}</dev></center></H1>";
        echo
        '<form action="../Back_Php/BlogShow.php" , method="post">
        <center><input type="submit" style="width: 100px;" value="خروج از اکانت" name="leaveAccount"></input></center>
        </form>';
    }
    include("../Front_Html/BlogShow.html");
    $blogs = new Blogs();
        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['leaveAccount'])){
            session_destroy();
            setcookie("blogEdit", "" , time() - 0 , '/');
            header("Location: http://localhost/araeghi/Project_WebLog/Back_Php/index.php");
        }elseif($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])){
            //echo $_POST['Topic'];
            if ($_POST['Topic'] === 'on'){
                $searchTopic =True;
            }
            if ($_POST['‌Body'] === 'on'){
                $searchBody =True;
            }
            if ($_POST['Authuor'] === 'on'){
                $searchAuthuor =True;
            }
            $searchText = "%{$_POST['searchText']}%";
            $blogs = new Blogs();
            if($searchTopic === true && $searchBody === true && $searchAuthuor === true){
            $blogs -> showBlogs($searchText ,$searchText , $searchText ,true);
            }elseif($searchTopic === true && $searchBody === true){
                echo "<h2><center style='margin-top:50px;'>یکی از ۳ تقسیم بندی یا هر ۳ تقسیم بندی را انتخاب کنید</center></h2>";
            }elseif($searchTopic === true && $searchAuthuor === true){
                echo "<h2><center style='margin-top:50px;'>یکی از ۳ تقسیم بندی یا هر ۳ تقسیم بندی را انتخاب کنید</center></h2>";
            }elseif($searchBody === true && $searchAuthuor === true){
                echo "<h2><center style='margin-top:50px;'>یکی از ۳ تقسیم بندی یا هر ۳ تقسیم بندی را انتخاب کنید</center></h2>";
            }elseif($searchTopic === true){
                $blogs -> showBlogs($searchText);
            }
            elseif($searchBody === true){
                $blogs -> showBlogs('%%' ,$searchText);
            }
            elseif($searchAuthuor === true){
                $blogs -> showBlogs('%%','%%',$searchText);
            }else{
                $blogs -> showBlogs();    
            }
        }elseif ($_SERVER['REQUEST_METHOD'] == "POST"){
        foreach ($_POST as $name => $value)
        {   
        if($name[0] == 'd'){
            $line = filter_var($name, FILTER_SANITIZE_NUMBER_INT); 
            $blogs->deleteBlog($line) ;
        }
        if($name[0] == 'e'){ 
            $line = filter_var($name, FILTER_SANITIZE_NUMBER_INT); 
            if(isset($_COOKIE['blogEdit'])){
                unset($_COOKIE['blogEdit']); 
                setcookie("blogEdit",$line,time() + (60 * 60) , '/');
                header("Location: http://localhost/araeghi/Project_WebLog/Back_Php/EditBlog.php");
            }else{
                setcookie("blogEdit",$line,time() + (60 * 60) , '/');
                header("Location: http://localhost/araeghi/Project_WebLog/Back_Php/EditBlog.php");
                }
            }
        if($name[0] == 'L'){
            $line = filter_var($name, FILTER_SANITIZE_NUMBER_INT); 
            $blogs = new Blogs();
            $blogs -> showBlogs();
            $blogs -> likeBlog($line);
            header('Location: '.$_SERVER['REQUEST_URI']);
            }
        }
    }else{
        $blogs = new Blogs();
        $blogs -> showBlogs();
    }
?>
