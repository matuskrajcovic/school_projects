#pragma once

#include <vector>
#include <string>
#include <iostream>

#include "../include/k_means_centroids.hpp"
#include "../include/clustering.hpp"
#include "../include/plane.hpp"
#include "../include/point.hpp"


class divisive : public clustering {
	//Planes to which our main plane is being divided.
	std::vector<std::pair<point, plane>> m_planes;
	
public:
	divisive(plane& plane, uint32_t clusters);
	void print(const std::string& file_name);
	void test();

private:
	void launch();
};