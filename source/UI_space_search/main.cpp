#include <iostream>
#include <unordered_map>
#include <string>
#include <vector>
#include <stack>
#include <queue>
#include <fstream>
#include <array>
#include <chrono>


#define INPUT_FILE "input.txt"
#define OUTPUT_FILE "output.txt"
#define HORIZONTAL '0'
#define VERTICAL '1'
#define DFS 0
#define BFS 1
#define COMPACT 0
#define VERBOSE 1




//------------------------------------------
//GLOBAL VARIABLES
//------------------------------------------

//Initial state imported from the file.
std::string INIT_STATE;

//Hash MAP for checking if state already exists and for reconstruction of steps.
std::unordered_map<std::string, std::string> MAP;

//Queue for BFS.
std::queue<std::string> QUEUE;

//Stack for DFS.
std::stack<std::string> STACK;

//Vector of COLORS loaded from the file.
std::vector<std::string> COLORS;

//Number of cars.
int32_t CAR_COUNT = 0;

//Number of rows and columns.
int32_t COLS = 0;
int32_t ROWS = 0;




//------------------------------------------
//FUNCTION DECLARATIONS
//------------------------------------------


//DFS and BFS solution search.
std::string search_solution(int32_t search_type);

//Returns all of the possible moves for the car in the current state.
std::vector<int32_t> get_moves(const std::string& state, int32_t car);

//Creates a new state with a car moved by a distance.
std::string create_state(const std::string& state, int32_t car, int32_t distance);

//Returns string with all squares and cars on them (Like array of the state, but string).
std::string get_str_array(const std::string& state);

//Checks if the state is the solution.
bool is_final_state(const std::string& state);


//Prints array with chosen state.
void print_state(const std::string& state, std::ostream& stream);

//Reconstruct the whole solution, with steps.
void print_solution(const std::string& state, int32_t print_type, int32_t search_type, std::ostream& stream, double time);


//Loads the data from an external file.
bool load_file(int argc, char* argv[]);

//Checks if the loaded data is valid - no cars are overlapping, or out of board.
bool is_valid();

//Converts char to int and vice versa.
int32_t char_to_int(uint8_t c);
uint8_t int_to_char(int32_t i);

//Converts car number {0,n} to characer {A,?}
uint8_t car_to_char(int32_t num);




//------------------------------------------
//MAIN()
//------------------------------------------

int main(int argc, char* argv[]) {
	std::chrono::steady_clock::time_point begin_bfs, end_bfs, begin_dfs, end_dfs;

	if (!load_file(argc, argv) || !is_valid()) {
		std::cout << "Couldn't open the file or the input is not valid.";
		return -1;
	}

	std::ofstream file;
	if (argc > 2)
		file.open(argv[2]);
	else
		file.open(OUTPUT_FILE);

	std::string solution;


	//Check the time to do the search.
	begin_bfs = std::chrono::steady_clock::now();
	solution = search_solution(BFS);
	end_bfs = std::chrono::steady_clock::now();

	//Print the solution.
	if (!solution.compare(""))
		std::cout << "No solution for BFS.\n";
	else {
		uint32_t count = -1;
		double time = std::chrono::duration_cast<std::chrono::nanoseconds>(end_bfs - begin_bfs).count() / 1000000000.0;
		print_solution(solution, VERBOSE, BFS, file, time);
		std::cout << "BFS solution printed into the file.\n";
	}

	//Clear all the data structures.
	MAP.clear();
	while (!QUEUE.empty())
		QUEUE.pop();


	begin_dfs = std::chrono::steady_clock::now();
	solution = search_solution(DFS);
	end_dfs = std::chrono::steady_clock::now();

	if(!solution.compare(""))
		std::cout << "No solution for DFS.\n";
	else {
		uint32_t count = -1;
		double time = std::chrono::duration_cast<std::chrono::nanoseconds>(end_dfs - begin_dfs).count() / 1000000000.0;
		print_solution(solution, COMPACT, DFS, file, time);
		std::cout << "DFS solution printed into the file.\n";
	}
	
	MAP.clear();
	while (!STACK.empty())
		STACK.pop();

	file.close();
	return 0;
}




//------------------------------------------
//SEARCH FUNCTIONS
//------------------------------------------


