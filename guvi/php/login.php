<?php

session_start();

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $host = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "registerform";
    
    $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    // $redis = new Redis();
    // $redis->connect('127.0.0.1', 6379);
    // $redis->auth('MERqihE0z2ZwtVVNW1ePQKhIlHrhDSkf');

$email = $_POST["email"];
$password = $_POST["password"];

$sql = "SELECT * FROM registration WHERE email = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $email); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result == FALSE){
        $response = array(
            "status" => "error",
            "message" => "User not found"
        );
        echo json_encode($response);
    }
    elseif (mysqli_num_rows($result) == 0) {
        $response = array(
            "status" => "error",
            "message" => "User not found"
        );
        echo json_encode($response);
    } else {
        $row = mysqli_fetch_assoc($result);
    
        if(password_verify($password, $row['password'])){
            $session_id = uniqid();
            $redis->set("session:$session_id", $email);
            $redis->expire("session:$session_id", 10*60);
           
    
            $payload = array(
                "email" => $row['email'],
            );
          
            $response = array(
                "status" => "success",
                "message" => "Login successful",
                'session_id' => $session_id
            );
            echo json_encode($response);
        } else {
            $response = array(
                "status" => "error",
                "message" => "Incorrect password"
            );
            echo json_encode($response);
        }
    }
}
    
    
        
}
?>
