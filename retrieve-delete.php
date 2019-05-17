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

  $params = array( 'readStatus' => "Read" );
  $r = $platform->get('/account/~/extension/~/message-store', $params);
  $records = $r->json()->records;
  $count = count($records);
  print_r("We get a list of {$count} messages\n");
  foreach ($records as $record){
      $messageId = $record->id;
      $platform->delete("/account/~/extension/~/message-store/{$messageId}");
      print_r("Message {$messageId} has been deleted.\n");
      break;
  }
}catch (\RingCentral\SDK\Http\ApiException $e) {
  print 'Expected HTTP Error: ' . $e->getMessage() . PHP_EOL;
}
