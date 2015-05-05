<?php
include_once('Mailchimp.php');

class MailChimpTools {

    public static $apikey = '1dc2cb5152762d18ed8eb879b7b3b37d-us9';

	public static $listIds = array(
        'cz' => array(
            'plain' => '3423967b09',
         )
    );

    // prihlasi clena k odberu newsletteru
    public static function subscribe($email, $fname, $lname, $postcode = '') {
        $mc = new Mailchimp(self::$apikey);
        $mcList = new Mailchimp_Lists($mc);

        // zjistim list, kam chci clena zapsat
        $listId = self::getMailListId($postcode);
        if (!self::isSubscribed($email, $listId)) {

            $emailVar = array('email' => $email);

            $mergeVars = array(
                'fname' => $fname,
                'lname' => $lname
            );
            $emailType = 'html'; // typ emailu (html | text)
            $doubleOptin = false; // posle se zakaznikovi email potvrzujici jeho prihlaseni k odberu newsletteru
            $updateExisting = true; // pri vlozeni zakaznika s emailovou adresou, ktera jiz je v MC se updatuje | je vyhozena chyba
            $replaceInterests = true; // zajmove skupiny nahradit | pripojit ke stavajicim
            $sendWelcome = false; // poslat uvitaci zpravu (na rozdil od povoleneho double_optin neobsahuje odkaz pro potvrzeni registrace)

            self::log('EMAIL: '.$email);
            self::log($mergeVars);
            self::log("List ID=".$listId."\n");

            try {
                $mcList->subscribe($listId, $emailVar, $mergeVars, $emailType, $doubleOptin, $updateExisting, $replaceInterests, $sendWelcome);
                self::log("Subscribed successfully - look for the confirmation email!\n");

                // zakaznik muze byt pouze v jednom listu pro danou zemi, proto ho odhlasim z ostatnich
                $allCountryLists = self::getCountryMailListIds();
                $allOtherCountryLists = array_diff($allCountryLists, array(0 => $listId));
                foreach ($allOtherCountryLists as $aocl) {
                    if (self::isSubscribed($email, $aocl)) {
                        self::unsubscribe($email, $aocl);
                    }
                }
            } catch (Mailchimp_List_DoesNotExist $mldne) {
                self::log("Unable to subscribe, Mailchimp List Does Not Exist!\n");
                self::log("\tCode=".$e->getCode()."\n");
                self::log("\tMsg=".$e->getMessage()."\n");
            } catch (Exception $e) {
                self::log("Unable to load subscribe()!\n");
                self::log("\tCode=".$e->getCode()."\n");
                self::log("\tMsg=".$e->getMessage()."\n");
            }
        }
    }
    // odhlasi clena z odberu newsletteru
    public static function unsubscribe($email, $listId = null) {
        $mc = new Mailchimp(self::$apikey);
        $mcList = new Mailchimp_Lists($mc);

        // list, odkud chci clena odhlasit
        if (!$listId) {
            // zjistit id listu pro danou zemi, ve kterem je zakaznik prihlasen
            $allCountryLists = self::getCountryMailListIds();
            foreach ($allCountryLists as $acl) {
                if (self::isSubscribed($email, $acl)) {
                    $listId = $acl;
                    break;
                }
            }
        }

        if ($listId) {
            $emailVar = array('email' => $email);
            $deleteMember = false;
            $sendGoodbye = false;
            $sendNotify = false;

            self::log('EMAIL: '.$email);
            self::log("List ID=".$listId."\n");

            try {
                $res = $mcList->unsubscribe($listId, $emailVar, $deleteMember, $sendGoodbye, $sendNotify);
                self::log("Unsubscribed successfully - look for the confirmation email!\n");
            } catch (Mailchimp_Email_NotExists $mene) {
                self::log("Unable to unsubscribe, Mailchimp Email Not Exists!\n");
                self::log("\tCode=".$e->getCode()."\n");
                self::log("\tMsg=".$e->getMessage()."\n");
            } catch (Mailchimp_List_DoesNotExist $mldne) {
                self::log("Unable to unsubscribe, Mailchimp List Does Not Exist!\n");
                self::log("\tCode=".$e->getCode()."\n");
                self::log("\tMsg=".$e->getMessage()."\n");
            } catch (Exception $e) {
                self::log("Unable to load unsubscribe()!\n");
                self::log("\tCode=".$e->getCode()."\n");
                self::log("\tMsg=".$e->getMessage()."\n");
            }
        }
    }

    // je email subscribed v listu?
    public static function isSubscribed($email, $listId) {
        $mc = new Mailchimp(self::$apikey);
        $mcList = new Mailchimp_Lists($mc);
        $emailArr = array(array('email' => $email));

        $info = $mcList->memberInfo($listId, $emailArr);

        if (!$info['error_count'] && isset($info['data'][0]['status']) && $info['data'][0]['status'] == 'subscribed') {
            return $info['data'][0]['id'];
        }
        return false;
    }

    // zmeni email uzivatele
    public static function updateEmail($oldEmail, $newEmail) {
        $mc = new Mailchimp(self::$apikey);
        $mcList = new Mailchimp_Lists($mc);
        // zjistit id listu pro danou zemi, ve kterem je zakaznik prihlasen
        $allCountryLists = self::getCountryMailListIds();
        $listId = false;
        foreach ($allCountryLists as $acl) {
            if (self::isSubscribed($oldEmail, $acl)) {
                $listId = $acl;
                break;
            }
        }

        if ($listId) {
            self::log('EMAIL: '.$oldEmail);
            self::log("ZMENIT: ".$newEmail."\n");

            $emailVar = array('email' => $oldEmail);
            $mergeVars = array('email' => $newEmail);
            try {
                $mcList->updateMember($listId, $emailVar, $mergeVars);
                self::log("Email changed successfully\n");
            } catch (Exception $e) {
                self::log("Unable to load updateMember()!\n");
                self::log("\tCode=".$e->getCode()."\n");
                self::log("\tMsg=".$e->getMessage()."\n");
            }

        }

    }

    public static function log($text, $file = 'mailchimp') {
        ob_start();
        echo "[" . date("Y/m/d H:i:s") . "]\r\n";
        print_r($text);
        echo "\r\n";
        $output = ob_get_clean();
        $file_name = dirname(__FILE__).'/' . $file . '.log';
        debug($file_name); die();
        fwrite(fopen($file_name, 'a'), $output);
    }

    public static function getMailListId($postCode = '') {

        switch ($_SERVER['HTTP_HOST']) {
            case 'sportnutrition.cz' :
            case 'www.sportnutrition.cz' :
            case 'localhost' :
                $listId = self::$listIds['cz']['plain'];
        }

        return $listId;
    }

    public static function getCountryMailListIds() {

        switch ($_SERVER['HTTP_HOST']) {
            case 'sportnutrition.cz' :
            case 'www.sportnutrition.cz' :
            case 'localhost' :
                $listIds = self::$listIds['cz'];
                break;
        }

        return $listIds;
    }
}