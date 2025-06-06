#include <iostream>
using namespace std;

int main() {
    float weight, height, bmi;

    cout << "Enter weight: ";
    cin >> weight;
    cout << "Enter height: ";
    cin >> height;

    bmi = weight / (height * height);

    if (bmi < 18.5) {
        cout << "You are underweight";
    } else if (bmi >= 18.5 && bmi < 25) {
        cout << "Your weight is desirable";
    } else if (bmi >= 25 && bmi < 30) {
        cout << "You are overweight";
    } else if (bmi >= 30) {
        cout << "You are obese";
    } else {
        cout << "Display invalid data";
    }

    return 0;
}
