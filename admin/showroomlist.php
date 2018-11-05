<?php
session_start ();
if (! isset ( $_SESSION ['adminmemid'] )) {
	header ( "Location: bookingadminlogin.php" );
}
# version 1.1
# updated 06 Nov 2018

# Version history
# 1.0 - Original version 06 Nov 2018
# 1.1 - Enable all people to be Lodge Leaders

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Room & Job Allocations</title>
<script language="JavaScript">
<!-- hide from JavaScript-challenged browsers
function openWindow(url)
{popupWin = window.open(url, 'remote',
'menubar=no, toolbar=no, location=no, directories=no, status=no, scrollbars=yes, resizable=yes, dependent, width=600, height=600, left=50, top=50')
}


// done hiding -->
</script>
</head>

<body>
<?php
include ('ormond1.css');
include ("../xzlog.php");
function lowerfirstletter($txt) {
	// expects string yyyy-mm-dd, returns yyyy
	return strtolower ( substr ( $txt, 0, 1 ) );
}
// functions
function create_member_list($thisday) {
    
    # function to return list of those who can be Lodge Leaders.
    # current version on returns Members
    # update to include all members, family members and buddies
	$form1 = "<td class='ms-column3-even '>";
	$form1 .= "<form><select>";
	$form1 .= "<option></option>"; // have blank for manually writing in someone else
	//$query = "SELECT DISTINCT memid FROM booking_rooms WHERE roomnight='$thisday' AND memguest='m' ORDER BY memid"; // get all members who have bookings
	$query = "SELECT * FROM booking_rooms WHERE roomnight='$thisday' "; // get all entries for that day
	$result = mysql_query ( $query );
	$num = mysql_numrows ( $result );
	
	for($i = 0; $i < $num; $i ++) {  // for each entry
		$memberval = mysql_result ( $result, $i, "memid" );
		$memguest = mysql_result ( $result, $i, "memguest" );
		
		
		if (strcmp ( $memguest, 'm' ) == 0) {
    		$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memberval'";
    		$result1 = mysql_query ( $query1 );
    		$firstname = mysql_result ( $result1, 0, "MemberFirstname" );
    		$surname = mysql_result ( $result1, 0, "MemberSurname" );
    		mysql_free_result ( $result1 );
    		$form1 .= "<option>$firstname $surname</option>";
		} 
		else if ((strcmp ( $memguest, 'f' ) == 0) || (strcmp ( $memguest, 'c' ) == 0)) 
		{
		   
		        $query1 = "select familyMemberFirstname, familyMemberSurname from familymembers where familyMemberID='$memberval'";
		        $result1 = mysql_query ( $query1 );
		        $firstname = mysql_result ( $result1, 0, "familyMemberFirstname" );
		        $surname = mysql_result ( $result1, 0, "familyMemberSurname" );
		        mysql_free_result ( $result1 );
		        $form1 .= "<option>$firstname $surname</option>";
		} 
		else if ((strcmp ( $memguest, 'b' ) == 0))
		{
		    $firstname = mysql_result ( $result, $i, "guestfirstname" );
		    $surname = mysql_result ( $result, $i, "guestsurname" );
		    $form1 .= "<option>$firstname $surname</option>";
		}
		// now check for all family and buddy members on the booking
	}
	$form1 .= "</select></form></td>";
	return $form1;
}
function memberagestring($memberage) {
	if ((strcmp ( $memberage, '0-2' ) == 0) || (strcmp ( $memberage, '3' ) == 0) || (strcmp ( $memberage, '4' ) == 0) || (strcmp ( $memberage, '5' ) == 0) || (strcmp ( $memberage, '6' ) == 0) || (strcmp ( $memberage, '7' ) == 0) || (strcmp ( $memberage, '8' ) == 0) || (strcmp ( $memberage, '9' ) == 0) || (strcmp ( $memberage, '10' ) == 0) || (strcmp ( $memberage, '11' ) == 0) || (strcmp ( $memberage, '12' ) == 0) || (strcmp ( $memberage, '13' ) == 0) || (strcmp ( $memberage, '14' ) == 0) || (strcmp ( $memberage, '15' ) == 0) || (strcmp ( $memberage, '16' ) == 0) || (strcmp ( $memberage, '17' ) == 0)) {
		
		$mstring = '(' . $memberage . ')';
	} else {
		$mstring = '';
	}
	
	return $mstring;
}
function getbookingmember($bookingref) 

