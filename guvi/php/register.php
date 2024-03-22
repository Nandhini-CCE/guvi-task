<?php
$firstname=$_POST['firstname'];
$lastname=$_POST['lastname'];
$email=$_POST['email'];
$password=$_POST['password'];
$phonenumber=$_POST['phonenumber'];

if(!empty($firstname) || !empty($lastname) || !empty($email)|| !empty($password)|| !empty($phonenumber)){
    $host="localhost";
    $dbusername="root";
    $dbpassword="";
    $dbname="registerform";

    $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
    
    if(mysqli_connect_error()){
        die('Connect Error('.mysqli_connect_error().')'
        .mysqli_connect_error());
    }


else{
    $SELECT ="SELECT email from registration where email=? limit 1";

    $INSERT = "INSERT into registration (firstname, lastname, email, password, phonenumber) Values (?, ?, ?, ?, ?)";

    $stmt= $conn->prepare($SELECT);
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->store_result();
    $rnum=$stmt->num_rows;

    if($rnum==0){
        $stmt->close();
        $stmt=$conn->prepare($INSERT);
        $stmt->bind_param("ssssi",$firstname,$lastname, $email, $password, $phonenumber);
        $stmt->execute();
        echo "New record inserted sucessfully";
    }
    else{
        echo "someone already registered using this email";
    }
    $stmt->close();
    $conn->close();
}
}
else{
    echo "All field are required";
    die();
}


$uri = 'mongodb://localhost:27017/';
$manager = new MongoDB\Driver\Manager($uri);

$database = "registerform";
$collection = "registration";

$bulk = new MongoDB\Driver\BulkWrite;

$document = [
    'email' => $email,
    'dob' => '',
    'age' => '',
    'contact'=>'',
];

$bulk = new MongoDB\Driver\BulkWrite;
$_id = $bulk->insert($document);
$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
$result = $manager->executeBulkWrite("$database.$collection", $bulk, $writeConcern);


$mongoId = (string)$_id;

$sql = "INSERT INTO registration (email, password, mongodbId) VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sss", $email, $password, $mongoId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response = ['status' => 'success', 'message' => 'Registered successfully'];
    } else {
        $response = ['status' => 'error', 'message' => 'Registration failed'];
    }

    $stmt->close();
}
else {
    echo "Error preparing statement: " . $conn->error;
}

echo json_encode($response);


?>
