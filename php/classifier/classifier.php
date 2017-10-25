<?

class NaiveBayesClassifier {
	private $store;
	private $categories;
	private $SAVE_DATA = TRUE;
	private $FILE;

	public function __construct() {
		$this->categories = array();
		$this->FILE = "trainer.txt";
	}

	public function train($words, $category) {
		$this->read();

		$words = explode(" ", $words);
		$words = $this->cleanup($words);

		if (!in_array($category, $this->categories)) {
			$this->categories[] = $category;
			$this->store[$category] = array();
		}

		foreach($words as $word) {
			if (!$this->store[$category][$word]) {
				$this->store[$category][$word] = 1;
			} else {
				$this->store[$category][$word]++;
			}
		}

		$this->save();
	}

	public function categorize($words) {
		$this->read();

		$words = explode(" ", $words);
		$words = $this->cleanup($words);

		$results = array();

		foreach($words as $word) {
			$totalCount = 0;
			foreach($this->categories as $category) {
				$totalCount += $this->store[$category][$word];
				$results[$word][$category] = $this->store[$category][$word];
			}

			foreach($this->categories as $category) {
				if ($totalCount > 0) $results[$word][$category] /= $totalCount;
			}
		}

		$scorePerCategory = array();

		foreach($this->categories as $category) {
			$sum = 0; $cnt = 0;
			foreach($words as $word) {
				$sum += $results[$word][$category];
				$cnt++;
			}
			$scorePerCategory[$category] = $sum/$cnt;
		}

		$largestCateg = "";
		$largestCategVal = 0;
		foreach($this->categories as $categ) {
			if ($scorePerCategory[$categ] > $largestCategVal) {
				$largestCategVal = $scorePerCategory[$categ];
				$largestCateg = $categ;
			}
		}

		$response = array();
		$response["category"] = $largestCateg;
		$response["score"] = round($largestCategVal * 100);

		return $response;
	}

	private function cleanup($words = array()) {
		$cleanList = array();
		foreach($words as $word) {
			$word = strtolower($word);
			$word = preg_replace("/[^a-z]/i", "", $word);
			if (strlen($word) > 2) {
				$cleanList[] = $word;
			}
		}

		return $cleanList;
	}

	private function save() {
		if ($this->SAVE_DATA) {
			file_put_contents($this->FILE, serialize($this->store));
		}
	}

	private function read() {
		if ($this->SAVE_DATA) {
			$this->store = unserialize(file_get_contents($this->FILE));
			$this->categories = array_keys($this->store);
		}
	}
}

?>