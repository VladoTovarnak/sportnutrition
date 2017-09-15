CREATE TABLE `post_boxes` (
  `id` int(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `PSC` int(6) NOT NULL,
  `NAZEV` varchar(100) NOT NULL,
  `ADRESA` varchar(100) NOT NULL,
  `TYP` varchar(10) NOT NULL,
  `SOUR_X` decimal(10,2) NOT NULL,
  `SOUR_Y` decimal(10,2) NOT NULL,
  `OBEC` varchar(100) NOT NULL,
  `C_OBCE` varchar(100) NOT NULL
) ENGINE='MyISAM';

ALTER TABLE `post_boxes`
ADD INDEX `PSC` (`PSC`);

ALTER TABLE `post_boxes`
ADD `pondeli_od1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `TYP`,
ADD `pondeli_do1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `pondeli_od1`,
ADD `pondeli_od2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `pondeli_do1`,
ADD `pondeli_do2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `pondeli_od2`,
ADD `pondeli_od3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `pondeli_do2`,
ADD `pondeli_do3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `pondeli_od3`,
ADD `utery_od1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `pondeli_do3`,
ADD `utery_do1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `utery_od1`,
ADD `utery_od2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `utery_do1`,
ADD `utery_do2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `utery_od2`,
ADD `utery_od3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `utery_do2`,
ADD `utery_do3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `utery_od3`,
ADD `streda_od1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `utery_do3`,
ADD `streda_do1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `streda_od1`,
ADD `streda_od2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `streda_do1`,
ADD `streda_do2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `streda_od2`,
ADD `streda_od3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `streda_do2`,
ADD `streda_do3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `streda_od3`,
ADD `ctvrtek_od1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `streda_do3`,
ADD `ctvrtek_do1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `ctvrtek_od1`,
ADD `ctvrtek_od2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `ctvrtek_do1`,
ADD `ctvrtek_do2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `ctvrtek_od2`,
ADD `ctvrtek_od3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `ctvrtek_do2`,
ADD `ctvrtek_do3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `ctvrtek_od3`,
ADD `patek_od1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `ctvrtek_do3`,
ADD `patek_do1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `patek_od1`,
ADD `patek_od2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `patek_do1`,
ADD `patek_do2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `patek_od2`,
ADD `patek_od3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `patek_do2`,
ADD `patek_do3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `patek_od3`,
ADD `sobota_od1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `patek_do3`,
ADD `sobota_do1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `sobota_od1`,
ADD `sobota_od2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `sobota_do1`,
ADD `sobota_do2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `sobota_od2`,
ADD `sobota_od3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `sobota_do2`,
ADD `sobota_do3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `sobota_od3`,
ADD `nedele_od1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `sobota_do3`,
ADD `nedele_do1` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `nedele_od1`,
ADD `nedele_od2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `nedele_do1`,
ADD `nedele_do2` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `nedele_od2`,
ADD `nedele_od3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `nedele_do2`,
ADD `nedele_do3` varchar(40) COLLATE 'utf8_general_ci' NOT NULL AFTER `nedele_od3`;

# vlozit pres admin noveho dopravce a jeho ID predat do nize uvedene query

INSERT INTO `settings` (`created`, `modified`, `name`, `value`)
VALUES (now(), now(), 'BALIKOVNA_POST_SHIPPING_ID', '31');

# vlozit spravnou value podle aktivni DB
INSERT INTO `settings` (`created`, `modified`, `name`, `value`)
VALUES (now(), now(), 'HOMEDELIVERY_POST_ID', '2');