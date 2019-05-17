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
                    getenv('RINGCENTRAL_PASSWORD') );

  $body = array(
     'from' => array( 'phoneNumber' => getenv('RINGCENTRAL_USERNAME') ),
     'to'   => array( array('phoneNumber' => getenv('RECIPIENT_PHONE_NUMBER')) ),
     'text' => 'Message content'
  );
  $request = $rcsdk->createMultipartBuilder()
      ->setBody( $body )
      ->add(fopen(__DIR__.'/test.jpg', 'r'))
      ->request('/account/~/extension/~/sms');
      
  $r = $platform->sendRequest($request);
  print("Message sent. Message delivery status: " . $r->json()->messageStatus . "\n");
  //track_status($r->json()->id, $r->json()->messageStatus);
}catch (\RingCentral\SDK\Http\ApiException $e) {
  print 'Expected HTTP Error: ' . $e->getMessage() . PHP_EOL;
}

function track_status($messageId, $messageStatus){
  global $platform;
  while ($messageStatus == "Queued"){
    sleep(1);
    $r = $platform->get("/account/~/extension/~/message-store/{$messageId}" );
    $messageStatus = $r->json()->messageStatus;
    print("Message delivery status: " . $messageStatus . "\n");
  }
}