{
	// Function to retrieve booking member from bookingref - if 534, get from booking_summary.
	$query5 = "select * from booking_summary where bookingref=$bookingref";
	$result5 = mysql_query ( $query5 );
	$num5 = mysql_numrows ( $result5 );
	if ($num5 > 0) {
		$memberid = mysql_result ( $result5, 0, "memid" );
		mysql_free_result ( $result5 );
		return $memberid;
	} else {
		mysql_free_result ( $result5 );
		return 0;
	}
}

$retval = mysql_connect ( $dbhostname, $username, $userpassword );
mysql_select_db ( $database ) or die ( "Unable to select database" );

$form .= "<h4>Room & Job Allocations - Please stay in your assigned rooms!</h4><table>";
$form .= "<tr><td class='text2'><A href='bookingadmin.php'>Home</a> -- Show Rooms Allocations</td>"; // </tr></table>";

#TODO get from database

$room [0] = "Any";
$room [1] = "Rm 1";
$room [2] = "Rm 2";
$room [3] = "Rm 3";
$room [4] = "Rm 4";
$room [5] = "Rm 5";
$room [6] = "Rm 6";
$room [7] = "Rm 7";
$room [8] = "Rm 8";
$room [9] = "Rm 9";
$room [10] = "Rm 10";
$room [11] = "Rm 11";
$room [12] = "Rm 12";
$room [13] = "Rm 14";
$room [14] = "Rm 15";
$bedmax [0] = "42";
$bedmax [1] = "3";
$bedmax [2] = "2";
$bedmax [3] = "4";
$bedmax [4] = "4";
$bedmax [5] = "2";
$bedmax [6] = "2";
$bedmax [7] = "4";
$bedmax [8] = "2";
$bedmax [9] = "2";
$bedmax [10] = "2";
$bedmax [11] = "2";
$bedmax [12] = "5";
$bedmax [13] = "4";
$bedmax [14] = "4";

if (! isset ( $_SESSION ['startdate'] )) {
    $startdate = mktime ( 0, 0, 0, 6, 3, 2018 ); #TODO get from database
	$_SESSION ['startdate'] = $startdate;
} else {
	$startdate = $_SESSION ['startdate'];
}

$dayinc = 24 * 60 * 60; // seconds in a day
$weekinc = 7 * $dayinc;
// Show Friday / Saturday before as well
// $startdate += $dayinc; // For windows - take out for website
$showDate = date ( "d/m/y", $startdate );
// echo "startdate is $showDate";
// $startdate += $dayinc;
// Show the options to update week
$form .= "<td>";
$showDate = date ( "d/m/y", $startdate );

$showdaydate = date ( "l d/m/y", $startdate );
// echo $showdaydate;
$selectdate = date ( "z", $startdate );
// $form .= "<td class='td2'>Week starting <B>$showDate<b> </td>";
// $form .= "<td>";
$form .= "<Form action='roomlistviewchange.php' method='POST'><select name='showweek'>";
$startselect = mktime ( 0, 0, 0, 6, 3, 2018 ); #TODO get from database
$endselect = mktime ( 0, 0, 0, 10, 7, 2018 ); #TODO get from database
$i = 1;
for($showselect = $startselect; $showselect < $endselect; $showselect += $weekinc) {
	$locdate = $showselect + 5 * $dayinc;
	$locdate2 = $locdate + 2 * $dayinc;
	$showdateselect = date ( "D d/m/y", $showselect );
	$dayval = date ( "z", $showselect );
	if ($selectdate == $dayval) {
		$selectval = "selected";
	} else {
		$selectval = "";
	}
	$showlocdate = date ( "D d/m/y", $locdate );
	$showlocdate2 = date ( "D d/m/y", $locdate2 );
	
	$form .= "<option value='$dayval' $selectval>$showdateselect - $showlocdate</option>";
	$i ++;
	$dayval = date ( "z", $locdate );
	if ($selectdate == $dayval) {
		$selectval = "selected";
	} else {
		$selectval = "";
	}
	$form .= "<option value='$dayval' $selectval>$showlocdate - $showlocdate2</option>";
	$i ++;
}
$form .= "</select></td><td><input style='font-family: Verdana, Helvetica; font-weight:bold;font-size: 9pt' type='submit' value='Change Week to view'></form></td>";
$form .= "</td></tr></table border='1'>";
$showDate1 = date ( "d/m/y", $startdate );
// $form .= "-$showDate1-";
$locdate = $startdate + 5 * $dayinc;
$locdate2 = $locdate + 2 * $dayinc;
$showfulllocdate = date ( "l d/m/y", $locdate );
$showfulllocdate2 = date ( "l d/m/y", $locdate2 );

