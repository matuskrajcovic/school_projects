#include"../include/divisive.hpp"

divisive::divisive(plane& plane, uint32_t clusters) : clustering(plane, clusters) { launch(); }

void divisive::launch() {
	m_planes.push_back(std::make_pair(point(), m_plane));

	double max_variation = 0, x_var = 0, y_var = 0;
	uint32_t index = 0;
	for (uint32_t i = 0; i < m_cluster_count - 1; i++) {

		//Get two planes from k_means algorithm.
		k_means_centroids centroids = k_means_centroids(m_planes[index].second, 2);
		std::vector<plane> x = centroids.get_planes();
		std::vector<point> y = centroids.get_centroids();
		m_planes.push_back(std::make_pair(y[0], x[0]));
		m_planes.push_back(std::make_pair(y[1], x[1]));

		//Erase the old, previous plane.
		m_planes.erase(m_planes.begin() + index, m_planes.begin() + index + 1);

		//Find, which cluster are we going to pick next.
		max_variation = 0;
		for (uint32_t j = 0; j < m_planes.size(); j++) {
			x_var = 0, y_var = 0;
			for (uint32_t k = 0; k < m_planes[j].second.get_size(); k++) {
				x_var += std::abs(m_planes[j].second.get(k).get_x()- m_planes[j].first.get_x());
				y_var += std::abs(m_planes[j].second.get(k).get_y() - m_planes[j].first.get_y());
			}
			if ((x_var + y_var) > max_variation) {
				max_variation = (x_var + y_var);
				index = j;
			}
		}
		
	}
}

void divisive::print(const std::string& file_name) {
	std::ofstream file;
	file.open(file_name);
	if (!file.is_open())
		return;
	file << m_cluster_count << '\n';
	for (auto it = m_planes.begin(); it != m_planes.end(); it++) {
		file << it->second.get_size() << '\n';
		file << it->first.get_x() << ' ' << it->first.get_y() << '\n';
		for (uint32_t i = 0; i != it->second.get_size(); i++) {
			file << it->second.get(i).get_x() << ' ' << it->second.get(i).get_y() << '\n';
		}
	}
	file.close();
}

void divisive::test() {
	double dist_sum;
	for (uint32_t i = 0; i < m_planes.size(); i++) {
		dist_sum = 0;
		for (uint32_t j = 0; j < m_planes[i].second.get_size(); j++) {
			dist_sum += distance(m_planes[i].first, m_planes[i].second.get(j));
		}
		std::cout << dist_sum / m_planes[i].second.get_size() << ", ";
	}
	std::cout << '\n';
}
