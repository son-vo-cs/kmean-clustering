<?php
require_once 'kmean.php';
if (!isset($_POST['content'])) die("Time out. You need to login again");
$filename = $_POST['filename'];
$content = $_POST['content'];
echo <<<_END
        
        File name: $filename <br>
        Content  : $content <br>
        
        <form action='computeKmean.php' method='post'>
        Enter number of clusters: <input type='number' name='cluster'><br>
        <input type='hidden' name='content' value="$content">
        <input type='hidden' name='filename' value="$filename">
        <input type='submit' value='COMPUTE KMEAN'>
        </form>
_END;   

if (isset($_POST['cluster']) && isset($_POST['content']))
{
    $cluster = $_POST['cluster'];
    $content = $_POST['content'];
    if (!is_numeric($cluster))
    {
        echo "Please enter an integer!<br>";
    }
    else
    {
        $valid = isValid($content, $cluster);
        if (strlen($valid) > 0)
        {
            echo $valid."<br>";
        }
        else
        {
            $result = kmean($content, $cluster);
            display($result);
        }
    }
}



?>