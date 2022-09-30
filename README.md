# Lazy Bitrix24
Simple wrapper to better usage experience of Bitrix24 API

## Installation
Use composer require command

`composer require f1yback/lazy-bitrix`

## Usage

Specify required keys for configuration `$credentials` array and request client.
In application context: `domain` and `auth`; when using webhook: `domain`, `webhook` and `id`

#### Application context configuration
```
$credentials = [
    'domain' => 'mycompany.bitrix24.com',
    'auth' => 'some_auth_key', 
]; 
```

#### Webhook configuration
```   
$credentials = [
    'domain' => 'mycompany.bitrix24.com',
    'webhook' => '8hgvhbcr19elk576', // webhook key
    'id' => '283', // webhook creator id
]; 
```

When `$credentials` are ready - just pass them to the constructor

Use the `request` method to call Bitrix24 API. Method takes 3 parameters: `method` - Bitrix24 method name (e.g. 'crm.lead.list'), `callback` - callable function to handle `$response` from Bitrix24 and `data` - should be `array|null` that may contain Bitrix24 API method input parameters:  
```
use f1yback\Bitrix24\LazyBitrix;

$api = new LazyBitrix($credentials);

// use method without $data
$api->request('crm.lead.list', function($response){
    // handle $response from Bitrix24 whatever you want
    ... your code
});

// use method with $data
$api->request('crm.deal.get', function($response){
    // handle $response from Bitrix24 whatever you want
    ... your code
}, ['id' => 187203]);
```

For additional information about available methods in Bitrix24 API, please - [read the docs](https://training.bitrix24.com/rest_help/)