std::string search_solution(int32_t search_type) {

	//Insert initial state into the hash map.
	MAP.insert(std::make_pair(INIT_STATE, "end"));

	//If the state is final, return it.
	if (is_final_state(INIT_STATE))
		return INIT_STATE;

	if (search_type == DFS)
		STACK.push(INIT_STATE);
	else
		QUEUE.push(INIT_STATE);

	//Iterate until the QUEUE/STACK is empty.
	while (search_type == DFS ? !STACK.empty() : !QUEUE.empty()) {

		//Set the top element as a current state.
		std::string act_state = search_type == DFS ? STACK.top() : QUEUE.front();

		//Pop the top element.
		if (search_type == DFS)
			STACK.pop();
		else
			QUEUE.pop();

		//For each car on the board get all of the possible moves.
		for (int32_t i = 0; i < CAR_COUNT; i++) {
			std::vector<int32_t> moves = get_moves(act_state, i);

			//Create a new state from each of the moves.
			for (uint32_t j = 0; j < moves.size(); j++) {
				std::string new_state = create_state(act_state, i, moves.at(j));

				//If the state is not already in the hash map, add it to a STACK/QUEUE and check if it is final.
				if (MAP.find(new_state) == MAP.end()) {
					MAP.insert(std::make_pair(new_state, act_state));

					if (search_type == DFS)
						STACK.push(new_state);
					else
						QUEUE.push(new_state);

					//Checks, if the state is final.
					if (is_final_state(new_state))
						return new_state;
				}
			}
		}
	}
	return "";
}


std::vector<int32_t> get_moves(const std::string &state, int32_t car) {
	std::vector<int32_t> output;
	int32_t car_pos = car * 4;
	int32_t car_row = char_to_int(state[car_pos]);
	int32_t car_col = char_to_int(state[car_pos + 1]);
	int32_t car_len = char_to_int(state[car_pos + 2]);
	uint8_t dir = state[car_pos + 3];
	std::string arr = get_str_array(state);

	//Check each negative possible move (upwards or to the left). Add to the output if it is valid (no obstacles).
	for (int32_t i = (dir == HORIZONTAL ? car_col : car_row) * (-1); i <= -1; i++) {
		bool possible = true;
		for (int32_t j = i; j < 0; j++)
			if (char_to_int(arr[(car_row + (dir == VERTICAL ? j : 0)) * COLS + car_col + (dir == HORIZONTAL ? j : 0)]) != 0) {
				possible = false;
				break;
			}
		if (possible)
			output.push_back(i);
	}

	//Check each possible move (to the right or downwards).
	for (int32_t i = 1; i <= (dir == HORIZONTAL ? COLS : ROWS) - (dir == HORIZONTAL ? car_col : car_row) - char_to_int(state[car_pos + 2]); i++) {
		bool possible = true;
		for (int32_t j = 0; j < i; j++)
			if (char_to_int(arr[(car_row + (dir == VERTICAL ? (car_len + j) : 0)) * COLS + car_col + (dir == HORIZONTAL ? (car_len + j) : 0)]) != 0) {
				possible = false;
				break;
			}
		if (possible)
			output.push_back(i);
	}
	return output;
}


std::string create_state(const std::string &state, int32_t car, int32_t distance) {
	std::string new_state(state);
	int32_t car_pos = car * 4;
	int32_t offset = new_state[car_pos + 3] == HORIZONTAL ? 1 : 0;

	new_state.replace(car_pos + offset, 1, 1, int_to_char((char_to_int(new_state[car_pos + offset]) + distance)));

	return new_state;
}


std::string get_str_array(const std::string& state) {
	std::string output(ROWS * COLS, '0');
	for (int32_t i = 0; i < CAR_COUNT; i++) {
		for (int32_t j = 0; j < char_to_int(state[i * 4 + 2]); j++) {
			output.replace((char_to_int(state[i * 4]) + (state[i * 4 + 3] == VERTICAL ? j : 0))* COLS + char_to_int(state[i * 4 + 1] + (state[i * 4 + 3] == HORIZONTAL ? j : 0)), 1, 1, car_to_char(i));
		}
	}
	return output;
}


bool is_final_state(const std::string& state) {
	//Check if the car is on the right wall / lower wall if vertical.
	if (state[3] == HORIZONTAL && char_to_int(state[1]) + char_to_int(state[2]) == COLS)
		return true;
	else if (state[3] == VERTICAL && char_to_int(state[0]) + char_to_int(state[2]) == ROWS)
		return true;
	else
		return false;
}




//------------------------------------------
//PRINTING FUNCTIONS
//------------------------------------------


void print_state(const std::string &state, std::ostream& stream) {
	std::string arr = get_str_array(state);

	for (int32_t i = 0; i < ROWS; i++) {
		for (int32_t j = 0; j < COLS; j++) {
			if (arr[i * COLS + j] == '0')
				stream << '.';
			else
				stream << arr[i * COLS + j];
		}
		stream << '\n';
	}
}


