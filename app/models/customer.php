<?php
class Customer extends AppModel {
	var $name = 'Customer';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('CustomerType');

	var $hasMany = array(
		'Order',
		'Address' => array(
			'dependent' => true
		),
		'CustomerLogin' => array(
			'dependent' => true
		)
	);

 	var $validate = array(
		'first_name' => array(
			'rule' => array('minLength', 3),
			'message' => 'Vyplňte prosím vaše jméno.'
		),
		'last_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyplňte prosím vaše příjmení.'
			)
		),
		'phone' => array(
			'minLength' => array(
				'rule' => array('minLength', 8),
				'message' => 'Vyplňte prosím správně vaše telefonní číslo.',
				'last' => true
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Vyplňte prosím existující emailovou adresu.',
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Uživatel s touto emailovou adresou již existuje. Zvolte jinou emailovou adresu, nebo se přihlašte.'
			)
		)
	);
 	
 	var $virtualFields = array(
 		'name' => 'CONCAT(Customer.last_name, " ", Customer.first_name)'	
 	);
 	
 	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
 		$parameters = compact('conditions');
 		$this->recursive = $recursive;
 		$count = $this->find('count', array_merge($parameters, $extra));
 		if (isset($extra['group'])) {
 			$count = $this->getAffectedRows();
 		}
 		return $count;
 	}
 	

	function assignPassword($customer_login_id, $email){
		$start = rand(0, 23);
		$password = md5($email . Configure::read('Security.salt'));
		$password = substr($password, $start, 8);
		$password = strtolower($password);
	
		$customer_login = array(
			'CustomerLogin' => array(
				'id' => $customer_login_id,
				'password' => md5($password)
			)
		);

		$this->CustomerLogin->save($customer_login, false);
		return $password;
	}
	
	
	function changePassword($customer){
		include 'class.phpmailer.php';

		$mail = &new phpmailer;

		$mail->CharSet = $this->CharSet = 'utf-8';
		$mail->Hostname = $this->Hostname = CUST_ROOT;
		$mail->Sender = $this->Sender = CUST_MAIL;
		$mail->From = $this->From = CUST_MAIL;
		$mail->FromName = $this->FromName = CUST_NAME;
		$mail->ReplyTo = $this->ReplyTo = CUST_MAIL;
		
		$mail->AddAddress($customer['Customer']['email'], $customer['Customer']['first_name'] . " " . $customer['Customer']['last_name']);
		$mail->Subject = 'změna hesla pro přístup do www.' . CUST_ROOT;
		$mail->Body = "Dobrý den,\n\n";
		$mail->Body .= "Váš požadavek na změnu hesla byl vykonán, pro přihlášení k účtu,
		použijte následující údaje: \n";
		$mail->Body .= "login: " . $customer['CustomerLogin'][0]['login'] . "\n";
		$mail->Body .= "heslo: " . $this->assignPassword($customer['CustomerLogin'][0]['id'], $customer['Customer']['email']) . "\n";
		$mail->Body .= "team " . CUST_NAME . "\n";
		$mail->Body .= "--\n";
		$mail->Body .= "emailová adresa " . $customer['Customer']['email'] . " byla použita pro vyžádání změny hesla pro přístup\n";
		$mail->Body .= "na " . CUST_ROOT . " Jste-li majitelem emailové schránky a neprováděl(a) jste žádnou žádost o změnu,\n";
		$mail->Body .= "upozorněte nás prosím na tuto skutečnost na adrese webmaster@" . CUST_ROOT;

		$mail->Send();
	}
	
	
	function loginExists($login){
		$condition = array('CustomerLogin.login' => $login);
		return $this->CustomerLogin->hasAny($condition);
	}

	
	function generateLogin($customer){
		// vygeneruje nahodne login
		do{
			// vytahnu si osm znaku z md5ky s nahodnym startem
			$start = rand(0, 23);
			$login = md5($customer['last_name'] . date("Y-m-d"));
			$login = substr($login, $start, 8);
			// dam si login do uppercase
			$login = strtoupper($login);
		} while ( $this->loginExists($login) === true );
		
		return $login;
	}
	
	
	function generatePassword($customer){
		// vytahnu si osm znaku z md5ky,
		// s nahodnym startem
		$start = rand(0, 23);
		$password = md5($customer['last_name']);
		$password = substr($password, $start, 8);
		$password = strtolower($password);
		return $password;
	}

	
	function notify_account_created($customer) {
		// musim zjistit, zda zakaznik uvedl
		// emailovou adresu, jinak nebudu mail posilat
		if (isset($customer['Customer']['email']) && !empty($customer['Customer']['email']) ){
			// vytvorim si objekt mailu
			App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
			$mail = new phpmailer();

			// uvodni nastaveni maileru
			$mail->CharSet = 'utf-8';
			$mail->Hostname = CUST_ROOT;
			$mail->Sender = CUST_MAIL;

			// nastavim adresu, od koho se poslal email
			$mail->From     = CUST_MAIL;
			$mail->FromName = "Automatické potvrzení";
			$mail->AddReplyTo(CUST_MAIL, CUST_NAME);

			// nastavim kam se posila email
			$mail->AddAddress($customer['Customer']['email'], $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name']);
			$mail->Subject = 'Vytvoření zákaznického účtu na ' . CUST_ROOT;

			// vytvorim si emailovou zpravu
			$customer_mail = 'Vážená(ý) ' . $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name'] . "\n\n";
			$customer_mail .= 'Tento email byl automaticky vygenerován a odeslán, abychom potvrdili Vaši registraci' .
			' v online obchodě http://www.' . CUST_ROOT . '/' . "\n";
			$customer_mail .= 'Váš účet byl vytvořen s těmito přihlašovacími údaji:' . "\n";
			$customer_mail .= 'LOGIN: ' . $customer['CustomerLogin'][0]['login'] . "\n";
			$customer_mail .= 'HESLO: ' . $customer['CustomerLogin'][0]['password'] . "\n";
			$customer_mail .= 'Pro přihlášení k Vašemu uživatelskému účtu použijte prosím přihlašovací formulář, který' .
			' najdete na adrese http://www.' . CUST_ROOT . '/customers/login ' . "\n";
			$customer_mail .= 'Pomocí Vašeho uživatelského účtu můžete operovat s uskutečněnými objednávkami, sledovat' .
			' jejich stav a vytvářet objednávky nové.' . "\n\n";
			$customer_mail .= 'Velmi si vážíme Vaší důvěry, děkujeme.' . "\n";

			$mail->Body = $customer_mail;

			return $mail->Send();
		}
		return false;
	}
	
	function orders_count($id) {
		return $this->Order->find('count', array(
			'conditions' => array('Order.customer_id' => $id)	
		));
	}
	
	function orders_amount($id) {
		$amount_field = 'SUM(Order.shipping_cost + Order.subtotal_with_dph)';
		$this->Order->virtualFields['amount'] = $amount_field;
		
		$amount = $this->Order->find('all', array(
			'conditions' => array('Order.customer_id' => $id),
			'fields' => array('Order.amount'),
			'contain' => array(),
			'group' => array('Order.customer_id')
		));
		
		return (empty($amount) ? 0 : $amount[0]['Order']['amount']);
	}
	
	function import() {
//		$this->truncate();
//		$this->Address->truncate();
//		$this->CustomerLogin->truncate();
		// vyberu distinct emailove adresy
		$emails = $this->find('all', array(
			'contain' => array(),
			'fields' => array('Customer.email')
		));
		$emails = Set::extract('/Customer/email', $emails);

		$condition = '';
		if (!empty($emails)) {
			$condition = 'SnCustomer.email NOT IN ("' . implode('","', $emails) . '")';
		}
		$snCustomerClasses = $this->findAllClassesSn($condition);
		if (empty($snCustomerClasses)) {
			trigger_error('Všichni zákazníci jsou již importováni.', E_USER_ERROR);
		}

		// vypnu validaci emailove adresy
		unset($this->validate['email']);
		unset($this->validate['first_name']);
		unset($this->validate['phone']);
		
		unset($this->Address->validate['street_no']);
		unset($this->Address->validate['street']);
		unset($this->Address->validate['zip']);
		
		// pro kazdou tridu adres
		foreach ($snCustomerClasses as $snCustomerClass) {
			$email = $snCustomerClass['SnCustomer']['email'];
			// jsou tam i ucty bez emailove adresy, ktere je treba opravit (kam se ma odesilat notifikace o objednavce atd.), tak je zahodim, stejne nejsou pouzivane
			if ($email != ' ' && $email != '') {
				$snCustomers = $this->findInClass($email);
				// najdu pivota
				$snCustomer = $this->findPivot($snCustomers);
				// zalozim ucet s informacemi pivota (adresa, telefon, prihlasovaci udaje)
				$customer = $this->transformSn($snCustomer);
	
				$this->create();
				if (!$this->saveAll($customer)) {
					debug($customer);
					debug($this->validationErrors);
					$this->saveAll($customer, array('validate' => false));
				}
			} else {
				$snCustomers = $this->findInClass($email);
				
				foreach ($snCustomers as $snCustomer) {
					$customer = $this->transformSn($snCustomer);
					
					$this->create();
					if (!$this->saveAll($customer)) {
						debug($customer);
						debug($this->validationErrors);
						$this->saveAll($customer, array('validate' => false));
					}
				}
			}
		}

		return true;
	}
	
	function findAllClassesSn($condition) {
		$this->setDataSource('sportnutrition');
		$query = '
			SELECT DISTINCT email
			FROM customers AS SnCustomer
		';
		
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
			
		$query .= '
			LIMIT 1500
		';

		$snCustomers = $this->query($query);
		$this->setDataSource('default');
		return $snCustomers;
	}
	
	function findPivot($snCustomers) {
		// na sportnutritionu neni email unikatni v ramci tabulky customers, ale my ho unikatni potrebujeme.
		// Proto musim v tride customeru, urcene stejnou emailovou adresou najit jeden
		// ucet, ktery prohlasim za pivota, importuju ho do db a zbytek v te tride zahodim
		// pokud je ve tride jen jeden zaznam, nemusim nic hledat a vratim ho, jako ze je pivot
		if (count($snCustomers) == 1) {
			return $snCustomers[key($snCustomers)];
		// ve tride je vice zaznamu
		} else {
			// odfiltruju, ktere nemaji uzivatelsky ucet (login)
			$theSnCustomers = $this->filterNoAccount($snCustomers);
			// pokud zbyde jeden, vratim ho jako pivota
			if (count($theSnCustomers) == 1) {
				return $theSnCustomers[key($theSnCustomers)];
			// pokud nemam pro dany email zadny s uzivatelskym uctem, vyberu libovolny (chci si ho pamatovat jen pro ucely newsletteru atd)
			} elseif (count($theSnCustomers) == 0) {
				return $snCustomers[key($snCustomers)];
			// pokud mam vice uctu pro dany email
			} else {
				// seradim ucty podle poctu prihlaseni
				$sortByLoginCount = function($a, $b) {
					if ($a['SnCustomer']['pocetprihlaseni'] == $b['SnCustomer']['pocetprihlaseni']) {
						return 0;
					}
					return ($a < $b) ? -1 : 1;
				};
				usort($theSnCustomers, $sortByLoginCount);
				// vyberu ten, ktery ma nejvic prihlaseni
				return $theSnCustomers[key($theSnCustomers)];
			}
		}
	}
	
	function filterNoAccount($snCustomers) {
		$callback = function($snCustomer) {
			return !empty($snCustomer['SnCustomer']['login']);
		};
		
		$snCustomers = array_filter($snCustomers, $callback);
		
		return $snCustomers;
	}
	
	function filterNoLogin($snCustomers) {
		$callback = function($snCustomer) {
			return $snCustomer['SnCustomer']['posledniprihlaseni'];
		};
		
		$snCustomers = array_filter($snCustomers, $callback);
		
		return $snCustomers;
	}
	
	function findInClass($email) {
		$this->setDataSource('sportnutrition');
		$query = '
			SELECT *
			FROM customers AS SnCustomer
			WHERE SnCustomer.email = "' . $email . '"';
		
		$snCustomers = $this->query($query);
		$this->setDataSource('default');
		return $snCustomers;
	}
	
	function findBySnId($snId) {
		$customer = $this->find('first', array(
			'conditions' => array('Customer.sportnutrition_id' => $snId),
			'contain' => array()
		));
		
		return $customer;
	}
	
	function findSn($snId) {
		$this->setDataSource('sportnutrition');
		$query = '
			SELECT *
			FROM customers AS SnCustomer
			WHERE SnCustomer.id = ' . $snId; 

		$snCustomer = $this->query($query);
		$this->setDataSource('default');
		return $snCustomer;
	}
	
	function transformSn($snCustomer) {
		$snCustomer['SnCustomer']['jmeno'] = trim($snCustomer['SnCustomer']['jmeno']);
		
		$customerTypeId = 0;
		$customerType = $this->CustomerType->findBySnId($snCustomer['SnCustomer']['cenova_kategorie']);
		if (!empty($customerType)) {
			$customerTypeId = $customerType['CustomerType']['id'];
		}
		
		$customer = array(
			'Customer' => array(
				'id' => $snCustomer['SnCustomer']['id'],
				'first_name' => $this->estimateFirstName($snCustomer['SnCustomer']['jmeno']),
				'last_name' => $this->estimateLastName($snCustomer['SnCustomer']['jmeno']),
				'phone' => $snCustomer['SnCustomer']['telefon'],
				'email' => $snCustomer['SnCustomer']['email'],
				'company_name' => $snCustomer['SnCustomer']['firma'],
				'company_ico' => $snCustomer['SnCustomer']['ic'],
				'company_dic' => $snCustomer['SnCustomer']['dic'],
				'newsletter' => $snCustomer['SnCustomer']['zasilat_novinky'],
				'login_count' => $snCustomer['SnCustomer']['pocetprihlaseni'],
				'login_date' => date('Y-m-d H:i:s', $snCustomer['SnCustomer']['posledniprihlaseni']),
				'customer_type_id' => $customerTypeId,
				'active' => $snCustomer['SnCustomer']['active'],
				'sportnutrition_id' => $snCustomer['SnCustomer']['id']
			),
			'Address' => array(
				array(
					'name' => (trim($snCustomer['SnCustomer']['firma']) ? trim($snCustomer['SnCustomer']['firma']) : $snCustomer['SnCustomer']['jmeno']),
					'street' => $this->estimateStreetName($snCustomer['SnCustomer']['uliceacp']),
					'street_no' => $this->estimateStreetNumber($snCustomer['SnCustomer']['uliceacp']),
					'zip' => $snCustomer['SnCustomer']['psc'],
					'city' => $snCustomer['SnCustomer']['mesto'],
					'state' => ($snCustomer['SnCustomer']['stat'] == 'CZ' ? 'Česká republika' : ($snCustomer['SnCustomer']['stat'])),
					'type' => 'f'
				),
				array(
					'name' => (trim($snCustomer['SnCustomer']['firma']) ? trim($snCustomer['SnCustomer']['firma']) : $snCustomer['SnCustomer']['jmeno']),
					'street' => $this->estimateStreetName($snCustomer['SnCustomer']['uliceacp']),
					'street_no' => $this->estimateStreetNumber($snCustomer['SnCustomer']['uliceacp']),
					'zip' => $snCustomer['SnCustomer']['psc'],
					'city' => $snCustomer['SnCustomer']['mesto'],
					'state' => ($snCustomer['SnCustomer']['stat'] == 'CZ' ? 'Česká republika' : ($snCustomer['SnCustomer']['stat'])),
					'type' => 'd'
				)
			)
		);
		
		if ($snCustomer['SnCustomer']['login']) {
			$customer['CustomerLogin'][] = array(
				'login' => $snCustomer['SnCustomer']['login'],
				'password' => ($snCustomer['SnCustomer']['heslo'] ? md5($snCustomer['SnCustomer']['heslo']) : ($snCustomer['SnCustomer']['heslo'] == '' ? md5('') : ''))
			);
		}
		return $customer;
	}
	
	function estimateFirstName($snName) {
		$customerFirstName = '';
		if (!empty($snName)) {
			$customerName = explode(' ', $snName);
			if (count($customerName) > 1) {
				$customerFirstName = $customerName[0];
			}
		}
		return $customerFirstName;
	}
	
	function estimateLastName($snName) {
		$customerLastName = '';
		if (!empty($snName)) {
			$customerName = explode(' ', $snName);
			if (count($customerName) > 1) {
				unset($customerName[0]);
				$customerLastName = implode(' ', $customerName);
			} else {
				$customerLastName = $customerName[0];
			}
		}
		return $customerLastName;
	}
	
	function estimateStreetName($streetInfo) {
		$streetName = $streetInfo;
		if (preg_match('/(.*) (([1-9][0-9]*)\/)?([1-9][0-9]*[a-cA-C]?)/', $streetInfo, $matches)) {
			$streetName = $matches[1];
		}
		return $streetName;
	}
	
	function estimateStreetNumber($streetInfo) {
		$streetNumber = '';
		if (preg_match('/.* ((([1-9][0-9]*)\/)?([1-9][0-9]*[a-cA-C]?))/', $streetInfo, $matches)) {
			$streetNumber = $matches[1];
		}
		return $streetNumber;
	}
}
?>