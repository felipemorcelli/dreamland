<?php

/**
 * Class for Alchemy API request
 *
 * @property string term
 * @author Felipe Morcelli
 */
 
class AlchemyAPI
{

   private $apiKey = '51f13d1b760c0fe9dc56b6807f89df45ca70641f';

   public function analyse($tweet = null)
   {
      $url = "http://access.alchemyapi.com/calls/url/URLGetTextSentiment?apikey={$this->apiKey}&url=$tweet&outputMode=json";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);

      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

      $result = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $http_info = curl_getinfo($ch);
      
      curl_close($ch);

      if ($http_code == 200) {
         $result = json_decode($result);
         $sentiment = ($result->docSentiment->score >= 0.49 ? "positive" : 
                      ($result->docSentiment->score < 0.4 ? "negative" : "mixed"));
      } else return null;
      
      return $result->docSentiment->type;
   }

}
?>
