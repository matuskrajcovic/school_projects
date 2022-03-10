#pragma once

#include <stdlib.h>
#include <iostream>

class point {
private:
	//Coordinates
	double m_x;
	double m_y;

public:
	//Default constructor
	point();

	//Create new point with given coordinates.
	point(double x, double y);

	//Cretae new random points from given range.
	point(int32_t min, int32_t max);

	//Create new offsetted point.
	point(const point& point, int32_t offset);

	//Get coordinates.
	double get_x();
	double get_y();

	//Set coordinates.
	void set(double x, double y);

	//Checks if two points are equal (operator overload).
	bool operator==(const point& point);
};