#include "../include/point.hpp"


point::point() : m_x(0), m_y(0) {}

point::point(double x, double y) : m_x(x), m_y(y) {}

point::point(int32_t min, int32_t max) {
	//std::uniform_int_distribution<int32_t> random_point(min, max);
	//m_x = (double)random_point(R);
	//m_y = (double)random_point(R);
	m_x = rand() % (max - min) + min;
	m_y = rand() % (max - min) + min;
	
}

point::point(const point& point, int32_t offset) {
	//std::uniform_int_distribution<int32_t> random_offset(-offset, offset);
	//m_x = point.m_x + (double)random_offset(R);
	//m_y = point.m_y + (double)random_offset(R);
	m_x = point.m_x + ((rand() % (offset * 2)) - offset);
	m_y = point.m_y + ((rand() % (offset * 2)) - offset);
}

double point::get_x() {
	return m_x;
}

double point::get_y() {
	return m_y;
}

void point::set(double x, double y) {
	m_x = x;
	m_y = y;
}

bool point::operator==(const point& point) {
	return m_x == point.m_x && m_y == point.m_y;
}