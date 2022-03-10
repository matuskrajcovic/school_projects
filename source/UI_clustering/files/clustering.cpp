#include "../include/clustering.hpp"


clustering::clustering(plane& plane, uint32_t clusters) : m_plane(plane), m_cluster_count(clusters) { }

double clustering::distance(point point1, point point2) {
	double x = point1.get_x() - point2.get_x();
	double y = point1.get_y() - point2.get_y();
	return std::sqrt((x * x + y * y));
}