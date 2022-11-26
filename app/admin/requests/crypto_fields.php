<?php
define('CryptExchanger_INSTALLED',TRUE);
ob_start();
session_start();
error_reporting(0);
include("../../configs/bootstrap.php");
include("../../includes/bootstrap.php");
$merchant = protect($_GET['merchant']);

if($merchant == "block.io") {
	?>
	<div class="form-group">
		<label>Select coin</label>
		<select class="form-control" name="coin">
			<option value=""></option>
			<?php
			$scoins = CryptoSupport($merchant);
			foreach($scoins as $coin) {
				echo '<option value="'.$coin.'">'.$coin.'</option>';
			}
			?>
		</select>
	</div>
	<div class="form-group">
								<label>Minimal amount for exchange</label>
								<input type="text" class="form-control" name="min_amount">
							</div>
							<div class="form-group">
								<label>Maximum amount for exchange</label>
								<input type="text" class="form-control" name="max_amount">
							</div>
							<div class="form-group">
								<label>Reserve</label>
								<input type="text" class="form-control" name="reserve">
							</div>
							<div class="form-group">
								<label>Block.io API Key</label>
								<input type="text" class="form-control" name="g_field_1">
							</div>
							<div class="form-group">
								<label>Block.io Merchant Secret</label>
								<input type="text" class="form-control" name="g_field_2">
							</div>
							<div class="checkbox">
								<label><input type="checkbox" name="option_2" value="yes"> Generate new address for each exchange</label>
							</div>
							<div class="form-group">
								<label>Block.io Address</label>
								<input type="text" class="form-control" name="g_field_4">
								<small>Enter here a address from your block.io account, if you do not want to generate new address for each exchange. Address must be from your block.io account to verify automatically transaction.</small>
							</div>
							<div class="checkbox">
								<label><input type="checkbox" name="include_fee" value="yes"> Include extra fee in total amount for payment</label>
							</div>
							
	<?php
} elseif($merchant == "coinpayments.net") {
	?>
	<div class="form-group">
		<label>Select coin</label>
		<select class="form-control" name="coin">
			<option value=""></option>
			<?php
			$scoins = CryptoSupport($merchant);
			foreach($scoins as $coin) {
				echo '<option value="'.$coin.'">'.$coin.'</option>';
			}
			?>
        </select>
        <small>If coin which you want to use and is supported by coinpayments.net but its not listed here, contact us <b>support@cryptoexchangerscript.com</b> to add them.</small>
	</div>
	<div class="form-group">
								<label>Minimal amount for exchange</label>
								<input type="text" class="form-control" name="min_amount">
							</div>
							<div class="form-group">
								<label>Maximum amount for exchange</label>
								<input type="text" class="form-control" name="max_amount">
							</div>
							<div class="form-group">
								<label>Reserve</label>
								<input type="text" class="form-control" name="reserve">
							</div>
							<div class="form-group">
								<label>Coinpayments.net API Public</label>
								<input type="text" class="form-control" name="g_field_1">
							</div>
							<div class="form-group">
								<label>Coinpayments.net API Private</label>
								<input type="text" class="form-control" name="g_field_2">
							</div>
							<div class="form-group">
								<label>Coinpayments.net Merchant ID</label>
								<input type="text" class="form-control" name="g_field_3">
							</div>
							<div class="form-group">
								<label>Coinpayments.net IPN Secret</label>
								<input type="text" class="form-control" name="g_field_4">
							</div>
							<div class="checkbox">
								<label>
								  <input type="checkbox" name="allow_send" value="yes"> Allow customers to send money through this gateway
								</label>
							</div>
                            <div class="checkbox">
								<label>
								  <input type="checkbox" name="require_login" value="yes"> Require user to login before exchange
								</label>
							</div> 
							<div class="checkbox">
								<label>
								  <input type="checkbox" name="require_email_verify" value="yes"> Require user to verify their email address before exchange
								</label>
							</div>
							<div class="checkbox">
								<label>
								  <input type="checkbox" name="require_document_verify" value="yes"> Require user to verify their identify before exchange
								</label>
							</div>
	<?php
} elseif($merchant == "blockchain.com") {
	?>
	<div class="form-group">
		<label>Select coin</label>
		<select class="form-control" name="coin">
			<option value="Bitcoin">Bitcoin</option>
		</select>
	</div>
	<div class="form-group">
								<label>Minimal amount for exchange</label>
								<input type="text" class="form-control" name="min_amount">
							</div>
							<div class="form-group">
								<label>Maximum amount for exchange</label>
								<input type="text" class="form-control" name="max_amount">
							</div>
							<div class="form-group">
								<label>Reserve</label>
								<input type="text" class="form-control" name="reserve">
							</div>
							
							<div class="form-group">
								<label>Your secret key</label>
								<input type="text" class="form-control" name="g_field_1">
								<small>Enter random secret key, to protect IPN requestst</small>
							</div>
							<div class="form-group">
								<label>Your Blockchain.info xPub</label>
								<input type="text" class="form-control" name="g_field_2">
							</div>
							<div class="form-group">
								<label>Your Blockchain.info API Key</label>
								<input type="text" class="form-control" name="g_field_3">
							</div>
							<div class="checkbox">
								<label>
								  <input type="checkbox" name="allow_send" value="yes"> Allow customers to send money through this gateway
								</label>
							</div>
                            <div class="checkbox">
								<label>
								  <input type="checkbox" name="require_login" value="yes"> Require user to login before exchange
								</label>
							</div> 
							<div class="checkbox">
								<label>
								  <input type="checkbox" name="require_email_verify" value="yes"> Require user to verify their email address before exchange
								</label>
							</div>
							<div class="checkbox">
								<label>
								  <input type="checkbox" name="require_document_verify" value="yes"> Require user to verify their identify before exchange
								</label>
							</div>
	<?php
} else {
	echo 'Unknown merchant.';
}
?>