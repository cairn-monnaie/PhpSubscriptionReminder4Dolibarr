<?php

require_once "Mail.php";

if (count($argv) <= 2){
    echo "need a date and a text file\n";
    die('');
}

$date = $argv[1];
$file = $argv[2];

if (!file_exists($file)) {
    die('file not found');
}

$content =  file_get_contents($file);

$mysqli = new mysqli($db_host,$db_user,$db_pass,$db_name);

if ($mysqli->connect_error) {
    die('Erreur de connexion (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

$query = sprintf("SELECT lastname,firstname,email,datefin,datec
FROM %sadherent WHERE datefin = '%s';",$db_prefix,$date);

//echo $query;

$result = $mysqli->query($query);
//var_dump($result);


$headers = array ('From' => $email_from, 'To' => $to, 'Subject' => $email_subject, 'Reply-To' => $email_address);
$smtp = Mail::factory('smtp', array ('host' => $host, 'port' => $port, 'auth' => true, 'username' => $username, 'password' => $password));
$mail = $smtp->send($to, $headers, $email_body);


if (PEAR::isError($mail)) {
echo("<p>" . $mail->getMessage() . "</p>");
} else {
echo("<p>Message successfully sent!</p>");
}
?>

$subject = 'Votre adhésion au Cairn';
$headers = 'From: Le Cairn <contact@cairn-monnaie.com>' . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
//$headers .= 'Bcc: janssensgaetan@gmail.com' . "\r\n";
//<nicolas.faus@cairn-monnaie.com>

while ($row = $result->fetch_row()) {
     $row = array('janssens','gaëtan','gaetan.janssens@ntymail.com','','');
     $to = $row[2];
     mail($row[1].' '.$row[0].' <'. $to . '>', $subject, $content, $headers);
}

$mysqli->close();