$enddate = mktime ( 0, 0, 0, 10, 7, 2018); #TODO get from database
$startdate1 = $startdate - (2 * $dayinc);
// $startdate1text = date("l d/m/y",$startdate1);
// echo "Startdate 1 = $startdate1text";
// Create form
// check if five days or two
$dayofweek = date ( "w", $startdate );
// echo "dayofweek = $dayofweek";
// $form .= "startdate = $showDate";
if ($dayofweek == 0) // Sunday
{
	// $form .= "Sunday - Thursday nights"
	// $form .= "<h3>Weekly Bed list $showdaydate to $showfulllocdate</h3>";
	$form .= "<table class='ms-column3-main'>";
	$sundaydate = date ( "d/n", $startdate );
	$mondaydate = date ( "d/n", $startdate + $dayinc );
	$tuesdaydate = date ( "d/n", $startdate + 2 * $dayinc );
	$wednesdaydate = date ( "d/n", $startdate + 3 * $dayinc );
	$thursdaydate = date ( "d/n", $startdate + 4 * $dayinc );
	$fridaydate = date ( "d/n", $startdate + 5 * $dayinc );
	
	$form .= "<tr><td class='ms-column3-tl'>Bed Night</td>
						<td class='ms-column3-top'>Sunday $sundaydate</td>
						<td class='ms-column3-top'>Monday $mondaydate</td>
						<td class='ms-column3-top'>Tuesday $tuesdaydate</td>
						<td class='ms-column3-top'>Wednesday $wednesdaydate</td>
						<td class='ms-column3-top'>Thursday $thursdaydate</td></tr>";
	$form .= "<tr><td class='ms-column3-tl'>Duty Day</td>
		<td class='ms-column3-top'>Monday $mondaydate</td>
		<td class='ms-column3-top'>Tuesday $tuesdaydate</td>
		<td class='ms-column3-top'>Wednesday $wednesdaydate</td>
		<td class='ms-column3-top'>Thursday $thursdaydate</td>
		<td class='ms-column3-top'>Friday $fridaydate</td></tr>";
	
	//
	$form .= "<tr><td class='ms-column3-tl'>Lodge Leader</td>";
	$headerdate = $startdate;
	$thisday = date ( "Y-m-d", $headerdate );
	$form .= create_member_list ( $thisday );
	$headerdate += $dayinc;
	$thisday = date ( "Y-m-d", $headerdate );
	$form .= create_member_list ( $thisday );
	$headerdate += $dayinc;
	$thisday = date ( "Y-m-d", $headerdate );
	$form .= create_member_list ( $thisday );
	$headerdate += $dayinc;
	$thisday = date ( "Y-m-d", $headerdate );
	$form .= create_member_list ( $thisday );
	$headerdate += $dayinc;
	$thisday = date ( "Y-m-d", $headerdate );
	$form .= create_member_list ( $thisday );
	// $form .= "<td class='ms-column3-odd1 '><form><input type='text' size='20'></input></form></td>";
	// $form .= "<td class='ms-column3-right '><form><input type='text' size='20'></input></form></td>";
	// $form .= "<td class='ms-column3-right '><form><input type='text' size='20'></input></form></td>";
	// $form .= "<td class='ms-column3-right '><form><input type='text' size='20'></input></form></td>";
	$form .= "</tr>";
	
	for($i = 0; $i < 15; $i ++) // Change to start at 0 to also show the Any rooms
{
		$daydate = $startdate;
		$form .= "<tr>";
		$form .= "<td class='ms-column3-left'>$room[$i]</td>";
		$form .= '<td class="ms-column3-even">';
		$thisday = date ( "Y-m-d", $daydate );
		$daydate += $dayinc;
		$roomval = $room [$i];
		$query = "select * from booking_rooms where roomnight='$thisday' and room = '$roomval'  and bookingref <> '0' and bookingstatus in ('Submitted','Approved','Confirmed')";
		$result = mysql_query ( $query );
		$num = mysql_numrows ( $result );
		if ($num > 0) {
			$form .= "<table style='width:100%'>";
			for($k = 0; $k < $num; $k ++) {
				$memberval = mysql_result ( $result, $k, "memid" );
				$bookingref = mysql_result ( $result, $k, "bookingref" );
				$guestnum = mysql_result ( $result, $k, "guestnum" );
				$query1 = "select age,vegetarian from booking_main where bookingref ='$bookingref' and guestnum='$guestnum'";
				$result1 = mysql_query ( $query1 );
				$memberage = mysql_result ( $result1, 0, "age" );
				$vegetarian = mysql_result ( $result1, 0, "vegetarian" );
				$memberagestring = memberagestring ( $memberage );
				if (strcmp ( $vegetarian, 'y' ) == 0) {
					$vegstring = '(V)';
				} else {
					$vegstring = '';
				}
				
				mysql_free_result ( $result1 );
				
				$memid = getbookingmember ( $bookingref );
				$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memid'";
				$result1 = mysql_query ( $query1 );
				$memberfirstname = mysql_result ( $result1, 0, "MemberFirstname" );
				$membersurname = mysql_result ( $result1, 0, "MemberSurname" );
				$memberinit = lowerfirstletter ( $memberfirstname ) . lowerfirstletter ( $membersurname );
				mysql_free_result ( $result1 );
				
				$memguest = mysql_result ( $result, $k, "memguest" );
				$firstname = mysql_result ( $result, $k, "guestfirstname" );
				$surname = mysql_result ( $result, $k, "guestsurname" );
				if (strcmp ( $memguest, 'm' ) == 0) {
					$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "MemberFirstname" );
					$surname = mysql_result ( $result1, 0, "MemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname <sup>$vegstring</sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				} else if ((strcmp ( $memguest, 'f' ) == 0) || (strcmp ( $memguest, 'c' ) == 0)) {
					$query1 = "select familyMemberFirstname, familyMemberSurname from familymembers where familyMemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "familyMemberFirstname" );
					$surname = mysql_result ( $result1, 0, "familyMemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname<sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				} else {
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname<sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				}
			}
			$form .= "</table>";
		} else {
			$form .= ' - ';
		}
		mysql_free_result ( $result );
		
		$form .= '</td>';
		$form .= '<td class="ms-column3-odd">';
		$thisday = date ( "Y-m-d", $daydate );
		$daydate += $dayinc;
		$roomval = $room [$i];
		$query = "select * from booking_rooms where roomnight='$thisday' and room = '$roomval'  and bookingref <> '0' and bookingstatus in ('Submitted','Approved','Confirmed')";
		$result = mysql_query ( $query );
		$num = mysql_numrows ( $result );
		if ($num > 0) {
			$form .= "<table style='width:100%'>";
			
			for($k = 0; $k < $num; $k ++) {
				$memberval = mysql_result ( $result, $k, "memid" );
				$bookingref = mysql_result ( $result, $k, "bookingref" );
				$guestnum = mysql_result ( $result, $k, "guestnum" );
				$query1 = "select age,vegetarian from booking_main where bookingref ='$bookingref' and guestnum='$guestnum'";
				$result1 = mysql_query ( $query1 );
				$memberage = mysql_result ( $result1, 0, "age" );
				$vegetarian = mysql_result ( $result1, 0, "vegetarian" );
				$memberagestring = memberagestring ( $memberage );
				if (strcmp ( $vegetarian, 'y' ) == 0) {
					$vegstring = '(V)';
				} else {
					$vegstring = '';
				}
				mysql_free_result ( $result1 );
				$memid = getbookingmember ( $bookingref );
				
				$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memid'";
				$result1 = mysql_query ( $query1 );
				$memberfirstname = mysql_result ( $result1, 0, "MemberFirstname" );
				$membersurname = mysql_result ( $result1, 0, "MemberSurname" );
				$memberinit = lowerfirstletter ( $memberfirstname ) . lowerfirstletter ( $membersurname );
				mysql_free_result ( $result1 );
				$memguest = mysql_result ( $result, $k, "memguest" );
				$firstname = mysql_result ( $result, $k, "guestfirstname" );
				$surname = mysql_result ( $result, $k, "guestsurname" );
				if (strcmp ( $memguest, 'm' ) == 0) {
					$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "MemberFirstname" );
					$surname = mysql_result ( $result1, 0, "MemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-odd1'>$firstname $surname <sup>$vegstring</sup></td><td class='ms-column3-odd2'>&nbsp</td></tr>";
				} else if ((strcmp ( $memguest, 'f' ) == 0) || (strcmp ( $memguest, 'c' ) == 0)) {
					$query1 = "select familyMemberFirstname, familyMemberSurname from familymembers where familyMemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "familyMemberFirstname" );
					$surname = mysql_result ( $result1, 0, "familyMemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-odd1'>$firstname $surname <sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-odd2'>&nbsp</td></tr>";
				} else {
					$form .= "<tr><Td class='ms-column3-odd1'>$firstname $surname <sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-odd2'>&nbsp</td></tr>";
				}
			}
			$form .= "</table>";
		} else {
			$form .= ' - ';
		}
		mysql_free_result ( $result );
		$form .= '</td>';
		$form .= '<td class="ms-column3-even">';
		$thisday = date ( "Y-m-d", $daydate );
		$daydate += $dayinc;
		$roomval = $room [$i];
		$query = "select * from booking_rooms where roomnight='$thisday' and room = '$roomval'  and bookingref <> '0' and bookingstatus in ('Submitted','Approved','Confirmed')";
		$result = mysql_query ( $query );
		$num = mysql_numrows ( $result );
		if ($num > 0) {
			$form .= "<table style='width:100%'>";
			
			for($k = 0; $k < $num; $k ++) {
				$memberval = mysql_result ( $result, $k, "memid" );
				$bookingref = mysql_result ( $result, $k, "bookingref" );
				$guestnum = mysql_result ( $result, $k, "guestnum" );
				$query1 = "select age,vegetarian from booking_main where bookingref ='$bookingref' and guestnum='$guestnum'";
				$result1 = mysql_query ( $query1 );
				$memberage = mysql_result ( $result1, 0, "age" );
				$vegetarian = mysql_result ( $result1, 0, "vegetarian" );
				$memberagestring = memberagestring ( $memberage );
				if (strcmp ( $vegetarian, 'y' ) == 0) {
					$vegstring = '(V)';
				} else {
					$vegstring = '';
				}
				mysql_free_result ( $result1 );
				$memid = getbookingmember ( $bookingref );
				
				$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memid'";
				$result1 = mysql_query ( $query1 );
				$memberfirstname = mysql_result ( $result1, 0, "MemberFirstname" );
				$membersurname = mysql_result ( $result1, 0, "MemberSurname" );
				$memberinit = lowerfirstletter ( $memberfirstname ) . lowerfirstletter ( $membersurname );
				mysql_free_result ( $result1 );
				$memguest = mysql_result ( $result, $k, "memguest" );
				$firstname = mysql_result ( $result, $k, "guestfirstname" );
				$surname = mysql_result ( $result, $k, "guestsurname" );
				if (strcmp ( $memguest, 'm' ) == 0) {
					$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "MemberFirstname" );
					$surname = mysql_result ( $result1, 0, "MemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname <sup>$vegstring</sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				} else if ((strcmp ( $memguest, 'f' ) == 0) || (strcmp ( $memguest, 'c' ) == 0)) {
					$query1 = "select familyMemberFirstname, familyMemberSurname from familymembers where familyMemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "familyMemberFirstname" );
					$surname = mysql_result ( $result1, 0, "familyMemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname <sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				} else {
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname <sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				}
			}
			$form .= "</table>";
		} else {
			$form .= ' - ';
		}
		mysql_free_result ( $result );
		$form .= '</td>';
		$form .= '<td class="ms-column3-odd">';
		$thisday = date ( "Y-m-d", $daydate );
		$daydate += $dayinc;
		$roomval = $room [$i];
		$query = "select * from booking_rooms where roomnight='$thisday' and room = '$roomval'  and bookingref <> '0' and bookingstatus in ('Submitted','Approved','Confirmed')";
		$result = mysql_query ( $query );
		$num = mysql_numrows ( $result );
		if ($num > 0) {
			$form .= "<table style='width:100%'>";
			for($k = 0; $k < $num; $k ++) {
				$memberval = mysql_result ( $result, $k, "memid" );
				$bookingref = mysql_result ( $result, $k, "bookingref" );
				$guestnum = mysql_result ( $result, $k, "guestnum" );
				$query1 = "select age,vegetarian from booking_main where bookingref ='$bookingref' and guestnum='$guestnum'";
				$result1 = mysql_query ( $query1 );
				$memberage = mysql_result ( $result1, 0, "age" );
				$vegetarian = mysql_result ( $result1, 0, "vegetarian" );
				$memberagestring = memberagestring ( $memberage );
				if (strcmp ( $vegetarian, 'y' ) == 0) {
					$vegstring = '(V)';
				} else {
					$vegstring = '';
				}
				mysql_free_result ( $result1 );
				$memid = getbookingmember ( $bookingref );
				
				$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memid'";
				$result1 = mysql_query ( $query1 );
				$memberfirstname = mysql_result ( $result1, 0, "MemberFirstname" );
				$membersurname = mysql_result ( $result1, 0, "MemberSurname" );
				$memberinit = lowerfirstletter ( $memberfirstname ) . lowerfirstletter ( $membersurname );
				mysql_free_result ( $result1 );
				$memguest = mysql_result ( $result, $k, "memguest" );
				$firstname = mysql_result ( $result, $k, "guestfirstname" );
				$surname = mysql_result ( $result, $k, "guestsurname" );
				if (strcmp ( $memguest, 'm' ) == 0) {
					$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "MemberFirstname" );
					$surname = mysql_result ( $result1, 0, "MemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-odd1'>$firstname $surname <sup>$vegstring</sup></td><td class='ms-column3-odd2'>&nbsp</td></tr>";
				} else if ((strcmp ( $memguest, 'f' ) == 0) || (strcmp ( $memguest, 'c' ) == 0)) {
					$query1 = "select familyMemberFirstname, familyMemberSurname from familymembers where familyMemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "familyMemberFirstname" );
					$surname = mysql_result ( $result1, 0, "familyMemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-odd1'>$firstname $surname <sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-odd2'>&nbsp</td></tr>";
				} else {
					$form .= "<tr><Td class='ms-column3-odd1'>$firstname $surname <sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-odd2'>&nbsp</td></tr>";
				}
			}
			$form .= "</table>";
		} else {
			$form .= ' - ';
		}
		mysql_free_result ( $result );
		$form .= '</td>';
		$form .= '<td class="ms-column3-even">';
		$thisday = date ( "Y-m-d", $daydate );
		$daydate += $dayinc;
		$roomval = $room [$i];
		$query = "select * from booking_rooms where roomnight='$thisday' and room = '$roomval'  and bookingref <> '0' and bookingstatus in ('Submitted','Approved','Confirmed')";
		$result = mysql_query ( $query );
		$num = mysql_numrows ( $result );
		if ($num > 0) {
			$form .= "<table style='width:100%'>";
			for($k = 0; $k < $num; $k ++) {
				$memberval = mysql_result ( $result, $k, "memid" );
				$bookingref = mysql_result ( $result, $k, "bookingref" );
				$guestnum = mysql_result ( $result, $k, "guestnum" );
				$query1 = "select age,vegetarian from booking_main where bookingref ='$bookingref' and guestnum='$guestnum'";
				$result1 = mysql_query ( $query1 );
				$memberage = mysql_result ( $result1, 0, "age" );
				$vegetarian = mysql_result ( $result1, 0, "vegetarian" );
				$memberagestring = memberagestring ( $memberage );
				if (strcmp ( $vegetarian, 'y' ) == 0) {
					$vegstring = '(V)';
				} else {
					$vegstring = '';
				}
				mysql_free_result ( $result1 );
				$memid = getbookingmember ( $bookingref );
				
				$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memid'";
				$result1 = mysql_query ( $query1 );
				$memberfirstname = mysql_result ( $result1, 0, "MemberFirstname" );
				$membersurname = mysql_result ( $result1, 0, "MemberSurname" );
				$memberinit = lowerfirstletter ( $memberfirstname ) . lowerfirstletter ( $membersurname );
				mysql_free_result ( $result1 );
				$memguest = mysql_result ( $result, $k, "memguest" );
				$firstname = mysql_result ( $result, $k, "guestfirstname" );
				$surname = mysql_result ( $result, $k, "guestsurname" );
				if (strcmp ( $memguest, 'm' ) == 0) {
					$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "MemberFirstname" );
					$surname = mysql_result ( $result1, 0, "MemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname <sup>$vegstring</sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				} else if ((strcmp ( $memguest, 'f' ) == 0) || (strcmp ( $memguest, 'c' ) == 0)) {
					$query1 = "select familyMemberFirstname, familyMemberSurname from familymembers where familyMemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "familyMemberFirstname" );
					$surname = mysql_result ( $result1, 0, "familyMemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname <sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				} else {
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname <sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				}
			}
			$form .= "</table>";
		} else {
			$form .= ' - ';
		}
		mysql_free_result ( $result );
		$form .= '</td>';
		
		$form .= "</tr>";
	}
	
	$form .= "</table>";
} 

// ////////////////////////////////////////////////////////////////
// ////////////////////////////////////////////////////////////////

else if ($dayofweek == 5) // Friday
{
	$locdate2 = $startdate + 2 * $dayinc;
	$showfulllocdate = date ( "l d/m/y", $startdate );
	$showfulllocdate2 = date ( "l d/m/y", $locdate2 );
	
	// $form .= "<h3>Weekend Bed list $showfulllocdate to $showfulllocdate2</h3>";
	$fridaydate = date ( "d/n", $startdate );
	$saturdaydate = date ( "d/n", $startdate + $dayinc );
	$sundaydate = date ( "d/n", $startdate + 2 * $dayinc );
	
	$form .= "<table class='ms-column3-main'>";
	$form .= "<tr><td class='ms-column3-tl'>Bed Night</td>
					<td class='ms-column3-top'>Friday $fridaydate</td>
					<td class='ms-column3-top'>Saturday $saturdaydate</td></tr>";
	$form .= "<tr><td class='ms-column3-tl'>Duty Day</td>
					<td class='ms-column3-top'>Saturday  $saturdaydate</td>
					<td class='ms-column3-top'>Sunday $sundaydate</td></tr>";
	$form .= "";
	$form .= "<tr><td class='ms-column3-tl'>Lodge Leader</td>";
	
	$headerdate = $startdate;
	$thisday = date ( "Y-m-d", $headerdate );
	$form .= create_member_list ( $thisday );
	$headerdate += $dayinc;
	$thisday = date ( "Y-m-d", $headerdate );
	$form .= create_member_list ( $thisday );
	$headerdate += $dayinc;
	$form .= "</tr>";
	for($i = 0; $i < 15; $i ++) 
	// Change to start at 0 to also show the Any rooms
	// cycle through each room, for each room, determine the people staying on Fri and Sat nights
	{
		$daydate = $startdate;
		$form .= "<tr>";
		$form .= "<td class='ms-column3-left'>$room[$i]</td>";
		$form .= '<td class="ms-column3-even">';
		$thisday = date ( "Y-m-d", $daydate );
		$thisdaytime = strtotime ( $thisday );
		$daydate += $dayinc;
		$roomval = $room [$i];
		
		// Get bed nights for Friday
		$query = "select * from booking_rooms where roomnight='$thisday' and room = '$roomval' and bookingref <> '0' and bookingstatus in ('Submitted','Approved','Confirmed')";
		$result = mysql_query ( $query );
		$num = mysql_numrows ( $result );
		if ($num > 0) {
			$form .= "<table style='width:100%'>";
			for($k = 0; $k < $num; $k ++) {
				$memberval = mysql_result ( $result, $k, "memid" );
				$bookingref = mysql_result ( $result, $k, "bookingref" );
				$guestnum = mysql_result ( $result, $k, "guestnum" );
				$query1 = "select * from booking_main where bookingref ='$bookingref' and guestnum='$guestnum'";
				$result1 = mysql_query ( $query1 );
				$memberage = mysql_result ( $result1, 0, "age" );
				$vegetarian = mysql_result ( $result1, 0, "vegetarian" );
				$fridaydinner = mysql_result ( $result1, 0, "fridaydinner" );
				$datein = mysql_result ( $result1, 0, "datein" );
				$dateintime = strtotime ( $datein );
				$memberagestring = memberagestring ( $memberage );
				
				if (strcmp ( $vegetarian, 'y' ) == 0) {
					$vegstring = 'V';
				} else {
					$vegstring = '';
				}
				$dinnerstring = '';
				$xxx = $dateintime - $thisdaytime;
				if ($xxx == 0) // first night
{
					if (strcmp ( $fridaydinner, 'n' ) == 0) {
						$dinnerstring = 'x';
					} else {
						$dinnerstring = '';
					}
				}
				
				mysql_free_result ( $result1 );
				$memid = getbookingmember ( $bookingref );
				
				$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memid'";
				$result1 = mysql_query ( $query1 );
				$memberfirstname = mysql_result ( $result1, 0, "MemberFirstname" );
				$membersurname = mysql_result ( $result1, 0, "MemberSurname" );
				$memberinit = lowerfirstletter ( $memberfirstname ) . lowerfirstletter ( $membersurname );
				mysql_free_result ( $result1 );
				$memguest = mysql_result ( $result, $k, "memguest" );
				$firstname = mysql_result ( $result, $k, "guestfirstname" );
				$surname = mysql_result ( $result, $k, "guestsurname" );
				if (strcmp ( $memguest, 'm' ) == 0) {
					$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "MemberFirstname" );
					$surname = mysql_result ( $result1, 0, "MemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname <sup>$vegstring$dinnerstring</sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				} else if ((strcmp ( $memguest, 'f' ) == 0) || (strcmp ( $memguest, 'c' ) == 0)) {
					$query1 = "select familyMemberFirstname, familyMemberSurname from familymembers where familyMemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "familyMemberFirstname" );
					$surname = mysql_result ( $result1, 0, "familyMemberSurname" );
					
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname <sup>$memberagestring $vegstring $dinnerstring $memberinit </sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				} else {
					$form .= "<tr><Td class='ms-column3-even1'>$firstname $surname <sup>$memberagestring $vegstring $dinnerstring $memberinit</sup></td><td class='ms-column3-even2'>&nbsp</td></tr>";
				}
			}
			$form .= "</table>";
		}  // if there are any guests for this night
else {
			$form .= ' - ';
		} // else
		mysql_free_result ( $result );
		
		$form .= '</td>';
		$form .= '<td class="ms-column3-odd">';
		$thisday = date ( "Y-m-d", $daydate );
		$daydate += $dayinc;
		$roomval = $room [$i];
		// Get bednights for Saturday
		$query = "select * from booking_rooms where roomnight='$thisday' and room = '$roomval' and bookingref <> '0' and bookingstatus in ('Submitted','Approved','Confirmed') ";
		$result = mysql_query ( $query );
		$num = mysql_numrows ( $result );
		if ($num > 0) {
			$form .= "<table style='width:100%'>";
			
			for($k = 0; $k < $num; $k ++) {
				$memberval = mysql_result ( $result, $k, "memid" );
				$bookingref = mysql_result ( $result, $k, "bookingref" );
				$guestnum = mysql_result ( $result, $k, "guestnum" );
				$query1 = "select age,vegetarian from booking_main where bookingref ='$bookingref' and guestnum='$guestnum'";
				$result1 = mysql_query ( $query1 );
				$memberage = mysql_result ( $result1, 0, "age" );
				$vegetarian = mysql_result ( $result1, 0, "vegetarian" );
				$memberagestring = memberagestring ( $memberage );
				if (strcmp ( $vegetarian, 'y' ) == 0) {
					$vegstring = '(V)';
				} else {
					$vegstring = '';
				}
				mysql_free_result ( $result1 );
				$memid = getbookingmember ( $bookingref );
				
				$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memid'";
				$result1 = mysql_query ( $query1 );
				$memberfirstname = mysql_result ( $result1, 0, "MemberFirstname" );
				$membersurname = mysql_result ( $result1, 0, "MemberSurname" );
				$memberinit = lowerfirstletter ( $memberfirstname ) . lowerfirstletter ( $membersurname );
				mysql_free_result ( $result1 );
				$memguest = mysql_result ( $result, $k, "memguest" );
				$firstname = mysql_result ( $result, $k, "guestfirstname" );
				$surname = mysql_result ( $result, $k, "guestsurname" );
				if (strcmp ( $memguest, 'm' ) == 0) {
					$query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "MemberFirstname" );
					$surname = mysql_result ( $result1, 0, "MemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-odd1'>$firstname $surname <sup>$vegstring</sup></td><td class='ms-column3-odd2'>&nbsp</td></tr>";
				} else if ((strcmp ( $memguest, 'f' ) == 0) || (strcmp ( $memguest, 'c' ) == 0)) {
					$query1 = "select familyMemberFirstname, familyMemberSurname from familymembers where familyMemberID='$memberval'";
					$result1 = mysql_query ( $query1 );
					$firstname = mysql_result ( $result1, 0, "familyMemberFirstname" );
					$surname = mysql_result ( $result1, 0, "familyMemberSurname" );
					mysql_free_result ( $result1 );
					$form .= "<tr><Td class='ms-column3-odd1'>$firstname $surname <sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-odd2'>&nbsp</td></tr>";
				} else {
					$form .= "<tr><Td class='ms-column3-odd1'>$firstname $surname <sup>$memberagestring $vegstring $memberinit</sup></td><td class='ms-column3-odd2'>&nbsp</td></tr>";
				}
			}
			$form .= "</table>";
		} else {
			$form .= ' - ';
		}
		mysql_free_result ( $result );
	}
	$form .= '</td>';
	$form .= '</tr></table>';
}
$form .= "Legend: <sup>ab</sup> - Booking Member,<sup>(10)</sup> - child age, <sup>V</sup>  - vegetarian, <sup>x</sup> is no dinner first night";
// $form .= "<h3>Please stay in your assigned rooms!</h3>";
echo $form;
?>

</body>
</html>
