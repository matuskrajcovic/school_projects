#pragma once

#include <iostream>
#include <vector>
#include <random>
#include <fstream>
#include <string>

#include "../include/point.hpp"

extern std::mt19937 R;

class plane {
private:
	//Points on the plane.
	std::vector<point> m_data;

public:
	//Initialize the plane with already calculated points.
	plane(std::vector<point> points);

	//Initialize with new points from selected range.
	plane(uint32_t initial, uint32_t offsetted, int32_t min, int32_t max, int32_t offset);

	//Get point from given index.
	point get(uint32_t index);

	//Get size of the plane.
	uint32_t get_size();
	
	//Print points into external file.
	void print(std::string file_name);


private:
	//Push unique points into the plane.
	void push(const point& point);

	//Checks if the point is unique.
	bool contains(const point& point);
};