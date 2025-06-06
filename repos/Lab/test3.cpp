#include <iostream>
using namespace std;

int main() {

    float feet, inches;

    cout << "give the length in feet: ";
    cin >> feet;

    cout << "give the length in inches: ";
    cin >> inches;

    // tukar feet jadi centimeter. 1 feet = 12 inch. 12 inch x 2.54 dapat 30.48
    double centimeters = (feet * 30.48) + (inches * 2.54);

    cout << "The length in centimeters is: " << centimeters << " cm";

    return 0;
}