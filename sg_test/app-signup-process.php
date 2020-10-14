

<?php
	include "./inc/db.php";
	include "./password.php";
	include "./inc/common.php";

	$secret_key = "123456789";
	$secret_iv = "#@$%^&*()_+=-";

	$uid = $_POST['user-id'];
	$uemail = $_POST['user-email'];

	$tmpPw = $_POST['user-password'];
	$upassword = password_hash($_POST['user-password'], PASSWORD_DEFAULT);

	$eth_key = Encrypt($_POST['user-password'], $secret_key, $secret_iv);

	$uname = $_POST['user-name'];
	$urecommender = trim($_POST['user-recommendor']);
	$usponser = trim($_POST['user-sponser']);
	$ucountry = $_POST['user-country'];
	$leg = $_POST['leg'];
	$send_key = $_POST['send_key'];




	$curl = curl_init();

	curl_setopt_array($curl, array(
    CURLOPT_URL => "https://mainnet.tarapay.net/Create_wallet",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{\"password\": \"". $tmpPw ."\", \"apikey\": \"any_text\"}",
    CURLOPT_HTTPHEADER => array(
        "content-type: application/json"
    ),
    ));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);
	$sendanswer=json_decode($response);

	if($sendanswer->ok==0){
		echo "<script>alert('Wallet create error Do it again later')</script>";
	}else{


		$sql = "SELECT * FROM tc_customer WHERE uid='$urecommender'";
		$result = mysqli_query($conn, $sql);
		$recommender = mysqli_fetch_array($result);

		$sql = "SELECT * FROM tc_customer WHERE uid='$usponser'";
		$result = mysqli_query($conn, $sql);
		$sponser = mysqli_fetch_array($result);

		$sql = "INSERT INTO tc_customer (uid,uemail,uname,upassword,urecommender, btc_address, xrp_address, tara_address, wallet_data , eth_address, send_key, btc_balance, eth_balance, xrp_balance, tara_balance, sg_balance,   ucountry) VALUES ('".$uid."','".$uemail."','".$uname."','".$upassword."','".$recommender['idx']."','','','".$sendanswer->ethereumaddress."', '".$eth_key."','','".$send_key."',0,0,0,0,0,'".$ucountry."')";

		$result = mysqli_query($conn, $sql);
		$last_id = $conn->insert_id;
		$sql = "UPDATE tc_customer SET ".$leg." =".$last_id." WHERE idx=".$sponser['idx']."";
		$result = mysqli_query($conn, $sql);
		echo "<script>location.href='index.php'</script>";
		include "./db-close.php";

	}
?>
