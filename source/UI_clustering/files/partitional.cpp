#include "../include/partitional.hpp"


partitional::partitional(plane& plane, uint32_t clusters) : clustering(plane, clusters) { }


void partitional::print(const std::string& file_name) {
	std::ofstream file;
	file.open(file_name);
	if (!file.is_open())
		return;
	file << m_cluster_count << '\n';
	for (auto it = m_best_groups.begin(); it != m_best_groups.end(); it++) {
		file << it->size() << '\n';
		file << m_best_centers[it - m_best_groups.begin()].get_x() << ' ' << m_best_centers[it - m_best_groups.begin()].get_y() << '\n';
		for (auto jt = it->begin(); jt != it->end(); jt++) {
			file << (int32_t)m_plane.get(*jt).get_x() << ' ' << (int32_t)m_plane.get(*jt).get_y() << '\n';
		}
	}
	file.close();
}

void partitional::assign_groups() {
	for (auto it = m_groups.begin(); it != m_groups.end(); it++)
		it->clear();

	double current_distance;
	double min_distance;
	uint32_t index;

	//Iterate over all points and assign each point to the closest cluster.
	for (uint32_t i = 0; i < m_plane.get_size(); i++) {
		index = 0;
		min_distance = -1;
		for (uint32_t j = 0; j < m_cluster_count; j++) {
			current_distance = distance(m_plane.get(i), m_centers[j]);
			if (current_distance < min_distance || min_distance == -1) {
				min_distance = current_distance;
				index = j;
			}
		}
		m_groups[index].push_back(i);
	}
}

void partitional::init_random() {
	std::uniform_int_distribution<int32_t> random_point(0, m_plane.get_size() - 1);

	//Initialize random center points.
	for (uint32_t i = 0; i < m_cluster_count; i++) {
		uint32_t index = random_point(R);
		while (contains(index))
			index = random_point(R);
		m_centers.push_back(m_plane.get(index));
		m_groups.push_back(std::vector<uint32_t>());
	}
}

bool partitional::contains(uint32_t index) {
	for (auto it = m_centers.begin(); it != m_centers.end(); it++) {
		if (*it == m_plane.get(index))
			return true;
	}
	return false;
}

bool partitional::converged() {
	for (auto it = m_centers.begin(); it != m_centers.end(); it++) {
		if (!(*it == m_previous_centers[it - m_centers.begin()]))
			return false;
	}
	return true;
}

void partitional::test() {
	double dist_sum;

	//Print all average distances to center points in each cluster.
	for (uint32_t i = 0; i < m_best_centers.size(); i++) {
		dist_sum = 0;
		for (uint32_t j = 0; j < m_best_groups[i].size(); j++) {
			dist_sum += distance(m_best_centers[i], m_plane.get(m_best_groups[i][j]));
		}
		std::cout << dist_sum / m_best_groups[i].size() << ", ";
	}
	std::cout << '\n';
}
