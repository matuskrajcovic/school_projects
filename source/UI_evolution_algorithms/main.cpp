#include <iostream>
#include <vector>
#include <fstream>
#include <cmath>
#include <random>
#include <algorithm>


#define INPUT_FILE "input.txt"
#define ANNEALING 1
#define ROULETTE 2
#define TOURNAMENT 3
#define PRINT true
#define NO_PRINT false





//--------------------------------------------
// GLOBAL VARIABLES
//--------------------------------------------

//Number of points.
uint32_t TOWN_COUNT;

//Graph with all computed distances between points.
double** GRAPH;

//All of the coordinates on the map.
std::vector<std::pair<double, double>> TOWN_COORDS;

//Generator for random values.
std::random_device RD;





//--------------------------------------------
// FUNCTION DECLARATIONS
//--------------------------------------------


//Function for simulated annealing.
std::vector<uint32_t> annealing(double temperature, double coefficient, uint32_t iteration_count, uint32_t repeat_count, bool print_type);

//Function for genetic algorithms - roulette and tournament parent selection.
std::vector<uint32_t> genetic(uint32_t population_size, uint32_t tournament_size, uint32_t iteration_count, double elitism_rate, double mutation_probability, uint32_t search_type, bool print_type);



//Functions that create random starting permutations for all algorithms.
std::vector<uint32_t> random_init_annealing();
std::vector<std::vector<uint32_t>> random_init_genetic(uint32_t population_size);


//Utility for getting current state's length (path size).
double get_fitnes(const std::vector<uint32_t>& state);

//Utility for getting the next state by switching two points (sim. annealing).
std::vector<uint32_t> get_next_state(const std::vector<uint32_t>& state);


//Get inversed global population fitnes value - sum of all fitneses (genetic roulette).
double get_global_fitnes(const std::vector<std::vector<uint32_t>>& population);

//Parent selection functions.
std::vector<std::vector<uint32_t>> get_parents_roulette(const std::vector<std::vector<uint32_t>>& population, double global_fitnes);
std::vector<std::vector<uint32_t>> get_parents_tournament(const std::vector<std::vector<uint32_t>>& population, uint32_t tournament_size);

//Returns new child individual from the parent ones.
std::vector<std::vector<uint32_t>> breed(std::vector<uint32_t> parent1, std::vector<uint32_t> parent2);

//Mutates an individual, with some probability.
std::vector<uint32_t> mutate(std::vector<uint32_t> path, double mutation_probability);


//Utility for population sorting.
bool is_fitter(const std::vector<uint32_t>& order1, const std::vector<uint32_t>& order2);



//Utility for loading the points from an external file.
double** init_graph(const char* file_name, uint32_t& town_count, std::vector<std::pair<double, double>>& town_coords);

//Prints the solution with coordinates and length.
void print_solution(const std::vector<uint32_t>& solution, uint32_t search_type);





//--------------------------------------------
// MAIN()
//--------------------------------------------

int main(int argc, char* argv[]) {

	if (argc >= 2 && !strcmp(argv[1], "help")) {
		std::cout << "annealing   [initial temperature] [coefficient] [iterations] [repeatings] [*print]\n";
		std::cout << "roulette    [population size] [iterations] [elitism percentage] [mutation probability] [*print]\n";
		std::cout << "tournament  [population size] [tournament size] [iterations] [elitism percentage] [mutation probability] [*print]\n";
	}
	else if (argc >= 6) {

		//Graph initialization from an input file.
		if (!(GRAPH = init_graph(INPUT_FILE, TOWN_COUNT, TOWN_COORDS)))
			return -1;

		if (!strcmp(argv[1], "annealing")) {
			std::vector<uint32_t> solution1 = annealing(atof(argv[2]), atof(argv[3]), atoi(argv[4]), atoi(argv[5]), (argc > 6 && !strcmp(argv[6], "print")) ? PRINT : NO_PRINT);
			print_solution(solution1, ANNEALING);
		}
		else if (!strcmp(argv[1], "roulette")) {
			std::vector<uint32_t> solution2 = genetic(atoi(argv[2]), 0, atoi(argv[3]), atof(argv[4]), atof(argv[5]), ROULETTE, (argc > 6 && !strcmp(argv[6], "print")) ? PRINT : NO_PRINT);
			print_solution(solution2, ROULETTE);
		}
		else if (argc >= 7 && !strcmp(argv[1], "tournament")) {
			std::vector<uint32_t> solution3 = genetic(atoi(argv[2]), atoi(argv[3]), atoi(argv[4]), atof(argv[5]), atof(argv[6]), TOURNAMENT, (argc > 7 && !strcmp(argv[7], "print")) ? PRINT : NO_PRINT);
			print_solution(solution3, TOURNAMENT);
		}
		
		for (uint32_t i = 0; i < TOWN_COUNT; i++) {
			delete GRAPH[i];
		}
		delete GRAPH;

	}

	return 0;
}





