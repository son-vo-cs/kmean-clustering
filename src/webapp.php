<?php
    require_once 'mysql.php';
    require_once 'kmean.php';
    $conn = new mysqli($hn,$un,$pw,$db);
    if ($conn->connect_error) die($conn->connect_error);
    if (!isset($_COOKIE['username'])) die("Time out. You need to login again");
    $id = $_COOKIE['username'];
    echo <<<_END
        <form action='' method='post'>
        <input type='hidden' name='delete' value='yes'>
        <input type='submit' value='DELETE THE WHOLE DATABASE'></form>
_END;

    echo <<<_END
        <html><head><title>PHP Form Upload</title></head><body>
        <form method='post' action='' enctype='multipart/form-data'>
        Name: <input type='text' name='filename'>
        Input scores seperated by spaces: <input type='text' name='input'>
        Or Select File: <input type='file' name='path' size='10'>
            <input type='submit' value='Upload'>
            
        </form>
_END;
    if (isset($_POST['delete']))
    {
        $query = "DELETE FROM files WHERE userid = '$id'";
        $result = $conn->query($query);
        if (!$result) echo "DELETE failed: $query<br>".$conn->error . "<br><br>";
    }
    if (isset($_POST['filename']) && ($_FILES || isset($_POST['input'])) && strlen($_POST['filename']) > 0)
    {
        $content = "";
        if (isset($_POST['input']) && strlen($_POST['input']) > 0)
        {
            $ext = ".txt";
            $name = mysql_entities_fix_string($conn, $_POST['filename']).$ext;
            $content = mysql_entities_fix_string($conn, $_POST['input']);
            
        }
        else if ($_FILES)
        {

            $name = $_FILES['path']['name'];
            switch($_FILES['path']['type']) {
                case 'text/xml'     :$ext='.xml';break;
                case 'text/css'     :$ext='.css';break;
                case 'text/html'    :$ext='.html';break;
                case 'text/plain'   :$ext='.txt';break;
                default             :$ext='';break;
            }
            if ($ext)
            {   
                $name = mysql_entities_fix_string($conn, $_POST['filename']).$ext;
                move_uploaded_file($_FILES['path']['tmp_name'], $name);
                $content = file_get_contents($name);
            }
            else echo "$name is not an accepted text file";   
        }
        $valid = isValid($content,1);
        if (strlen($valid) > 0)
        {
            echo $valid. "<br>";
        }
        else if (checkValidateName($conn, $name,$id))
        {
            $query = "INSERT INTO files VALUES"."('$id','$name','$content')";
            $result = $conn->query($query);
            if (!$result) echo "INSERT failed: " . $conn->error . "<br><br>";
            echo "Succesfully added file into the database<br>";
        }
        else
        {
            echo "The file name is already existed!<br>";
        }

        
    }
    echo "</body></html>";
    
    $query = "SELECT * FROM files WHERE userid = '$id'";
    $result = $conn->query($query);
    if (!$result) die ("Database access failed: " .$conn->error);
    $num_char = 100;
    $space = 40;
    $rows = $result->num_rows;
    echo "<br>Name".str_repeat("&nbsp;",$space)."Content (First $num_char characters will be shown)<br>";
    for ($j = 0; $j < $rows; ++$j)
    {
        $result->data_seek($j);
        $row = $result->fetch_array(MYSQLI_NUM);
        $temp = substr($row[2],0,$num_char);
        echo "<br><br>$row[1]".str_repeat("&nbsp;",$space)."$temp";
        echo <<<_END
        <form action = "computeKmean.php" method="post">
        <input type="hidden" name="kmean" value ="yes">
        <input type="hidden" name="filename" value ="$row[1]">
        <input type="hidden" name="content" value="$row[2]">
        <input type="submit" value="COMPUTE KMEAN">
        </form>
    _END;
        
        
        
    }

    $result->close();
    $conn->close();

    function checkValidateName($connection, $filename, $userid)
    {
        
        $query = 'SELECT * FROM files WHERE userid = '.$userid.'  AND name = '. '"'.$filename.'"';
        $result = $connection->query($query);
        $rows = $result->num_rows;
        if ($rows > 0)
        {
            return false;
        }
        return true;
    }
    
    function mysql_entities_fix_string($connection, $string) 
    {
        return htmlentities(mysql_fix_string($connection, $string));
    }

    function mysql_fix_string($connection, $string) {
        if (get_magic_quotes_gpc()) $string = stripslashes($string);
        return $connection->real_escape_string($string);
    }
?>
    
