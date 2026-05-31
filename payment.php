<?php
	session_start();
	include "connection.php";
	
	
	$ono=$_SESSION['orderno'];

	if(isset($_POST['btncredit']))
	{
		$qry="update orders set status=1 where orderno='$ono'";
		mysqli_query($conn,$qry);

		$qry1="update cart set status=2 where orderno='$ono'";
		mysqli_query($conn,$qry1);		

		echo "<script>alert('Payment Success.');window.location='myorders.php';</script>";
	}

	if(isset($_POST['btndebit']))
	{
		$qry="update orders set status=1 where orderno='$ono'";
		mysqli_query($conn,$qry);

		$qry1="update cart set status=2 where orderno='$ono'";
		mysqli_query($conn,$qry1);		

		echo "<script>alert('Payment Success.');window.location='myorders.php';</script>";
	}

	if(isset($_POST['btnupi']))
	{
		$qry="update orders set status=1 where orderno='$ono'";
		mysqli_query($conn,$qry);

		$qry1="update cart set status=2 where orderno='$ono'";
		mysqli_query($conn,$qry1);		

		echo "<script>alert('Payment Success.');window.location='myorders.php';</script>";
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<title>Payment :: GreenKart</title>
<!-- for-mobile-apps -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Payment Form Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //for-mobile-apps -->
<link href="css/style1.css" rel="stylesheet" type="text/css" media="all" />
<link href='//fonts.googleapis.com/css?family=Fugaz+One' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Alegreya+Sans:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,800,800italic,900,900italic' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="js/jquery.min.js"></script>
</head>
<body>
	<div class="main">
		<h1>Payment Form </h1>
		<div class="content">
			
			<script src="js/easyResponsiveTabs.js" type="text/javascript"></script>
					<script type="text/javascript">
						$(document).ready(function () {
							$('#horizontalTab').easyResponsiveTabs({
								type: 'default', //Types: default, vertical, accordion           
								width: 'auto', //auto or any width like 600px
								fit: true   // 100% fit in a container
							});
						});
						
					</script>
						<div class="sap_tabs">
							<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">
								<div class="pay-tabs">
									<h2>Select Payment Method</h2>
									  <ul class="resp-tabs-list">
										  <li class="resp-tab-item" aria-controls="tab_item-0" role="tab"><span><label class="pic1"></label>Credit Card</span></li> 
										  <li class="resp-tab-item" aria-controls="tab_item-3" role="tab"><span><label class="pic2"></label>Debit Card</span></li>
										  <li class="resp-tab-item" aria-controls="tab_item-4" role="tab"><span><label class="pic2"></label>UPI</span></li>
										  <div class="clear"></div>
									  </ul>	
								</div>
								<div class="resp-tabs-container">
									<div class="tab-1 resp-tab-content" aria-labelledby="tab_item-0">
										<div class="payment-info">

											<h3 class="pay-title">Credit Card Info</h3>
											<form action="#" method="post">
												<div class="tab-for">				
													<h5>NAME ON CARD</h5>
														<input type="text" value="" name="txtnm" required="">
													<h5>CARD NUMBER</h5>													
														<input class="pay-logo" type="text" name="txtcardno" value=""maxlength="16" required="">
												</div>	
												<div class="transaction">
													<div class="tab-form-left user-form">
														<h5>EXPIRATION</h5>
															<ul>
																<li>
																	<input type="number" name="txtmnth" class="text_box" type="text" value="6" min="1" max="12" />	
																</li>
																<li>
																	<input type="number" name="txtyear" class="text_box" type="text" min="2024" value="2024" />	
																</li>
																
															</ul>
													</div>
													<div class="tab-form-right user-form-rt">
														<h5>CVV NUMBER</h5>													
														<input type="password" value="" maxlength="3" minlength="3" required="">
													</div>
													<div class="clear"></div>
												</div>
												<input type="submit" value="Submit" name="btncredit">
											</form>
										</div>
									</div>
									<div class="tab-1 resp-tab-content" aria-labelledby="tab_item-3">	
										<div class="payment-info">
											
											<h3 class="pay-title">Dedit Card Info</h3>
											<form action="#" method="post">
												<div class="tab-for">				
													<h5>NAME ON CARD</h5>
														<input type="text" name="txtnm" value="" required="">														
													<h5>CARD NUMBER</h5>													
														<input class="pay-logo" type="text" name="txtcardno" value="" maxlength="16" minlength="16" required="">
												</div>	
												<div class="transaction">
													<div class="tab-form-left user-form">
														<h5>EXPIRATION</h5>
															<ul>
																<li>
																	<input type="number" name="txtdmnth" class="text_box" type="text" value="6" min="1" />	
																</li>
																<li>
																	<input type="number" onkeydown="return /[0-9 backspace]/i.test(event.key)" name="txtdyear" class="text_box" type="text" value="2024" min="2024" />	
																</li>
																
															</ul>
													</div>
													<div class="tab-form-right user-form-rt">
														<h5>CVV NUMBER</h5>													
														<input type="password" maxlength="3" minlength="3" required="">
													</div>
													<div class="clear"></div>
												</div>
												<input type="submit" value="Submit" name="btndebit">
											</form>
										</div>	
									</div>
									<div class="tab-1 resp-tab-content" aria-labelledby="tab_item-4">	
										<div class="payment-info">
											
											<h3 class="pay-title">UPI Information</h3>
											<form action="#" method="post">
												<div class="tab-for">				
													<h5>UPI ID</h5>
														<input type="text" name="txtupi" value="" maxlength="" required="">														
												</div>	
												<input type="submit" value="Submit" name="btnupi">
											</form>
										</div>	
									</div>
								</div>	
							</div>
						</div>	

		</div>
		<p class="footer">Copyright © 2025 Aroma Hub. All Rights Reserved | Designed by <a href="index.php">Aroma Hub</a></p>
	</div>
</body>
</html>