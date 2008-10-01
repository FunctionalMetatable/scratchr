<?
$to = "andresmh@media.mit.edu";
$from = $_POST['from'];
$from_header = "From: $from";
$contents = $_POST['contents'];
$subject = $_POST['subject'];
if($contents != "")
{
//send mail - $subject & $contents come from surfer input
   mail($to, $subject, $contents, $from_header);
 // redirect back to url visitor came from
 #header("Location: $HTTP_REFERER");
print("<HTML><BODY>Yey, it worked");
}
else
{
print("<HTML><BODY>Error, no comments were submitted!");
print("$to,$from");
print("</BODY></HTML>");
}
?>
