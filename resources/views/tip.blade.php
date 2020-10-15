<?php
?>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://www.paypalobjects.com/api/checkout.js"></script>
	
	<style>
	html, body {
    max-width: 100%;
    overflow-x: hidden;
	}
	.container {
	  height: 600px;
	  position: relative;
	  border: 1px solid green;
	}

	.vertical-center {
	  margin: 0;
	  position: absolute;
	  top: 50%;
	  left: 10%;
	  -ms-transform: translateY(-50%);
	  transform: translateY(-50%);
	}
</style>

</head>

<body>
	<div class="container">
		<div class="vertical-center">
			<button id="paypal-button"></button>
		</div>
	</div>
</body>

<script>
const CURRENCY='USD';
$( document ).ready(function() {
const AMOUNT=<?=$amount?>;
 paypal.Button.render({
    
                env: 'sandbox', // sandbox | production
    
                // PayPal Client IDs - replace with your own
                // Create a PayPal app: https://developer.paypal.com/developer/applications/create
                client: {
                    sandbox: 'AYiFUAFyJxc6aMmrnIj1Gjcb52UUWrdgmTD0QBlG5YMQ4YRooSWchT80Jrq1Rlw_sphJRZnbCMe0Lpc_',
                    production: 'AdfIVpK5DykWsVuPy9q600QOSUjrknP5h20lYCUVVtzRt0wLUdMSkndUwTmahVdP8MGq9Wbw7s3jX0Ly'
                },
    
                // Show the buyer a 'Pay Now' button in the checkout flow
                commit: true,
				style: {
			 size: 'medium',
			 color: 'gold',
			 shape: 'pill',
			 label: 'checkout',
			 tagline: 'false',
			  fundingicons: 'true',
				},
			funding: {
			 allowed: [ paypal.FUNDING.CARD ],
			 disallowed: [ paypal.FUNDING.CREDIT ]
			},
    
                // payment() is called when the button is clicked
                payment: function (data, actions) {
                    // Make a call to the REST api to create the payment
                    return actions.payment.create({
                        payment: {
                            transactions: [
                                {
                                    amount: {total: AMOUNT, currency: CURRENCY}
                                }
                            ]
                        }
                    });
                },
    
                // onAuthorize() is called when the buyer approves the payment
                onAuthorize: function (data, actions) {
                    // Make a call to the REST api to execute the payment
                    return actions.payment.execute().then(function () {
                        //console.log(data);
						 savePayment(data.paymentID);
                    });
                }
            }, '#paypal-button');
			
			//$( "#paypal-button" ).text("");
   
});
			
			
						
function savePayment(paymentID){
	const userID=<?=$user_id?>
	$.ajax({
		 type: "get",
		url: "api/stream/donate",
		data: {userID:},
		success: function(data){
			console.log(data);
			if(data=="success")
			{ 
				document.getElementById(variable+"_"+id).remove();
				 Swal.fire(
				  'Donated!',
				  'Donation added Successful.',
				  'success'
				)
			}	
			else if(data=="unsuccessful")
			{
				 Swal.fire(
				  'Error!',
				  'Donated but Could Not saved.',
				  'error'
				)
			}
		},
		error: function(err)
		{
			console.log(err);
				Swal.fire(
				  'Error!',
				  'Some Error Occured While Saving Paymnet',
				  'error'
				);
		}
	});
}

			
</script>
</html>