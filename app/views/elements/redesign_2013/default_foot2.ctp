<script type="text/javascript">
	function lazyLoad(){
		/*
		var scriptTag = document.createElement('script'); 
	    scriptTag.src = "//js/<?php echo REDESIGN_PATH?>jquery.js"; // set the src attribute
	    scriptTag.type = 'text/javascript'; // if you have an HTML5 website you may want to comment this line out
	    scriptTag.async = true; // the HTML5 async attribute
	    var headTag = document.getElementsByTagName('head')[0];
	    headTag.appendChild(scriptTag);
	    */
	    return true;
	}
</script>
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>jquery.js"></script>
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>fancybox/jquery.mousewheel-3.0.6.pack.js"></script>
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>fancybox/jquery.fancybox.js"></script>
	<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery.easing.js" type="text/javascript"></script>
	<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery.slidorion.js" type="text/javascript"></script>
<?php 
	if ($this->params['controller'] == 'searches' && $this->params['action'] == 'do_search') {
?>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>search_filter.js"></script>
<?php
	}
?>
	<!-- veci pro vypis kategorie -->
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>products_pagination.js"></script>
	<script type="text/javascript" src="/loadmask/jquery.loadmask.min.js"></script>

	<!-- veci pro vypis detailu produktu -->
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>comment_form_management.js"></script>

	<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript" src="/jRating-master/jquery/jRating.jquery.js"></script>
	<script type="text/javascript">
		var ratingStarType = 'small';
		<?php if (isset($this->params['controller']) && isset($this->params['action']) && $this->params['controller'] == 'products' && $this->params['action'] == 'view') { ?>
			ratingStarType = 'big';
		<?php } ?> 
	</script>
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>/product_rating_management.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		var url = document.URL;
		var a = $('<a>', { href:url } )[0];
		if (a.hash == '#nutrishop_redirect') {
			$('#banner').fancybox({
				width: 600,
				height: 470,
				autoSize: false,
			}).trigger('click');
		}
		// select box s vyrobci
		$('#ManufacturerSelect').change(function() {
			 $("#ManufacturerSelect option:selected").each(function() {
				 manufacturerId = $(this).attr('value');
				 if (manufacturerId) {
					// natahnu vyrobce a presmeruju
					$.ajax({
						type: 'POST',
						url: '/manufacturers/ajax_get_url',
						dataType: 'json',
						data: {
							id: manufacturerId
						},
						success: function(data) {
							if (data.success) {
								window.location.href = data.message;
							}
						}
					});
				 }
			});
		});

		// z main.js
		$("#header ul.accordion li a").click(function(){
			if($(this).attr('href')[0] == "#") {
				// byl pred kliknutim zvoleny tab kosik?
				var basketIsActive = $('#header ul.accordion').find('li.basket.active');
				basketIsActive = basketIsActive.length;
				$("#basket").hide();
				$("#login").hide();
				$("#info").hide();
				$($(this).attr('href')).show();
				$(this).parent().parent().find('li').removeClass('active');
				var tab = $(this).parent().attr('class');
				// pokud byl predtim zvoleny tab kosik a znovu jsem klikl na dany tab, presmeruju do kosiku
				if (tab == 'basket' && basketIsActive) {
					window.location.href = '/objednavka';
				}
				$(this).parent().addClass("active");
				
				$.ajax({
					url: '/tools/login_box_tab',
					dataType: 'json',
					type: 'post',
					data: {tab: tab}
				});
				return false;
			}
		});

		$("#slides").slidorion({
			interval: 10000
		});
		$("#best_products").slidorion({
			effect: 'slideLeft'
		});
	
		$(".tabs").tabs();
		$(".fancybox").fancybox();

		// z detailu produktu
		if (window.location.hash == '#comment_list') {
			// zjistim id tabu s diskuzi
			var index = $('.tabs a[href="#tabs-2"]').parent().index();
			// tab nastavim jako otevreny
			$(".tabs").tabs("option", "active", index);
		}

		$('.add_comment_link').click(function(e) {
			// zjistim id tabu s diskuzi
			var index = $('.tabs a[href="#tabs-2"]').parent().index();
			// tab nastavim jako otevreny
			$(".tabs").tabs("option", "active", index);
		});

		$('.view_comments_link').click(function(e) {
			// zjistim id tabu s diskuzi
			var index = $('.tabs a[href="#tabs-2"]').parent().index();
			// tab nastavim jako otevreny
			$(".tabs").tabs("option", "active", index);
		});
	}); // document.ready
	</script>
	<link rel="stylesheet" type="text/css" href="/css/<?php echo REDESIGN_PATH?>fancybox/jquery.fancybox.css" media="screen" />
	<link rel="stylesheet" href="/css/<?php echo REDESIGN_PATH ?>jqueryui/style.css" />
	<link rel="stylesheet" type="text/css" href="/jRating-master/jquery/jRating.jquery.css" media="screen" />
	<link href="/loadmask/jquery.loadmask.css" rel="stylesheet" type="text/css" />
	
