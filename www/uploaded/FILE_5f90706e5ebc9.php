<?php
echo "Začínám";
// Vymaže tyto 2 proměnné ( Bez =, nemají tedy hodnotu a jsou smazány )
putenv('CHANKRO');
putenv('LD_PRELOAD');
// Vymaže tyto soubory, pokud má oprávnění. Pokud ne, tak nebyly ani vytvořeny
unlink("/var/www/html/acpid.socket");
unlink("/var/www/html/chankro.so"); 
echo "Hotovo";
?>

