#include "../include/plane.hpp"

plane::plane(std::vector<point> points) {
	m_data = points;
}

plane::plane(uint32_t initial, uint32_t offsetted, int32_t min, int32_t max, int32_t offset) {
	m_data.reserve(initial + offsetted);
	for (uint32_t i = 0; i < initial; i++) {
		point x = point(min, max);
		while (contains(x)) {
			x = point(min, max);
		}
		push(x);
	}

	uint32_t index = 0;
	for (uint32_t i = 0; i < offsetted; i++) {
		std::uniform_int_distribution<int32_t> random_point(0, m_data.size() - 1);
		index = random_point(R);
		point to_insert(m_data.at(index), offset);
		while (contains(to_insert))
			to_insert = point(m_data.at(index), offset);
		push(to_insert);
	}
}


point plane::get(uint32_t index) {
	return m_data[index];
}

uint32_t plane::get_size() {
	return m_data.size();
}

void plane::print(std::string file_name) {
	std::ofstream file;
	file.open(file_name);
	if (!file.is_open())
		return;
	for (auto it = m_data.begin(); it != m_data.end(); it++) {
		file << it->get_x() << ' ' << it->get_y() << '\n';
	}
	file.close();
}

void plane::push(const point& point) {
	if (!contains(point))
		m_data.push_back(point);
}
bool plane::contains(const point& point) {
	for (auto it = m_data.begin(); it != m_data.end(); it++) {
		if (*it == point)
			return true;
	}
	return false;
}