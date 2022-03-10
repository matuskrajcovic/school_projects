#pragma once

#include <vector>
#include <iostream>

#include "../include/point.hpp"
#include "../include/plane.hpp"
#include "../include/partitional.hpp"


class k_means_centroids : public partitional {

public:
	k_means_centroids(plane& plane, uint32_t clusters);

	//Functions used in divisive clustering algorithm.
	std::vector<plane> get_planes();
	std::vector<point> get_centroids();

private:
	void launch();
};