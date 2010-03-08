<?php if($customizableCountry) : ?>
<div id='userCountryDiv'>
	<div id='announcement'>
	<b style='font-size:11px'>
		<?php printf(___('Welcome visitor from %s! We\'ll try to show you some projects from %s.You can always change this location at the bottom of any page %s(hide)%s', true),  $userCountryName, $userCountryName, '<a  href="javascript:void(0)" onclick="hideUserCountryDiv()">', '</a>'); ?>
	</b>
	</div>
</div>
<script>
if(readCookie('country_welcomed')) {
	hideUserCountryDiv('fast');
}
</script>
<?php endif; ?>