<?php

require 'vendor/autoload.php';

use OpenCloud\Rackspace;

class raxdns {
	private $token;
	public $account;
	public $domains;
	function __construct($api){
		$url = "https://auth.api.rackspacecloud.com/v1.0";
		$user = $api["user"];
		$key = $api["key"];
		$this->account = $api["account"];
		$ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Auth-Key: $key","X-Auth-User: $user"));
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
	        $result = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($result, 0, $header_size);
		$temp = explode("\n",$header);
		foreach($temp as $index => $row){
			$pos = strpos($row,"X-Auth-Token");
			if($pos === 0){
				$rt = explode(" ",$row);
				$token = $rt[1];
			}
		}
		$this->token = $token;
		curl_close($ch);
	}
	function list_domains($domits){
		foreach($domits as $domaint){
			$temp["domains"][] = array(
				"accountId" => $domaint->accountId,
				"id" => $domaint->id,
				"name" => $domaint->name
			);
		}

		$this->domains = $temp["domains"];
	}
	function get_domain($zone){
		$temp = array();
		foreach($zone as $record){
			$temp[] = array(
				"id" => $record->id,
				"name" => $record->name,
				"type" => $record->type,
				"data" => $record->data
			);
		}
		return $temp;
	}
	function update_records($domainid,$data,$single=FALSE){
		$jdata = json_encode($data);
		print_r($jdata);
		$account = $this->account;
                $token = $this->token;
                $url = "https://dns.api.rackspacecloud.com/v1.0/$account/domains/$domainid/records";
		if($single !== FALSE) $url = "https://dns.api.rackspacecloud.com/v1.0/$account/domains/$domainid/records/$single";
		echo "$url\n";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Auth-Token: $token","Content-type: application/json"));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$jdata);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                $result = curl_exec($ch);
                $temp = json_decode($result,true);
                curl_close($ch);
                return $temp;
	}
}

function get_ip(){
	$url = "http://ip-api.com/json";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$result = curl_exec($ch);
	$adata = json_decode($result,true);
	$ip = $adata["query"];

	return $ip;
}

echo "Starting run\n";

$api = array(
	"account" => $_ENV["RAX_ACCOUNT"],
	"user" => $_ENV["RAX_USER"],
	"key" => $_ENV["RAX_KEY"]
);

$csv = "data/domains.csv";

$handle = fopen($csv,'r');

$ip = get_ip();

$dns = new raxdns($api);

$domains = array();

$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => $api["user"],
    'apiKey'   => $api["key"]
));

$dnsService = $client->dnsService();
$dns->list_domains($dnsService->domainList());
while($row = fgetcsv($handle)){
	$domains[$row[0]][] = array(
		"name" => $row[1],
		"type" => $row[2],
		"ndata" => $ip
	);
}
foreach($domains as $domain => $records){
	foreach($dns->domains as $rdom){
		if($rdom["name"] == $domain){
			$id = $rdom["id"];
			break;
		}
	}
	$sdkdomain = $dnsService->domain($id);
	$raxrecs = $dns->get_domain($sdkdomain->recordList());
	$adata = array("records"=>array());
	foreach($records as $record){
		echo "Checking ".$record["name"];
		foreach($raxrecs as $raxrec){
			if(($raxrec["name"] === $record["name"])&&($raxrec["type"] === $record["type"])){
				$record["data"] = $raxrec["data"];
				$record["id"] = $raxrec["id"];
				break;
			}
		}
		if($record["ndata"] !== $record["data"]){
			echo " - Needs updating\n";
			print_r($record);
			$adata["records"][] = array(
				"name" => $record["name"],
				"id" => $record["id"],
				"data" => $record["ndata"],
				"ttl" => 300
			);
		} else {
			echo " - OK!\n";
		}
	}
	foreach($adata["records"] as $updrec){
		$sdkrecord = $sdkdomain->record($updrec["id"]);
		$sdkrecord->data = $updrec["data"];
		$sdkrecord->update();
	}
}

fclose($handle);
echo "Finished run\n";
?>
