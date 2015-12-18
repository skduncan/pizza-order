<?php
//asg9Request.php
// responds to request

require_once "mydb.php";
//print_r($_GET);
if(isset($_GET['phone']))
{
	phoneFind();
}
else
{
	processOrder();
}

function phoneFind()
{
	$db = dbConnect();
	//$db->debug=1;
	$phone = htmlentities($_GET['phone'], ENT_QUOTES);
	$query = "select * from myCustomer where phone = '$phone'";
	$result = $db->Execute($query);
	if(!$result)
	{
		print "Error with result";
		return;
	}

	$count = $result->RowCount();

	if($count == 0)
	{
		printOrder($phone);
	}
	else 
	{
		$row = $result->FetchRow();
  		$name = $row ['name'];
  		$customer = $row['customerId'];

  		$queryCO = "select * from myCustomerToMyOrder where customerId = '$customer'";
  		$resultCO = $db->Execute($queryCO);

  		$rowCO = $resultCO->FetchRow();
  		$order = $rowCO['orderId'];
  		$query1 = "Select * from myOrder where orderId='$order'";
  		$result1 = $db->Execute($query1);

  		$row1 = $result1->FetchRow();
  		$toppingId = $row1['toppingsId'];
  		$sizeId = $row1['sizeId'];
  		printOrder($phone, $name, $toppingId, $sizeId);
	}
}

function processOrder()
{
	$namePro = htmlentities($_GET['name'], ENT_QUOTES);
	$phonePro = htmlentities($_GET['thePhone'], ENT_QUOTES);
	$sizeIdPro = htmlentities($_GET['sizeId'], ENT_QUOTES);
	$toppingIdPro = htmlentities($_GET['toppingId'], ENT_QUOTES);

	$db = dbConnect();
        //$db->debug=true;
	$queryPro = "select * from myCustomer where phone = '$phonePro'";
	$resultPro = $db->Execute($queryPro);
	if(!$resultPro)
	{
		print "error with thing...";
		return;
	}
	if($row = $resultPro->FetchRow())
	{
		$customer = $row['customerId'];
		if($namePro != $row['name'])
		{	
			$queryChangeCust = "update myCustomer set name = '$namePro' where customerId = '$customer'";
			$resultChangeCust = $db->Execute($queryChangeCust);
			if(!$resultChangeCust)
			{
				print "error with thing...#2"; 
				return;
			}
		}
	}
	else
	{
		$queryInsert = "insert into myCustomer (name, phone) values ('$namePro', '$phonePro')";
		$resultInsert = $db->Execute($queryInsert);
		if(!$resultInsert)
		{
			print "error with thing...#3";
			return;
		}
                $customer=$db->Insert_ID();
	}

	$queryInsert2 = "insert into myOrder (toppingsId, sizeId) ".
		      	"values ('$toppingIdPro', '$sizeIdPro')";
	$resultInsert2 = $db->Execute($queryInsert2);
	if(!resultInsert2)
	{
		print "oh...so close";
		return;
	}
	$id = $db->Insert_ID();
	$queryRemove = "delete from myCustomerToMyOrder where customerId = '$customer'";
	$resultRemove = $db->Execute($queryRemove);

	$queryInsert3 = "insert into myCustomerToMyOrder (customerId, orderId) ".
		      	"values ('$customer', '$id')";
	$resultInsert3 = $db->Execute($queryInsert3);
	if(!$resultInsert3)
	{
		print "big big error";
		return;
	}
}

function printOrder($phone="", $name="",$topping="", $size="")
{
  print "<p> Phone Number:<input type='text' id = 'phoneNumber' value = '$phone' readonly /></p>\n";
  print "<p> Customer Name: <input type='text' id = 'customerName' value= '$name' /></p>\n";
  showTopping($topping);
  showSize($size);
  print "<p> <button type='button' onclick='finishOrder();'> Finish Order </button>\n";
  print " <button type='button' onclick='clearOrder();'> Clear Order </button>\n";
  //print " <button type='button' onclick='showPhonePage();'> Start Over </button>\n";
  print "</p>\n";

}//end of printOrder

function showTopping($theToppingId)
{
   print "<p> Choose Topping: </p>";
   $db = dbConnect();
   $query = "Select * from myToppings";
   $result = $db->Execute($query);
   print "<select id='topping' name='topping'> \n";
   while($row = $result->FetchRow())
   { 
     $topping = $row['description'];
     $toppingId = $row['toppingsId'];
     if($theToppingId == $toppingId)
     {
      $sel = " selected='selected' ";
      }
     else
     {
      $sel = "";
      }     
     print "<option value='$toppingId' $sel >$topping</option>\n";
   }
   print "</select>\n";
}

function showSize($theSize)
{
   print "<p> Choose Size: </p>";
   $db = dbConnect();
   $query = "Select * from mySize";
   $result = $db->Execute($query);
   print "<select id='size' name='size'> \n";
   while($row = $result->FetchRow())
   { 
     $size = $row['description'];
     $sizeId = $row['sizeId'];
     if($theSize == $sizeId)
     {
      $sel = " selected='selected' ";
      }
     else
     {
      $sel = "";
      }
     print "<option value='$sizeId' $sel >$size</option>\n";
   }
   print "</select>\n";
}

?>