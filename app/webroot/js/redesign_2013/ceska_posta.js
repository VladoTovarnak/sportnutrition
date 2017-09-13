/*

1. Balik do ruky

- kliknu na sluzbu balik do ruky
- vyskoci okno, kde vyzaduji _PSC_ nebo _MESTO_
- dotazuji se na https://b2c.cpost.cz/services/PostCode/getDataAsJson?cityOrPart=mokr%C3%A1
- dostanu odpoved v JSON
- zpracuji odpoved
	- dostal jsem seznam post a info o tom, zda mohu volit dorucovaci "okna"
		- pokud se neda volit dorucovaci "okno" vyberu postu a oknu zavru
		- pokud se da volit dorucovaci "okno" v modalu vyzvu zakaznika,
		aby si vybral v jakem case chce zasilku dorucit
	- nedostal jsem v seznamu nic
		- vybidnu zakaznika aby zkontroloval jeho zadani PSC nebo MESTA
		a zkusil znovu vyhledavat + vybidnu, ze pokud ma problem, tak
		at se ozve na telefon nebo email
	- sluzba ceske posty je nedostupna - vybidnu zakaznika, aby seckal
	s vytvorenim objednavky, nebo aby se pro dokonceni objednavky
	ozval na telefon, nebo email
- prenesu zakaznikem navolene PSC a MESTO do formulare pro vypis adresy,
nemusi to vyplnovat znovu, kdyz to uz vime

********************

2. Balik na postu

- zkontrolovat funkcnost implementace

********************

3. Balik do balikovny

- funguje podobne jako Balik na postu
- po kontrole Baliku na postu
	- naimportovat udaje o balikovnach
	- replikace Baliku na postu



CHYBY:
	- LOW PRIORITY:
		- pokud neni vybrana doprava, tak se natvrdo vklada cena od 50 Kc (problem bude, kdyz se zmeni nejnizsi cena dopravy),
		  kosik take nebere v potaz, ze je tam zbozi za vic nez 1000 Kc
		
		- neni spravne udelana tabulka s payments v objednavce, pokud zvolim dopravu na slovensko,
		  neni rozdelena cena podle toho, zda to chci na dobirku, kde je ted 250 Kc a zda to chci prevodem,
		  kde je cena 180 Kc 


*/

