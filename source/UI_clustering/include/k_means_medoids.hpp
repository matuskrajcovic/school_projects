#pragma once

#include <iostream>

#include "../include/partitional.hpp"


class k_means_medoids : public partitional {
public:
	k_means_medoids(plane& plane, uint32_t clusters);

private:
	void launch();
};