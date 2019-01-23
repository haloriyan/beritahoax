<?php


class news {
	public function __construct() {
		$this->total = 0;
	}
	public function regex($string, $tagname) {
		$pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
	    preg_match_all('/<'.$tagname.'>(.*?)<\/'.$tagname.'>/s', $string, $matches);
	    return $matches[1];
	}
	public function check($a, $b) {
		$nilai = 0;
		$b = strtolower($b);
		$pA = explode(" ", $a);
		$pB = explode(" ", $b);
		$i = 1;
		foreach ($pA as $key => $value) {
			if(strpos($b, $value) === FALSE) {
				// echo "tiada ";
			}else {
				// echo "ada ";
				$nilai = $i++ / count($pA) * 100;
			}
		}
		return $nilai;
	}
	public function getFromTag($str, $tag) {
		preg_match_all("/<$tag ?.*>(.*)<\/$tag>/", $str, $matches);
		return $matches[1];
	}
	public function getDetik($keyword) {
		$nilaiSementara = 0;
		$get = file_get_contents("https://www.detik.com/search/searchall?query=".urlencode($keyword));
		$article = $this->regex($get, "article");
		// $this->total = $newTotal;
		$tot = count($article);
		foreach ($article as $key => $value) {
			$tAwal = explode('<h2 class="title">', $value);
			$title = explode('</h2>', $tAwal[1])[0];
			// echo $title."<br />";
			$nilaiSementara += $this->check($keyword, $title);
		}
		$res = $nilaiSementara / ($tot * 100) * 100;
		$newTotal = $this->total + $res;
		$this->total = $newTotal;
	}
	public function getCNN($keyword) {
		$nilaiSementara = 0;
		$get = file_get_contents("https://www.cnnindonesia.com/search/?query=".urlencode($keyword));
		$article = $this->regex($get, "article");
		$tot = count($article);
		foreach ($article as $key => $value) {
			$tAwal = explode('<h2 class="title">', $value);
			$title = explode('</h2>', $tAwal[1])[0];
			// echo $title."<br />";
			$nilaiSementara += $this->check($keyword, $title);
		}
		$res = $nilaiSementara / ($tot * 100) * 100;
		$newTotal = $this->total + $res;
		$this->total = $newTotal;
	}
	public function getViva($keyword) {
		$nilaiSementara = 0;
		$get = file_get_contents("https://www.viva.co.id/search?q=".urlencode($keyword)."&type=all");
		$p = explode('id="load_content">', $get);
		$a = $this->regex($p[1], "h3");
		$tot = count($a);
		foreach ($a as $key => $value) {
			// echo $value."<br />";
			$nilaiSementara += $this->check($keyword, $value);
		}
		$res = $nilaiSementara / ($tot * 100) * 100;
		$newTotal = $this->total + $res;
		$this->total = $newTotal;
	}
	public function getSindo($keyword) {
		$get = file_get_contents("https://search.sindonews.com/search?type=artikel&q=".urlencode($keyword));
		$p = explode('class="news-search">', $get);
		$a = explode('<div class="news-title ekbis-title">', $p[1]);
		foreach ($a as $key => $value) {
			echo $value;
		}
	}
	public function persen($angka) {
		return explode(".", $angka)[0];
	}
	public function get() {
		$keyword = $_POST['keyword'];
		$this->getDetik($keyword);
		$this->getCNN($keyword);
		$this->getViva($keyword);
		
		$res = $this->total;
		if($res < 50) {
			$status = "tidak benar";
		}else if($res >= 50 && $res < 70) {
			$status = "bisa jadi benar";
		}else if($res >= 70) {
			$status = "benar";
		}
		$respon = ["status" => $status,"percentage" => $this->persen($res)];
		echo json_encode($respon);
	}
}

$news = new news();
echo $news->get("prabowo capres");
// $news->test();
// $news->getViva("pajak selebgram");