//--------------------------------------------
// SEARCH FUNCTIONS
//--------------------------------------------


std::vector<uint32_t> annealing(double temperature, double coefficient, uint32_t iteration_count, uint32_t repeat_count, bool print_type) {

	std::uniform_real_distribution<double> random_chance(0, 1);

	double current_length = 0;
	double next_length = 0;

	std::vector<uint32_t> current_state;
	std::vector<uint32_t> next_state;
	std::vector<uint32_t> best_state;
	double temp;
	uint32_t iteration;

	//We repeat the annealing with initial temperature several times.
	for (uint32_t i = 0; i < repeat_count; i++) {

		temp = temperature;
		iteration = 0;
		current_state = random_init_annealing();

		//While the temperature is more tha 0.01 or we elapsed the iteration count.
		while (temp > 0.01 && iteration < iteration_count) {

			current_length = get_fitnes(current_state);

			//Get the next state by random swap. Algo get it's fitnes.
			next_state = get_next_state(current_state);
			next_length = get_fitnes(next_state);

			if (print_type)
				std::cout << get_fitnes(current_state) << '\n';

			//If fitnes is smaller (better), replace current state. Else, there is a chance it will be replaced anyway.
			if (next_length < current_length) {
				current_state = next_state;
			}
			else if (std::exp((-(next_length - current_length)) / temp) > random_chance(RD))
				current_state = next_state;

			//Lower the temperature, increase iteration.
			temp *= coefficient;
			iteration++;
		}

		//Keep track of the best state.
		if (get_fitnes(current_state) < get_fitnes(best_state) || get_fitnes(best_state) == 0)
			best_state = current_state;
		
	}

	return best_state;
}


std::vector<uint32_t> genetic(uint32_t population_size, uint32_t tournament_size, uint32_t iteration_count, double elitism_rate, double mutation_probability, uint32_t search_type, bool print_type) {
	
	std::vector<std::vector<uint32_t>> current_population = random_init_genetic(population_size);
	std::vector<std::vector<uint32_t>> next_population;
	std::vector<std::vector<uint32_t>> parents;

	uint32_t breeding = (uint32_t)(population_size * (1 - elitism_rate));
	breeding -= breeding % 2;
	uint32_t elitism = (uint32_t)population_size - breeding;
	mutation_probability = 1 - mutation_probability;

	double global_fitnes;

	//Repeat the cycle given amount of times.
	while (iteration_count--) {
		
		next_population.reserve(population_size);

		//If roulette selection is taking place, get sum of all inverted fitneses.
		if (search_type == ROULETTE) {
			global_fitnes = get_global_fitnes(current_population);
		}

		//Select parents and breed them, according to chosen type (roulette, tournament) - push mutated children into the new population.
		for (uint32_t k = 0; k < breeding / 2; k++) {
			parents = search_type == ROULETTE ? get_parents_roulette(current_population, global_fitnes) : get_parents_tournament(current_population, tournament_size);
			std::vector<std::vector<uint32_t>> children = breed(parents.at(0), parents.at(1));
			next_population.push_back(mutate(children.at(0), mutation_probability));
			next_population.push_back(mutate(children.at(1), mutation_probability));
			parents.clear();
		}

		//Sort the population, add best n % to the new population.
		std::sort(current_population.begin(), current_population.end(), is_fitter);
		if (print_type)
			std::cout << get_fitnes(current_population.at(0)) << '\n';

		for (uint32_t i = 0; i < elitism; i++) {
			next_population.push_back(current_population.at(i));
		}

		//Random shuffle the population (no idea if it does something - just so the best individials aren't all on the end of the popoulation).
		std::random_shuffle(next_population.begin(), next_population.end());

		current_population = next_population;
		next_population.clear();
	}

	std::sort(current_population.begin(), current_population.end(), is_fitter);
	return current_population.at(0);
}





//--------------------------------------------
// SEARCH UTILITIES
//--------------------------------------------


std::vector<uint32_t> random_init_annealing() {
	std::vector<uint32_t> state;
	state.reserve(TOWN_COUNT);
	for (uint32_t i = 0; i < TOWN_COUNT; i++) {
		state.push_back(i);
		std::random_shuffle(state.begin(), state.end());
	}
	return state;
}


