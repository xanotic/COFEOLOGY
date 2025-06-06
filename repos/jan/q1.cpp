#include <iostream>
#include <string>
using namespace std;

int main() {
    double input;
    string warningmessages, input2;

    cout << "Insert river basins: ";
    cin >> input2;
    cout << "Insert water level: ";
    cin >> input;

    if (input2 == "SB") {
        if (input >= 7.30 && input <= 8.90) {
            warningmessages = "Alert";
        } else if (input >= 8.90) {
            warningmessages = "Danger";
        }
    }

    if (input2 == "SS") {
        if (input >= 7.60 && input <= 8.8) {
            warningmessages = "Alert";
        } else if (input >= 8.80) {
            warningmessages = "Danger";
        }
    }

    cout << "Warning: " << warningmessages << endl;

    return 0;
}
