<?php
require_once 'mysql.php';
$connection = new mysqli($hn, $un, $pw, $db);
if ($connection->connect_error) die($connection->connect_error);
echo <<<_END
        <center><form action='' method='post'><pre>
        Username <input type='text' name='username'>
        Password <input type='password' name='password'>
        Email    <input type='text' name='email'>
        <input type='submit' name = 'click' value='Signup'></pre>
        </form></center>
_END;   

if (isset($_POST['username']) && isset($_POST['password']) &&
   isset($_POST['email']) && $_POST['click'] === 'Signup')
{
        
    $un_temp = mysql_entities_fix_string($connection, $_POST['username']);
    $pw_temp = mysql_entities_fix_string($connection, $_POST['password']);
    $em_temp = mysql_entities_fix_string($connection, $_POST['email']);
    
    $fail = validateUsername($_POST['username']);
    $fail = $fail. validatePassword($_POST['password']);
    $fail = $fail. validateEmail($_POST['email']);
    if ($fail=="")
    {

        $query = "SELECT * FROM users WHERE username='$un_temp'";
        $query2= "SELECT * FROM users WHERE email='$em_temp'";
        $result = $connection->query($query);
        if (!$result ) die($connection->error);
        $result2 = $connection->query($query2);
        if (!$result2 ) die($connection->error);
        $rows = $result->num_rows;
        $rows2 = $result2->num_rows;
        $result->close();
        $result2->close();
        if ($rows > 0)
        {
            echo "The username is already existed. Please choose different username";
        }
        else if($rows2 > 0)
        {
            echo "The email is already existed. Please choose different email";
        }
        else 
        {
            $salt1 = "qm&h*";
            $salt2 = "pg!@";
            $token = hash('ripemd128', "$salt1$pw_temp$salt2");
            addUser($connection, $un_temp, $em_temp, $token);
            echo <<<_END
                <center><form action='main.php' method='post'>
                <input type= 'submit' value='Successfully Register. Click here to process'>
                </body>
                </form></center>
            _END;

        }   
    }
    else
    {
        echo $fail;
    }
}

function validateUsername($field)
{
	if ($field == "") return "No Username was entered.\n";
	else if (strlen($field)< 5)
		return "Usernames must be at least 5 characters.\n";
	else if (preg_match('/[^a-zA-Z0-9_-]/',$field))
		return "Only a-z, A-Z, 0-9, - and _ allowed in Usernames.\n";
	return "";
}
function validatePassword($field)
{
	if ($field == "") return "No Password was entered.\n";
	else if (strlen($field) < 6)
		return "Passwords must be at least 6 characters.\n";
	else if (!preg_match('/[a-z]/',$field) || !preg_match('/[A-Z]/',$field) ||!preg_match('/[0-9]/',$field))
		return "Passwords require one each of a-z, A-Z and 0-9.\n";
	return "";
}

function validateEmail($field)
{
	if ($field == "") return "No Email was entered.\n";
	else if (!((strpos($field,".") > 0) && (strpos($field,"@") > 0)) || preg_match('/[^a-zA-Z0-9.@_-]/',$field))
		return "The Email address is invalid.\n";
	return "";
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