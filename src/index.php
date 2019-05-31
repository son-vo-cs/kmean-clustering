<?php
echo <<<_END
        <center><form action='' method='post'><pre>
        Please choose one of the options below
        <input type='submit' name = 'login' value='Login'>
        <input type='submit' name = 'signup' value='Signup'></pre>
        </form></center>
_END;   

if (isset($_POST['login']))
{
    echo <<<_END
        <center><form action='loginuser.php' method='post' id='myform'><pre>
        <body onload="document.forms['myform'].submit()">
        </body>
        </form></center>
_END; 
}
elseif (isset($_POST['signup']))
{
    echo <<<_END
        <center><form action='signup.php' method='post' id='myform'><pre>
        <body onload="document.forms['myform'].submit()">
        </body>
        </form></center>
_END; 
}

?>