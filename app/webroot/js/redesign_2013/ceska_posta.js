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

*/