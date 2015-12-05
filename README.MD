Usage:

Check session

$session = ModuleSessions::check();

$session will be false or SessionEntity



Start session, example with ModuleClients

// Check supplied client data
$client = ModuleClients::checkAuthAndGetClient($data['email'], $data['password']);
if ($client) {
    // Start session and store logged-in user's ID
    ModuleSessions::start($client->getId());
}