#include <iostream>
#include <string>
using namespace std;

int main() {
    string state, supplyType;
    double waterUsage, totalCharge;
    double minCharge = 0.0, usageCharge = 0.0;

    cout << "Welcome to the Water Usage Charges Calculator" << endl;
    
    // Get the user's state
    cout << "Enter your state (NEGERI SEMBILAN, MELAKA, TERENGGANU): ";
    getline(cin, state);

    // Get the user's supply type
    cout << "Enter your type of supply (DOMESTIC or NON-DOMESTIC): ";
    getline(cin, supplyType);

    // Get the water usage in cubic meters
    cout << "Enter your water usage in cubic meters: ";
    cin >> waterUsage;

    // Input validation
    if (cin.fail() || waterUsage < 0) {
        cout << "Invalid input. Please enter a positive numeric value for water usage." << endl;
        return 1;
    }

    // Convert state and supply type to uppercase for case-insensitive checks
    for (char &c : state) {
        c = toupper(c);
    }

    for (char &c : supplyType) {
        c = toupper(c);
    }

    // Calculate charges based on state and supply type
    if (state == "NEGERI SEMBILAN") {
        if (supplyType == "DOMESTIC") {
            minCharge = 5.00;
            if (waterUsage <= 20) {
                usageCharge = waterUsage * 0.55;
            } else if (waterUsage <= 35) {
                usageCharge = waterUsage * 0.85;
            } else {
                usageCharge = waterUsage * 1.40;
            }
        } else if (supplyType == "NON-DOMESTIC") {
            minCharge = 18.50;
            if (waterUsage <= 35) {
                usageCharge = waterUsage * 1.85;
            } else {
                usageCharge = waterUsage * 2.70;
            }
        }
    } else if (state == "MELAKA") {
        if (supplyType == "DOMESTIC") {
            minCharge = 7.00;
            if (waterUsage <= 20) {
                usageCharge = waterUsage * 0.70;
            } else if (waterUsage <= 35) {
                usageCharge = waterUsage * 1.15;
            } else {
                usageCharge = waterUsage * 1.75;
            }
        } else if (supplyType == "NON-DOMESTIC") {
            minCharge = 25.00;
            if (waterUsage <= 35) {
                usageCharge = waterUsage * 2.40;
            } else {
                usageCharge = waterUsage * 2.45;
            }
        }
    } else if (state == "TERENGGANU") {
        if (supplyType == "DOMESTIC") {
            minCharge = 4.00;
            if (waterUsage <= 20) {
                usageCharge = waterUsage * 0.42;
            } else if (waterUsage <= 35) {
                usageCharge = waterUsage * 0.65;
            } else {
                usageCharge = waterUsage * 0.90;
            }
        } else if (supplyType == "NON-DOMESTIC") {
            minCharge = 15.00;
            if (waterUsage <= 35) {
                usageCharge = waterUsage * 1.00;
            } else {
                usageCharge = waterUsage * 1.40;
            }
        }
    } else {
        cout << "Invalid state entered." << endl;
        return 1;
    }

    // Calculate total charge
    totalCharge = max(minCharge, usageCharge);

    // Display the results
    cout << "\nWater Usage Charges Calculator" << endl;
    cout << "State: " << state << endl;
    cout << "Supply Type: " << supplyType << endl;
    cout << "Water Usage (cubic meters): " << waterUsage << endl;
    cout << "Usage Charge: RM " << usageCharge << endl;
    cout << "Minimum Charge: RM " << minCharge << endl;
    cout << "Total Charge: RM " << totalCharge << endl;

    return 0;
}
