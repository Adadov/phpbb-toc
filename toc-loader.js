jQuery.fn.tagName = function() {
  return this.prop("tagName").toLowerCase();
};
function createLevel() {
	var lvl = $
}
function createEntry(obj, level) {
  var link = $("<a></a>")
    .attr('href', '#'+$(obj).attr('id'))
    .text($(obj).text());
	return $("<li></li>").append(link);
}

console.log('-- [DEBUG] LOADER TOC')
function createTOC() {
	console.log('-- BEGIN TOC loader');
	$('.content > .phead').each(function(index) {
		console.log('Title find:',this);
		if ($(this).tagName() == 'h5') {
			var link = $("<a></a>")
			 .attr('href', '#'+$(this).attr('id'))
			 .text($(this).text());
			$('#toc').append(tmp).append('<br />');
		}
	});
	console.log('-- END TOC loader');
};
//window.addEventListener("load", createTOC, true);
createTOC();
