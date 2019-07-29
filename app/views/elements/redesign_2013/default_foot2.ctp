<script type="text/javascript">
	function lazyLoad(){
	    return true;
	}
</script>

	<script type="text/javascript">
		var ratingStarType = 'small';
		<?php if (isset($this->params['controller']) && isset($this->params['action']) && $this->params['controller'] == 'products' && $this->params['action'] == 'view') { ?>
			ratingStarType = 'big';
		<?php } ?>
	</script>


	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>jquery.js"></script>
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>hp.min.js?v=4"></script>
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>jquery.lazy.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$('.lazy').Lazy({
        
         effect: 'fadeIn',
         //visibleOnly: true,
         onError: function(element) {
             console.log('error loading ' + element.data('src'));
         }
     });
	});
	</script>
	<style media="screen">
		img {
			display: block;
		}
	</style>
	<!-- Required mobile resets JS -->
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>mobile_resets.js?v=1.223" /></script>
<?php
	if ($this->params['controller'] == 'searches' && $this->params['action'] == 'do_search') {

?>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>search_filter.js"></script>
<?php
	}
?>
	<script type="text/javascript">
	$(document).ready(function() {
		// upozornovaci hlaska o zruseni Nutrishopu
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
    <!-- Async css load by JS + noscript fallback -->
	<!--
    <link rel="stylesheet" property="stylesheet" type="text/css" href="/jRating-master/jquery/jRating.jquery.css" media="none" onload="if(media!='screen')media='screen'">
    <noscript><link rel="stylesheet" href="/jRating-master/jquery/jRating.jquery.css"></noscript>-->

    <link rel="stylesheet" property="stylesheet" type="text/css" href="/css/<?php echo REDESIGN_PATH?>fancybox/jquery.fancybox.css" media="none" onload="if(media!='screen')media='screen'">
    <noscript><link rel="stylesheet" href="/css/<?php echo REDESIGN_PATH?>fancybox/jquery.fancybox.css"></noscript>

    <link rel="stylesheet" property="stylesheet" type="text/css" href="/css/<?php echo REDESIGN_PATH ?>jqueryui/style.css" media="none" onload="if(media!='all')media='all'">
    <noscript><link rel="stylesheet" href="/css/<?php echo REDESIGN_PATH ?>jqueryui/style.css"></noscript>

    <link rel="stylesheet" property="stylesheet" type="text/css" href="/loadmask/jquery.loadmask.css" media="none" onload="if(media!='all')media='all'">
    <noscript><link rel="stylesheet" href="/loadmask/jquery.loadmask.css"></noscript>


<!-- SEZNAM retargeting -->
<script type="text/javascript">
/* <![CDATA[ */
var seznam_retargeting_id = 21540;
/* ]]> */
</script>
<script type="text/javascript" src="//c.imedia.cz/js/retargeting.js"></script>

<!-- JEDNOKROKOVA OBJEDNAVKA -->
<?php if ($this->params['controller'] == 'orders' && $this->params['action'] == 'one_step_order') { ?>
<script type="text/javascript">
$(document).ready(function() {
	PERSONAL_PURCHASE_SHIPPING_ID = parseInt(<?php echo PERSONAL_PURCHASE_SHIPPING_ID?>);
	ON_POST_SHIPPING_ID = parseInt(<?php echo ON_POST_SHIPPING_ID?>);
	BALIKOMAT_SHIPPING_ID = parseInt(<?php echo BALIKOMAT_SHIPPING_ID?>);
	BALIKOVNA_POST_SHIPPING_ID = parseInt(<?php echo BALIKOVNA_POST_SHIPPING_ID?>);
	HOMEDELIVERY_POST_SHIPPING_ID = parseInt(<?php echo HOMEDELIVERY_POST_SHIPPING_ID?>);
	
	// zobrazit form pro prihlaseni, pokud jsem zaskrtnul, ze zakaznik je jiz registrovany
	if ($('#CustomerIsRegistered1').is(':checked')) {
		$('#CustomerOneStepOrderDiv').show();
	}

	// zobrazit / skryt form pro prihlaseni, pokud jsem zaskrtnul, ze zakaznik je jiz registrovany
	$('input.customer-is-registered').change(function() {
		if (this.id == 'CustomerIsRegistered1') {
			$('#CustomerOneStepOrderDiv').show();
		} else {
			$('#CustomerOneStepOrderDiv').hide();
		}
	});

	// zobrazit / skryt form pro druhou adresu, pokud jsem zaskrtnul, ze ho chci
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

	// vstoupim na stranku a mam zaskrtnut osobni odber (napr pri chybe ve validaci)
	if ($('OrderShippingId' . PERSONAL_PURCHASE_SHIPPING_ID).is(':checked')) {
		$('#InvoiceAddressBox').hide();
		$('#DeliveryAddressBox').hide();
	}

	// vstoupim na stranku a mam zaskrtnutou dopravu na postu (napr pri chybe ve validaci)
	if ($('OrderShippingId' . ON_POST_SHIPPING_ID).is(':checked')) {
		$('#InvoiceAddressBox').hide();
	}

	// promenna, kde si zapamatuju typ vyhledavani pobocky ceske posty (pobocka / balikomat)
	var choiceType = 'postOffice';
	// pri zmene dopravy chci prepsat jeji cenu v kosiku 
	$('input[name="data[Order][shipping_id]"]').change(function(e) {
		// zaskrtnu radio, dostanu se sem i kliknutim mimo radio
		// potom zustava radio nezaskrtnute
		$(this).prop("checked", true);


		var shippingId = this.value;
		// doprava je take zavisla na zpusobu platby
		var paymentId = $('input[name="data[Order][payment_id]"]:checked').val();
		fillShippingPriceCell(shippingId, paymentId);

		// pokud doslo k zmene zpusobu doruceni
		// zresetuju vsechno nejdriv do puvodniho stavu
		$('#InvoiceAddressBox').show();
		$('#DeliveryAddressBox').show();
		$('#PostDeliveryChoiceLink').text("zvolit čas doručení");
		$('#PostBoxChoiceLink').text("vyberte pobočku");
		$('#PostOfficeChoiceLink').text("vyberte pobočku");
		$('#OrderShippingDeliveryPsc').val(""); // zapomenu zvolene PSC
		$("#OrderShippingDeliveryInfo").val(""); // zapomenu volbu dopo/odpo doruceni
		
		// podle aktualne zvoleneho zpusobu dopravy
		// musim zobrazit / skryt elementy s adresami apod.
		switch ( true ){ // workaround pro pouziti variables vevnitr switche
			case shippingId == PERSONAL_PURCHASE_SHIPPING_ID: // osobni odber
				$('#InvoiceAddressBox').hide(); // nepotrebuji fakturacni adresu
				$('#DeliveryAddressBox').hide(); // nepotrebuji dorucovaci adresu
			break;

			case shippingId == ON_POST_SHIPPING_ID: // dorucovani NA POSTU
				$('#DeliveryAddressBox').hide(); // nepotrebuji dorucovaci adresu
				postOfficeChoice(); // vyhodim okno s vyberem posty
			break;
			
			case shippingId == BALIKOVNA_POST_SHIPPING_ID: // dorucovani DO BALIKOVNY
				$('#DeliveryAddressBox').hide(); // nepotrebuji dorucovaci adresu
				postBoxChoice(); // vyhodim okno s vyberem balikovny
			break;

			case shippingId == HOMEDELIVERY_POST_SHIPPING_ID: // dorucovani DO RUKY
				// listener pro otevreni okna mam vytazeny ven do externiho
				// javascriptu, nemusim poustet zadny trigger
			break;
		}
	});


	// pokud chci vybrat pobocku posty
	$('#PostOfficeChoiceLink').click(function(e) {
		e.preventDefault();
		// trigger pro zmenu inputu, chova se stejne
		$('#OrderShippingId' + ON_POST_SHIPPING_ID).change();
	});

	// pokud chci vybrat BALIKOVNU ceske posty
	$('#PostBoxChoiceLink').click(function(e) {
		e.preventDefault();
		// trigger pro zmenu inputu, chova se stejne
		$('#OrderShippingId' + BALIKOVNA_POST_SHIPPING_ID).change();
	});

	function postOfficeChoice() {
		// zobrazit form pro vyber pobocky
		$.fancybox(
			$('#PostOfficeChoice').html(), {
				'autoSize'		    : true,
				'transitionIn'      : 'none',
				'transitionOut'     : 'none',
				'hideOnContentClick': false,
				'autoResize': true,
			}
        );

		$('.post-offices-list').empty();
	}

	function postBoxChoice() {
		// zobrazit form pro vyber pobocky
		$.fancybox(
			$('#PostBoxChoice').html(), {
				'autoSize'		    : true,
				'transitionIn'      : 'none',
				'transitionOut'     : 'none',
				'hideOnContentClick': false,
				'autoResize': true,
			}
        );

		$('.post-boxes-list').empty();
	}

	
	// odeslani formulare pro vyber pobocky posty
	$(document).on('submit', '#PostOfficeChoiceForm', function(e) {
		e.preventDefault();
		$('.no-input').hide();
		$('.empty-output').hide();
		$('.post-offices-list').empty();
		
		var zip = $(this).find('#PostOfficePSC').val();
		var city = $(this).find('#PostOfficeNAZPROV').val();
		if (zip == '' && city == '' && choiceType == 'postOffices') {
			$('.no-input').show();
			$('.post-offices-list').empty();
		} else {
			$.ajax({
				url: '/post_offices/ajax_search',
				method: 'POST',
				dataType: 'json',
				data: {
					zip: zip,
					city: city,
					type: choiceType
				},
				success: function(data) {
					if (data.success) {
						$('.post-offices-list').empty();
						var postOffices = data.data;
						if (postOffices.length == 0) {
							$('.empty-output').show();
						} else {
							$('.post-offices-list').append(drawPostOfficesDiv(postOffices));
						}
					} else {
						alert(data.message);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(textStatus);
				},
				complete: function(jqXHR, textStatus) {
					$.fancybox.update();
				}
			});
		}
	});


	// odeslani formulare pro vyber BALIKOVNY posty
	$(document).on('submit', '#PostBoxesChoiceForm', function(e) {
		e.preventDefault();
		$('.no-input').hide();
		$('.empty-output').hide();
		$('.post-boxes-list').empty();
		
		var zip = $(this).find('#PostBoxPSC').val();
		var city = $(this).find('#PostBoxNAZPROV').val();
		if (zip == '' && city == '' && choiceType == 'postBoxes') {
			$('.no-input').show();
			$('.post-boxes-list').empty();
		} else {
			$.ajax({
				url: '/post_boxes/ajax_search',
				method: 'POST',
				dataType: 'json',
				data: {
					zip: zip,
					city: city,
					type: choiceType
				},
				success: function(data) {
					if (data.success) {
						$('.post-boxes-list').empty();
						var postBoxes = data.data;
						if (postBoxes.length == 0) {
							$('.empty-output').show();
						} else {
							$('.post-boxes-list').append(drawPostBoxesDiv(postBoxes));
						}
					} else {
						alert(data.message);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(textStatus);
				},
				complete: function(jqXHR, textStatus) {
					$.fancybox.update();
				}
			});
		}
	});

	function drawPostOfficesDiv(postOffices) {
		var content = '<h2>Vyberte vaši pobočku</h2>';
		content += '<table style="width:100%" class="content-table"><thead><tr><th>PSČ</th><th>Adresa</th><th>&nbsp;</th></tr></thead><tbody>';
		for (i=0; i<postOffices.length; i++) {
			postOffice = postOffices[i];
			content += '<tr><td>' + postOffice.PostOffice.PSC + '</td><td>' + postOffice.PostOffice.ADRESA + '</td><td><a href="#" class="choose-post-office-link button_like_link silver" data-post-office-id="' + postOffice.PostOffice.id + '" data-post-office-zip="' + postOffice.PostOffice.PSC + '" data-post-office-address="' + postOffice.PostOffice.ADRESA + '">Vybrat</a></td></tr>';
		}
		content += '</tbody></table>';
		return content;
	}

	function drawPostBoxesDiv(postBoxes) {
		var content = '<h2>Vyberte Balíkovnu</h2>';
		content += '<table style="width:100%" class="content-table"><thead><tr><th>PSČ</th><th>Adresa</th><th>&nbsp;</th></tr></thead><tbody>';
		for (i=0; i<postBoxes.length; i++) {
			postBox = postBoxes[i];
			content += '<tr><td>' + postBox.PostBox.PSC + '</td><td>' + postBox.PostBox.ADRESA + '</td><td><a href="#" class="choose-post-box-link button_like_link silver" data-post-box-id="' + postBox.PostBox.id + '" data-post-box-zip="' + postBox.PostBox.PSC + '" data-post-box-address="' + postBox.PostBox.ADRESA + '">Vybrat</a></td></tr>';
		}
		content += '</tbody></table>';
		return content;
	}


	// vybiram pobocku posty pro doruceni baliku na postu nebo balikomat
	$(document).on('click', '.choose-post-office-link', function(e) {
		e.preventDefault();
		var postOfficeId = $(this).attr('data-post-office-id');
		var postOfficeZip = $(this).attr('data-post-office-zip');
		var postOfficeAddress = $(this).attr('data-post-office-address');
		// zobrazim adresu vybrane pobocky
		if (choiceType == 'postOffice') {
			$('#PostOfficeChoiceLink').text(postOfficeAddress);
		} else if (choiceType == 'balikomat') {
			$('#BalikomatChoiceLink').text(postOfficeAddress);
		}
		// zapamatuju si PSC vybrane posty v dorucovaci adrese
		$("#OrderShippingDeliveryPsc").val(postOfficeZip);
		$.fancybox.close();
	});


	// vybiram pobocku posty pro doruceni baliku do balikovny
	$(document).on('click', '.choose-post-box-link', function(e) {
		e.preventDefault();
		var postBoxId = $(this).attr('data-post-box-id');
		var postBoxZip = $(this).attr('data-post-box-zip');
		var postBoxAddress = $(this).attr('data-post-box-address');
		// zobrazim adresu vybrane pobocky
		$('#PostBoxChoiceLink').text(postBoxAddress);
		// zapamatuju si PSC vybrane posty v dorucovaci adrese
		$("#OrderShippingDeliveryPsc").val(postBoxZip);
		$.fancybox.close();
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
			async: true,
			beforeSend: function(jqXHR, settings) {
				// zobrazim loading spinner
				body.addClass("loading");
			},
			success: function(data) {
				shippingPrice = parseInt(data.value);

				// zjistim, jaka cena je v soucasne dobe zobrazena
				var prevShippingPrice = $('.shipping-price-span').text();
				
				if (prevShippingPrice == 'ZDARMA') {
					prevShippingPrice = 0;
				}
				// a pokud je nova cena ruzna, prepocitam hodnoty ceny za dopravu a celkove ceny objednavky
		 		if (prevShippingPrice != shippingPrice) {
					// cena za zbozi v kosiku
		 	 		var goodsPrice = parseInt($('#GoodsPriceSpan').text());
		 			// cena dopravy
		 			var shippingPriceInfo = '';
		 	 		if (shippingPrice == 0) {
		 	 			shippingPriceInfo = '<span class="final-price shipping-price-span">ZDARMA</span>';
		 	 		} else if ( shippingPrice != -1 ){
			 	 		shippingPriceInfo = '<span class="final-price shipping-price-span">' + shippingPrice + '</span> Kč';
			 	 	} else {  // doprava jeste neni vybrana, musim vlozit "cena od:"
		 	 			shippingPriceInfo = '<span class="final-price shipping-price-span">ZDARMA</span>';
		 	 			shippingPrice = 0;
				 	 	if ( goodsPrice < 1000 ){
			 	 			shippingPriceInfo = '<span class="final-price shipping-price-span">od 50</span> Kč';
			 	 			shippingPrice = 50; // @TODO - pokud neni vybrana doprava, tak se natvrdo vklada cena od 50 Kc (problem bude, kdyz se zmeni nejnizsi cena dopravy)
				 	 	}
		 	 		}
		 	 		$('.shipping-price-cell').empty();
		 	 		$('.shipping-price-cell').html('<strong>' + shippingPriceInfo + '</strong>');

		 	 		// celkova cena za objednavku
		 	 		var totalPrice = goodsPrice + shippingPrice;
		 	 		var totalPriceInfo = '<span class="final-price total-price-span">' + totalPrice + '</span> Kč';
		 	 		$('.total-price-cell').empty();
		 	 		$('.total-price-cell').html('<strong>' + totalPriceInfo + '</strong>');
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Došlo k chybě, při změně způsobu platby: " + textStatus);
			},
			complete: function(jqXHR, textStatus) {
				// skryju loading spinner
				body.removeClass("loading");
			}
		});
	}

	// JAVASCRIPTOVA VALIDACE FORMU PRO ODESLANI OBJEDNAVKY
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
		} else {
			// pokud je doprava na postu, do ruky, nebo do balikovny - mam vybrane vse, co ma byt vybrane?
			if (shippingId == ON_POST_SHIPPING_ID || shippingId == BALIKOVNA_POST_SHIPPING_ID || shippingId == HOMEDELIVERY_POST_SHIPPING_ID) {
				var postOfficeZip = $('#OrderShippingDeliveryPsc').val();
				if (typeof(postOfficeZip) == 'undefined' || postOfficeZip == '') {
					messageOpenings.push(flashOpening());
					messageClosings.push(flashClosing());
					// default message pro ON_POST_SHIPPING_ID
					var messageText = 'Vyberte prosím pobočku České pošty, kam si přejete zboží doručit.';

					if (shippingId == BALIKOVNA_POST_SHIPPING_ID) {
						messageText = 'Vyberte prosím balíkovnu České pošty, kam si přejete zboží doručit.';
					}

					if (shippingId == HOMEDELIVERY_POST_SHIPPING_ID) {
						messageText = 'Zvolte prosím čas doručení pro Balík do ruky České pošty.';
					}
					
					messageTexts.push(messageText);
					messageTargets.push(element);
					messageMethods.push('before');
					if (!skipTarget) {
						skipTarget = '#ShippingInfo';
					}
				}
			}


			// musim jeste vyzkouset integritu dat, pokud zvolil odpo/dopo doruceni
			// a zaroven doslo k zmene PSC ve formulari s adresama, musim ho na to
			// upozornit, at znovu podle dorucovaciho PSC vybere cas doruceni
			// za dorucovaci se bere primarne FAKTURACNI ADRESA a v pripade, ze
			// je zakliknute ze chce odlisnou FAKTURACNI A DORUCOVACI
			// tak se bere za dorucovaci adresu DORUCOVACI ADRESA
			if ( shippingId == HOMEDELIVERY_POST_SHIPPING_ID ){
				var chosen_psc = $("#OrderShippingDeliveryPsc").val();
				var compare_psc = $("#Address0Zip").val();

				// zjistim, zda nahodou nepouziva "jina dorucovaci adresa" nez
				// fakturacni - v tom pripade to porovnam s tim dorucovacim PSC
				if ( $("#isDifferentAddressCheckbox").prop("checked") ){
					compare_psc = $("#Address1Zip").val();
				}

				// porovnam PSC - pokud se nerovnaji, tak upozornim
				if ( chosen_psc != compare_psc ){
					messageOpenings.push(flashOpening());
					messageClosings.push(flashClosing());
					messageTexts.push("V doručovací adrese jste zvolil(a) jako PSČ: <strong>" + compare_psc + "</strong>, termín doručování jste, ale zvolil(a) pro jiné PSČ: <strong>" + chosen_psc + "</strong>.<br> Zvolte prosím znovu termín doručení podle PSČ zadaného v doručovací adrese.");
					messageTargets.push(element);
					messageMethods.push('before');
					if (!skipTarget) {
						skipTarget = '#ShippingInfo';
					}
				}
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
		// u dopravy osobnim odberem nechci validovat fakturacni adresu
		if (shippingId != PERSONAL_PURCHASE_SHIPPING_ID) {
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
		}

		var deliveryAddressValid = true;
		if (shippingId != PERSONAL_PURCHASE_SHIPPING_ID && shippingId != ON_POST_SHIPPING_ID && shippingId != BALIKOMAT_SHIPPING_ID) {
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

$.getScript("/js/redesign_2013/ceska_posta.js");
</script>
<?php } ?>

<?php 
	echo $this->element('sql_dump');