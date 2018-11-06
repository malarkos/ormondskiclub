<?php
  session_start();
  // check for valid memid, if not, go to memberlogin.php
  if (!isset($_SESSION['adminmemid'])) {
      header("Location: bookingadminlogin.php");
      
  
      #Version info
      # updated 06 Nov 2018 to add link to order of bookings in the menu
  }
?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-au">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Ormond Ski Club - Current Bookings</title>
<script language="JavaScript">
<!-- hide from JavaScript-challenged browsers
function openWindow(url)
{popupWin = window.open(url, 'remote',
'menubar=no, toolbar=no, location=no, directories=no, status=no, scrollbars=yes, resizable=yes, dependent, width=500, height=300, left=50, titlebar=yes, top=50')
}
function submit_check()
{
      var returnval;
      
      returnval=confirm('WARNING: This option will completely delete this draft booking. If you want to delete a row, click on the Delete link on that row. Click OK if you wish to delete the entire booking, or Cancel to return to the booking.');
      return returnval;
}
function delete_submit_check()
{
      var returnval;
      
      returnval=confirm('WARNING: You are about to delete the full booking. Click OK if you wish to delete the entire booking, or Cancel to return to the booking.');
      return returnval;
}


// done hiding -->
</script>
</head>
<body>
<?php

  include("ormond1.css");
  include("../xzlog.php");
  $retval = mysql_connect($dbhostname, $username, $userpassword);
  mysql_select_db($database) or die("Unable to select database");
  $form = "<table><tr><td><h2>2018 Current Bookings  Page</h2></td></tr>";
  $form .= "<tr><td class='text2'><A href='bookingadmin.php'>Home</a> -- Current Bookings -- <A href='adminbookinglist.php'>Order of Bookings</a></td></tr></table>";
  if (isset($_POST['bookingmember'])) {
      $bookingmemberset = true;
  } else {
      $bookingmemberset = false;
  }
  if (isset($_POST['curbooking'])) {
      $curbookingset = true;
  } else {
      $curbookingset = false;
  }
  
  $agearray[0]='0-2';$agearray[1]='3';
  $agearray[2]='4';$agearray[3]='5';$agearray[4]='6';$agearray[5]='7';$agearray[6]='8';
  $agearray[7]='9';$agearray[8]='10';$agearray[9]='11';$agearray[10]='12';$agearray[11]='13';
  $agearray[12]='14';$agearray[13]='15';$agearray[14]='16';$agearray[15]='17';
  $agearray[16]='18-25';$agearray[17]='26+';
  
  // Use session variables to track bookingmember for current member, curbooking for current booking
  // user GET variables to check for posting.
  if (($bookingmemberset == false) && ($curbookingset == false)) {
      // nothing set, show members who have bookings
      //$form .= "<table><tr><td><Form action='bookingadmin.php' method='POST'><input  type='submit' value='Bookings admin'></form></td></tr></table>";
      //unset($_SESSION['totalcost']);
      //unset($_SESSION['originalcost']);
      $form .= "<form method='POST' action='currentbookings.php'>";
      //$query = "select distinct memid from booking_summary where bookingref like '2018%'";
	  $query = "SELECT memberid, memberfirstname, membersurname FROM members WHERE memberid IN (SELECT DISTINCT memid FROM booking_summary WHERE bookingref LIKE '2018%') ORDER BY membersurname, memberfirstname;";
      $result = mysql_query($query) or die("Unable to get member list");
      $num = mysql_numrows($result);
      $form .= "<b>Members with bookings</b><br><br><select name='bookingmember' size='30'>";
      for ($i = 0; $i < $num; $i++) {
          $memberid = mysql_result($result, $i, "memberid");
          //$query1 = "select MemberFirstname, MemberSurname from members where memberid='$memberid' ";
          //$result1 = mysql_query($query1) or die("Unable to get member list");
          //$num1 = mysql_numrows($result1);
          $firstname = mysql_result($result, $i, "MemberFirstname");
          $lastname = mysql_result($result, $i, "MemberSurname");
          $_SESSION['memfirstname'] = $firstname;
          $_SESSION['memsurname'] = $lastname;
          $form .= "<option value='$memberid'>$lastname, $firstname</option>";
      }
      $form .= "</Select>";
      $form .= "<p></p><input type='submit' value='View bookings'>";
      $form .= "</form>";
      mysql_free_result($result);
      //mysql_free_result($result1);
  }
  // if
  if (($bookingmemberset == true) && ($curbookingset == false)) {
      // nothing set, show members who have bookings
      $bookingmember = $_POST['bookingmember'];
      $query1 = "select MemberFirstname, MemberSurname from members where memberid='$bookingmember'";
      $result1 = mysql_query($query1) or die("Unable to get member list");
      $num1 = mysql_numrows($result1);
      $firstname = mysql_result($result1, 0, "MemberFirstname");
      $lastname = mysql_result($result1, 0, "MemberSurname");
      $form .= "<b>Bookings for $firstname $lastname</b><p>";
      $query = "select * from booking_summary where memid='$bookingmember' and bookingref like '2018%'";
      $result = mysql_query($query) or die("Unable to get member list");
      $num = mysql_numrows($result);
      /*$form .= "<form method='POST' action='currentbookings.php'>";
       $form .= "<select name='curbooking' size='3'>";
       for($i=0;$i<$num;$i++)
       {
       $bookingrefid =mysql_result($result,$i,"bookingref");
       $bookingcost =mysql_result($result,$i,"bookingcost");
       $bookingrefid =mysql_result($result,$i,"bookingref");
       $bookingrefid =mysql_result($result,$i,"bookingref");
       
       $form .= "<option value='$bookingrefid'>$bookingrefid</option>";
       }
       $form .= "</Select>";
       $form .= "<p><input type='submit' value='View Booking'>";
       $form .= "<input type='hidden' name='bookingmember' value='$bookingmember'>";
       $form .= "</form>";*/
      $form .= "<Table border='1'><tr><th class='th1'>Booking</th><Th class='th1'>Status</Th><Th class='th1'>Cost</Th><Th class='th1'>Paid</Th><Th class='th1'>View</Th></tr>";
      for ($i = 0; $i < $num; $i++) {
          $bookingrefid = mysql_result($result, $i, "bookingref");
          $bookingcost = mysql_result($result, $i, "bookingcost");
          $bookingpaid = mysql_result($result, $i, "amountpaid");
          $bookingstatus = mysql_result($result, $i, "bookingstatus");
          $form .= "<Tr>";
          $form .= "<Td class='td2'>$bookingrefid</Td>";
          $form .= "<Td class='td2'>$bookingstatus</Td>";
          $form .= "<Td class='td2' align='right'>$$bookingcost</Td>";
          $form .= "<Td class='td2' align='right'>$$bookingpaid</Td>";
          $form .= "<td><Form action='currentbookings.php' method='POST'><input type='hidden' name='bookingmember' value='$bookingmember'><input type='hidden' name='curbooking' value='$bookingrefid'><input type='submit' style='font-family: Verdana, Helvetica; ;font-size: 8pt' value='View Booking'></form></td>";
          $form .= "</Tr>";
      }
      $form .= "</Table>";
      $form .= "<hr><Form action='currentbookings.php' method='POST'><input style='font-family: Verdana, Helvetica; font-weight:bold;font-size: 9pt' type='submit' value='<-- Back to Current Bookings'></form>";
      mysql_free_result($result);
      mysql_free_result($result1);
  }
  if (($bookingmemberset == true) && ($curbookingset == true)) {
      // show members booking.
      $bookingmember = $_POST['bookingmember'];
      $curbooking = $_POST['curbooking'];
      // ='0';
      unset($_SESSION['totalcost']);
      // = '0';
      unset($_SESSION['originalcost']);
      $bookingref = $curbooking;
      $_SESSION['bookingref'] = $bookingref;
      $_SESSION['bookingmember'] = $bookingmember;
      $query1 = "select MemberFirstname, MemberSurname from members where memberid='$bookingmember'";
      $result1 = mysql_query($query1) or die("Unable to get member list");
      $num1 = mysql_numrows($result1);
      $firstname = mysql_result($result1, 0, "MemberFirstname");
      $lastname = mysql_result($result1, 0, "MemberSurname");
      $query = "select *,date_format(bookingmade,'%d %M %Y') as bookingmadedate,date_format(bookingaltered,'%d %M %Y') as bookingalterdate from booking_summary where bookingref='$curbooking'";
      $result = mysql_query($query);
      $num = mysql_numrows($result);
      if ($num > 0) {
          $bookingstatus = mysql_result($result, 0, "bookingstatus");
          $bookingmadedate = mysql_result($result, 0, "bookingmadedate");
          $comment = mysql_result($result, 0, "comment");
          $bookingalterdate = mysql_result($result, 0, "bookingalterdate");
          $comment = mysql_result($result, 0, "comment");
          if ((!isset($_SESSION['originalcost'])) || ($_SESSION['originalcost'] === '0')) {
              $originalcost = mysql_result($result, 0, "bookingcost");
              $_SESSION['originalcost'] = $originalcost;
          } else {
              $originalcost = $_SESSION['originalcost'];
          }
          $amountpaid = mysql_result($result, 0, "amountpaid");
          $paymethod = mysql_result($result, 0, "paymentmethod");
      }
      $form .= "<h3>Lodge Booking $bookingref</h3>";
      $form .= "<table border='1'>";
      $form .= "<tr><td>Room allocations</td><td>Click to add comments to booking <br>
       and send email to member.</td>
       <td>Click to open booking for editing.</td>
       <td>To make a payment for the booking.</td>
       <td>Click to select refund option then <br>click on next page to actually cancel booking.</td><td>Edit booking summary</td></tr>";
      $form .= "<tr>";
      $form .= "<td><Form action='adminroomalloc.php' method='POST'>
         <input type='submit' value='Room Allocation'>
       
       </form></td>";
      $form .= "<td>";
      if (strcmp("Submitted", $bookingstatus) == 0) {
          $form .= "<Form action='approvebooking.php' method='POST'>
         <input type='submit' value='Approve booking'>       
       </form>";
      } elseif (strcmp("Approved", $bookingstatus) == 0) {
          $form .= "Already Approved";
      } else {
          $form .= "N/A";
      }
      $form .= "</td>";
      $form .= "<td><Form action='admineditbooking.php' method='POST'>
         <input type='submit' value='Edit booking'>
       
       </form></td>";
      $form .= "<td><Form action='adminbookingpayment.php' method='POST'>
         <input type='submit' value='Booking Payment'>
       
       </form></td>";
      $form .= "<td><Form action='admindeletebooking.php' method='POST' >
         <input type='submit' value='Cancel booking'>
       
       </form></td>
       <td><Form action='adminshowbookingsummary.php' method='POST' >
         <input type='submit' value='Booking Summary'>
       
       </form></td>
       
       </tr>";
      $form .= "</table>";
      $form .= "<table><tr>";
      $form .= "<Td class='td11'><b>Booking Member:</b></td><Td class='td11'>$firstname $lastname</td><td class='td11'><b>Booking Reference:</b></td><Td class='td11'>$bookingref</td>";
      $form .= "</tr>";
      $form .= "<tr><td class='td11'><b>Booking Status:</b></td><td class='td11'>";
      $form .= "$bookingstatus";
      $form .= "</td><td class='td11'><b>Booking Date:</b></td><td class='td11'>$bookingmadedate</td></tr>";
      $form .= "<tr><td class='td11'><b>Amount Paid:</b></td><td class='td11'>$$amountpaid</td><td class='td11'><b>Booking Altered</b></td><td class='td11'>$bookingalterdate</td></tr>";
      $form .= "</table>";
      // show current entries
      $query = "select *,date_format(datein,'%a %d/%m/%y') as dateinfmt,date_format(dateout,'%a %d/%m/%y') as dateoutfmt from booking_main where bookingref='$bookingref' order by id";
      $result = mysql_query($query);
      $num = mysql_numrows($result);
      if ($num == 0) {
          // no entries yet
          $form .= "There are no entries for your booking.<p>";
      } else {
          // display current additions
          // create table
          $form .= "<table border='1' cellpadding='1' cellspacing='1'>";
          $form .= "<tr>
              <th class='th1'>M/G</th>
          <th class='th1'>Name</th>
          <th class='th1'>Age</th>
		  <th class='th1'>DOB</th>
		  <th class='th1'>Paid</th>
          <th class='th1'>M/F</th>
          <th class='th1'>Date In</th>
          <th class='th1'>Date Out</th>
          <th class='th1'>Room</th>
         <th class='th1'>WP Days</th>
          <th class='th1'>WP%</th>
          <th class='th1'>Cost</th>
          <th class='th1'>Din</th>
          <th class='th1'>Veg</th>
		   <th class='th1'>Vis</th>
          </tr>";
          $totalcost = 0;
          for ($i = 0; $i < $num; $i++) {
              $memberval = mysql_result($result, $i, "memberval");
              $memguest = mysql_result($result, $i, "memguest");
              $age = mysql_result($result, $i, "age");
              $ageval = $age;
              $gender = mysql_result($result, $i, "gender");
              $room = mysql_result($result, $i, "room");
              $wpdisc = mysql_result($result, $i, "wpdisc");
              $wpdays = mysql_result($result, $i, "wpdays");
              $cost = mysql_result($result, $i, "cost");
              $datein = mysql_result($result, $i, "datein");
              $dateout = mysql_result($result, $i, "dateout");
              $fridaydinner = mysql_result($result, $i, "fridaydinner");
              $vegetarian = mysql_result($result, $i, "vegetarian");
              $id = mysql_result($result, $i, "id");
              $dateinfmt = mysql_result($result, $i, "dateinfmt");
              $dateoutfmt = mysql_result($result, $i, "dateoutfmt");
              $firstname = mysql_result($result, $i, "guestfirstname");
              $surname = mysql_result($result, $i, "guestsurname");
              $guestnum = mysql_result($result, $i, "guestnum");
			  $bookinglistdisplay = mysql_result($result, $i, "bookinglistdisplay");
			  $fammemsubspaid = "&nbsp;";
			  $birthdateval = "&nbsp;";
              if (strcmp($memguest, 'm') == 0) {
                  $query1 = "select MemberFirstname, MemberSurname from members where MemberID='$memberval'";
                  $result1 = mysql_query($query1);
                  $firstname = mysql_result($result1, 0, "MemberFirstname");
                  $surname = mysql_result($result1, 0, "MemberSurname");
              }
              if (strcmp($memguest, 'f') == 0) {
                  $query1 = "select * from familymembers where familyMemberID='$memberval'";
                  $result1 = mysql_query($query1);
                  $firstname = mysql_result($result1, 0, "familyMemberFirstname");
                  $surname = mysql_result($result1, 0, "familyMemberSurname");
				  $fammemsubspaid = mysql_result($result1, 0, "CurrentSubsPaid");
				  if ($fammemsubspaid == "No")
				  {
					$fammemsubspaid = "<font color='red'><b>$fammemsubspaid </b></font>";
				  }
				  $fammembirthdateval = mysql_result($result1, 0, "FamilyMemberBirthDate");
				  list($fmyear,$fmmonth,$fmday) = explode("-",$fammembirthdateval);
					$year_diff  = date("Y") - $fmyear;
					$month_diff = date("m") - $fmmonth;
					$day_diff   = date("d") - $fmday;
				   $birthdateval = "$year_diff years";
              }
              if (strcmp($memguest, 'c') == 0) {
                  $query1 = "select familyMemberFirstname, familyMemberSurname from familymembers where familyMemberID='$memberval'";
                  $result1 = mysql_query($query1);
                  $firstname = mysql_result($result1, 0, "familyMemberFirstname");
                  $surname = mysql_result($result1, 0, "familyMemberSurname");
              }
              $form .= "<tr>
            <td class='td1' align='center'>$memguest</td>
          <td class='td1'>$firstname $surname</td>
          <td class='td1' align='center'>$age</td>
		  <td class='td1' align='center'>$birthdateval</td>
		  <td class='td1' align='center'>$fammemsubspaid</td>
          <td class='td1' align='center'>$gender</td>
          <td class='td1' align='right'>$dateinfmt</td>
          <td class='td1' align='right'>$dateoutfmt</td>
          <td class='td1' align='center'>$room</td>
          <td class='td1' align='right'>$wpdays</td>
          <td class='td1' align='right'>$wpdisc</td>
          <td class='td1' align='right'><a href='javascript:openWindow(\"costdetail.php?id=$id&status=submit\",\"Cost Detail\")'>$$cost</a></td>
          <td class='td1' align='center'>$fridaydinner</td>
          <td class='td1' align='center'>$vegetarian</td>
		  <td class='td1' align='center'>$bookinglistdisplay</td>
          
          </tr>";
              // <td class='td1' align='right'><a href='javascript:openWindow(\"costdetail.php?id=$id&status=submit\",\"Cost Detail\")'>$$cost</a></td>
              $totalcost += $cost;
          }
          mysql_free_result($result1);
          $totalcost = sprintf("%05.2f", $totalcost);
          $form .= "<Tr><Td colspan ='11' align='right' class='td1'><b>Total:</b></td><td class='td1' align='right'><b>$$totalcost</b></td><td colspan='3'><i>(GST inclusive)</i></td></tr>";
          $topay = sprintf("%05.2f", $totalcost - $amountpaid);
          $form .= "<Tr><Td colspan ='11' align='right' class='td1'><b>To Pay:</b></td><td class='td1' align='right'><b>$$topay</b></td><td colspan='3'><i>(GST inclusive)</i></td></tr>";
          $form .= "<tr><Td class='td1' colspan='13'><b>Request to Booking Officer: <b></td></tr>";
          $form .= "<tr><td colspan='11'>$comment</td></tr>";
          $form .= "</tr>";
          $form .= "</table>";
      }
      // else
     //SELECT SUM(Amount) AS balance FROM finances WHERE memberid='351';
	 $query = "SELECT SUM(Amount) AS balance FROM finances WHERE memberid='$bookingmember'";
	 $result = mysql_query($query);
	$balance1 = mysql_result($result, 0, "balance");
	$balance1 = sprintf("%01.2f",$balance1);
	
	$form .= "<b>Current Balance = $ $balance1</b>";
     
	$form .= "<h4>Payment history</h4>";
      // get entries from finances
      $query = "select * from finances where description like '$bookingref%' and memberid <> '0'";
      $result = mysql_query($query);
      $num = mysql_numrows($result);
      if ($num == 0) {
          // no entries yet
          $form .= "<tr><td>There are no entries for your booking.<p></td></tr>";
      }
      //if
      else {
          // display current additions
          $form .= "<table border='1' cellpadding='1' cellspacing='1'>";
          $form .= "<tr>
       <th class='th1'><b>ID</b></td><th class='th1'><b>Date</b></td>
      <th class='th1'><b>Description</b></td>
      <th class='th1'><b>Amount</b></td>
      <th class='th1'><b>Total</b></td>
      <th class='th1'><b>Comment</b></td></tr>";
          $total = 0;
          for ($i = 0; $i < $num; $i++) {
              $financeid = mysql_result($result, $i, "FinanceID");
              $dateval = date("d/m/Y", strtotime(mysql_result($result, $i, "TransactionDate")));
              $description = mysql_result($result, $i, "Description");
              $gst = mysql_result($result, $i, "GST");
              $amountnogst = mysql_result($result, $i, "AmountNoGST");
              $creditdebit = mysql_result($result, $i, "CreditDebit");
              $amount = mysql_result($result, $i, "Amount");
              $comment = mysql_result($result, $i, "Comment");
              if (strlen($comment) == 0) {
                  $comment = "&nbsp;";
              }
              $total += $amount;
              $totalvar = sprintf("%5.2f", $total);
              $form .= "<tr><Td class='td1'>$financeid</td><Td class='td1'>$dateval</td><Td class='td1'>$description</td><Td class='td1' align='right'>$$amount</td><Td class='td1' align='right'>$$totalvar</td><Td class='td1'>$comment  </td></tr>";
          }
          //for
          // add total
          $form .= "<tr><td class='td1' colspan='3' align='right'><b>Current Balance:</b></td><td class='td1' align='right'>$$totalvar</td>";
          if ($total < 0) {
              // indicate payment required
              $form .= "<td  class='td1'><font color='red'><B>Payment required!</b></font></td>";
          } else {
              $form .= "<td  class='td1'>Paid</td>";
          }
          $form .= "</tr>";
          $form .= "</table>";
          mysql_free_result($result);
      }
      // if
      $form .= "<hr><table><tr><td><Form action='currentbookings.php' method='POST'><input style='font-family: Verdana, Helvetica; font-weight:bold;font-size: 9pt' type='submit' value='<-- Back to Current Bookings'></form></td>";
      $form .= "<td><Form action='currentbookings.php' method='POST'><input type='hidden' name='bookingmember' value='$bookingmember'><input style='font-family: Verdana, Helvetica; font-weight:bold;font-size: 9pt' type='submit' value='<-- Back to Bookings for Member'></form></td>";
      $form .= "<td><Form action='adminbookinglist.php' method='POST'><input style='font-family: Verdana, Helvetica; font-weight:bold;font-size: 9pt' type='submit' value='<-- Back to Order of Bookings'></form></td>";
      $form .= "</tr></table>";
  }
  // if
  echo $form;
  //mysql_free_result($result);
?>
</body></html>