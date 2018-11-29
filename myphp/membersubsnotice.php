<?php

// include file with login details
include("../xzlog.php");

$user =& JFactory::getUser();
 

// Get email which is user id
$mememail = $user->username;

// use member email to retrieve member id
$retval=mysql_connect($dbhostname,$username,$userpassword);
mysql_select_db($database) or die( "Unable to select database");
$query = "select memberid from members where memberemail='$mememail'";
$result = mysql_query($query);

$num = mysql_numrows($result);
if ($num > 0)  // if we have a valid result
{
	$memid= mysql_result($result,0,"MemberID");
	$query="SELECT * FROM members WHERE MemberID = $memid";
	$result = mysql_query($query);

	$memfirstname = mysql_result($result,0,"MemberFirstname");
	$memsurname = mysql_result($result,0,"MemberSurname");
	$currentsubspaid = mysql_result($result,0,"CurrentSubsPaid");
	$memtype= mysql_result($result,0,"MemberType");
	$loa= mysql_result($result,0,"MemberLeaveOfAbsence");
	$query="SELECT sum(Amount) as balance FROM finances WHERE MemberID='$memid' ";
	$result = mysql_query($query);
	$balance= mysql_result($result,0,"balance");
	$query2 = "select * from oscmemberrates where year='2019' "; // TODO get from database for current year.
	$result2 = mysql_query($query2);
	$graduaterate = mysql_result($result2,0,"Graduate");
	
	$spouserate= mysql_result($result2,0,"Spouse");
	$buddyrate= mysql_result($result2,0,"Student");
	$childrate= mysql_result($result2,0,"Child");
	$studentrate= mysql_result($result2,0,"Student");
	$lockerrate= mysql_result($result2,0,"Locker");
	$summerrate= mysql_result($result2,0,"Summer");
	

	//$form = "Your subscriptions for 2015 are as follows:<p>";
	$form = "<table border='0' cellpadding='2' cellspacing='2' width='300'>";
	$form .= "<tr><th align='left'>Subscription item added</th><th b'>Amount</th><th >Paid</th></tr>";
	
	$rc=0;
	if ($rc % 2) {$oddstring = "";} else {$oddstring = "class=\"odd\"";}

	if ($loa == 'Yes')
	{
		$graduaterate = 0;
		$studentrate = 0;
		$spouserate = 0;
		$childrate=0;
		$form .= "<tr $oddstring><td>On Leave of Absence</td><td align='right'>$ -</td></tr>";
	}
	
	// Graduate
	if ($memtype == 'Graduate')
	{
		$form .= "<tr $oddstring><td>$memtype subscription for $memfirstname </td><td align='right'>$ $graduaterate</td><td>$currentsubspaid</td></tr>";
	}
	elseif ($memtype == 'Student')
	{
		$form .= "<tr $oddstring><td>$memtype subscription for $memfirstname </td><td align='right'>$ $studentrate</td><td>$currentsubspaid</td></tr>";
	}
	elseif ($memtype == 'Life' || $memtype == 'Hon Life')
	{
		$form .= "<tr $oddstring><td>$memtype subscription for $memfirstname </td><td align='right'>$ 0.00</td><td>N/A</td></tr>";
	}
	else
	{
		$form .= "<tr $oddstring><td>No subscription for Non-member</td><td align='right'>$ -</td><td>&nbsp;</td></tr>";
	}
	$rc++;
	// Spouse/Buddy
	$query2="SELECT * FROM familymembers WHERE MemberID='$memid' ";
	$result2 = mysql_query($query2);
	$num2 = mysql_numrows($result2);
	
	if ($num2 > 0)
	{
		$i2=0;
		while ($i2 < $num2) // scroll through the entries
		{
			$fammemno = mysql_result($result2,$i2,"FamilyMemberID");
			$fammemfirstname = mysql_result($result2,$i2,"FamilyMemberFirstname");
			$fammemsurname = mysql_result($result2,$i2,"FamilyMemberSurname");
			$fammemtype = mysql_result($result2,$i2,"FamilyMembershipType");
			$fammemcurrentsubspaid = mysql_result($result2,$i2,"CurrentSubsPaid");
			
			if ($rc % 2) {$oddstring = "";} else {$oddstring = "class=\"odd\"";}
			if ($fammemtype == "Spouse")
			{
				$form .= "<tr $oddstring><td>$fammemtype subscription for $fammemfirstname </td><td align='right'>$ $spouserate</td><td>$fammemcurrentsubspaid</td></tr>";
				$rc++;
			}
			if ($fammemtype == "Buddy")
			{
				$form .= "<tr $oddstring><td>$fammemtype subscription for $fammemfirstname </td><td align='right'>$ $spouserate</td><td>$fammemcurrentsubspaid</td></tr>";
				$rc++;
			}
			if ($fammemtype == "Child")
			{
				$form .= "<tr $oddstring><td>$fammemtype subscription for $fammemfirstname </td><td align='right'>$ $childrate</td><td>$fammemcurrentsubspaid</td></tr>";
				$rc++;
			}
			$i2++; // Increment counter
			
		}
	}
	// Locker
	$query3="SELECT * FROM lockers WHERE MemberID='$memid'";
	$result3 = mysql_query($query3);
	$num3 = mysql_numrows($result3);
	if ($num3 > 0 )
	{
		$i3=0;
		while ($i3 < $num3)
		{
			$locker=mysql_result($result3,$i3,"LockerNumber");
			$billed=mysql_result($result3,$i3,"BilledAnnually");
			$lastyear=mysql_result($result3,$i3,"lastyear");
			$lockercurrentsubspaid = mysql_result($result3,$i3,"CurrentSubsPaid");
			if ($rc % 2) {$oddstring = "";} else {$oddstring = "class=\"odd\"";}
			if ($billed == "Yes" )
			{
				$form .= "<tr $oddstring><td>Locker $locker annual subscription</td><td align='right'>$ $lockerrate</td><td>$lockercurrentsubspaid</td></tr>";
			}
			else
			{
				$form .= "<tr $oddstring><td>Locker $locker paid to end of $lastyear</td><td align='right'>$ 0.00</td><td>N/A</td></tr>";
			}
			$i3++; 
			$rc++;
		}
	} 
	// check if balance owed
	
	if ($balance < 0)
	{
		$owing = "Please pay the total:";
	}
	else
	{
		$owing = "No payment required.";
	}
	if ($rc % 2) {$oddstring = "";} else {$oddstring = "class=\"odd\"";}
	$form .= "<tr $oddstring> <td align='right'><b>$owing</b> </td><td align='right'>$ $balance</td></tr>";
	$form .= "</table>";
}
else
{
	$form = "<font color='red'>Your email address is invalid, please contact the Membership officer.</font>";
}
echo $form;
mysql_free_result($result);
mysql_free_result($result2);
mysql_free_result($result3);
?>