std::vector<std::vector<uint32_t>> random_init_genetic(uint32_t population_size) {
	std::vector<std::vector<uint32_t>> population;
	population.reserve(population_size);
	std::vector<uint32_t> temp;
	for (uint32_t i = 0; i < population_size; i++) {
		temp.reserve(TOWN_COUNT);
		for (uint32_t j = 0; j < TOWN_COUNT; j++)
			temp.push_back(j);
		std::random_shuffle(temp.begin(), temp.end());
		population.push_back(temp);
		temp.clear();
	}
	return population;
}


double get_fitnes(const std::vector<uint32_t>& state) {
	double length = 0;
	for (uint32_t i = 0; i < state.size(); i++) {
		length += GRAPH[state.at(i)][state.at((i + 1) % state.size())];
	}
	return length;
}


std::vector<uint32_t> get_next_state(const std::vector<uint32_t>& state) {
	std::uniform_int_distribution<uint32_t> random_town(0, TOWN_COUNT - 2);
	std::vector<uint32_t> next_state(state);
	uint32_t pos1 = random_town(RD);
	uint32_t pos2 = random_town(RD);

	//Switch two random positions.
	next_state.at(pos1) = state.at(pos2);
	next_state.at(pos2) = state.at(pos1);
	return next_state;
}


bool is_fitter(const std::vector<uint32_t>& order1, const std::vector<uint32_t>& order2) {
	return get_fitnes(order1) < get_fitnes(order2);
}


double get_global_fitnes(const std::vector<std::vector<uint32_t>>& population) {
	double global_fitnes = 0;
	for (uint32_t i = 0; i < population.size(); i++) {
		global_fitnes += (1 / get_fitnes(population.at(i)));
	}
	return global_fitnes;
}


std::vector<std::vector<uint32_t>> get_parents_tournament(const std::vector<std::vector<uint32_t>>& population, uint32_t tournament_size) {
	std::vector<std::vector<uint32_t>> tournament;
	std::vector<std::vector<uint32_t>> parents;
	std::uniform_int_distribution<uint32_t> random_town(0, TOWN_COUNT - 1);
	tournament.reserve(tournament_size);

	//Repeat for two parents.
	for (uint32_t j = 0; j < 2; j++) {

		//Add random chromosomes into the tournament.
		for (uint32_t j = 0; j < tournament_size; j++) {
			tournament.push_back(population.at(random_town(RD)));
		}

		//Sort the tournament and return the best individuals.
		std::sort(tournament.begin(), tournament.end(), is_fitter);
		parents.push_back(tournament.at(0));
		tournament.clear();
	}

	return parents;
}


std::vector<std::vector<uint32_t>> get_parents_roulette(const std::vector<std::vector<uint32_t>>& population, double global_fitnes) {
	double roulette_end;
	double cumulative_fitnes;
	std::vector<std::vector<uint32_t>> parents;
	std::uniform_real_distribution<double> fitnes_distribution(0, global_fitnes);

	//Repeat for two parents.
	for (uint32_t i = 0; i < 2; i++) {
		cumulative_fitnes = 0;

		//Select random treshold.
		roulette_end = fitnes_distribution(RD);

		//Count the cumulative fitnes until treshhold has been reached, add parent to output.
		for (uint32_t j = 0; j < population.size(); j++) {
			cumulative_fitnes += (1 / get_fitnes(population.at(j)));
			if (cumulative_fitnes >= roulette_end) {
				parents.push_back(population.at(j));
				break;
			}
		}
	}
	return parents;
}


std::vector<std::vector<uint32_t>> breed(std::vector<uint32_t> parent1, std::vector<uint32_t> parent2) {
	std::vector<uint32_t> child1, child2, temp1, temp2;
	child1.reserve(TOWN_COUNT);
	child2.reserve(TOWN_COUNT);
	temp1.reserve(TOWN_COUNT);
	temp2.reserve(TOWN_COUNT);
	std::uniform_int_distribution<uint32_t> random_town(0, TOWN_COUNT - 1);

	uint32_t pos1 = random_town(RD);
	uint32_t pos2 = random_town(RD);

	//Swap indexes if pos1>pos2
	if (pos1 > pos2) {
		uint32_t temp = pos1;
		pos1 = pos2;
		pos2 = temp;
	}

	//Add intervals from both parents into temporary variables.
	for (uint32_t i = pos1; i <= pos2; i++) {
		temp1.push_back(parent1.at(i));
		temp2.push_back(parent2.at(i));
	}

	//Start from the pos2 + 1 index.
	//Add unique towns from parent2 to temp1 and vise versa.
	//After chromosome end, start from the beginning, now add to child1 and child2 variables.
	uint32_t index = pos2 + 1;
	while (temp1.size() + child1.size() != parent1.size() || temp2.size() + child2.size() != parent1.size()) {
		if (index == parent1.size())
			index = 0;
		uint32_t i = 0, j = 0;
		for (i; i < temp1.size(); i++) {
			if (temp1.at(i) == parent2.at(index))
				break;
		}
		for (j; j < temp2.size(); j++) {
			if (temp2.at(j) == parent1.at(index))
				break;
		}
		if (i == temp1.size()) {
			if (index > pos2)
				temp1.push_back(parent2.at(index));
			else
				child1.push_back(parent2.at(index));
		}
		if (j == temp2.size()) {
			if (index > pos2)
				temp2.push_back(parent1.at(index));
			else
				child2.push_back(parent1.at(index));
		}
		index++;
	}

	//Join child1 with temp1 and child2 with temp2.
	std::vector<std::vector<uint32_t>> result;
	child1.insert(child1.end(), temp1.begin(), temp1.end());
	child2.insert(child2.end(), temp2.begin(), temp2.end());
	result.push_back(child1);
	result.push_back(child2);

	return result;
}


