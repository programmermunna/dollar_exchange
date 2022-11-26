<style type="text/css">
	.tab_emailtemplate {
		border:1px solid #c1c1c1;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		padding:10px;
	}
	.tab_emailtemplate tbody {
		padding:20px;
	}
	.tab_emailtemplate tbody > tr {
		padding:20px;
	}
</style>
<!-- Modified Script by DoridroTech.Com -->
<div style="background: linear-gradient(#1f6142,#1f6142);">
<table width="600px" align="center" class="tab_emailtemplate">
<tbody style="padding:20px;">
	<tr>
		<td><img src="[@url]app/templates/Email_Templates/logo.png" alt="[@name]"><br><br></td>
	</tr>
	<tr>
		<td>
			<center>
				<h3>You have new exchange order request..</h3>
				<h5>Exchange from [@amount_send] [@gateway_send_name] [@gateway_send_currency] to [@amount_receive] [@gateway_receive_name] [@gateway_receive_currency]</h5>
				<h5>Login to take action: <a href="[@url]app/admin">[@url]app/admin</a></h5>
				<br><br>
				Regards,
				[@name]
			</center>
		</td>
	</tr>
</tbody>
</table></div>