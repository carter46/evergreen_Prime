<?php
/**
 * Atmospheric background for auth pages.
 * $authBgStyle: 'login' | 'register' | 'simple'
 */
$authBgStyle = $authBgStyle ?? 'simple';
$mapUrl = 'https://lh3.googleusercontent.com/aida/AP1WRLuaqlZwMUTllIVowJvsPs71UrRrOaPOvonLjjptWfNtUe89eodKTGsJELawmdRPTKUT3_hJ0tpi3hoatIQo1H8PScnwZigbNa9QZPVYYjwOmPP7WeMcZ8xN3JqNaU3I-RzfDr2CGZvmHaMVaG7Nt0aewolZdG-y4NHFq8Kdfh8HMIQoQbrYrEfYHTTYb1KSyEh_93YaTd4MmpDPGknsEOh3AMslYaIoDyqkomb33wnIg-vcUFgV7FUnJw';
$candleUrl = 'https://lh3.googleusercontent.com/aida/AP1WRLsKriSbY6BJi-Xp2Gkc7D7CVwxW2aLMAeU3vslR5SSitI_47iRoKte8OAQPNNm9SVIVAJP-rxuMAgVJSJdgU79P5g1FgzlR3L1T3iKisxILmQwUVbRBpe9jP9AcBhmn5dOT2lGX6TkC3LxSMhG_7zFbayukNlnb63bYjV8lzW6sJhcDohhWpwHwt7jiN5I_ApLCsQeZ4HaS-BEOnuPIsgpW6dVCbSLy14ewi2QOegd2_aontl0Sqgbjst8';
$dashUrl = 'https://lh3.googleusercontent.com/aida/AP1WRLt8Ib-M7remp6Rbq9rVpLhdRW1GN3Oibv3p4f7wgyDHSObC0U3gIP3H3Nhg0ZPyvMw-kY5EO4C5w2GrXD6FHYqNKBGGCauR0BLY_WB9FS9S0lL98e5Z6V4d4xPNj7TEE4QqYHxBNdWb15OT78wcQYENwskK7r9C8YNkaP7E8jiGzoCX-OSVAQHxPUg3kS0gNpQelKXlekkS7iSH89yxrnyuU66uEU6XRspLsNrFV3lnRpwtagMQFp4Grw';
?>
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none" aria-hidden="true">
<?php if ($authBgStyle === 'login'): ?>
<div class="absolute inset-0 opacity-40 mix-blend-screen scale-110">
<img alt="" class="w-full h-full object-cover" src="<?php echo htmlspecialchars($mapUrl); ?>"/>
</div>
<div class="absolute inset-0 opacity-20 mix-blend-lighten">
<img alt="" class="w-full h-full object-cover" src="<?php echo htmlspecialchars($candleUrl); ?>"/>
</div>
<div class="absolute -right-1/4 top-1/2 -translate-y-1/2 w-full max-w-[1200px] opacity-30 blur-sm">
<img alt="" class="w-full h-auto rounded-xl shadow-2xl" src="<?php echo htmlspecialchars($dashUrl); ?>"/>
</div>
<div class="absolute inset-0 bg-gradient-to-tr from-surface-container-lowest via-transparent to-transparent"></div>
<?php elseif ($authBgStyle === 'register'): ?>
<div class="absolute inset-0 bg-[#0B0E11] opacity-90"></div>
<img alt="" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-luminosity" src="<?php echo htmlspecialchars($mapUrl); ?>"/>
<div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#0B0E11]/50 to-[#0B0E11]"></div>
<div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary-container/5 blur-[120px] rounded-full auth-glow"></div>
<div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-tertiary-container/5 blur-[140px] rounded-full auth-glow"></div>
<?php else: ?>
<div class="absolute inset-0 bg-[#0B0E11] opacity-92"></div>
<img alt="" class="absolute inset-0 w-full h-full object-cover opacity-25 mix-blend-luminosity" src="<?php echo htmlspecialchars($mapUrl); ?>"/>
<div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#0B0E11]/60 to-[#0B0E11]"></div>
<div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-80 h-80 bg-primary-container/5 blur-[100px] rounded-full auth-glow"></div>
<?php endif; ?>
</div>
