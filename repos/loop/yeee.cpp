#include <iostream>

int main() {
    // Initialize a counter variable to keep track of salaries greater than RM2000
    int count_salaries_above_2000 = 0;

    // Input loop for ten salaries
    for (int i = 1; i <= 10; ++i) {
        // Input salary
        double salary;
        std::cout << "Enter salary " << i << " (in RM): ";
        std::cin >> salary;

        // Check if the salary is greater than RM2000
        if (salary > 2000) {
            count_salaries_above_2000++;
        }
    }

    // Display the count of salaries greater than RM2000
    std::cout << "\nNumber of salaries greater than RM2000: " << count_salaries_above_2000 << std::endl;

    return 0;
}
