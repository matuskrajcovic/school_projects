#include <iostream>
#include <string>
#include <stdlib.h>
#include <time.h>

#include "../include/point.hpp"
#include "../include/plane.hpp"
#include "../include/clustering.hpp"
#include "../include/partitional.hpp"
#include "../include/k_means_centroids.hpp"
#include "../include/k_means_medoids.hpp"
#include "../include/aglomerative.hpp"
#include "../include/divisive.hpp"


#define OUTPUT_FILE "output.txt"
#define K_MEANS_CENTROIDS 1
#define K_MEANS_MEDOIDS 2
#define AGLOMERATIVE 3
#define DIVISIVE 4


//Random device global variables.
std::random_device rd;
std::mt19937 R{ rd() };


int main() {
	srand(time(NULL));
	uint32_t initial, offsetted;
	int32_t min, max, offset;
	std::string input;

	while (true) {

		initial = 20, offsetted = 20000;
		min = -5000, max = 5000, offset = 100;

		std::cout << "default     -no parameters-\n";
		std::cout << "            initializes the points with default values(20, 20000, -5000, 5000, 100)\n\n";
		std::cout << "set         [initial points] [offsetted points] [min. coord] [max. coord] [offset]\n";
		std::cout << "            sets custom values\n\n";
		std::cout << "exit        exit the program\n\n";

		//Loading the plane.
		while (true) {
			std::cout << "> ";
			std::cin >> input;
			if (!input.compare("default")) {
				break;
			}
			else if (!input.compare("set")) {
				std::cin >> initial >> offsetted >> min >> max >> offset;
				break;
			}
			else if (!input.compare("exit"))
				return 0;
		}

		std::cout << "Loading points...";
		plane p_data(initial, offsetted, min, max, offset);
		std::cout << " loaded!\n\n";

		p_data.print(OUTPUT_FILE);

		std::cout << "0                          exit\n";
		std::cout << "1 [number of clusters]     k - means with centroids\n";
		std::cout << "2 [number of clusters]     k - means with medoids\n";
		std::cout << "3 [number of clusters]     aglomerative\n";
		std::cout << "4 [number of clusters]     divisive\n\n";

		uint16_t choice = -1;
		uint32_t clusters = 0;

		//Launching algorithms.
		while (choice != 0) {
			std::cout << ">> ";
			std::cin >> choice;

			if (choice == 0)
				break;

			std::cin >> clusters;

			clustering* algorithm = nullptr;

			if (choice == K_MEANS_CENTROIDS)
				algorithm = new k_means_centroids(p_data, clusters);
			else if (choice == K_MEANS_MEDOIDS)
				algorithm = new k_means_medoids(p_data, clusters);
			else if (choice == AGLOMERATIVE)
				algorithm = new aglomerative(p_data, clusters);
			else if (choice == DIVISIVE)
				algorithm = new divisive(p_data, clusters);
			else
				continue;

			algorithm->print(OUTPUT_FILE);
			algorithm->test();
			system("python visualizer.py");
			delete algorithm;
		}
	}

	return 0;
}