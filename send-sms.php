<?php
use Symfony\Component\Dotenv\Dotenv;
require('vendor/autoload.php');

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$rcsdk = new RingCentral\SDK\SDK(getenv('RINGCENTRAL_CLIENT_ID'),
                                 getenv('RINGCENTRAL_CLIENT_SECRET'),
      	                         getenv('RINGCENTRAL_SERVER_URL'));

$platform = $rcsdk->platform();

try {
  $platform->login( getenv('RINGCENTRAL_USERNAME'),
                    getenv('RINGCENTRAL_EXTENSION'),
                    getenv('RINGCENTRAL_PASSWORD'));

  $params = array(
        'from' => array('phoneNumber' => getenv('RINGCENTRAL_USERNAME')),
        'to' => array(
            array('phoneNumber' => getenv('RECIPIENT_PHONE_NUMBER')),
        ),
        'text' => 'This is a test message from PHP.',
    );
  $r = $platform->post('/account/~/extension/~/sms', $params);
  print("Message sent. Message delivery status: " . $r->json()->messageStatus . "\n");
}catch (\RingCentral\SDK\Http\ApiException $e) {
  print 'Expected HTTP Error: ' . $e->getMessage() . PHP_EOL;
}
