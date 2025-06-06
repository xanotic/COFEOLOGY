#include <iostream>
using namespace std;

int main() {
    string gender;
    int age;
    float policyRate;

    cout << "Insert your gender (male or female): ";
    cin >> gender;
    cout << "Insert your age: ";
    cin >> age;

    if (gender == "male") {
        if (age < 21) {
            policyRate = 0.05;
        } else {
            policyRate = 0.035;
        }
    } else if (gender == "female") {
        if (age < 21) {
            policyRate = 0.04;
        } else {
            policyRate = 0.025;
        }
    } 

    cout << "Your policy rate is " << policyRate << endl;
    return 0;
}
