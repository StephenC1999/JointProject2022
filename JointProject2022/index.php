<?php
$SESSION_lifetime = 86400;


session_start();


$host = 'localhost';
$username = 'Admin';
$password = 'CovidAdmin321!';
$conn = new mysqli($host, $username, $password);

$cipher = 'AES-128-CBC';
$key = 'thebestsecretkey';

if ($conn->connect_error)
{
  die('Connection failed: ' . $conn->connect_error);
}

$sql = 'CREATE DATABASE IF NOT EXISTS clientsdb;';
if (!$conn->query($sql) === TRUE)
{
  die('Error creating database: ' . $conn->error);
}

$sql = 'USE clientsdb;';
if (!$conn->query($sql) === TRUE)
{
  die('Error using database: ' . $conn->error);
}

$sql = ' CREATE TABLE IF NOT EXISTS `clients`
  (
  `Client No.` int(8) NOT NULL auto_increment,
  `First Name` varchar(250)  NOT NULL,
  `Last Name` varchar(250)  NOT NULL,
  `Address` varchar(250)  NOT NULL,
  `PPSN` varchar(250)  NOT NULL,
  `Phone`  varchar(250)  NOT NULL,
  `Email` varchar(250)  NOT NULL,
  `Password` varchar(250)  NOT NULL,
  `DOB` varchar(250)  NOT NULL,
  `iv` varchar(250)  NOT NULL,
   PRIMARY KEY  (`Client No.`)
  ); ';

if (isset($_POST['validate']) || isset($_POST['view']))
{
	$sql = "SELECT `Client No.`, `PPSN`, `Password`, `iv` FROM clients";
	$result = $conn->query($sql);
	$found = "false";
	if ($result->num_rows > 0)
	{
	  while($row = $result->fetch_assoc())
		{
        $iv = hex2bin($row['iv']);
		    $ppsn = hex2bin($row['PPSN']);
		    $ppsn = openssl_decrypt($ppsn, $cipher, $key, OPENSSL_RAW_DATA, $iv);
				if($ppsn == $_POST['medNum'])
				{
					$found = "true";
          $clientno = $row['Client No.'];
          $passwd = hex2bin($row['Password']);
          $passwd = openssl_decrypt($passwd, $cipher, $key, OPENSSL_RAW_DATA, $iv);
          $_SESSION['ppsnum'] = $ppsn;
          $_SESSION['clientnum'] = $clientno;
				}
		}
	}

  if($found == "false" || $passwd != $_POST['attempt'])
  {
     echo '<p style="font-size:25px;color:red;padding-left:44%;">Error! Incorrect Details</p>';
  }
  else
  {
    $_SESSION['ppsno.'] = $_POST['medNum'];
    if (isset($_POST['validate']))
    {
      header("Location: upload.php");
    }
    else if (isset($_POST['view']))
    {
      header("Location: view.php");
    }
  }
}
?>

<html>
<head>
<title>Covid Portal - Home </title>
<link rel="stylesheet" type="text/css" href="index.css">
</style>
</head>

<body>
<div class = "container">
	<form method="POST" action="">
		<h2>Covid Portal Home</h2>
		<h4>Welcome! </h4><br>

	 	<div class="inputbox"><label for="MedNum">PPS Number:</label>
			 <input type="text" title="Must contain 7 digits followed by one or two characters" name="medNum" required id="MedNum" pattern="(\d{7})([A-Z]{1,2})"/>
	 	</div>

    <div class="inputbox"><label for="attempt">Password:</label>
       <input type="text" name="attempt" required id="attempt" />
   </div>

		<input type="submit" formmethod="post" value="Upload Pos Antigen" name="validate" />
    <input type="submit" formmethod="post" value="View Profile" name="view" />
    <input type="hidden" name="token" value=<?php random_bytes(16); ?>/>
  </form>

	<br><br>
	<h3 style="padding-left:32%;">New user? Register below...</h3>
	<input type="button" value="Register Profile" onclick="location.href='form.php';" />
<br>
<p><h4 class="disclaimer">Disclaimer: By continuing to use this site, you agree that all data you input to this application can be stored for COVID Tracing Purposes. This data is encrypted during transmission and stays encrypted in storage to protect your privacy. <br> If you ever wish to review/ammend or delete this data, you can do so by viewing your profile    </h4></p>
</div>

</body>
</html>
