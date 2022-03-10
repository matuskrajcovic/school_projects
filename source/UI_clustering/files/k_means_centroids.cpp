#include "../include/k_means_centroids.hpp"


k_means_centroids::k_means_centroids(plane& plane, uint32_t clusters) : partitional(plane, clusters) { launch(); }

void k_means_centroids::launch() {

	//How many times we launch the algorithm.
	int tries = 1;

	double best_variation = -1;
	double x_sum, y_sum;
	double average, variation;
	int count;

	while (tries--) {

		m_centers.clear();
		m_groups.clear();
		count = 100;

		//Random centroids
		init_random();

		while (count--) {

			m_previous_centers = m_centers;

			//Assign all points into clusters.
			assign_groups();

			//Set new centroids.
			for (uint32_t i = 0; i < m_groups.size(); i++) {
				x_sum = 0;
				y_sum = 0;
				for (uint32_t j = 0; j < m_groups[i].size(); j++) {
					x_sum += m_plane.get(m_groups[i][j]).get_x();
					y_sum += m_plane.get(m_groups[i][j]).get_y();
				}
				m_centers[i].set(x_sum / m_groups[i].size(), y_sum / m_groups[i].size());
			}

			//If previous and current centers are the same, end.
			if (converged())
				break;

		}

		//Each time, we check the variation, and since we can launch the algorithm more
		//times, the solution with the lowest variation is saved.
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

std::vector<point> k_means_centroids::get_centroids() {
	return m_best_centers;
}

std::vector<plane> k_means_centroids::get_planes() {
	std::vector<plane> result;
	for (uint32_t i = 0; i < m_best_groups.size(); i++) {
		std::vector<point> output;
		for (uint32_t j = 0; j < m_best_groups[i].size(); j++) {
			output.push_back(point(m_plane.get(m_best_groups[i][j]).get_x(), m_plane.get(m_best_groups[i][j]).get_y()));
		}
		result.push_back(plane(output));
	}
	return result;
}

