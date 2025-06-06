#include <iostream>

int main() {
    int number;

    do {
        std::cout << "Enter a number (0 to exit): ";
        std::cin >> number;

        if(number != 0) {
            std::cout << "You entered: " << number << std::endl;
        }
    } while(number != 0);

    std::cout << "Exiting program." << std::endl;

    return 0;
}
