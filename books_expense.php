<!DOCTYPE HTML>
<?PHP
	require 'functions.php';
	check_logon();
	connect();
	$timestamp = time();
	
	//NEW EXPENDITURE-Button
	if(isset($_POST['expnew'])){
		
		//Sanitize user input
		$exptype_id = sanitize($_POST['exptype_id']);
		$exp_amount = sanitize($_POST['exp_amount']);
		$exp_date = strtotime(sanitize($_POST['exp_date']));
		$exp_text = sanitize($_POST['exp_text']);
		$exp_recipient = sanitize($_POST['exp_recipient']);
		$exp_receipt = sanitize($_POST['exp_receipt']);
		$exp_voucher = sanitize($_POST['exp_voucher']);
		
		//Insert into expenses
		$sql_expnew = "INSERT INTO expenses (exptype_id, exp_amount, exp_date, exp_text, exp_recipient, exp_receipt, exp_voucher, exp_created, user_id) VALUES ('$exptype_id', '$exp_amount', '$exp_date','$exp_text', '$exp_recipient', '$exp_receipt', '$exp_voucher', '$timestamp', '$_SESSION[log_id]')";
		$query_expnew = mysql_query($sql_expnew);
		check_sql($query_expnew);
	}
			
	//Select current expenses from expenses
	$sixtydays = time() - 5184000;
	$sql_expcur = "SELECT * FROM expenses, exptype WHERE expenses.exptype_id = exptype.exptype_id AND exp_date > $sixtydays ORDER BY exp_date DESC, exp_voucher DESC";
	$query_expcur = mysql_query($sql_expcur);
	check_sql($query_expcur);
	
	//Select Types of Expenses from EXPTYPE
	$sql_exptype = "SELECT * FROM exptype";
	$query_exptype = mysql_query($sql_exptype);
	check_sql($query_exptype);
?>

<html>
	<?PHP include_Head('Expenses',0) ?>	
		<script>
			function validate(form){
				fail = validateDate(form.exp_date.value)
				fail += validateAmount(form.exp_amount.value)
				fail += validateRecipient(form.exp_recipient.value)
				fail += validateVoucher(form.exp_voucher.value)
				if (fail == "") return true
				else { alert(fail); return false }
			}
		</script>
		<script src="functions_validate.js"></script>
		<script src="function_randCheck.js"></script>
	</head>
	
	<body>
	
		<!-- MENU -->
		<?PHP 
				include_Menu(4);
		?>
	
		<!-- MENU MAIN -->
		<div id="menu_main">
			<a href="start.php">Back</a>
			<a href="books_expense.php" id="item_selected">Expenses</a>
			<a href="books_income.php">Incomes</a>
			<a href="books_annual.php">Annual Accounts</a>
		</div>
		
			
		<!-- LEFT SIDE: Form for entering new expense -->
		<div class="content_left">
			
			<p class="heading_narrow">New Expense</p>
			<form action="books_expense.php" method="post" onSubmit="return validate(this)">
				
				<table id="tb_fields">
					<tr>
						<td>Date:</td>
						<td><input type="text" name="exp_date" value="<?PHP echo date("d.m.Y", $timestamp); ?>"/></td>
						<td>Type:</td>
						<td>
							<select name="exptype_id">
								<?PHP
								while ($row_exptype = mysql_fetch_assoc($query_exptype)){
									echo '<option value="'.$row_exptype['exptype_id'].'">'.$row_exptype['exptype_type'].'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Amount:</td>
						<td><input type="number" name="exp_amount" placeholder="<?PHP echo $_SESSION['set_cur']; ?>" /></td>
						<td>Recipient:</td>
						<td><input type="text" name="exp_recipient"/></td>
					</tr>
					<tr>
						<td>Voucher No:</td>
						<td><input type="text" name="exp_voucher"/></td>
						<td>Receipt No:</td>
						<td><input type="text" name="exp_receipt" placeholder="if any"/></td>
					</tr>
					<tr>
						<td>Details:</td>
						<td><input type="text" name="exp_text"/></td>
					</tr>
				</table>
				<input type="submit" name="expnew" value="New Entry" />
			</form>
		</div>
		
		<!-- RIGHT SIDE: Expenses of the current month -->
		<div class="content_right">
			<table id="tb_table">
				<tr>
					<th class="title" colspan="7">Current Expenses</th>
				</tr>
				<tr>
					<th>Date</th>
					<th>Type</th>
					<th>Amount</th>
					<th>Recipient</th>
					<th>Details</th>
					<th>Voucher</th>
					<th>Delete</th>
				</tr>
			<?PHP
			$color=0;
			while ($row_expcur = mysql_fetch_assoc($query_expcur)){
				tr_colored($color); 		//Alternating row colors
				echo '	<td>'.date("d.m.Y",$row_expcur['exp_date']).'</td>
								<td>'.$row_expcur['exptype_type'].'</td>
								<td>'.number_format($row_expcur['exp_amount']).' '.$_SESSION['set_cur'].'</td>
								<td>'.$row_expcur['exp_recipient'].'</td>
								<td>'.$row_expcur['exp_text'].'</td>
								<td>'.$row_expcur['exp_voucher'].'</td>
								<td>';
								if ($_SESSION['log_delete'] == 1) echo '<a href="books_expense_del.php?exp_id='.$row_expcur['exp_id'].'" onClick="return randCheck();" ><i class="fa fa-remove fa-lg"></i></a>';
				echo '	</td>
							</tr>';
			}
			?>
			</table>
		</div>
	</body>	
</html>