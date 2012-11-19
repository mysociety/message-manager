<script>
$(document).ready(function() {
	$('.mm-string-choices').show();
	$('.mm-string-choices').on('click', 'a', function() {
		var target = $(this).attr('href').substr(1);
		target = target.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
		$("#BoilerplateString"+target).val($(this).text());
	})
});
</script>