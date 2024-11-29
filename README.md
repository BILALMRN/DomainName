# DomainName
Extension Paymenter for Domain Name

![Screenshot from 2024-11-29 22-35-51](https://github.com/user-attachments/assets/e671c870-eca9-4ee4-9c97-d6d157d8f200)

![Screenshot from 2024-11-29 23-40-18](https://github.com/user-attachments/assets/44d1e58a-53b3-43b0-badb-a1c9fea6e6cc)

![Screenshot from 2024-11-29 22-37-53](https://github.com/user-attachments/assets/37bcf0f8-23d0-4fa0-9b9b-23d207371853)

![Screenshot from 2024-11-29 22-38-48](https://github.com/user-attachments/assets/0ea18b3e-047d-46a7-81ae-aa29b99950e4)

## Testing Code 
To test the code, follow these steps: 

1. - Go to the `theme` folder and locate the dashboard.blade.php file to add the hooks. 
2. **Add Hook Lines:** 
- Open the dashboard.blade.php file and add the following lines of code:
  
  ![Screenshot from 2024-11-29 23-44-43](https://github.com/user-attachments/assets/2669d09a-d609-4bda-90e7-1a9b853a3dcc)
  
  ```php
  {!! hook('pages.dashboard.buttons') !!}
  {!! hook('pages.dashboard') !!} ```



## Adding a New Register 
To add a new register to the project, follow these steps: 
Create a new class that implements the `IRegister` interface.

- Example:
  
```php
namespace Paymenter\Extensions\Others\DomainName\Registers\Namecheap;
use Paymenter\Extensions\Others\DomainName\Registers\IRegister;
final class Namecheap implements IRegister
{
// Your custom registration logic

}
```
### DomainName Extension Methods 
1. **searchDomain** : Searches for a domain by name and returns detailed information about its availability and pricing.
2. **registerDomain** : Registers a specified domain for the given user over a selected number of years.
3. **renewDomain** : Renews the registration of a domain for a specified number of additional years.
4. **updateDomainInfo** : Updates the domain's contact details and other pertinent information.
5. **changeNameservers** : Changes the nameservers for a domain to the specified list of nameservers.
6. **getDomain** :Retrieves information about a specific domain, including its name, price, and availability.
