// asg9.js
// javascript for use with asg9.php script using AJAX

var thePhoneNumber = "1234567890";

$(document).ready(function () {
    showPhonePage();
   });


//displays in div main1b, the input phone form and a start over button
function showPhonePage()
{
   mainHeading(); 
   output = "<div> Enter your 10 digit phone number:  " +
             "<input type='text' id='phone'  /><br />";
    output += "\n<button type='button' id='findPhoneButton'> submit </button>\n";

//alert("in showPhonePage");    

     $("#main1b").html(output);
     $("#main1c").html("");
     $("#main1a").html("");
     $("#findPhoneButton").click(function(){
        findPhone();
       });    
     showRestart();
}

// Function that checks that phone number given from showPhonePage is correct
// if incorrect, returns page to showPhonePage
// else, checks database for same phone.
function findPhone()
{
    var phone = $("#phone").val();
    var newPhone = checkPhone(phone);
    //alert("result from checkPhone="+result);
    if(newPhone.length==10)
    {
        //alert("in find phone, result = "+result);
        $("#main1b").html("<h3> your number: "+phone + "</h3>");
	$("#main1a").html(""); // clear main1a content
  
        $.get( "../cosc2328/asg9Requesta.php",     //url
	       {phone:newPhone},  // argument list for get
	       function(response)    // handler
	       {
                $("#main1a").html(response);
		$("#main1b").html("");
		$("#main1c").html("");
                $("#errorDiv").html("");
                $("#finishOrder").click(function () {
	           finishOrder();
                  });
                $("#clearOrder").click(function() {
	           clearOrder();
                  });
                $("#startOver").click(function() {
	          showPhonePage();
                });

           });
        thePhoneNumber = newPhone; //store as global variable in script
    }
    else
    {
	error = "<h3> Please enter a 10 digit phone number "+
                "(no dashes or extra characters) </h3>\n";    
        $("#errorDiv").html(error);
        showPhonePage();
    }
 }



//displays start over button, which calls showPhonePage()
// puts in bottom div (main1d)
function showRestart()
{
    var restart = "<button type='button' id='startOver'>start over</button>\n";
    $("#main1d").html(restart);
    $("#startOver").click( function() {
         showPhonePage();
      });
}



// outputs the main heading for the page
function mainHeading()
{
    var output="<h2>Welcome to This-Pizza-Place-Pie-Shop</h2>\n";
    $("#content").html(output);
}



// gets phone number checks for 10 digits only, if not correct
// displays error message and asks for phone number again
function checkPhone(thePhone)
{
    var result="";
    var good=false;
    // must 
    var newPhone="";
    for(i=0; i<thePhone.length; i++)
    {   var ch=thePhone.charAt(i);
	if(ch>='0' && ch <='9')
	{  
            newPhone += ch;
        }
    }
    return newPhone;
}



//clears main div with given number
function clearDiv(letter)
{
    var divName = "#main1" + letter;
    $(divName).html("");
}



function finishOrder()
{
    var output = "Your order is complete. See you soon to pick it up. ";
     // now store order back into database
    url = "../cosc2328/asg9Requesta.php";
    var topId = $("#topping").val();
    var theName = $("#customerName").val();
    var thePhoneNumber = $("#phoneNumber").val();
    var sizeId = $("#size").val();
    var toppingName=$("#topping option:selected").text();
    var sizeName=$("#size option:selected").text();
    
    $.get(url,
	  {thePhone:thePhoneNumber,toppingId:topId,name:theName,
	   sizeId:sizeId},
           function (response)
           {
	       output1 = "<h3> Thank you for your order " +theName+ "  <br /> " +
                         "Topping: "+toppingName + "<br />" +
		         "Size: " + sizeName + "<br /><br /><br />" + 
		        "Please stop by to pick it up in 10 minutes. </h3>\n";
               $("#main1a").html(output1);
	       $("#main1b").html( output);
               $("main1c").html(response);
          });
    
    showRestart();
}



// zeroes out all data in an order   
function clearOrder()
{
    $("#topping").val(0);
    $("#size").val(0);
}
