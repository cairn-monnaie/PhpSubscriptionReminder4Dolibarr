<?php
/**
 * @author gaetan janssens <gaetan@plopcom.fr>
 */

if (!is_dir(__DIR__.'/vendor')){
    echo "please run 'composer install' to start\n";
    die('');
}

include_once "vendor/autoload.php";
use Symfony\Component\Dotenv\Dotenv;

echo "==================="."\n";
echo date('Y-m-d H:i:s')."\n";
echo "==================="."\n";

if (!file_exists(__DIR__.'/.env')){
    echo "please create .env file\n";
    die('');
}
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');


foreach (array('DOLIBARR_DB_HOST','DOLIBARR_DB_PORT','DOLIBARR_DB_NAME','DOLIBARR_DB_PREFIX','DOLIBARR_DB_USER',
             'DOLIBARR_DB_PASSWORD','SMTP_HOST','SMTP_USERNAME','SMTP_AUTH','SMTP_PASSWORD','SMTP_PORT','SUBJECT','FROM_EMAIL') as $ENV_VAR){
    if (!isset($_SERVER[$ENV_VAR])){
        echo "🤯 ".$ENV_VAR.' should be defined in your .env file'."\n";
        die('');
    }
}
if (count($argv) > 1){
    if ($argv[1]=='--help' || $argv[1] == '-h'){
        echo "ℹ usage : \n";
        echo "php ".__FILE__." YYYY-mm-dd /absolute/path/to/mail/content.txt";
        echo "\n";
        die('');
    }
}

if (count($argv) <= 2){
    echo "⚠ Oups, You need to pass a date and a content text file\n";
    die('');
}

$date = $argv[1];
$file = $argv[2];

if (!file_exists($file)) {
    die('file '.$file.' not found');
}

$content =  file_get_contents($file);

$mysqli = new mysqli($_SERVER['DOLIBARR_DB_HOST'],$_SERVER['DOLIBARR_DB_USER'],$_SERVER['DOLIBARR_DB_PASSWORD'],$_SERVER['DOLIBARR_DB_NAME']);

if ($mysqli->connect_error) {
    echo 'Erreur de connexion (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error;
    echo "\n"; die();
}

$query = sprintf("SELECT lastname,firstname,email,datefin,datec
FROM %sadherent WHERE datefin = '%s';",$_SERVER['DOLIBARR_DB_PREFIX'],$date);

$result = $mysqli->query($query);

$headers = array ('From' => $_SERVER['FROM_EMAIL'], 'Subject' => $_SERVER['SUBJECT']);
$smtp = Mail::factory('smtp', array ('host' => $_SERVER['SMTP_HOST'], 'port' => $_SERVER['SMTP_PORT'], 'auth' => $_SERVER['SMTP_AUTH'],
    'username' => $_SERVER['SMTP_USERNAME'], 'password' => $_SERVER['SMTP_PASSWORD']));

$counter = 0;
$fail = 0;

while ($row = $result->fetch_row()) {
    if (isset($_SERVER['DEBUG_EMAIL']))
        $row[2] = $_SERVER['DEBUG_EMAIL'];
    $to = $row[1].' '.$row[0].' <'. $row[2] . '>';
    $headers['To'] = $to;
    $mail = $smtp->send($row[2], $headers, $content);
    if (PEAR::isError($mail)) {
        echo( $mail->getMessage());
        echo "\n";
        $fail++;
    } else {
        echo("Message successfully sent to ".$to."!\n");
        $counter++;
    }
}
if (($counter+$fail) == 0){
    echo "No mail to send for ".$date."\n";
}else{
    echo $counter.' mail(s) where send'."\n";
    echo $fail.' mail(s) failed'."\n";
}

$mysqli->close();