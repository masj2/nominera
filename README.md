# nominera
Nomineringsverktyg för Ungpirat

Dependencies:
https://github.com/PHPMailer
https://bulma.io/

Installation:
PHPMailer-mappen ska läggas i dokumentrooten. 
I Bulma så är primary-färgen utbytt mot #F18902 (piratorange). Namnge CSS-filen style.css och lägg i dokumentrooten.
Ange databasuppgifterna i filen databaseinfo.php
Ange datum, namn och URL till webbplatsen i config.php
Installera databasen med install.sql och byt lösenordet på adminkontot. Ta bort sql-filen när databasen är klar.
Inloggning med adminkontot sker med /login.php?t=password