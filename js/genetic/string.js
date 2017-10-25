var population = [];
const TARGET = "Hello World!";
const MIN = 33;
const MAX = 125;
const IND_COUNT = TARGET.length;
const POP_SIZE = 200;
const CLOSE_ENOUGH = 0.0001;
var RETAIN = 0.10;
var RANDOM_SELECTION = 0.2;
var MUTATION_PROBABILITY = 0.1;

var lowerCases = [97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122];
var upperCases = [65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90];
var numbers = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
var symbols = [33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 58, 59, 60, 61, 62, 63, 64, 91, 92, 93, 94, 95, 96, 123, 124, 125];

function randomInt(min, max) {
	return Math.round(random(min, max));
}

function random(min, max) {
	if (max == undefined) {
		max = min;
		min = 0;
	}
	if (max == undefined) {
		max = MAX;
	}
	return (Math.random()*(max-min)) + min;
}

function fitness(individual) {
	var fit = 0;
	arrA = individual.split("");
	arrB = TARGET.split("");

	for (var i = 0; i < arrA.length; i++) {
		if (arrA[i] !== arrB[i]) {
			fit += Math.abs(arrA[i].charCodeAt(0) - arrB[i].charCodeAt(0));
		}
	}

	return fit;
	for (var i = 0; i < individual.length; i++) {
		var charA = individual.charCodeAt(i);
		var charB = TARGET.charCodeAt(i);
		if (charA != charB) {
			if (lowerCases.indexOf[charA] > -1 && lowerCases.indexOf[charB] > -1) {
				fit += 5 + Math.abs(charA-charB);
				continue;
			}
			if (upperCases.indexOf[charA] > -1 && upperCases.indexOf[charB] > -1) {
				fit += 5 + Math.abs(charA-charB);
				continue;
			}
			if (numbers.indexOf[charA] > -1 && numbers.indexOf[charB] > -1) {
				fit += 5 + Math.abs(charA-charB);
				continue;
			}
			if (symbols.indexOf[charA] > -1 && symbols.indexOf[charB] > -1) {
				fit += 5 + Math.abs(charA-charB);
				continue;
			}
			fit += 10 + Math.abs(charA-charB);
		}
	}

	return fit;
}

function sortByFitness(population) {
	population.sort((a, b) => {
		var fitA = fitness(a);
		var fitB = fitness(b);
		return fitA > fitB ? 1 : -1;
	});

	return population;
}

function randomIndividual() {
	var individual = [];
	for (var i = 0; i < IND_COUNT; i++) {
		individual.push(String.fromCharCode(random(MIN, MAX)));
	}
	return individual.join("");
}

function randomPopulation(size) {
	var population = [];
	for (var i = 0; i < size; i++) {
		population.push(randomIndividual());
	}
	return population;
}

function mutate(population) {
	for (var i=0; i < population.length; i++) {
		if (MUTATION_PROBABILITY > Math.random()) {
			var index = randomInt(population[i].length);
			if (population[i][index] !== TARGET[i]) {
				var current = population[i].charCodeAt(index);
				population[i][index] = String.fromCharCode(random(MIN, MAX));
			}
		}
	}

	return population;
}

function reproduce(father, mother) {
	var half = father.length / 2;
	var child = [];
	child = child.concat(father.slice(0, half), mother.slice(half, mother.length));
	return child.join("");
}

function evolve(population) {
	var parents = [];

	//Keep the best solutions
	parents = sortByFitness(population).slice(0, Math.round(POP_SIZE*RETAIN));

	//Randomly add new elements
	for (var i = parents.length; i < POP_SIZE - parents.length; i++) {
		if (RANDOM_SELECTION > Math.random()) {
			parents.push(randomIndividual());
		}
	}

	//Mutate elements
	parents = mutate(parents);
	var rndMax = parents.length - 1;

	while (parents.length < POP_SIZE) {
		var father = randomInt(rndMax);
		var mother = randomInt(rndMax);
		if (father != mother) {
			father = parents[father];
			mother = parents[mother];
			parents.push(reproduce(father, mother));
		}
	}

	return parents;

}

function findSolution() {
	var population = randomPopulation(POP_SIZE);
	var generation = 0;
	var best = "";
	while (fitness(population[0]) > CLOSE_ENOUGH) {
		if (best != population[0]) {
			best = population[0];
			console.log(best + " (" + generation + ")");
		}
		generation++;
		population = evolve(population);
	}
	console.log(population[0]);

	return {solution: population[0], generations: generation};
}

var start = (new Date()).getTime();
var sol = findSolution();
var end = (new Date()).getTime();
var delay = end - start;
console.log("Solution found in " + sol.generations + " generations.  Found in " + delay/1000 + " seconds.");