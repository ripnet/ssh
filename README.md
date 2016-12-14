# ripnet/ssh

Uses phpseclib and provides and easy to use interface for using SSH on certain devices.

Install uses composer. https://getcomposer.org/download/

```bash
php composer.phar require ripnet/ssh dev-master
```

Example

```php
require "vendor/autoload.php";

use ripnet/ssh/SSH;

$ssh = new SSH('192.168.1.1', 'cisco-ios');
$isLoggedIn = $ssh->login('username', 'password');
$output = $ssh->send("show version");
```

| Adapters      | Description                               |
|---------------|-------------------------------------------|
| cisco-ios     | Cisco IOS devices                         |
| adva-825      | Adva 825                                  |
| alcatel-sr    | Alcatel-Lucent (Nokia) TiMOS devices      |
| adva-new      | Adva 206/114/etc                          |
