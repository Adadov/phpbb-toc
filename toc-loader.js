console.log('-- [DEBUG] LOADER TOC')
function createTOC() {
	$.fn.tagName = function() {
		return this.prop("tagName").toLowerCase();
	};
	function createLevel(level=0) {
		return $('<ul></ul>').attr('class', 'level'+level);
	}
	function createLink(header) {
		return $("<a></a>")
			.attr('href', '#'+$(header).attr('id'))
			.text($(header).text());
	}

	console.log('-- BEGIN TOC loader');

	var last = 0, levels = [];
	levels[0] = createLevel(0);

	$('#toc').append(levels[0]);
	$('.content > .phead').each(function(index) {
		console.log('Title find:',this);
		var lvl = parseInt(/h([0-9])/.exec($(this).tagName())[1]) - 5;

		var entry;

		if (lvl < last) { // On redescend d'un niveau
			for(var i=last; i>lvl; i--) {
				delete levels[i];
			}
		} else if (lvl > last) {
			if ( (lvl-last) > 1 ) {
				console.log('Niveau manquant !!');
			}
			levels[lvl] = createLevel(lvl);
			entry = $(levels[lvl]).appendTo(levels[last]);
		} else {
			entry = levels[last];
		}
		entry = $('<li></li>').appendTo(entry);
		$(entry).append(createLink(this));
		$(levels[lvl]).append(entry);
	});
	console.log('-- END TOC loader');
};
window.addEventListener("load", createTOC, true);
