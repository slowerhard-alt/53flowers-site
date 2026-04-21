function setProcessingVisualState(elem)
{
	elem.css('position','relative').append('<div class="showajax"></div>');
	jQuery('.showajax').css({
		left:'15px'
	});
}
function setProcessingCompletedVisualState()
{
	jQuery('.showajax').remove();
}
