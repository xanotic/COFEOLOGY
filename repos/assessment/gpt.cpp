#include <iostream>
#include <string>
#include <algorithm>

using namespace std;

int main() {
    string state, supplyType;
    double usage;
    double charge = 0.0;

    // Prompt the user for the type of supply
    cout << "Enter the type of supply (Domestic Individual, Domestic Bulk, Non-Domestic): ";
    getline(cin, supplyType);

    // Prompt the user to enter the state.
    cout << "Enter the state (Negeri Sembilan, Melaka, Terengganu): ";
    getline(cin, state);

    // Prompt the user to enter the amount of water used.
    cout << "Enter the amount of water used (in cubic meters): ";
    cin >> usage;

    // Check for invalid input
    if(cin.fail() || usage < 0) {
        cout << "Invalid input. Please enter a positive number for water usage." << endl;
        return 1; // Terminate the program if the input is invalid
    }
    
    // Calculate charges based on the state, type of supply, and the amount of water used.
    if (state == "Negeri Sembilan") {
        if (supplyType == "Domestic Individual") {
            if (usage <= 20) {
                charge = max(5.00, usage * 0.55);
            } else if (usage <= 35) {
                charge = 20 * 0.55 + (usage - 20) * 0.85;
            } else {
                charge = 20 * 0.55 + 15 * 0.85 + (usage - 35) * 1.40;
            }
        } else if (supplyType == "Domestic Bulk") {
            charge = max(30.00, usage * 1.40);
        } else if (supplyType == "Non-Domestic") {
            if (usage <= 35) {
                charge = max(18.50, usage * 1.85);
            } else {
                charge = 35 * 1.85 + (usage - 35) * 2.70;
            }
        }
    } else if (state == "Melaka") {
        if (supplyType == "Domestic Individual") {
            if (usage <= 20) {
                charge = max(7.00, usage * 0.70);
            } else if (usage <= 35) {
                charge = 20 * 0.70 + (usage - 20) * 1.15;
            } else {
                charge = 20 * 0.70 + 15 * 1.15 + (usage - 35) * 1.75;
            }
        } else if (supplyType == "Domestic Bulk") {
            charge = max(25.00, usage * 1.80);
        } else if (supplyType == "Non-Domestic") {
            if (usage <= 35) {
                charge = max(25.00, usage * 2.40);
            } else {
                charge = 35 * 2.40 + (usage - 35) * 2.45;
            }
        }
    } else if (state == "Terengganu") {
        if (supplyType == "Domestic Individual") {
            if (usage <= 20) {
                charge = max(4.00, usage * 0.42);
            } else if (usage <= 35) {
                charge = 20 * 0.42 + (usage - 20) * 0.65;
            } else {
                charge = 20 * 0.42 + 15 * 0.65 + (usage - 35) * 0.90;
            }
        } else if (supplyType == "Domestic Bulk") {
            charge = max(6.20, usage * 0.62);
        } else if (supplyType == "Non-Domestic") {
            if (usage <= 35) {
                charge = max(15.00, usage * 1.00);
            } else {
                charge = 35 * 1.00 + (usage - 35) * 1.40;
            }
        }
    } else {
        cout << "Invalid state entered. Please run the program again with a valid state." << endl;
        return 1;
    }

    // Output the total charges.
    cout << "Total water charges for " << state << " (" << supplyType << "): RM " << charge << endl;

    return 0;
}