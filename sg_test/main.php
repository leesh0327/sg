<?php

define("_AUTH_", "MEMBER");
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
include "./header.php";
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
include_once "./inc/db.php";
include_once './lib/news_list.php';

	$sql = "select * from {$tara['member_table']} where uid = '".$_SESSION['user-id']."'   ";
	$rs = mysqli_query($conn,$sql);
	$info = mysqli_fetch_array($rs);


	$curl = curl_init();

	curl_setopt_array($curl, array(
    CURLOPT_URL => "https://mainnet.tarapay.net/check_tara_balance",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{\"ethereumaddress\": \"".$info["tara_address"]."\", \"apikey\": \"any_text\"}",
    CURLOPT_HTTPHEADER => array(
        "content-type: application/json"
    ),
    ));

	$response = json_decode(curl_exec($curl));
	curl_close($curl);


	$arg_token = "0x43b0b2ba1a5953ac4177272881b9f8123f2a2ff6";
	$curl_token = curl_init();

    curl_setopt_array($curl_token, array(
    CURLOPT_URL => "https://mainnet.tarapay.net/check_token_information/balance",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{\"ethereumaddress\": \"".$info["tara_address"]."\",\"token_contract\": \"".$arg_token."\", \"apikey\": \"any_text\"}",
    CURLOPT_HTTPHEADER => array(
    "content-type: application/json"
    ),
    ));

    $response_token = json_decode(curl_exec($curl_token));
    curl_close($curl_token);

	$sql = "UPDATE {$tara['member_table']} SET tara_balance = ".$response->balance.", sg_balance = ".$response_token->balance." WHERE uid = '".$_SESSION['user-id']."'   ";
	$result = mysqli_query($conn, $sql);






?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Success Global</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport"content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=0.1, viewport-fit=cover" />
    <meta name="description" content="Finapp HTML Mobile Template">
    <meta name="keywords" content="bootstrap, mobile template, cordova, phonegap, mobile, html, responsive" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="assets/img/favicon.png" sizes="32x32">
    <link rel="shortcut icon" href="assets/img/favicon.png">
<script language="javascript">
<!--

function main_reflash() {
	location.href='main.php';
}

function send_coin(arg_coin)
{
	document.listForm.s_coin.value = arg_coin;
	document.listForm.action ="./pop_up1.php"
	document.listForm.target = "";
	document.listForm.submit();
}

function copy_recom(){
	var t = document.createElement("textarea");
	document.body.appendChild(t);
	t.value = "http://successglobal.cafe24.com/signup?referer=<?php echo $_SESSION['user-id'];?>";
	t.select();
	document.execCommand("copy");
	document.body.removeChild(t);
	alert("An address that you can recommend to other users has been copied.");
}

function copy_addr(arg_addr){
	var t = document.createElement("textarea");
	document.body.appendChild(t);
	t.value = arg_addr;
	t.select();
	document.execCommand("copy");
	document.body.removeChild(t);
	alert("Your wallet address has been copied.");
}
-->
</script>

</head>

<body>
<form method=post name=listForm action="" >
	<input type="hidden" name="s_idx"  id="s_idx" value="">
	<input type="hidden" name="s_coin"  id="s_coin" value="">
</form>

    <!-- loader -->
    <div id="loader">
        <img src="assets/img/logo-icon.png" alt="icon" class="loading-icon">
    </div>
    <!-- * loader -->

    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="pageTitle">
            <!-- <img src="assets/img/logo.png" alt="logo" class="logo"> -->
            <h1>SuccessGlobal</h1>
        </div>
    </div>
    <!-- * App Header -->


    <!-- App Capsule -->
    <div id="appCapsule" >

        <!-- Wallet Card -->
        <div class="section wallet-card-section pt-1">
            <div class="wallet-card">
                <!-- Balance -->
                <div class="balance">
                    <div class="left">
						<p class="m-0 text-dark">Member : <?=$info['uid']?></p>
                        <span class="title">Total Balance</span>
                        <h1 class="total">$ <?php echo number_format(($response->balance * 11 + $response_token->balance * 5) / 100,0)?></h1>
                    </div>
                    <div class="right">
                        <!-- <a href="#" class="button" data-toggle="modal" data-target="#depositActionSheet">
                            <ion-icon name="add-outline"></ion-icon>
                        </a> -->
                        <button class="button" type="button" onclick="copy_recom();">Link Copy</button>
                    </div>
                </div>
                <!-- * Balance -->
                <!-- Wallet Footer -->
                <div class="wallet-footer">
                    <div class="item">
                        <a href="app-pages.php">
                            <div class="icon-wrapper bg-danger">
                                <ion-icon name="person-circle-outline" role="img" class="md hydrated" aria-label="person circle outline"></ion-icon>
                            </div>
                            <strong>My Page</strong>
                        </a>
                    </div>
                    <div class="item">
                        <a href="pop_up1.php"  target="_self">
                            <div class="icon-wrapper">
                                <ion-icon name="arrow-forward-outline"></ion-icon>
                            </div>
                            <strong>Tranjection</strong>
                        </a>
                    </div>
                    <div class="item">
                        <a href="#">
                            <div class="icon-wrapper bg-success">
                                <ion-icon name="card-outline"></ion-icon>
                            </div>
                            <strong>Enquiry</strong>
                        </a>
                    </div>
                    <div class="item">
                        <a href="#" data-toggle="modal" data-target="#exchangeActionSheet">
                            <div class="icon-wrapper bg-warning">
                                <ion-icon name="swap-vertical"></ion-icon>
                            </div>
                            <strong>Notice</strong>
                        </a>
                    </div>
                    <div class="item">
                        <a href="./signout.php" >
                            <div class="icon-wrapper bg-warning">
                                <ion-icon name="swap-vertical"></ion-icon>
                            </div>
                            <strong>Log out</strong>
                        </a>
                    </div>
                </div>
                <!-- * Wallet Footer -->
            </div>
        </div>
        <!-- Wallet Card -->



      <!-- Send Action Sheet -->
      <div class="modal fade action-sheet" id="sendActionSheet" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Money</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="post" name="sendForm" action="">
                                <div class="form-group basic">
                                    <div class="input-wrapper">
                                        <label class="label" for="account3">From</label>
                                        <select class="form-control custom-select" id="account3" name="account3">
                                            <option value="TARA">TARA (<?php echo substr($info["eth_address"],0,20)?>...)</option>

                                        </select>
                                    </div>
                                </div>

                                <div class="form-group basic">
                                    <div class="input-wrapper">
                                        <label class="label" for="text11">To</label>
                                        <input type="email" class="form-control" id="toaddr3" name="toaddr3"
                                            placeholder="Enter Tara Addr">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>

                                <div class="form-group basic">
                                    <label class="label">Enter Amount</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="input14">TARA</span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg" placeholder="0" id="amount3" name="amount3">
                                    </div>
                                </div>
							</form>
                                <div class="form-group basic">
                                    <button type="button" onclick="send_coin();"  class="btn btn-primary btn-block btn-lg"
                                        data-dismiss="modal">Send</button>
                                </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- * Send Action Sheet -->

        <div class="modal fade modalbox" id="ModalBasic" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"  id="txt0">Agency application</h5>
                        <a href="javascript:;" data-dismiss="modal">Close</a>
                    </div>
                    <div class="modal-body" id="agency">
                    <div class="card-body">

                        <div class="custom-control custom-checkbox mb-1">

                            <label class="custom-control-label position-relative" id="txt1" for="customChecka1">
                            ++ Agency role ++ <br> <br>
                            The importance of block chain technology and cryptography technology is proven worldwide. As global companies are competitively activating the block chain and crypto-pe ecosystem in the digital economy industry, they will lead the global block chain market through Tara-based mainnet, where global block chain networks are being formed, and will be in charge of forming a block chain network to promote Tara-based block chain technology.It is in charge of establishing a system that receives sales commission according to qualifications as a public relations project to train agency managers.</label>
                        </div>
                        <p  class="check_box1"><input type="checkbox" id="txt6">I agree after confirmation.</p>

                        <div class="custom-control custom-checkbox mb-1">

                            <label class="custom-control-label position-relative" id="txt2" for="customChecka2"> ++ Agency Benefit ++ <br> <br>
                                (1) Acquisition of Agency manager qualification shall be one year later than the date of application. <br>
                                (2) If you apply to an agency, you will receive a Tara Wallet promotion fee every day for a year <br>
                                (3) If an agency is qualified, 10% of the gas (transfer fee) generated through the new agency and agency Wallet will be paid for the operation. <br>
                                (4) When obtaining agency qualifications, 20% of the application amount for recommended operation expenses shall be paid as operating expenses for each application recommended by the new agency. <br>
                                (5) Qualifying the agency will receive 10 percent of all recommended operating costs and promotions. <br>
                                (6) He will be promoted to the representative one year after being qualified as a substitute and will pay 5,000 won in congratulatory money.
                            </label>
                        </div>
                        <p  class="check_box1"><input type="checkbox" id="txt6">I agree after confirmation.</p>
                        <div class="custom-control custom-checkbox mb-1">

                            <label class="custom-control-label position-relative"  id="txt3" for="customChecka3">
                                ++ Agency application cost usage details ++ <br> <br>
                                (1) Use as an agency server registration. <br>
                                (2) Used for agency server administration costs. <br>
                                (3) Used for maintenance of agency Tara wallet. <br>
                            </label>
                        </div>
                        <p class="check_box1"><input type="checkbox" id="txt6">I agree after confirmation.</p>
                        <div class="custom-control custom-checkbox mb-1">

                            <label class="custom-control-label position-relative" id="txt4"  for="customChecka4">
                                ++ agency registration terms and conditions ++ <br> <br>
                                (1) The agency application is charged. <br>
                                (2) The agency's application amount is $1,000. <br>
                                (3) An agency may be eligible once the application cost for the agency is confirmed. <br>
                                (4) Since the application and registration fee for the Agency is used as the server registration fee, the applicant must check, agree not to apply for a refund under any circumstances, and agree to fully verify the terms and conditions and to apply for the Agency.
                            </label>
                        </div>
                        <p class="check_box1"><input type="checkbox" id="txt6">I agree after confirmation.</p>
                        <p id="txt5"> Tara Wallet Global Operations Support Headquarters</p>
                    </div>
                    <div class="agency_check">
                        <div class="check_wrap">
                        <input type="checkbox" id="c1" name="cc" />
                        <label for="c1" id="agency_label">
                        <span></span>Agency pay : $1000</label>

                        </div>
                        <div class="btn_pay">
                            <a href="#">결제하기</a>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>




        <!-- * Exchange Action Sheet -->

        <!-- Stats -->
       <div class="section">
            <div class="row mt-2">
                <div class="col-6">
                    <div class="stat-box">
                        <div class="title">Matching Bouns</div>
                        <div class="value text-success">$ 552.95</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box">
                        <div class="title">Sponsored Bonus</div>
                        <div class="value text-danger">$ 86.45</div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <div class="stat-box">
                        <div class="title">Roll-up Bonus</div>
                        <div class="value">$ 53.25</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box">
                        <div class="title">LEVEL POSITION</div>
                        <div class="value">$ 120.99</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- * Stats -->

        <!-- Transactions -->

        <!-- Transactions -->
        <div class="section mt-4">
            <div class="section-heading">
                <h2 class="title">Transactions</h2>
                <a href="#" class="link">View All</a>
            </div>
            <div class="transactions">
                <!-- item -->
				<div class="coin">
                    <div class="detail">
                        <img src="./assets/img/tara.png" alt="img" class="image-block imaged w48 white">
                        <div>
                            <strong>SG Coin : $ <?php echo number_format(($response_token->balance * 5) / 100,5)?></strong>
                            <p><?php echo number_format($response_token->balance,6);?> </p>
                            <!-- <p>Shopping</p> -->
                        </div>
                    </div>
                    <div class="detail_menu">



                        <img class="menu_x" src="./assets/img/x_icon.png" alt="x_icon">

                        <div class="menu_circle">
                        <div class="circle" onclick="location.href ='http://successglobal.cafe24.com/app-tranlist.php?coin=SG';">history</div>
                        <div class="circle" onclick="javascript:send_coin('SG');">Send</div>
                        <div class="circle" onclick="javascript:copy_addr('<?php echo $info["tara_address"];?>');">address</div>
                    </div>

                    <div class="copy_bg"></div>
                        <div class="send_password">
                            <p class="x_icon">X</p>
                            <form action="" method="POST">
                                <p class="psw">Password</p>
                                <input type="password" placeholder="********">
                                <input type="submit" value="Click">
                            </form>
                        </div>
                    </div>

				</div>
                <!-- * item -->

				<div class="coin">
                    <div class="detail">
                        <img src="./assets/img/tara.png" alt="img" class="image-block imaged w48 white">
                        <div>
                            <strong>Tara : $ <?php echo number_format(($response->balance * 11) / 100,5)?></strong>
                            <p><?php echo number_format($response->balance,6);?></p>
                            <!-- <p>Shopping</p> -->
                        </div>
                    </div>
                    <!-- <div class="right">
                        <div class="price text-danger"> - $ 150</div>
                    </div> -->
                    <div class="detail_menu">



                        <img class="menu_x" src="./assets/img/x_icon.png" alt="x_icon">

                        <div class="menu_circle">
                        <div class="circle" onclick="location.href = 'http://successglobal.cafe24.com/app-tranlist.php?coin=TARA';">history</div>
                        <div class="circle" onclick="javascript:send_coin('TARA');">Send</div>
                        <div class="circle" onclick="javascript:copy_addr('<?php echo $info["tara_address"];?>');">address</div>
                        </div>

                        <div class="copy_bg"></div>
                        <div class="send_password">
                            <p class="x_icon">X</p>
                            <form action="" method="POST">
                                <p class="psw">Password</p>
                                <input type="password" placeholder="********">
                                <input type="submit" value="Click">
                            </form>
                        </div>
                    </div>

				</div>
                <!-- * item -->



                <!-- item -->
                <div class="coin">
                    <div class="detail">
                        <img src="./assets/img/bit.png" alt="img" class="image-block imaged w48 blue">
                        <div>
                            <strong>Bit</strong>
                            <p>00000000</p>
                            <!-- <p>Appstore Purchase</p> -->
                        </div>
                    </div>
                    <!-- <div class="right">
                        <div class="price text-danger">- $ 29</div>
                    </div> -->
                    <div class="detail_menu">

                        <img class="menu_x" src="./assets/img/x_icon.png" alt="x_icon">

                        <div class="menu_circle">
                            <div class="circle" onclick="#">history</div>
                            <div class="circle" onclick="javascript:alert('Coming soon');">Send</div>
                            <div class="circle">address</div>
                        </div>

                        <div class="copy_bg"></div>
                        <div class="send_password">
                            <p class="x_icon">X</p>
                            <form action="" method="POST">
                                <p class="psw">Password</p>
                                <input type="password" placeholder="********">
                                <input type="submit" value="Click">
                            </form>
                        </div>

                    </div>
				</div>
                <!-- * item -->
                <!-- item -->
                <div class="coin">
                    <div class="detail">
                        <img src="./assets/img/eth.png" alt="img" class="image-block imaged w48 green">
                        <div>
                            <strong>Eth</strong>
                            <p>00000000</p>
                            <!-- <p>Transfer</p> -->
                        </div>
                    </div>
                    <!-- <div class="right">
                        <div class="price">+ $ 1,000</div>
                    </div> -->
                    <div class="detail_menu">

                        <img class="menu_x" src="./assets/img/x_icon.png" alt="x_icon">

                        <div class="menu_circle">
                            <div class="circle" onclick="#">history</div>
                            <div class="circle" onclick="javascript:alert('Coming soon');">Send</div>
                            <div class="circle">address</div>
                        </div>

                        <div class="copy_bg"></div>
                        <div class="send_password">
                            <p class="x_icon">X</p>
                            <form action="" method="POST">
                                <p class="psw">Password</p>
                                <input type="password" placeholder="********">
                                <input type="submit" value="Click">
                            </form>
                        </div>

                    </div>
				</div>
                <!-- * item -->
                <!-- item -->
                <div class="coin">
                    <div class="detail">
                        <img src="./assets/img/xrp.png" alt="img" class="image-block imaged w48 purple">
                        <div>
                            <strong>Ripple</strong>
                            <p>00000000</p>
                            <!-- <p>Transfer</p> -->
                        </div>
                    </div>
                    <!-- <div class="right">
                        <div class="price text-danger">- $ 186</div>
                    </div> -->
                    <div class="detail_menu">

                        <img class="menu_x" src="./assets/img/x_icon.png" alt="x_icon">

                        <div class="menu_circle">
                            <div class="circle" onclick="#">history</div>
                            <div class="circle" onclick="javascript:alert('Coming soon');">Send</div>
                            <div class="circle">address</div>
                        </div>

                        <div class="copy_bg"></div>
                        <div class="send_password">
                            <p class="x_icon">X</p>
                            <form action="" method="POST">
                                <p class="psw">Password</p>
                                <input type="password" placeholder="********">
                                <input type="submit" value="Click">
                            </form>
                        </div>

                    </div>
				</div>

            </div>

        </div>
         <!-- my cards -->
        <div class="section full mt-4">
            <div class="section-heading padding">
                <h2 class="title"><?=TARA_CUST_BIZ?> News</h2>
                <!-- <a href="#" class="link">View All</a> -->
            </div>
            <div class="carousel-single owl-carousel owl-theme shadowfix">
                <?=$list?>
            </div>
        </div>
        <!-- * my cards -->
        <!-- shopping mall -->
        <div class="section full mt-4 mb-3">
            <div class="section-heading padding">
                <h2 class="title">Shopping Mall</h2>
				<a href="./app-shop-prod.php" class="link">View All</a>
            </div>
            <div class="shadowfix carousel-multiple owl-carousel owl-theme">
			<?php
            $sql = "SELECT * FROM {$tara['prod_tb']} WHERE DELETED_DT IS NULL ORDER BY PROD_IX DESC";
            $result = sql_query($sql);
            $item = "";
            while($row = mysqli_fetch_array($result)){
                $price = number_format($row['PROD_PRICE']);
                $item = $item."<div class='item'>";
                $item = $item."<a href='app-shop-prod.php?id={$row['PROD_IX']}'>";
                $item = $item."<div class='blog-card p-1'>";
                $item = $item."<div style ='background: url(./".$row['PROD_IMG_PATH'].") no-repeat 50% 50%; background-size: cover;' class='w-100 hequal'></div>";
                $item = $item."<div class='text p-0 pt-1 text-center'>";
                $item = $item."<p class='mb-0 text-dark'><strong>".$row['PROD_NM']."</strong></p>";
                $item = $item."<p class='mb-0 text-dark'><strong>".$price." 원</strong></p>";
                $item = $item."</div>";
                $item = $item."</div>";
                $item = $item."</a>";
                $item = $item."</div>";
            }
            ?>
                <!-- item -->
                <?=$item?>
                <!-- * item -->

            </div>
        </div>
        <!-- * Shopping mall -->
        <!-- Send Money -->
        <div class="section full mt-4">
            <div class="section-heading padding">
                <h2 class="title">New member</h2>
                <a href="javascript:;" class="link">Add New</a>
            </div>
            <div class="shadowfix carousel-small owl-carousel owl-theme">
                <!-- item -->
                <div class="item">
                    <a href="#">
                        <div class="user-card">
                            <!-- <img src="assets/img/sample/avatar/avatar2.jpg" alt="img" class="imaged w-48">
                             -->
                             <img src="./assets/img/photo1.png" alt="photo">
                            <strong>Jurrien</strong>
                        </div>
                    </a>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <a href="#">
                        <div class="user-card">
                            <!-- <img src="assets/img/sample/avatar/avatar3.jpg" alt="img" class="imaged w-48"> -->
                            <img src="./assets/img/photo2.png" alt="photo">
                            <strong>Elwin</strong>
                        </div>
                    </a>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <a href="#">
                        <div class="user-card">
                            <!-- <img src="assets/img/sample/avatar/avatar4.jpg" alt="img" class="imaged w-48"> -->
                            <img src="./assets/img/photo3.png" alt="photo">
                            <strong>Alma</strong>
                        </div>
                    </a>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <a href="#">
                        <div class="user-card">
                            <!-- <img src="assets/img/sample/avatar/avatar5.jpg" alt="img" class="imaged w-48"> -->
                            <img src="./assets/img/photo4.png" alt="photo">
                            <strong>Justine</strong>
                        </div>
                    </a>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <a href="#">
                        <div class="user-card">
                            <img src="assets/img/sample/avatar/avatar6.jpg" alt="img" class="imaged w-48">
                            <strong>Maria</strong>
                        </div>
                    </a>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <a href="#">
                        <div class="user-card">
                            <img src="assets/img/sample/avatar/avatar7.jpg" alt="img" class="imaged w-48">
                            <strong>Pamela</strong>
                        </div>
                    </a>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <a href="#">
                        <div class="user-card">
                            <img src="assets/img/sample/avatar/avatar8.jpg" alt="img" class="imaged w-48">
                            <strong>Neville</strong>
                        </div>
                    </a>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <a href="#">
                        <div class="user-card">
                            <img src="assets/img/sample/avatar/avatar9.jpg" alt="img" class="imaged w-48">
                            <strong>Alex</strong>
                        </div>
                    </a>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <a href="#">
                        <div class="user-card">
                            <img src="assets/img/sample/avatar/avatar10.jpg" alt="img" class="imaged w-48">
                            <strong>Stina</strong>
                        </div>
                    </a>
                </div>
                <!-- * item -->
            </div>
        </div>
        <!-- * Send Money -->

        <!-- Monthly Bills -->
        <div class="section full mt-4">
            <div class="section-heading padding">
                <h2 class="title">Total Informations</h2>
                <a href="#" class="link">View All</a>
            </div>
            <div class="carousel-multiple owl-carousel owl-theme shadowfix">
                <!-- item -->
                <div class="item">
                    <div class="bill-box">
                        <div class="img-wrapper">
                            <img src="assets/img/organization_icon.png" alt="img" class="image-block imaged w48" style="background: yellowgreen;" >
                        </div>
                        <div class="price">$ 14</div>
                        <p>Unilevel</p>
                        <a href="#" class="btn btn-primary btn-block btn-sm">Click</a>
                    </div>
                </div>
                <div class="item">
                    <div class="bill-box">
                        <div class="img-wrapper">
                            <img src="assets/img/organization_icon.png" alt="img" class="image-block imaged w48" style="background: purple;">
                        </div>
                        <div class="price">$ 14</div>
                        <p>Bilevel</p>
                        <a href="#" class="btn btn-primary btn-block btn-sm">Click</a>
                    </div>
                </div>
                <div class="item">
                    <div class="bill-box">
                        <div class="img-wrapper">
                        <button type="button" class="btn btn-icon btn-warning mr-1">
                        <ion-icon name="share-outline" role="img" class="md hydrated" aria-label="share outline"></ion-icon>
                    </button>
                        </div>
                        <div class="price">$ 14</div>
                        <p> Change</p>
                        <a href="#" class="btn btn-primary btn-block btn-sm">Click</a>
                    </div>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <div class="bill-box">
                        <div class="img-wrapper">
                            <button type="button" id="icon_Trans" class="btn btn-icon btn-outline-primary mr-1">
                                <ion-icon name="newspaper-outline"></ion-icon>
                            </button>
                        </div>
                        <div class="price">$ 9</div>
                        <p>Transactions</p>
                        <a href="#" class="btn btn-primary btn-block btn-sm">Click</a>
                    </div>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <div class="bill-box">
                        <div class="img-wrapper">
                            <div class="iconbox bg-danger">
                                <ion-icon name="code-working"></ion-icon>
                            </div>
                        </div>
                        <div class="price">$ 299</div>
                        <p>Daily Bonus</p>
                        <a href="#" class="btn btn-primary btn-block btn-sm">Click</a>
                    </div>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <div class="bill-box">
                        <div class="img-wrapper">
                            <div class="iconbox">
                                <ion-icon name="cash-outline"></ion-icon>
                            </div>
                        </div>
                        <div class="price">$ 962</div>
                        <p>Bonus</p>
                        <a href="#" class="btn btn-primary btn-block btn-sm">Click</a>
                    </div>
                </div>
                <!-- * item -->
                <div class="item">
                    <div class="bill-box">
                        <div class="img-wrapper">
                            <div class="iconbox">
                                <ion-icon name="people"></ion-icon>
                            </div>
                        </div>
                        <div class="price">$ 962</div>
                        <p>Avatar Buying</p>
                        <a href="#" class="btn btn-primary btn-block btn-sm">Click</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- * Monthly Bills -->


        <!-- Saving Goals -->
        <div class="section mt-4">
            <div class="section-heading">
                <h2 class="title">Total profits</h2>
                <a href="#" class="link">View All</a>
            </div>
            <div class="goals">
                <!-- item -->
                <div class="item">
                    <div class="in">
                        <div>
                            <h4>Your profits</h4>
                            <p>300%</p>
                        </div>
                        <div class="price">$ 499</div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 85%;" aria-valuenow="85"
                            aria-valuemin="0" aria-valuemax="100">85%</div>
                    </div>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <div class="in">
                        <div>
                            <h4>New House</h4>
                            <p>Living</p>
                        </div>
                        <div class="price">$ 100,000</div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 55%;" aria-valuenow="55"
                            aria-valuemin="0" aria-valuemax="100">55%</div>
                    </div>
                </div>
                <!-- * item -->
                <!-- item -->
                <div class="item">
                    <div class="in">
                        <div>
                            <h4>Sport Car</h4>
                            <p>Lifestyle</p>
                        </div>
                        <div class="price">$ 42,500</div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 15%;" aria-valuenow="15"
                            aria-valuemin="0" aria-valuemax="100">15%</div>
                    </div>
                </div>
                <!-- * item -->
            </div>
        </div>
        <!-- * Saving Goals -->


        <!-- News -->



        <!-- app footer -->
        <div class="appFooter">
            <div class="footer-title m-0">
                Copyright © Eco Ginseng 2020. All Rights Reserved.
            </div>
        </div>
        <!-- * app footer -->

    </div>
    <!-- * App Capsule -->


    <!-- App Bottom Menu -->
    <div class="appBottomMenu" style="display: none !important;">
        <a href="main.php" class="item active">
            <div class="col">
                <ion-icon name="pie-chart-outline"></ion-icon>
                <strong>Overview</strong>
            </div>
        </a>
        <a href="app-pages.html" class="item">
            <div class="col">
                <ion-icon name="document-text-outline"></ion-icon>
                <strong>Pages</strong>
            </div>
        </a>
        <a href="#" class="item">
            <div class="col">
                <ion-icon name="apps-outline"></ion-icon>
                <strong>Components</strong>
            </div>
        </a>
        <a href="#" class="item">
            <div class="col">
                <ion-icon name="card-outline"></ion-icon>
                <strong>My Cards</strong>
            </div>
        </a>
        <a href="#" class="item">
            <div class="col">
                <ion-icon name="settings-outline"></ion-icon>
                <strong>Settings</strong>
            </div>
        </a>
    </div>
    <!-- * App Bottom Menu -->

    <!-- App Sidebar -->
    <div class="modal fade panelbox panelbox-left" id="sidebarPanel" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <!-- profile box -->
                    <div class="profileBox pt-2 pb-2">
                        <div class="image-wrapper">
                            <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="imaged  w36">
                        </div>
                        <div class="in">
                            <strong>Sebastian Doe</strong>
                            <div class="text-muted">4029209</div>
                        </div>
                        <a href="#" class="btn btn-link btn-icon sidebar-close" data-dismiss="modal">
                            <ion-icon name="close-outline"></ion-icon>
                        </a>
                    </div>
                    <!-- * profile box -->
                    <!-- balance -->
                    <div class="sidebar-balance">
                        <div class="listview-title">Balance</div>
                        <div class="in">
                            <h1 class="amount">$ 2,562.50</h1>
                        </div>
                    </div>
                    <!-- * balance -->

                    <!-- action group -->
                    <div class="action-group">
                        <a href="#" class="action-button">
                            <div class="in">
                                <div class="iconbox">
                                    <ion-icon name="add-outline"></ion-icon>
                                </div>
                                Deposit
                            </div>
                        </a>
                        <a href="#" class="action-button">
                            <div class="in">
                                <div class="iconbox">
                                    <ion-icon name="arrow-down-outline"></ion-icon>
                                </div>
                                Withdraw
                            </div>
                        </a>
                        <a href="#" class="action-button">
                            <div class="in">
                                <div class="iconbox">
                                    <ion-icon name="arrow-forward-outline"></ion-icon>
                                </div>
                                Send
                            </div>
                        </a>
                        <a href="#" class="action-button">
                            <div class="in">
                                <div class="iconbox">
                                    <ion-icon name="card-outline"></ion-icon>
                                </div>
                                My Cards
                            </div>
                        </a>
                    </div>
                    <!-- * action group -->

                    <!-- menu -->
                    <div class="listview-title mt-1">Menu</div>
                    <ul class="listview flush transparent no-line image-listview">
                        <li>
                            <a href="#" class="item">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="pie-chart-outline"></ion-icon>
                                </div>
                                <div class="in">
                                    Overview
                                    <span class="badge badge-primary">10</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="app-pages.html" class="item">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="document-text-outline"></ion-icon>
                                </div>
                                <div class="in">
                                    Pages
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="apps-outline"></ion-icon>
                                </div>
                                <div class="in">
                                    Components
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="card-outline"></ion-icon>
                                </div>
                                <div class="in">
                                    My Cards
                                </div>
                            </a>
                        </li>
                    </ul>
                    <!-- * menu -->

                    <!-- others -->
                    <div class="listview-title mt-1">Others</div>
                    <ul class="listview flush transparent no-line image-listview">
                        <li>
                            <a href="#" class="item">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="settings-outline"></ion-icon>
                                </div>
                                <div class="in">
                                    Settings
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="chatbubble-outline"></ion-icon>
                                </div>
                                <div class="in">
                                    Support
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="log-out-outline"></ion-icon>
                                </div>
                                <div class="in">
                                    Log out
                                </div>
                            </a>
                        </li>
                    </ul>
                    <!-- * others -->

                    <!-- send money -->
                    <div class="listview-title mt-1">Send Money</div>
                    <ul class="listview image-listview flush transparent no-line">
                        <li>
                            <a href="#" class="item">
                                <img src="assets/img/sample/avatar/avatar2.jpg" alt="image" class="image">
                                <div class="in">
                                    <div>Artem Sazonov</div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item">
                                <img src="assets/img/sample/avatar/avatar4.jpg" alt="image" class="image">
                                <div class="in">
                                    <div>Sophie Asveld</div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item">
                                <img src="assets/img/sample/avatar/avatar3.jpg" alt="image" class="image">
                                <div class="in">
                                    <div>Kobus van de Vegte</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <!-- * send money -->

                </div>
            </div>
        </div>
    </div>
    <!-- * App Sidebar -->

    <!-- ///////////// Js Files ////////////////////  -->
    <!-- Jquery -->
    <script src="assets/js/lib/jquery-3.4.1.min.js"></script>
    <!-- Bootstrap-->
    <script src="assets/js/lib/popper.min.js"></script>
    <script src="assets/js/lib/bootstrap.min.js"></script>
    <!-- Ionicons -->
    <script src="https://unpkg.com/ionicons@5.0.0/dist/ionicons.js"></script>
    <!-- Owl Carousel -->
    <script src="assets/js/plugins/owl-carousel/owl.carousel.min.js"></script>
    <!-- Base Js File -->
    <script src="assets/js/base.js"></script>
    <script src="assets/js/toggle_backup.js"></script>

</body>

</html>



<script>
var hequal = $('.hequal').width();
$('.hequal').css({'height':hequal+'px'});
var news_img = $('.news-img').width();
$('.news-img').css({'height':(news_img*4/7)+'px'});
var news_pdf = $('.news-pdf').width();
$('.news-pdf').css({'height':(news_pdf*4/7)+'px'});
var news_video = $('.news-video').width();
$('.news-video').css({'height':(news_video*4/7)+'px'});

window.onresize = function(){
    hequal = $('.hequal').width();
    $('.hequal').css({'height':hequal+'px'});
    news_img = $('.news-img').width();
    $('.news-img').css({'height':(news_img*4/7)+'px'});
    news_pdf = $('.news-pdf').width();
    $('.news-pdf').css({'height':(news_pdf*4/7)+'px'});
    news_video = $('.news-video').width();
    $('.news-video').css({'height':(news_video*4/7)+'px'});
}
</script>
