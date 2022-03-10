#include "../include/aglomerative.hpp"


aglomerative::aglomerative(plane& plane, uint32_t clusters) : clustering(plane, clusters) { launch(); }

void aglomerative::launch() {

	//Get all clusters, each one with one element.
	for (uint32_t i = 0; i < m_plane.get_size(); i++) {
		m_clusters.push_back(std::make_pair(point(m_plane.get(i).get_x(), m_plane.get(i).get_y()), std::vector<uint32_t>()));
		m_clusters[i].second.push_back(i);
	}
	
	//Reserve matrix space.
	std::vector<std::vector<float>> m_matrix;
	m_matrix.reserve(m_plane.get_size());
	
	//Initialize the matrix with distances between clusters (centroids).
	for (uint32_t i = 0; i < m_plane.get_size(); i++) {
		m_matrix.push_back(std::vector<float>());
		m_matrix[i].reserve(m_plane.get_size());
		for (uint32_t j = 0; j < m_plane.get_size(); j++) {
			if (i < j)
				m_matrix[i].push_back(distance(m_plane.get(i), m_plane.get(j)));
			else
				m_matrix[i].push_back(0);
		}
		
	}

	//While we achieve final cluster count.
	while (m_clusters.size() != m_cluster_count) {
		double min_distance = -1;
		uint32_t index_x = 0;
		uint32_t index_y = 0;

		//Find two closest clusters.
		for (uint32_t i = 0; i < m_clusters.size() - 1; i++) {
			for (uint32_t j = i + 1; j < m_clusters.size(); j++) {
				if ((m_matrix[i][j] < min_distance && m_matrix[i][j] > 0) || min_distance == -1) {
					index_x = i;
					index_y = j;
					min_distance = m_matrix[i][j];
				}
			}
		}

		//Join the clusters, erase the other one.
		m_clusters[index_x].second.insert(m_clusters[index_x].second.end(), m_clusters[index_y].second.begin(), m_clusters[index_y].second.end());
		m_clusters[index_y].second.clear();
		m_clusters.erase(m_clusters.begin() + index_y);

		//Get new centroid.
		double x_sum = 0;
		double y_sum = 0;
		for (auto it = m_clusters[index_x].second.begin(); it != m_clusters[index_x].second.end(); it++) {
			x_sum += m_plane.get(*it).get_x();
			y_sum += m_plane.get(*it).get_y();
		}
		m_clusters[index_x].first.set(x_sum / m_clusters[index_x].second.size(), y_sum / m_clusters[index_x].second.size());

		//Erase row and column from matrix.
		m_matrix.erase(m_matrix.begin() + index_y);

		for (uint32_t i = 0; i < m_matrix.size(); i++) {
			m_matrix[i].erase(m_matrix[i].begin() + index_y);
		}

		//Compute new distances between new cluster and other ones.
		for (uint32_t i = 0; i < index_x; i++) {
			m_matrix[i][index_x] = distance(m_clusters[i].first, m_clusters[index_x].first);
		}

		for (uint32_t i = index_x + 1; i < m_matrix.size(); i++) {
			m_matrix[index_x][i] = distance(m_clusters[i].first, m_clusters[index_x].first);
		}
	}
}

void aglomerative::print(const std::string& file_name) {
	std::ofstream file;
	file.open(file_name);
	if (!file.is_open())
		return;
	file << m_cluster_count << '\n';
	for (auto it = m_clusters.begin(); it != m_clusters.end(); it++) {
		file << it->second.size() << '\n';
		file << it->first.get_x() << ' ' << it->first.get_y() << '\n';
		for (auto jt = it->second.begin(); jt != it->second.end(); jt++) {
			file << m_plane.get(*jt).get_x() << ' ' << m_plane.get(*jt).get_y() << '\n';
		}
	}
	file.close();
}

void aglomerative::test() {
	double dist_sum;
	for (uint32_t i = 0; i < m_clusters.size(); i++) {
		dist_sum = 0;
		for (uint32_t j = 0; j < m_clusters[i].second.size(); j++) {
			dist_sum += distance(m_clusters[i].first, m_plane.get(m_clusters[i].second[j]));
		}
		std::cout << dist_sum / m_clusters[i].second.size() << ", ";
	}
	std::cout << '\n';
}
