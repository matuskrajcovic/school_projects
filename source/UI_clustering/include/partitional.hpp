#pragma once

#include <vector>
#include <string>
#include <fstream>
#include <iostream>

#include "../include/point.hpp"
#include "../include/plane.hpp"
#include "../include/clustering.hpp"

extern std::mt19937 R;


class partitional : public clustering {
protected:
	//Best centers (centroids, medoids) and best clusters around them.
	std::vector<point> m_best_centers;
	std::vector<std::vector<uint32_t>> m_best_groups;

	//Current configuration.
	std::vector<point> m_centers;
	std::vector<point> m_previous_centers;
	std::vector<std::vector<uint32_t>> m_groups;

public:
	partitional(plane& plane, uint32_t clusters);
	virtual void launch() = 0;

	void print(const std::string& file_name);

	void test();

protected:
	//Assigns all points to corresponding clusters.
	void assign_groups();

	//Random initialization of centers.
	void init_random();

	//Checks, if previous centers are the same as current.
	bool converged();

private:

	//Checks if centers already contain the point in a plane.
	bool contains(uint32_t index);
};