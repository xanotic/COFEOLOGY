#include <iostream>
#include <string>

using namespace std;

int main() {
    string typeOfCharge, domesticType, meterType;
    double meter, charge = 0.0, totalCharge;
    double mincharge = 0.0;

    cout << "Enter your state (NEGERI SEMBILAN, MELAKA, TERENGGANU): ";
    cin >> typeOfCharge;

    cout << "Enter your type of supply (DOMESTIC or NON-DOMESTIC): ";
    cin >> domesticType;

    if (domesticType == "DOMESTIC") {
        cout << "Choose your meter type (INDIVIDUAL or BULK): ";
        cin >> meterType;
    }

    cout << "Enter your water usage in cubic meters: ";
    cin >> meter;

    if (cin.fail()) {
        cout << "Invalid input data. Please enter numeric values for water usage." << endl;
        return 1;
    }

    if (typeOfCharge == "NEGERI SEMBILAN") {
        if (domesticType == "DOMESTIC") {
            if (meterType == "INDIVIDUAL") {
                mincharge = 5.00;
                if (meter <= 25) {
                charge = meter * 0.55;
                } 
                else if (meter > 25 && meter <= 35) {
                charge = meter * 0.85;
                } 
             else if (meter > 35) {
                charge = meter * 1.40;
                }
            } else if (meterType == "BULK") {
                mincharge = 30.00;
                charge = meter * 1.40;
            }
        } else if (domesticType == "NON-DOMESTIC") {
            // NON-DOMESTIC logic for NEGERI SEMBILAN
        }
    } else if (typeOfCharge == "MELAKA") {
        if (domesticType == "DOMESTIC") {
            if (meterType == "INDIVIDUAL") {
                mincharge = 7.00;
                // Add specific rates and logic for MELAKA INDIVIDUAL meter here
            } else if (meterType == "BULK") {
                mincharge = 25.00;
                // Add specific rates and logic for MELAKA BULK meter here
            }
        } else if (domesticType == "NON-DOMESTIC") {
            // NON-DOMESTIC logic for MELAKA
        }
    } else if (typeOfCharge == "TERENGGANU") {
        if (domesticType == "DOMESTIC") {
            if (meterType == "INDIVIDUAL") {
                mincharge = 4.00;
                // Add specific rates and logic for TERENGGANU INDIVIDUAL meter here
            } else if (meterType == "BULK") {
                mincharge = 6.20;
                // Add specific rates and logic for TERENGGANU BULK meter here
            }
        } else if (domesticType == "NON-DOMESTIC") {
            // NON-DOMESTIC logic for TERENGGANU
        }
    } else {
        cout << "Invalid state entered." << endl;
        return 1;
    }

    if ((domesticType != "DOMESTIC" && domesticType != "NON-DOMESTIC") ||
        (domesticType == "DOMESTIC" && meterType != "INDIVIDUAL" && meterType != "BULK")) {
        cout << "Invalid type of supply or meter type entered." << endl;
        return 1;
    }

    totalCharge = charge;

    // Apply the minimum charge if the calculated charge is less than the minimum
    if (totalCharge < mincharge) {
        cout << "Total usage less than minimum charge, the water will be subsidized by the government." << endl;
        totalCharge = mincharge;
    } else {
        cout << "Total usage is more than the minimum charge." << endl;
    }

    cout << "The water charge is: RM " << totalCharge << endl;

    return 0;
}
