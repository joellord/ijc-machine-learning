<?

class GeneticAlgorithm {
	private $population;
	private $TARGET = 200;
	private $IND_COUNT = 4;
	private $MIN;
	private $MAX;

	private $POP_SIZE = 100;
	private $CLOSE_ENOUGH = 0.01;

	private $RETAIN = 10;
	private $RANDOM_SELECTION = 10;
	private $MUTATION_PROBABILITY = 25;

	public function __construct() {
		$this->population = array();

		$this->MIN = 0;
		$this->MAX = $this->TARGET - 1;
	}

	private function random($min = null, $max = null) {
		if (!$max) {
			$max = $min;
			$min = 0;
		}
		if (!$max) {
			$max = 100;
		}
		return (lcg_value()*($max-$min)) + $min;
	}

	private function randomInt($min = null, $max = null) {
		return round($this->random($min, $max));
	}

	private function randomIndividual() {
		$individual = array();
		for ($i = 0; $i < $this->IND_COUNT; $i++) {
			$individual[] = $this->random($this->MIN, $this->MAX);
		}
		return $individual;
	}

	private function randomPopulation($size) {
		$population = array();
		for ($i = 0; $i < $size; $i++) {
			$population[] = $this->randomIndividual();
		}
		return $population;
	}

	private function fitness($individual) {
		$sum = 0;
		for ($i = 0; $i < count($individual); $i++) {
			$sum += $individual[$i];
		}

		return abs(0 - $sum);
	}

	private function sortByFitness($population) {
		uasort($population, function($a, $b) {
			$fitA = $this->fitness($a);
			$fitB = $this->fitness($b);
			return $fitA > $fitB ? 1 : -1;
		});
		return $population;
	}

	private function mutate($population) {
		for ($i = 0; $i < count($population); $i++) {
			if ($this->MUTATION_PROBABILITY > $this->random()) {
				$index = $this->randomInt(count($population[$i]));
				$population[$i][$index] = $this->random($this->MIN, $this->MAX);
			}
		}

		return $population;
	}

	private function reproduce($father, $mother) {
		$half = count($father) / 2;
		$child = array();
		$child = array_merge(array_slice($father, $half), array_slice($mother, $half, count($mother)));
		return $child;
	}

	private function evolve($population) {
		$parents = array_slice($this->sortByFitness($population), round($this->POP_SIZE * $this->RETAIN/100));

		for ($i = count($parents); $i < $this->POP_SIZE - count($parents); $i++) {
			if ($this->RANDOM_SELECTION > $this->random()) {
				$parents[] = $this->randomIndividual();
			}
		}

		$parents = $this->mutate($parents);

		$rndMax = count($parents) - 1;

		while(count($parents) < $this->POP_SIZE) {
			$fatherIndex = $this->randomInt($rndMax);
			$motherIndex = $this->randomInt($rndMax);
			if ($fatherIndex != $motherIndex) {
				$father = $parents[$fatherIndex];
				$mother = $parents[$motherIndex];
				$parents[] = $this->reproduce($father, $mother);
			}
		}

		return $parents;
	}

	public function setRetainRate($rate) {
		$this->RETAIN = $rate;
	}

	public function setRandomSelectionRate($rate) {
		$this->RANDOM_SELECTION = $rate;
	}

	public function setMutationRate($rate) {
		$this->MUTATION_PROBABILITY = $rate;
	}

	public function solve($numIndividuals, $sumToFind, $debug) {
		$this->IND_COUNT = $numIndividuals;
		$this->TARGET = $sumToFind;

		$this->population = $this->randomPopulation($this->POP_SIZE);
		$generation = 0;

		while($this->fitness($this->population[0]) > $this->CLOSE_ENOUGH) {
			$generation++;
			$this->population = $this->evolve($this->population);
			if ($debug) {
				echo ("Generation " . $generation . " Fitness " . $this->fitness($this->population[0]) . "\n");
			}
		}

		return array(
			"solution" => $this->population[0],
			"generations" => $generation
		);
	}
}

$gen = new GeneticAlgorithm();

$gen->setRetainRate(10);
$gen->setRandomSelectionRate(10);
$gen->setMutationRate(25);

var_dump($gen->solve(4, 200, TRUE));

?>