void print_solution(const std::string& state, int32_t print_type, int32_t search_type, std::ostream& stream, double time) {
	std::string act_state = state;
	std::vector<std::string> solution;

	//Fill the solution vector.
	while (MAP[act_state].compare("end")) {
		solution.push_back(act_state);
		act_state = MAP[act_state];
	}

	stream << "---------------------------------------------------\n";
	stream << "Search type: " << (search_type == BFS ? "BFS" : "DFS") << '\n';
	stream << MAP.size() << " unique states found (before final state discovery).\n";
	stream << (MAP.size() - (search_type == BFS ? QUEUE.size() : STACK.size())) << " explored states.\n";
	stream << (search_type == BFS ? QUEUE.size() : STACK.size()) << " states still in " << (search_type == BFS ? "QUEUE" : "STACK") << ".\n";
	stream << "Duration: " << time << " seconds.\n";
	stream << "\nSolution: " << solution.size() << " steps." << '\n';
	stream << "\nInitial state.\n";
	
	print_state(INIT_STATE, stream);
	stream << '\n';

	//Pritns all states (operators, boards).
	for (auto it = solution.rbegin(); it != solution.rend(); it++) {
		uint32_t index = 0;

		//Finds the first different character (and therefore finds operator)
		for (index = 0; index < (*it).size(); index++) {
			if ((*it)[index] != (MAP[(*it)]).at(index))
				break;
		}
		uint32_t c1 = char_to_int((MAP[(*it)])[index]);
		uint32_t c2 = char_to_int((*it)[index]);
		int32_t car = index / 4;
		uint8_t dir = (*it)[car * 4 + 3];

		stream << (c1 < c2 ? (dir == HORIZONTAL ? "RIGHT" : "DOWN") : (dir == HORIZONTAL ? "LEFT" : "UP"));
		stream << '(' << COLORS.at(car);
		if (print_type == VERBOSE)
			stream << '(' << car_to_char(car) << ')';
		stream << ", " << (c1 < c2 ? c2 - c1 : c1 - c2) << ")\n";

		if (print_type == VERBOSE) {
			print_state((*it), stream);
			stream << '\n';
		}
	}
	solution.clear();
}




//------------------------------------------
//LOAD AND CONVERT
//------------------------------------------


bool load_file(int argc, char* argv[]) {
	std::ifstream file;
	uint16_t number;
	uint8_t character;
	std::string color;

	if (argc > 1)
		file.open(argv[1]);
	else
		file.open(INPUT_FILE);

	if (!file) {
		return false;
	}

	file >> ROWS >> COLS;
	if (ROWS > 10 || COLS > 10)
		return false;

	file >> CAR_COUNT;
	for (uint8_t i = 0; i < CAR_COUNT; i++) {
		for (uint8_t j = 0; j < 2; j++) {
			file >> number;
			INIT_STATE.push_back(int_to_char(number - 1));
		}
		file >> number;
		INIT_STATE.push_back(int_to_char(number));
		file >> character;
		if (character == 'h')
			INIT_STATE.push_back(HORIZONTAL);
		else
			INIT_STATE.push_back(VERTICAL);
		file >> color;
		COLORS.push_back(color);
	}

	file.close();
	return true;
}


bool is_valid() {
	std::string temp(ROWS * COLS, '0');
	int32_t row = 0, col = 0;

	//Checks each car.
	for (int32_t i = 0; i < CAR_COUNT; i++) {
		if(char_to_int(INIT_STATE[i * 4 + 2]) > 9)
			return false;

		//Checks each part of the car.
		for (int32_t j = 0; j < char_to_int(INIT_STATE[i * 4 + 2]); j++) {
			row = char_to_int(INIT_STATE[i * 4]) + (INIT_STATE[i * 4 + 3] == VERTICAL ? j : 0);
			col = char_to_int(INIT_STATE[i * 4 + 1] + (INIT_STATE[i * 4 + 3] == HORIZONTAL ? j : 0));
			if (INIT_STATE[i * 4 + 3] == VERTICAL && (row > ROWS - 1 || row < 0))
				return false;
			if (INIT_STATE[i * 4 + 3] == HORIZONTAL && (col > COLS - 1 || col < 0))
				return false;
			if (temp[row * COLS + col] == 'X')
				return false;
			temp.replace(row * COLS + col, 1, 1, 'X');
		}
	}
	return true;
}


uint8_t car_to_char(int32_t num) {
	return num + 65;
}


int32_t char_to_int(uint8_t c) {
	return (int32_t)(c) - 48;
}


uint8_t int_to_char(int32_t i) {
	return (uint8_t)(i + 48);
}