std::vector<uint32_t> mutate(std::vector<uint32_t> path, double mutation_probability) {
	std::uniform_real_distribution<double> random_chance(0, 1);
	std::uniform_int_distribution<uint32_t> random_town(0, TOWN_COUNT - 1);
	uint32_t pos1, pos2;
	double chance = random_chance(RD);

	//Mutate three times, each time with less probability.
	for (uint32_t i = 0; i < 3; i++) {
		if (chance > mutation_probability) {
			pos1 = random_town(RD);
			pos2 = random_town(RD);
			uint32_t temp = path.at(pos1);
			path.at(pos1) = path.at(pos2);
			path.at(pos2) = temp;
		}
		chance = chance + ((1 - chance) / 4 * 3);
	}
	return path;
}





//--------------------------------------------
// OTHER UTILITIES
//--------------------------------------------

double** init_graph(const char* file_name, uint32_t& town_count, std::vector<std::pair<double, double>>& town_coords) {
	
	double x, y, x_diff, y_diff;
	std::ifstream file;

	file.open(file_name);
	if (!file.is_open()) {
		std::cout << "File couldn't be open.\n";
		return nullptr;
	}

	file >> town_count;
	double** graph = new double* [town_count];

	//Load all points.
	for (uint32_t i = 0; i < town_count; i++) {
		graph[i] = new double[town_count];
		file >> x >> y;
		town_coords.push_back(std::make_pair(x, y));
	}

	file.close();

	//Calculate the distances and insert them into the graph.
	for (uint32_t i = 0; i < town_count; i++) {
		for (uint32_t j = 0; j < town_count; j++) {
			x_diff = town_coords.at(i).first - town_coords.at(j).first;
			y_diff = town_coords.at(i).second - town_coords.at(j).second;
			graph[i][j] = std::sqrt(std::pow(x_diff, 2) + std::pow(y_diff, 2));
		}
	}

	return graph;
}


void print_solution(const std::vector<uint32_t> &solution, uint32_t search_type) {
	switch (search_type) {
	case 1:
		std::cout << "SIMULATED ANNEALING:\n\n";
		break;
	case 2:
		std::cout << "GENETIC ALGORITHM - ROULETTE SELECTION:\n\n";
		break;
	case 3:
		std::cout << "GENETIC ALGORITHM - TOURNAMENT SELECTION:\n\n";
		break;
	}
	double length = 0;
	for (uint32_t i = 0; i < solution.size(); i++) {
		length += GRAPH[solution.at(i)][solution.at((i + 1) % solution.size())];
	}
	for (uint32_t i = 0; i < solution.size(); i++)
		std::cout << solution.at(i) + 1 << ": " << TOWN_COORDS.at(solution.at(i)).first << " " << TOWN_COORDS.at(solution.at(i)).second << "\n";
	std::cout << '\n' << "Length: " << length << "\n\n";
}


std::vector<uint32_t> greedy_srch() {
	std::vector<uint32_t> current_state;
	std::vector<uint32_t> numbers;
	double minimum = 0;
	uint32_t index = 0;

	for (uint32_t i = 1; i < TOWN_COUNT; i++) {
		numbers.push_back(i);
	}
	current_state.push_back(0);

	while (!numbers.empty()) {
		minimum = GRAPH[current_state.back()][numbers.at(0)];
		index = 0;
		for (uint32_t i = 1; i < numbers.size(); i++) {
			if (GRAPH[current_state.back()][numbers.at(i)] < minimum) {
				index = i;
				minimum = GRAPH[current_state.back()][numbers.at(i)];
			}
		}
		current_state.push_back(numbers.at(index));
		numbers.erase(numbers.begin() + index);
	}
	return current_state;
}