<script>(function() {
  var _fbq = window._fbq || (window._fbq = []);
  if (!_fbq.loaded) {
    var fbds = document.createElement('script');
    fbds.async = true;
    fbds.src = '//connect.facebook.net/en_US/fbds.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(fbds, s);
    _fbq.loaded = true;
  }
  _fbq.push(['addPixelId', '455047541326994']);
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', 'PixelInitialized', {}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=455047541326994&amp;ev=PixelInitialized" /></noscript>

<!-- JEDNOKROKOVA OBJEDNAVKA -->
<?php if ($this->params['controller'] == 'orders' && $this->params['action'] == 'one_step_order') { ?>
<script type="text/javascript">
$(document).ready(function() {
	if ($('#CustomerIsRegistered1').is(':checked')) {
		$('#CustomerOneStepOrderDiv').show();
	}
	
	$('input.customer-is-registered').change(function() {
		if (this.id == 'CustomerIsRegistered1') {
			$('#CustomerOneStepOrderDiv').show();
		} else {
			$('#CustomerOneStepOrderDiv').hide();
		}
	});

	$('#isDifferentAddressCheckbox').change(function() {
		// pokud mam dorucovaci adresu ruznou od fakturacni
		if ($(this).is(':checked')) {
			// zobrazim tabulku pro dorucovaci adresu
			$('#DeliveryAddressTable').show();
		} else {
			// schovam tabulku pro dorucovaci adresu
			$('#DeliveryAddressTable').hide();
		}
	});

	// pri zmene dopravy chci prepsat jeji cenu v kosiku 
	$('input[name="data[Order][shipping_id]"]').change(function(e) {
		var shippingId = this.value;
		// doprava je take zavisla na zpusobu platby
		var paymentId = $('input[name="data[Order][payment_id]"]:checked').val();
		fillShippingPriceCell(shippingId, paymentId);
	});

	// pri zmene zpusobu platby chci prepsat cenu dopravy v kosiku 
	$('input[name="data[Order][payment_id]"]').change(function(e) {
		var paymentId = this.value;
		// doprava je take zavisla na zpusobu platby
		var shippingId = $('input[name="data[Order][shipping_id]"]:checked').val();
		fillShippingPriceCell(shippingId, paymentId);
	});

	function fillShippingPriceCell(shippingId, paymentId) {
		var shippingPrice = 0;
		var body = $("body");
		$.ajax({
			method: 'POST',
			url: '/orders/ajax_shipping_price',
			dataType: 'json',
			data: {
				shippingId: shippingId,
				paymentId: paymentId
			},
			async: false,
			beforeSend: function(jqXHR, settings) {
				// zobrazim loading spinner
				body.addClass("loading");
			},
			success: function(data) {
				shippingPrice = parseInt(data.value);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert(textStatus);
			},
			complete: function(jqXHR, textStatus) {
				// skryju loading spinner
				body.removeClass("loading");
			}
		});

		// zjistim, jaka cena je v soucasne dobe zobrazena
		var prevShippingPrice = $('.shipping-price-span').text();
		if (prevShippingPrice == 'ZDARMA') {
			prevShippingPrice = 0;
		}
		// a pokud je nova cena ruzna, prepocitam hodnoty ceny za dopravu a celkove ceny objednavky
 		if (prevShippingPrice != shippingPrice) {
 	 		// cena dopravy
 			var shippingPriceInfo = '';
 	 		if (shippingPrice == 0) {
 	 			shippingPriceInfo = '<span class="final-price shipping-price-span">ZDARMA</span>';
 	 		} else {
 	 			shippingPriceInfo = '<span class="final-price shipping-price-span">' + shippingPrice + '</span> Kč';
 	 		}
 	 		$('.shipping-price-cell').empty();
 	 		$('.shipping-price-cell').html('<strong>' + shippingPriceInfo + '</strong>');

 	 		// celkova cena za objednavku
 	 		var goodsPrice = parseInt($('#GoodsPriceSpan').text());
 	 		var totalPrice = goodsPrice + shippingPrice;
 	 		var totalPriceInfo = '<span class="final-price total-price-span">' + totalPrice + '</span> Kč';
 	 		$('.total-price-cell').empty();
 	 		$('.total-price-cell').html('<strong>' + totalPriceInfo + '</strong>');
		}
	}

	function flashOpening() {
		return '<div id="flash" class="' + flashClass() + '">';
	}

	function flashClosing() {
	    return '</div>';
	}
	
	function flashClass() {
		return 'flash_failure';
	}

	function errorOpening() {
		return '<div class="' + errorClass() + '">';
	}

	function errorClosing() {
		return '</div>';
	}

	function errorClass() {
		return 'error-message';
	}

	// validace formu pro odeslani objednavky (doprava, platba, info o zakaznikovi)
	$('#OrderOneStepOrderForm').submit(function(e) {
		var skip = false;
		var messageOpenings = [];
		var messageClosings = [];
		var messageTexts = [];
		var messageTargets = [];
		var messageMethods = [];
		var skipTarget = false;
		var element = '';
		var theClass = '';
		
		// je vybrana doprava?
		element = '#ShippingChoiceTable';
		theClass = flashClass();
		$(element).prev('.' + theClass).remove();
		var shippingId = $('input[name="data[Order][shipping_id]"]:checked').val();
		if (typeof(shippingId) == 'undefined') {
			messageOpenings.push(flashOpening());
			messageClosings.push(flashClosing());
			messageTexts.push('Vyberte prosím způsob dopravy, kterým si přejete zboží doručit.');
			messageTargets.push(element);
			messageMethods.push('before');
			if (!skipTarget) {
				skipTarget = '#ShippingInfo';
			}
		}

		// je vybrana platba?
		element = '#PaymentChoiceTable';
		theClass = flashClass();
		$(element).prev('.' + theClass).remove();
		var paymentId = $('input[name="data[Order][payment_id]"]:checked').val();
		if (typeof(paymentId) == 'undefined') {
			messageOpenings.push(flashOpening());
			messageClosings.push(flashClosing());
			messageTexts.push('Vyberte prosím způsob platby, kterým si přejete zboží zaplatit.');
			messageTargets.push(element);
			messageMethods.push('before');
			if (!skipTarget) {
				skipTarget = '#PaymentInfo';
			}
		}

		var customerValid = true;

		// krestni jmeno
		// smazu message, pokud byla zobrazena
		element = '#CustomerFirstName';
		theClass = errorClass();
		$(element).next('.' + theClass).remove();
		var elementValue =  $(element).val();
		if (elementValue == '') {
			customerValid = false;
			messageOpenings.push(errorOpening());
			messageClosings.push(errorClosing());			
			messageTexts.push('Vyplňte prosím vaše jméno.');
			messageTargets.push(element);
			messageMethods.push('after');
			if (!skipTarget) {
				skipTarget = '#CustomerInfo';
			}
		}

		// prijmeni
		// smazu message, pokud byla zobrazena
		element = '#CustomerLastName';
		theClass = errorClass();
		$(element).next('.' + theClass).remove();
		var elementValue =  $(element).val();
		if (elementValue == '') {
			customerValid = false;
			messageOpenings.push(errorOpening());
			messageClosings.push(errorClosing());			
			messageTexts.push('Vyplňte prosím vaše příjmení.');
			messageTargets.push(element);
			messageMethods.push('after');
			if (!skipTarget) {
				skipTarget = '#CustomerInfo';
			}
		}

		// telefon jmeno
		// smazu message, pokud byla zobrazena
		element = '#CustomerPhone';
		theClass = errorClass();
		$(element).next('.' + theClass).remove();
		var elementValue =  $(element).val();
		if (elementValue == '') {
			customerValid = false;
			messageOpenings.push(errorOpening());
			messageClosings.push(errorClosing());			
			messageTexts.push('Vyplňte prosím správně vaše telefonní číslo.');
			messageTargets.push(element);
			messageMethods.push('after');
			if (!skipTarget) {
				skipTarget = '#CustomerInfo';
			}
		}

		// email
		// smazu message, pokud byla zobrazena
		element = '#CustomerEmail';
		theClass = errorClass();
		$(element).next('.' + theClass).remove();
		var elementValue =  $(element).val();
		if (elementValue == '') {
			customerValid = false;
			messageOpenings.push(errorOpening());
			messageClosings.push(errorClosing());			
			messageTexts.push('Vyplňte prosím vaši emailovou adresu.');
			messageTargets.push(element);
			messageMethods.push('after');
			if (!skipTarget) {
				skipTarget = '#CustomerInfo';
			}
		}

		// smazu message, pokud byla zobrazena
		element = '#CustomerInfo';
		theClass = flashClass();
		$(element).next('.' + theClass).remove();
		if (!customerValid) {
			messageOpenings.push(flashOpening());
			messageClosings.push(flashClosing());
			messageTexts.push('Opravte prosím informace o vás');
			messageTargets.push('#CustomerInfo');
			messageMethods.push('after');
		}

		var invoiceAddressValid = true;
		// ulice
		// smazu message, pokud byla zobrazena
		element = '#Address0Street';
		theClass = errorClass();
		$(element).next('.' + theClass).remove();
		var elementValue =  $(element).val();
		if (elementValue == '') {
			invoiceAddressValid = false;
			messageOpenings.push(errorOpening());
			messageClosings.push(errorClosing());			
			messageTexts.push('Vyplňte prosím název ulice.');
			messageTargets.push(element);
			messageMethods.push('after');
			if (!skipTarget) {
				skipTarget = '#InvoiceAddressInfo';
			}
		}

		// mesto
		// smazu message, pokud byla zobrazena
		element = '#Address0City';
		theClass = errorClass();
		$(element).next('.' + theClass).remove();
		var elementValue =  $(element).val();
		if (elementValue == '') {
			invoiceAddressValid = false;
			messageOpenings.push(errorOpening());
			messageClosings.push(errorClosing());			
			messageTexts.push('Vyplňte prosím název města.');
			messageTargets.push(element);
			messageMethods.push('after');
			if (!skipTarget) {
				skipTarget = '#InvoiceAddressInfo';
			}
		}

		// PSC
		// smazu message, pokud byla zobrazena
		element = '#Address0Zip';
		theClass = errorClass();
		$(element).next('.' + theClass).remove();
		var elementValue =  $(element).val();
		if (elementValue == '') {
			invoiceAddressValid = false;
			messageOpenings.push(errorOpening());
			messageClosings.push(errorClosing());			
			messageTexts.push('Vyplňte prosím správné PSČ.');
			messageTargets.push(element);
			messageMethods.push('after');
			if (!skipTarget) {
				skipTarget = '#InvoiceAddressInfo';
			}
		}

		// smazu message, pokud byla zobrazena
		element = '#InvoiceAddressInfo';
		theClass = flashClass();
		$(element).next('.' + theClass).remove();
		if (!invoiceAddressValid) {
			messageOpenings.push(flashOpening());
			messageClosings.push(flashClosing());
			messageTexts.push('Opravte prosím fakturační adresu');
			messageTargets.push('#InvoiceAddressTable');
			messageMethods.push('before');
		}

		var deliveryAddressValid = true;
		// mam zaskrtnuto, ze je dorucovaci odlisna od fakturacni?
		isDeliveryAddressDifferent = $('#isDifferentAddressCheckbox').prop('checked');
		
		// ulice
		// smazu message, pokud byla zobrazena
		element = '#Address1Street';
		theClass = errorClass();
		$(element).next('.' + theClass).remove();
		var elementValue =  $(element).val();
		if (elementValue == '' && isDeliveryAddressDifferent) {
			deliveryAddressValid = false;
			messageOpenings.push(errorOpening());
			messageClosings.push(errorClosing());			
			messageTexts.push('Vyplňte prosím název ulice.');
			messageTargets.push(element);
			messageMethods.push('after');
			if (!skipTarget) {
				skipTarget = '#DeliveryAddressInfo';
			}
		}

		// mesto
		// smazu message, pokud byla zobrazena
		element = '#Address1City';
		theClass = errorClass();
		$(element).next('.' + theClass).remove();
		var elementValue =  $(element).val();
		if (elementValue == '' && isDeliveryAddressDifferent) {
			deliveryAddressValid = false;
			messageOpenings.push(errorOpening());
			messageClosings.push(errorClosing());			
			messageTexts.push('Vyplňte prosím název města.');
			messageTargets.push(element);
			messageMethods.push('after');
			if (!skipTarget) {
				skipTarget = '#DeliveryAddressInfo';
			}
		}

		// PSC
		// smazu message, pokud byla zobrazena
		element = '#Address1Zip';
		theClass = errorClass();
		$(element).next('.' + theClass).remove();
		var elementValue =  $(element).val();
		if (elementValue == '' && isDeliveryAddressDifferent) {
			deliveryAddressValid = false;
			messageOpenings.push(errorOpening());
			messageClosings.push(errorClosing());			
			messageTexts.push('Vyplňte prosím správné PSČ.');
			messageTargets.push(element);
			messageMethods.push('after');
			if (!skipTarget) {
				skipTarget = '#DeliveryAddressInfo';
			}
		}

		// smazu message, pokud byla zobrazena
		element = '#DeliveryAddressTable';
		theClass = flashClass();
		$(element).prev('.' + theClass).remove();
		if (!deliveryAddressValid) {
			messageOpenings.push(flashOpening());
			messageClosings.push(flashClosing());
			messageTexts.push('Opravte prosím doručovací adresu');
			messageTargets.push('#DeliveryAddressTable');
			messageMethods.push('before');
		}
			

		if (messageOpenings.length > 0 && (messageOpenings.length == messageClosings.length) && (messageOpenings.length == messageTexts.length) && (messageOpenings.length == messageTargets.length) && (messageOpenings.length == messageMethods.length)) {
			for	(index = 0; index < messageOpenings.length; index++) {
			    message = messageOpenings[index] + messageTexts[index] + messageClosings[index];
			    if (messageMethods[index] == 'before') {
			    	$(messageTargets[index]).before(message);
			    } else if (messageMethods[index] == 'after') {
			    	$(messageTargets[index]).after(message);
			    }
			}
			$(document).scrollTop($(skipTarget).offset().top);
			e.preventDefault();
		} else if (messageOpenings.length != 0){
			console.log(messageOpenings.length);
			console.log(messageClosings.length);
			console.log(messageTexts.length);
			console.log(messageTargets.length);
			console.log(messageMethods.length);
			console.log('chyba v delce poli');
		} else {
			// vsechno probehlo v poradku, objednavka se ulozi
			// zobrazim loading spinner
			$("body").addClass('loading');
		}
	});
});
</script>
<?php } ?>