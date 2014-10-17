$(document).ready(function() {
	var url = document.URL;
	var a = $('<a>', { href:url } )[0];
	// pokud mam  info, ze jsem prisel na stranku redirectem z nutrishopu
	if (a.hash == '#nutrishop_redirect') {
		// zobrazim banner
		_baner = document.getElementById("banner");
		_baner.style.left = count_display() + 'px';
		_baner.style.display = 'block';		
	}
});

function count_display(){
	_width = screen.width / 2;
	_width = _width - 250;
	return _width;
}

function close_baner(){
	_baner = document.getElementById("banner");
	_baner.style.display = 'none';
	return false;
}