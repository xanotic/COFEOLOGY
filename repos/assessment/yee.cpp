#include <iostream>
#include <string>

using namespace std;

int main(){
    double usage;
    string state, type;
    double charge = 0.0, min_charge = 0.0;
    cout << "Enter the state (NEGERI SEMBILAN, MELAKA, TERENGGANU): ";
    cin >> state;
    cout << "Enter the type of supply (DOMESTIC or NON-DOMESTIC): ";
    cin >> type;
    cout << "Enter the water usage in cubic meters: ";
    cin >> usage;

    if (cin.fail()) {
        cout << "Invalid input data. Please enter numeric values." << endl;
        return 1;
    }

    if (type == "DOMESTIC") {
        if (state == "NEGERI SEMBILAN") {
            min_charge = 5.0;
            if (usage <= 20) charge = usage * 0.55;
            else if (usage > 20 && usage <= 35) charge = usage * 0.85;
            else charge = usage * 1.40;
        } 
        else if (state == "MELAKA") {
            min_charge = 7.0;
            if (usage <= 20) charge = usage * 0.70;
            else if (usage > 20 && usage <= 35) charge = usage * 1.15;
            else charge = usage * 1.75;
        } 
        else if (state == "TERENGGANU") {
            min_charge = 4.0;
            if (usage <= 20) charge = usage * 0.42;
            else if (usage > 20 && usage <= 35) charge = usage * 0.65;
            else charge = usage * 0.90;
        } 
        else {
            cout << "Invalid state entered." << endl;
            return 1;
        }
    } 
    else if (type == "NON-DOMESTIC") {
        if (state == "NEGERI SEMBILAN") {
            min_charge = 18.50;
            if (usage <= 35) charge = usage * 1.85;
            else charge = usage * 2.70;
        } 
        else if (state == "MELAKA") {
            min_charge = 25.0;
            if (usage <= 35) charge = usage * 2.40;
            else charge = usage * 2.45;
        } 
        else if (state == "TERENGGANU") {
            min_charge = 15.0;
            if (usage <= 35) charge = usage * 1.00;
            else charge = usage * 1.40;
        } 
        else {
            cout << "Invalid state entered." << endl;
            return 1;
        }
    } 
    else {
        cout << "Invalid type of supply entered." << endl;
        return 1;
    }

    // Apply the minimum charge if the calculated charge is less than the minimum
    if (charge < min_charge) {
        cout << "The water usage charge is less than the minimum charge. The government will subsidize the charge to the minimum." << endl;
        charge = min_charge;
    } 
    else {
        cout << "The calculated charge meets or exceeds the minimum charge." << endl;
    }

    cout << "The water charge is: RM " << charge << endl;

    return 0;
}
