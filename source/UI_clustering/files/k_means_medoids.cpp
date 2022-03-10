#include "../include/k_means_medoids.hpp"

k_means_medoids::k_means_medoids(plane& plane, uint32_t clusters) : partitional(plane, clusters) { launch(); }

void k_means_medoids::launch() {

	double min_distance_sum, distance_sum;
	double best_variation = -1, average, variation;
	uint32_t index;
	int count;

	//How many times we launch the algorithm.
	int tries = 10;

	//Random centroids
	init_random();

	while (tries--) {
		count = 100;

		while (count--) {

			m_previous_centers = m_centers;

			assign_groups();

			min_distance_sum = -1;
			distance_sum = 0;
			index = 0;

			//Assign new medoids (find the one with lowest distance).
			for (uint32_t group = 0; group < m_groups.size(); group++) {
				min_distance_sum = -1;
				
				for (uint32_t i = 0; i < m_groups[group].size(); i++) {

					distance_sum = 0;

					for (auto it = m_groups[group].begin(); it != m_groups[group].end(); it++) {
						distance_sum += distance(m_plane.get(*it), m_centers[group]);
					}

					if (distance_sum < min_distance_sum || min_distance_sum == -1) {
						min_distance_sum = distance_sum;
						index = i;
						
					}
				}
				m_centers[group] = m_plane.get(m_groups[group][index]);
			}

			if (converged()) {
				break;
			}

		}

		average = m_plane.get_size() / m_cluster_count;
		variation = 0;
		for (auto it = m_groups.begin(); it != m_groups.end(); it++) {
			variation += std::abs(average - it->size());
		}

		if (variation < best_variation || best_variation == -1) {
			m_best_centers = m_centers;
			m_best_groups = m_groups;
			best_variation = variation;
		}
	}
}