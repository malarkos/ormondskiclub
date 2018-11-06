<?php

   	session_start();
	// check for valid memid, if not, go to memberlogin.php
	if (!isset($_SESSION['adminmemid']))
	{
		header("Location: bookingadminlogin.php");
	}
	function getyear($txt)
    {
	// expects string yyyy-mm-dd, returns yyyy
	
	   return substr($txt,0,4);
    }
	function getmonth($txt)
    {
	// expects string yyyy-mm-dd, returns yyyy
	
	   return substr($txt,5,2);
    }
	function getday($txt)
    {
	// expects string yyyy-mm-dd, returns yyyy
	
	   return substr($txt,8,2);
    }
	
	include("../ormond1.css");
	include("../xzlog.php");
	$retval=mysql_connect($dbhostname,$username,$userpassword);
	mysql_select_db($database) or die("Unable to select database");
	$bookingstatusval[0] = "Submitted";$bookingstatusval[1] = "Approved";$bookingstatusval[2] = "Confirmed";
	$now = date("z");  // day of the year
	$nowdate = date("d/m/y");
	$form = "<table><tr><td><h2>2018 Chronological Booking List</h2></td></tr>";
	$form .= "<tr><td class='text2'><A href='bookingadmin.php'>Home</a> -- Current Bookings as of $nowdate</td></tr></table>";
			$form .= "<table border='1' cellspacing='0'><tr><th class='th1'>ID</th><th class='th1'>Booking Date</th><th class='th1'>bk age</th><th class='th1'>Book Alt</th><th class='th1'>Alt Age</th><th class='th1'>Reference</th><th class='th1'>Member</th><th class='th1'>Cost</th><th class='th1'>Paid</th><th class='th1'>Balance</th><th class='th1'>Email</th><th class='th1'>Receipt</th><th class='th1'>Method</th><th class='th1'>FP?</th><th class='th1'>Status</th><th class='th1'>BN</th><th class='th1'>SBN</th><th class='th1'>Same</th></tr>";
		$totalcost =0;
				$totalpaid=0;
		$totalbalance = 0;
		$totalroomnights = 0;
		$totalsumbn=0;
		$totalbooked=0;$totalapproved=0;$totalconfirmed=0;
	for ($kk=0;$kk<3;$kk++)
	{
		$bookval = $bookingstatusval[$kk];
		
	
	
		// cycle through all bookings
		$query = "select *,date_format(bookingmade,'%d %M %Y') as bookingmadedate, date_format(bookingaltered,'%d %M %Y') as bookingaltereddate from booking_summary where bookingstatus = '$bookval' and bookingref like '2018%' order by bookid desc";
		$result = mysql_query($query) or die("Unable to get member list");
		$num = mysql_numrows($result);
		$i=0;
		
		
		  
		$form .= "<tr><td colspan='18'><h4>$bookval</h4></td></tr>";

		for($i=0;$i<$num;$i++)
		{
			$id = mysql_result($result,$i,"bookid");
			$ref = mysql_result($result,$i,"bookingref");
			$member = mysql_result($result,$i,"memid");
			$query1 = "select memberfirstname,membersurname from members where memberid='$member'";
			$result1 = mysql_query($query1);
			$firstname = mysql_result($result1,0,"memberfirstname");
			$surname = mysql_result($result1,0,"membersurname");
			$cost = mysql_result($result,$i,"bookingcost");
			$totalcost += $cost;
			$paid = mysql_result($result,$i,"amountpaid");
			$paymethod = mysql_result($result,$i,"paymentmethod");
			if ($paymethod == "internettransfer") $paymethodval = "I/T";
			if ($paymethod == "cheque") $paymethodval = "Chq";
			$balance = $cost - $paid;
			$totalbalance +=$balance;
			$balance = sprintf("%01.2f",$balance);
			if ($paid < $cost) 
			{$fullypaid = "<font color='red'>No</font>";
			$rowstart = "";
			$rowend = "";}
			else
			{$fullypaid = "Yes";
			$rowstart = "";
			$rowend = "";}
			
			$totalpaid += $paid;
			$status = mysql_result($result,$i,"bookingstatus");
			if (strcmp($status,"Submitted") == 0) $totalbooked++;
			if (strcmp($status,"Approved") == 0 ) $totalapproved++;
			if (strcmp($status,"Confirmed") == 0 ) $totalconfirmed++;
			$datebooked =mysql_result($result,$i,"bookingmadedate"); 
			$datealtered =mysql_result($result,$i,"bookingaltereddate"); 
			$nowdatebooking = date('z',strtotime($datebooked)); // convert booking date to day of year
			$nowaltdatebooking = date('z',strtotime($datealtered)); // convert booking date to day of year
			$diff = $now - $nowdatebooking;
			$diffaltered = $now - $nowaltdatebooking ;
			$query1 = "select count(*) as roomcount from booking_rooms where bookingref='$ref' ";
			$result1 = mysql_query($query1);
			$roomnights = mysql_result($result1,0,"roomcount");
			$totalroomnights += $roomnights;
			$query1 = "select * from booking_main where bookingref='$ref'";
			$result1 = mysql_query($query1);
			$num1 = mysql_numrows($result1);
			$summarybn=0;
			$daysec = 24*60*60;
			for($i1=0;$i1<$num1;$i1++)
			{
			 $datein = mysql_result($result1,$i1,"datein");
			 $dateout = mysql_result($result1,$i1,"dateout");
			 
				  $dateinyear = getyear($datein);
				  $dateinmonth = getmonth($datein);
				  $dateinday = getday($datein);
				  $dateinval = mktime(0,0,0,$dateinmonth,$dateinday,$dateinyear);
				  $dateoutyear = getyear($dateout);
				  $dateoutmonth = getmonth($dateout);
				  $dateoutday = getday($dateout);
				  $dateoutval = mktime(0,0,0,$dateoutmonth,$dateoutday,$dateoutyear);
				  for ($dateval=$dateinval;$dateval<$dateoutval;$dateval+=$daysec)
				  {
					  $summarybn++;
				  }
			}
			$totalsumbn+=$summarybn;
			$same = "y";
			if ($roomnights <> $summarybn) $same = "NN";
			if ($balance > 0) // if still cost outstanding, show as red if older than five days
			{
				if ($diff > 5)
				{
					$diff = "<font color='red'>$diff</font>";
				}
				if ($diffaltered > 5)
				{
					$diffaltered = "<font color='red'>$diffaltered</font>";
				}
			} //if
			$form .="<tr><td>$id</td><td>$datebooked</td><td align='center'>$diff</td><td align='center'>$datealtered</td><td align='center'>$diffaltered</td>";
			$form .= "<td><Form action='currentbookings.php' method='POST'><input style='font-family: Verdana, Helvetica; font-weight:bold;font-size: 7pt' type='submit' value='$ref'><input type='hidden' name = 'bookingmember' Value='$member'>
		<input type='hidden' name = 'curbooking' Value='$ref'></form></td>";
			$form .= "<td>$firstname $surname</td><td align='right'>$$cost</td><td align='right'>$$paid</td><td align='right'>$$balance</td>";
			$form .= "<td><Form action='notifymemberrebooking.php' method='POST'><input style='font-family: Verdana, Helvetica; font-weight:bold;font-size: 7pt' type='submit' value='Email Member'><input type='hidden' name = 'bookingmember' Value='$member'>
		<input type='hidden' name = 'curbooking' Value='$ref'></form></td>";
		$form .= "<td><Form action='mailreceipt.php' method='POST'><input style='font-family: Verdana, Helvetica; font-weight:bold;font-size: 7pt' type='submit' value='Receipt'><input type='hidden' name = 'bookingmember' Value='$member'>
		<input type='hidden' name = 'curbooking' Value='$ref'></form></td>";
			$form .= "<td align='center'>$paymethodval</td><td align='center'>$fullypaid</td><td align='center'>$status</td><td align='right'>$roomnights</td><td align='right'>$summarybn</td><td align='center'>$same</td></tr>";
		}

		
		// Free results
		mysql_free_result($result);
		if ($num > 0) mysql_free_result($result1);
		
	} // for
	$totalcost = sprintf("%01.2f",$totalcost);
	$totalpaid = sprintf("%01.2f",$totalpaid);
	$totalbalance = sprintf("%01.2f",$totalbalance);
	$form .= "<tr><td colspan='18'><h4>Totals</h4></td></tr>";	
	$form .= "<tr><td colspan='7' align='right'><b>Total</b></td><td align='right' class='td2'><B>$$totalcost</B></td><td align='right' class='td2'><B>$$totalpaid</B></td><td align='right' class='td2'><B>$$totalbalance</B></td><td colspan='5' align='right'>Room nights</td><td align='right'><b>$totalroomnights</b></td><td align='right'><b>$totalsumbn</b></td><Td>&nbsp;</td></tr></table>";
	$form .= "<Br><table border='1' cellspacing='0'>";
	$form .= "<tr><th class='th1'>Status</th><th class='th1'>Count</th></tr>";
	$form .= "<tr><td align='right' class='td2'>Booked</td><td align='right' class='td2'>$totalbooked</td></tr>";
	$form .= "<tr><td align='right' class='td2'>Approved</td><td align='right' class='td2'>$totalapproved</td></tr>";
	$form .= "<tr><td align='right' class='td2'>Confirmed</td><td align='right' class='td2'>$totalconfirmed</td></tr>";
		
	$form .= "</table>";
	echo $form;
?>
