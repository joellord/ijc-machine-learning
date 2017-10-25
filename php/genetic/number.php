<?
class GeneticAlgorithm {
	private $RETAIN = 10;
	private $RANDOM_SELECTION = 10;
	private $MUTATION = 25;

	private $POPULATION_SIZE = 100;
	private $CLOSE_ENOUGH = 0.01;

	private $NUM_COUNT = 4;
	private $TARGET_SUM = 200;

	private $_population;
	private $_generation;

	public function __construct() {

	}

	public function setRetainRate($rate) {
		$this->RETAIN = $rate;
	}

	public function setRandomSelectionRate($rate) {
		$this->RANDOM_SELECTION = $rate;
	}

	public function setMutationRate($rate) {
		$this->MUTATION = $rate;
	}

	private function initialize() {
		$this->_population = $this->randomPopulation();
		$this->_generation = 0;
	}

	private function randomIndividual() {
		$individual = array();
		for ($i = 0; $i < $this->NUM_COUNT; $i++) {
			$individual[] = $this->random($this->TARGET_SUM);
		}

		return $individual;
	}

	private function randomPopulation() {
		$population = array();
		for ($i = 0; $i < $this->POPULATION_SIZE; $i++) {
			$population[] = $this->randomIndividual();
		}

		return $population;
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
		return (int)round($this->random($min, $max));
	}

	private function bestIndividual() {
		return $this->fitness($this->_population[0]);
	}

	private function fitness($individual) {
		$sum = array_reduce($individual, function($a, $b) { return $a + $b; });
		return abs($this->TARGET_SUM - $sum);
	}

	private function sortByFitness() {
		uasort($this->_population, function($a, $b) {
			return ($this->fitness($a) > $this->fitness($b)) ? 1 : -1;
		});
	}

	private function mutate($population) {
		for ($i = 0; $i < count($population); $i++) {
			if ($this->random() < $this->MUTATION) {
				$index = $this->randomInt(count($population[$i]));
				$population[$i][$index] = $this->random($this->TARGET_SUM);
			}
		}
		return $population;
	}

	private function reproduce($father, $mother) {
		$half = count($father) / 2;
		$child = array();
		$child = array_merge(array_slice($father, $half), array_slice($mother, $half, $half));
		return $child;
	}

	private function evolve() {
		//Order the elements by fitness
		$this->sortByFitness();

		//Get the best individuals and keep them in the parents array
		$parents = array_slice($this->_population, 0, round($this->RETAIN / 100 * $this->POPULATION_SIZE));

		//Introduce random elements 
		for ($i = 0; $i < ($this->POPULATION_SIZE * $this->RANDOM_SELECTION / 100); $i++) {
			$parents[] = $this->randomIndividual();
		}

		//Introduce random mutations
		$parents = $this->mutate($parents);

		//Fill the rest of the array with childen
		while(count($parents) < $this->POPULATION_SIZE) {
			$father = $this->randomInt(count($parents) - 1);
			$mother = $this->randomInt(count($parents) - 1);
			if ($father != $mother) {
				$father = $parents[$father];
				$mother = $parents[$mother];
				$parents[] = $this->reproduce($father, $mother);
			}
		}

		//Use this as the new population
		$this->_population = $parents;
	}

	public function solve($numberCount, $targetSum, $debug = TRUE) {
		$this->NUM_COUNT = $numberCount;
		$this->TARGET_SUM = $targetSum;

		$this->initialize();

		while($this->bestIndividual() > $this->CLOSE_ENOUGH) {
			$this->_generation++;
			$this->evolve();
			if ($debug) {
				echo ("Generation " . $this->_generation . " Fitness " . $this->bestIndividual() . "\n");
			}
		}

		return array(
			"solution" => $this->_population[0],
			"generations" => $this->_generation
		);
	}
}

$gen = new GeneticAlgorithm();


$gen->setRetainRate(20);
$gen->setRandomSelectionRate(10);
$gen->setMutationRate(10);

var_dump($gen->solve(4, 200));

?>