$(document).ready(function(){
	// listener na vyber baliku do ruky
	$("#OrderShippingId" + HOMEDELIVERY_POST_SHIPPING_ID).change(function(){
		// otevrit okno s vyzvou na zadani PSC nebo mesta
		$.fancybox(
			$('#PostDeliveryChoice').html(), {
				'autoSize'		    : true,
				'transitionIn'      : 'none',
				'transitionOut'     : 'none',
				'hideOnContentClick': false,
				'autoResize': true,
			}
        );
	});
	
	// kdyz kliknu na zvoleni casu doruceni
	$("#PostDeliveryChoiceLink").click(function(e){
		e.preventDefault();
		// vyvolam change inputu
		$('#OrderShippingId' + HOMEDELIVERY_POST_SHIPPING_ID).change();
	});
	
	// listener pro submit formulare s PSC
	$(document).on('submit', '#PostDeliveryChoiceForm', function(e) {
		e.preventDefault();
		// schovam error hlasky, predpokladam, ze to bude ok
		$('.bad-input').hide();
		$(".delivery-holder").hide();
		// otrimovat data s PSC a odstranit zbytecne mezery
		psc = $(this).find("#PostDeliveryPSC").val().trim();
		psc = psc.replace(" ", "");

		// otestovat, zda se jedna o PSC - 5 cislic
		result = psc.match("^[0-9]{5}$");
		if ( result ){
			// mam validni PSC - poslu dotaz na ceskou postu,
			// jak to je s dorucovanim na toto PSC
			$.ajax({
				url: '/post_offices/delivery_search/' + psc,
				method: 'POST',
				dataType: 'json',
				data: {
				},
				success: function(data) {
					data = data[0];
					if (data.casovaPasma) {
						if ( data.casovaPasma == 'NE' ){
							// nemam data o pochuzkach
							$(".delivery-holder").html("Na vaší adrese Česká pošta nenabízí možnost vybrat termín doručení." +
									"<br><a id=\"closeFancy\" style=\"color:red\" href=\"#\"><strong>Kliknutím pokračujte</strong> k výběru zbůsobu platby.&nbsp;&raquo;</a>");
							$(".delivery-holder").addClass("red_alert");
							$(".delivery-holder").show();
							
							$(document).on('click', '#closeFancy', function(e){
								e.preventDefault();
								$(".delivery-holder").hide(); // schovam hlasku, kdyby to znovu otevrel
								if ( $("#Address0Zip").val().length == 0 ){
									$("#Address0Zip").val(psc); // nastavim PSC, protoze uz ho znam, nemusi to znovu vypisovat
								}
								
								if ( $("#Address1Zip").val().length == 0 ){
									$('#Address1Zip').val(psc); // nastavim PSC2, odsud validuju data
								}
									
								$("#Address0CpostDeliveryPsc").val(psc); // nastavim delivery PSC - kvuli kontrole integrity
								$("#Address0CpostDeliveryInfo").val('A'); // nastavim delivery na A - default hodnota, pokud nemaji dorucovaci okna
								$("#PostDeliveryChoiceLink").html("běžný režim doručení"); // odstranim vyzvu k volbe casu, at to nelaka
								$('html, body').animate({ 
								    scrollTop: ($('#PaymentInfo').first().offset().top)
								},500); // odskroluju dolu k informacim o platbe
								parent.$.fancybox.close(); // zavru fancybox
							});
						} else{
							// mam data o pochuzkach jsou dve
							$(".delivery-holder").html("<strong>Vyberte prosím kliknutím jeden z termínů doručení:</strong><br>" +
									"Dopolední pochůzka: <a id=\"A\" class=\"closeFancy\" style=\"color:red\" href=\"#\"><strong>" + data.casDopoledniPochuzky + "</strong>&nbsp;&raquo;</a>" +
									"<br>Odpolední pochůzka: <a id=\"B\" class=\"closeFancy\" style=\"color:red\" href=\"#\"><strong>" + data.casOdpoledniPochuzky + "</strong>&nbsp;&raquo;</a>");
							$(".delivery-holder").removeClass("red_alert");
							$(".delivery-holder").show();
							
							$(document).on('click', '.closeFancy', function(e){
								e.preventDefault();
								$(".delivery-holder").hide(); // schovam hlasku, kdyby to znovu otevrel
								if ( $("#Address0Zip").val().length == 0 ){
									$("#Address0Zip").val(psc); // nastavim PSC, protoze uz ho znam, nemusi to znovu vypisovat
								}
								
								if ( $("#Address1Zip").val().length == 0 ){
									$('#Address1Zip').val(psc); // nastavim PSC2, odsud validuju data
								}
								
								$("#Address0CpostDeliveryPsc").val(psc); // nastavim delivery PSC - kvuli kontrole integrity
								$("#Address0CpostDeliveryInfo").val(
									$(this).attr("id")
								); // nastavim delivery na to co zvolil
								
								$("#PostDeliveryChoiceLink").html($(this).find("strong").text()); // nastavim na to co zvolil
								parent.$.fancybox.close(); // zavru fancybox
							});
						}
					} else {
						// @TODO - hlasku vyhodit pres HTML
						// nemam casova pasma doslo k chybe
						alert('Došlo k chybě, při vyhledávání na serveru České pošty.');
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert("error: " + textStatus);
				},
				complete: function(jqXHR, textStatus) {
					$.fancybox.update();
				}
			});
		} else {
			$('.bad-input').show();
		}
	});
	
});