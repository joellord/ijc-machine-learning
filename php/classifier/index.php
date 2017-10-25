<?
require_once("./classifier.php");

$classifier = new NaiveBayesClassifier();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//Training
	$classifier->train($_POST["train"], $_POST["category"]);
	echo "OK";
	echo "Trained " . $_POST["train"] . "\nCategory: " . $_POST["category"];
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
	//Categorizing
	$resp = $classifier->categorize($_GET["sentence"]);
	header('Content-Type: application/json');
	echo json_encode($resp);
}

?>
