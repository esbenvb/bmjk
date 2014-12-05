<script type="text/javascript">
$( document ).ready(function() {
	$.get( "register_read.php?card_sha=<?php print $sha1; ?>", function( data ) { return; });
});
</script>