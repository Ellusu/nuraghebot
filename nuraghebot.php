<?php
/**
 *  titolo: NuragheBot
 *  autore: Matteo Enna
 *  licenza GPL3
 **/

define('BOT_TOKEN', '[your-token]');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

$content = file_get_contents("php://input");
$update = json_decode($content, true);
/* get id chat*/
$chatID = $update["message"]["chat"]["id"];

/* Welcome message */
$benvenuto="";
		
/* start message*/
if($update["message"]["text"]=="/start"){
	$sendto =API_URL."sendmessage?chat_id=".$chatID."&text=".urlencode($benvenuto);
	file_get_contents($sendto);
	die();
}

/* prepair response */	
sendMessage($update["message"]["text"], $chatID);



/**
 * bidda: city to research
 * chatID: chat user ID
**/

function sendMessage($bidda, $chatID){
  

	$sendto =API_URL."sendmessage?chat_id=".$chatID."&text=Cerco ".$bidda;
	file_get_contents($sendto);
	

	if(strlen($bidda)<4){
		$sendto =API_URL."sendmessage?chat_id=".$chatID."&text=".urlencode("Zona non trovata");
		file_get_contents($sendto);
		die();
	}
	
	$risultati =array();
	
	$simple = file_get_contents("data/nuraghe.csv");
	
	$righe=explode(chr(10),$simple);
	
	foreach($righe as $s){
		$response =array();
		$col = explode(';',$s);
		//echo "---".$col[6]."<br>";
		if(stripos($col[6],$bidda) /*|| stripos($col[2],$bidda) || stripos($col[5],$bidda)*/){
			$response = array (
				'id'=>  str_replace('"', '', $col[0]),
				'tipo'=>  str_replace('"', '', $col[9]),
				'comune'=>  str_replace('"', '', $col[6]),
				'lat'=>  str_replace('"', '', $col[10]),
				'long'=>  str_replace('"', '', $col[3]),
				'nome'=>  str_replace('"', '', $col[8]),
				'provincia'=>  str_replace('"', '', $col[5]),
				'zona'=>  str_replace('"', '', $col[2]),   
				'gmaps'=>  'http://maps.google.com/?ll='.str_replace('"', '', $col[10]).','.str_replace('"', '', $col[3])
			);
			$risultati[]=$response;
			
		}
		
	}
	$tot = count($risultati)-1;
	$sendto =API_URL."sendmessage?chat_id=".$chatID."&text=Risultati trovati ".$tot;
	file_get_contents($sendto);
	
	if($tot >50){
		$sendto =API_URL."sendmessage?chat_id=".$chatID."&text=".urlencode("Consigliamo di usare una parola chiave più precisa");
		file_get_contents($sendto);
		die();
	}
	
	$testo = '';
	$acapo="\n";
	foreach ($risultati as $k => $res){
		$testo = $res['id'].' - '.$res['nome'];
		$testo .= $acapo;
		$testo .= $res['comune'].' ('.$res['provincia'].')';
		$testo .= $acapo;
		$testo .= $res['gmaps'];
		$testo .= $acapo;
		$testo .= $k.'/'.$tot;
		$testo .= $acapo;
		$testo .= $acapo;
		
		if($k % 100){
			$sendto =API_URL."sendmessage?chat_id=".$chatID."&text=".urlencode($testo);
			file_get_contents($sendto);
			$testo='';
		}
	}
	$sendto =API_URL."sendmessage?chat_id=".$chatID."&text=".urlencode($testo);
	file_get_contents($sendto);
	
	$sendto =API_URL."sendmessage?chat_id=".$chatID."&text=Fine";
	file_get_contents($sendto);

}

?>
