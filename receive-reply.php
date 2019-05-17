<?php
use Symfony\Component\Dotenv\Dotenv;
use RingCentral\SDK\Subscription\Events\NotificationEvent;
use RingCentral\SDK\Subscription\Subscription;

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

  $subscription = $rcsdk->createSubscription();
  $eventFilters = array(
    '/restapi/v1.0/account/~/extension/~/message-store/instant?type=SMS',
    '/restapi/v1.0/account/~/extension/~/voicemail',
  );

  $subscription->addEvents( $eventFilters );

  $notificationHandler = function (NotificationEvent $message) use ($platform) {
      $payload = $message->payload();
      if (preg_match('/\/message-store\/instant/', $payload["event"])) {
        $senderNumber = $payload['body']['from']['phoneNumber'];
        $text = "This is an automatic reply. Thank you for your message!";
        reply_sms_message($senderNumber, $text);
      }else if (preg_match('/\/voicemail/', $payload["event"])){
        if ($payload['body']['from']['phoneNumber'] != null){
          $senderNumber = $payload['body']['from']['phoneNumber'];
          $text = "This is an automatic reply. Thank you for your voice message! I will get back to you asap.";
          reply_sms_message($senderNumber, $text);
        }
      }else{
        print_r("Not an event we are waiting for.\n");
      }
  };

  $subscription->addListener(Subscription::EVENT_NOTIFICATION, $notificationHandler);
  $subscription->setKeepPolling(true);
  $r = $subscription->register();
}catch (\RingCentral\SDK\Http\ApiException $e) {
  print 'Expected HTTP Error: ' . $e->getMessage() . PHP_EOL;
}

function reply_sms_message($senderNumber, $message){
  global $platform;
  $r = $platform->post('/account/~/extension/~/sms', array(
      'from' => array('phoneNumber' => getenv('RINGCENTRAL_USERNAME')),
      'to' => array(
          array('phoneNumber' => $senderNumber),
      ),
      'text' => $message
  ));
  print_r('Replied message sent. Message delivery status: ' . $r->json()->messageStatus . "\n");
}
