<?php
require_once 'mysql.php';
$connection = new mysqli($hn, $un, $pw, $db);
if ($connection->connect_error) die($connection->connect_error);
echo <<<_END
        <center><form action='' method='post'>
        Username<input type='text' name='username'>
        <br>Password   <input type='password' name='password'>
        <br><input type='submit' name = 'click' value='Login'>
        </form></center>
_END;   

if (isset($_POST['username']) && isset($_POST['password']) &&
   $_POST['click'] === 'Login')
{
        
    $un_temp = mysql_entities_fix_string($connection, $_POST['username']);
    $pw_temp = mysql_entities_fix_string($connection, $_POST['password']);
    $query = "SELECT * FROM users WHERE username='$un_temp'";
    $result = $connection->query($query);
    if (!$result) die($connection->error);
    elseif ($result->num_rows) 
    {
        $row = $result->fetch_array(MYSQLI_NUM);
        $result->close();
        $salt1 = "qm&h*";
        $salt2 = "pg!@";
        $token = hash('ripemd128', "$salt1$pw_temp$salt2");
        if ($token === $row[3])
        {
        if (isset($_COOKIE['username']))
        {
            $temp = $_COOKIE['username'];
            setcookie('username',$temp, time() - 2592000,'/');
        }
        setcookie('username', $row[0], time() + 60 * 30, '/');
        echo <<<_END
            <center><form action='webapp.php' method='post' id='myform'>
            <body onload="document.forms['myform'].submit()">
            <input type='hidden' name='id' value = '$row[0]'>
            </body>
            </form></center>
        _END;

        }
        else die("Invalid username/password combination");
    }
    else die("Invalid username/password combination");
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