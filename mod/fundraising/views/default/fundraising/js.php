$(function() {			
	 $('.fundraising-bankaccount-contribute-button').click(function() {	 	
		$('.fundraising-bankaccount-contribute-form').toggleClass("fundraising-display");
		$(this).toggleClass("fundraising-hidden");
	});

        $('.fundraising-bitcoin-contribute-button').click(function() {	 	
		$('.fundraising-bitcoin-contribute-form').toggleClass("fundraising-display");
		$(this).toggleClass("fundraising-hidden");
		
		$.post($("#callback_url").val(), {entity_guid:$("#entity_guid").val()}, 
			function( data ) {
				$("#bitcoin_address").text(data);
				$("#bitcoin_qrcode").attr("src",'https://blockchain.info/es/qr?data=' + data + '&size=200');			
			}
		);
	});
});

