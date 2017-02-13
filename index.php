<?php

class VkApi
{
    protected $endpoint = 'https://api.vk.com/method/';
    protected $apiVersion = '5.26';

    protected function getWallPostsAmount($id) {
        $data = $this->performRequest('wall.get', array( 'owner_id' => $id, 'count' => 100,));
        return $data;
    }

    protected function performRequest($method, $params) {
         $params['v'] = $this->apiVersion;
         $url = $this->endpoint . $method . '?' . http_build_query($params);
         return json_decode(file_get_contents($url), true);
    }

    public static function getNewsToday() {

    	$api = new VkApi;
    	$result = [];

		$newsGroup = preg_split('~/~', file_get_contents("group.txt"));

		foreach ($newsGroup as $group) {
			$result = array_merge ($result, $api->getWallPostsAmount(-$group)['response']['items']);
		}

		$by = 'date';
		usort($result, function($first, $second) use( $by  ) {
		    if ($first[$by]<$second[$by]) { return 1; }
		    elseif ($first[$by]>$second[$by]) { return -1; }
		    return 0;
		});

		return $result;
    }

}


foreach (VkApi::getNewsToday() as $value) {
	if(date('d-m-Y',$value['date']) == date('d-m-Y') && preg_match(file_get_contents("key.txt"), strtolower($value['text']))) {
		echo date('d-m-Y H:i:s',$value['date']);
		echo "<br/>";
		echo $value['text'];
		echo "<br/><br/>";
	}
}


echo '<script type="text/javascript">

		setInterval("location.reload();", 60000);
		</script>';