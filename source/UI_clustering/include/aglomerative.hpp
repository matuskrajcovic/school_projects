#pragma once

#include <iostream>
#include <chrono>

#include "../include/plane.hpp"
#include "../include/clustering.hpp"


class aglomerative : public clustering {
	//Clusters with centroids and vector of points.
	std::vector<std::pair<point, std::vector<uint32_t>>> m_clusters;

public:
	aglomerative(plane& plane, uint32_t clusters);
	void print(const std::string& file_name);
	void test();

private:
	void launch();
};