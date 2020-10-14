<?php
  include "./inc/db.php";
  include "./password.php";


	if($_POST['user-id'] == "" || $_POST['user-password'] == ""){
    echo "<script>alert('아이디 패스워드를 확인하세요.'); location.href='./index.php';</script>";
  }else{
    $password = $_POST['user-password'];
    $sql = "SELECT * FROM SG_customer WHERE uid='{$_POST['user-id']}'";
    $result =mysqli_query($conn, $sql);
    $member = mysqli_fetch_array($result);
    $hash_pw = $member['upassword'];

    if(password_verify($password, $hash_pw)) {
      $_SESSION['user-id'] = $member["uid"];
      $_SESSION['user-password'] = $member["upassword"];
    	$_SESSION['_sign'] = "USER";
    	$_SESSION['tara_address'] = $member["tara_address"];
      echo "<script>location.href='./main.php';</script>";
    } else {
      echo "<script>alert('아이디 혹은 비밀번호가 다릅니다 확인하세요.'); history.back();</script>";
    }
  }

  include "./db-close.php";
?>
