#pragma once

#include <string>
#include <iostream>

#include "../include/point.hpp"
#include "../include/plane.hpp"


class clustering {
protected:
	//Each clustering class has its own plane and target cluster count.
	plane m_plane;
	uint32_t m_cluster_count;

public:
	//Creates new clustering class with given plane and target cluster count. 
	clustering(plane& plane, uint32_t clusters);

	//Each class has to implement launch method for the algorithm.
	//Also print method for printing into external file.
	//And test method to check success of our algorithm.
	virtual void launch() = 0;
	virtual void print(const std::string& file_name) = 0;
	virtual void test() = 0;

protected:
	//Computes distance betwwen two points.
	double distance(point point1, point point2);
};