<?php
/* 09-07-2019 : Berat Kara - https://www.linkedin.com/in/beratkara/ */
include("anticaptcha.php");
include("imagetotext.php");
set_time_limit(0);
error_reporting(E_ALL);
ignore_user_abort(true);
$cerez=str_replace('\\','/',dirname(__FILE__)).'/cerez/cerez.txt';

function VeriOku2($Url,$data = NULL,$proxy = NULL){
			global $cerez;
			
			$Curl = curl_init ();
			curl_setopt($Curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
			curl_setopt($Curl, CURLOPT_URL, $Url);
			curl_setopt($Curl, CURLOPT_REFERER, 'https://www.linkedin.com/in/beratkara/');
			curl_setopt($Curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($Curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);

			$request_headers = array(
			  'Connection: keep-alive',
			  'Upgrade-Insecure-Requests: 1',
			  'User-Agent: Opera/9.80 (J2ME/MIDP; Opera Mini/4.2.14912/870; U; id) Presto/2.4.15',
			  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			  'Accept-Encoding: compressed',
			  'Accept-Language: tr-TR,tr;q=0.8,en-US;q=0.5,en;q=0.3',
			  'Content-Type: application/x-www-form-urlencoded',
			);
			
			curl_setopt($Curl, CURLOPT_HTTPHEADER, $request_headers);
			
			if(!empty($data))
			{
				curl_setopt($Curl, CURLOPT_POST ,1);
				curl_setopt($Curl, CURLOPT_POSTFIELDS, http_build_query($data));
			}
			
			curl_setopt($Curl, CURLOPT_ENCODING,  'gzip,deflate');
			if(!empty($proxy))
				curl_setopt($Curl, CURLOPT_PROXY, $proxy);
			
			curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($Curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($Curl,CURLOPT_COOKIEFILE,$cerez);
			curl_setopt($Curl,CURLOPT_COOKIEJAR,$cerez);
	
			$VeriOkux = curl_exec ($Curl);
			curl_close($Curl);
			
			
			return str_replace(array("\n","\t","\r"), null, $VeriOkux);
}

function VeriOkufile_download($Url,$dosya_adi){
			global $cerez;
			$Curl = curl_init ();
			curl_setopt($Curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
			curl_setopt($Curl, CURLOPT_URL, $Url);
			curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($Curl, CURLOPT_BINARYTRANSFER,1);
			curl_setopt($Curl, CURLOPT_REFERER, 'https://internet.btk.gov.tr/sitesorgu/');
			curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, true);
			
			$request_headers = array(
			  'Connection: keep-alive',
			  'Upgrade-Insecure-Requests: 1',
			  'User-Agent: Opera/9.80 (J2ME/MIDP; Opera Mini/4.2.14912/870; U; id) Presto/2.4.15',
			  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			  'Accept-Encoding: compressed',
			  'Accept-Language: tr-TR,tr;q=0.8,en-US;q=0.5,en;q=0.3',
			  'Content-Type: application/x-www-form-urlencoded',
			);
			
			curl_setopt($Curl, CURLOPT_HTTPHEADER, $request_headers);
			
			if(!empty($data))
				curl_setopt($Curl, CURLOPT_POSTFIELDS, $data);
			
			curl_setopt($Curl, CURLOPT_ENCODING,  'gzip,deflate');
			if(!empty($proxy))
				curl_setopt($Curl, CURLOPT_PROXY, $proxy);
			
			curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($Curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($Curl,CURLOPT_COOKIEFILE,$cerez);
			curl_setopt($Curl,CURLOPT_COOKIEJAR,$cerez);
	
			$VeriOkux = curl_exec ($Curl);
			curl_close($Curl);
			
			$dosyayolu = dirname( __FILE__ )."/";

			$fp = fopen($dosyayolu."/images/".$dosya_adi,'w');
			fwrite($fp, $VeriOkux);
			fclose($fp);
}

	$alldata = array();
	$allpage = array();

	if (file_exists($cerez))
		unlink($cerez);

	$proxyp = @$_POST['proxy'];
		
	$proxy = null;
	
	if($proxyp == "use")
	{
		$getproxydata =file_get_contents('txt/proxy_http_ip.txt');
		$getproxydata = explode(PHP_EOL, $getproxydata);
		shuffle($getproxydata);
		$proxy = $getproxydata[rand(0,count($getproxydata)-1)];
	}
	
	$getsiteler =file_get_contents('txt/sitelerim.txt');
	$getsitelerdata = explode(PHP_EOL, $getsiteler);
	$apicaptchakey =file_get_contents('txt/api_captcha_apikey.txt');
	$aranacak_kelime =file_get_contents('txt/aranacak_kelime.txt');
	$sayfasayisi =file_get_contents('txt/kacsayfa.txt');
	$mailayarlar =file_get_contents('txt/mail_ayarlari.txt');
	$sikayetedilenler =file_get_contents('txt/sikayetler.txt');
	$mailayarlardata = explode(PHP_EOL, $mailayarlar);
	
	$datas = VeriOku2("https://www.google.com/search?hl=en&tbo=d&site=&source=hp&q=".urlencode($aranacak_kelime),null,$proxy);
	if(empty($datas))
	{
		print_r(json_encode(array("success"=>false,"error"=>"Proxyden veri gelmedi !")));
		die();
	}

	preg_match_all('@<a href="/url\?q=(.*?)"><div class=".*?">(.*?)</div><div class=".*?">(.*?)</div></a>@si', $datas, $siteler);
	
	if(!empty($siteler[3]) && count($siteler[3]) > 0)
		$alldata = array_merge($siteler[3]);
	
	if(!empty($siteler[1]) && count($siteler[1]) > 0)
		$allpage = array_merge($siteler[1]);
	
	/* diğer sayfaları gez */
	for($i = 0; $i < $sayfasayisi; $i++)
	{
		if($i == 0)
			preg_match('@<div class=".*?"><div class=".*?"><a class=".*?" href="/search\?q=(.*?)" aria-label="Next page">(.*?)</a></div></div>@si', $datas, $next);
		else
			preg_match('@<span class=".*">Page .*</span><a class=".*?" href="/search\?q=(.*?)" aria-label="Next page"@si', $datas, $next);
		
		if(!empty($next[1]))
			$nexturl = "https://www.google.com/search?q=".htmlspecialchars_decode($next[1]);
		else
			break;
	
		$datas = VeriOku2($nexturl,null,$proxy);
		if(empty($datas))
			break;
		
		preg_match_all('@<a href="/url\?q=(.*?)"><div class=".*?">(.*?)</div><div class=".*?">(.*?)</div></a>@si', $datas, $siteler);
		
		if(!empty($siteler[3]) && count($siteler[3]) > 0)
			$alldata = array_merge($alldata,$siteler[3]);
		
		if(!empty($siteler[1]) && count($siteler[1]) > 0)
			$allpage = array_merge($siteler[1]);
	}
	
	for($i = 0; $i < count($alldata); $i++)
	{
		$konum = strpos($alldata[$i], "div>");
		if ($konum !== false)
			continue;
		
		if (in_array($alldata[$i], $getsitelerdata))
			continue;
		
		if (in_array($alldata[$i], $sikayetedilenler))
			continue;
		
		sikayetet($allpage[$i],$alldata[$i]);
	}
	
	function sikayetet($url,$baseurl)
	{
		global $apicaptchakey;
		global $proxy;
		echo "Şikayet Edilecek Url : ".$url."<br>".PHP_EOL;
		$datas = VeriOku2("https://www.ihbarweb.org.tr/ihbar.php?subject=9",null,$proxy);
		if(empty($datas))
			return false;
		
		VeriOkufile_download("https://www.ihbarweb.org.tr/captcha/get_captcha.php","test.png");
		
		$api = new ImageToText();
		$api->setVerboseMode(false);
		$api->setCaseFlag(true);
		$api->setKey($apicaptchakey);
		$dosyayolu = dirname( __FILE__ )."/";
		$api->setFile($dosyayolu."/images/"."test.png");
		if (!$api->createTask()) {
			return false;
		}

		$taskId = $api->getTaskId();

		if (!$api->waitForResult()) {} else {
			$captchaText =   $api->getTaskSolution();
			$arr = array(
				"adres"=>$url,
				"detay"=>"Sahte İçerik",
				"tar"=>date("Y-m-d H:i:s"),
				"suc"=>2,
				"ad"=>"",
				"soyad"=>"",
				"tckimlik"=>"",
				"email"=>"",
				"tel"=>"",
				"security_code"=>$captchaText
			);
			$register = VeriOku2("https://www.ihbarweb.org.tr/ihbar.php?subject=9",$arr,$proxy);
			mail($mailayarlardata[1], $baseurl." Sitesi İçin İhbarWeb'e Bildirim Gönderildi", $baseurl." Sitesi İçin İhbarWeb'e Bildirim Gönderildi . Captcha : ".$captchaText." Tarih Saat :".$arr['tar'],"From:" . $mailayarlardata[0]);
		}
	}
	
?>