Usage:

Check session
```php
$session = ModuleSessions::check();
```
$session will be false or instance of SessionEntity class


Start session, example with ModuleClients
```php
// Check supplied client data
$client = ModuleClients::checkAuthAndGetClient($email, $password);
if ($client) {
    // Start session and store logged-in user's ID
    ModuleSessions::start($client->getId(), $some_additional_data);
}
```

One of the most useful new functions is kicking other client's session to prevent multiple log-ins with one account
```php
// Remove all old client's sessions to kick all previous log-ins
ModuleSessions::deleteAllClientSessions($client);
```
