#include <iostream>
using namespace std;
#include <string>
#include <iomanip>

int main() {

    string typeOfCharge, domesticType, meterType;
    double meter, totalCharge;
    double mincharge = 0.0;
    double charge = 0.0;

    cout << "Welcome to the Water Usage Charges Suruhanjaya Perkhidmatan Air Negara (SPAN)" << endl;
    //ask the user for the state
    cout << "Enter your state in uppercase (NEGERI SEMBILAN, MELAKA, TERENGGANU): ";
    getline(cin, typeOfCharge);
    //ask the user for the type of supply
    cout << "Enter your type of supply in uppercase (DOMESTIC or NON-DOMESTIC): ";
    getline (cin, domesticType);

    if (domesticType == "DOMESTIC") {
    cout << "Choose your meter type in uppercase (INDIVIDUAL or BULK): ";
    cin >> meterType;

    // ask the user for meter type
    if (meterType != "INDIVIDUAL" && meterType != "BULK") {
        cout << "Invalid meter type entered." << endl;
        return 1;
    }
}
    //ask the user for water usage
    cout << "Enter your water usage in meter: ";
    cin >> meter;
    //display error message if the user doesnt enter a number
    if (cin.fail()) {
        cout << "Please enter a number for water usage." << endl;
        return 1;
    }
    if (typeOfCharge == "NEGERI SEMBILAN" ) {
        if (domesticType == "DOMESTIC"){ 
            if (meterType == "INDIVIDUAL") {
            mincharge = 5.00;
            if (meter <= 20) {
                charge = meter * 0.55 ;
            } 
            else if (meter > 20 && meter <= 35) {
                charge = (meter-20) * 0.85 + 11;
            } 
            else if (meter > 35) {
                charge = (meter-35) * 1.40 + 23.75;
            }
            } 
            else if (meterType == "BULK") {
            mincharge = 30.00;
            charge = meter * 1.40;
            }
        } 
        else if (domesticType == "NON-DOMESTIC") {
            mincharge = 18.50;
            if (meter <= 35) {
                charge = meter * 1.85;
            } 
            else if (meter > 35) {
                charge = (meter-35) * 2.70 + 64.75;
            }
        }
    } 
    else if (typeOfCharge == "MELAKA" ) {
        if (domesticType == "DOMESTIC"){ 
            if (meterType == "INDIVIDUAL") {
            mincharge = 7.00;
            if (meter <= 20) {
                charge = meter * 0.70;
            } 
            else if (meter > 20 && meter <= 35) {
                charge = (meter-20) * 1.15 + 14;
            } 
            else if (meter > 35) {
                charge = (meter-35) * 1.75 + 31.25;
            }
            } 
            else if (meterType == "BULK") {
            mincharge = 25.00;
            charge = meter * 1.80;
            }
        } 
        else if (domesticType == "NON-DOMESTIC") {
            mincharge = 25.00;
            if (meter <= 35) {
                charge = meter * 2.40;
            } 
            else if (meter > 35) {
                charge = (meter-35) * 2.45 + 84;
            }
        }
    } 
    else if (typeOfCharge == "TERENGGANU" ) {
        if (domesticType == "DOMESTIC"){ 
            if (meterType == "INDIVIDUAL") {
            mincharge = 4.00;
            if (meter <= 20) {
                charge = meter * 0.42;
            }
            else if (meter > 20 && meter <= 35) {
                charge = (meter-20) * 0.65 + 8.4;
            } 
            else if (meter > 35) {
                charge = (meter-35) * 0.90 + 18.15;
            }
            } 
            else if (meterType == "BULK") {
            mincharge = 6.20;
            charge = meter * 0.62;
            }
        } 
        else if (domesticType == "NON-DOMESTIC") {
            mincharge = 15.00;
            if (meter <= 35) {
                charge = meter * 1.00;
            } 
            else if (meter > 35) {
                charge = (meter-35)* 1.40 + 35;
            }
        }
    }// display error message if the user entered the wrong state
    if (typeOfCharge != "NEGERI SEMBILAN" && typeOfCharge != "TERENGGANU" && typeOfCharge != "MELAKA") {
        cout << "Invalid state entered." << endl;
        return 1;
    }// display error message if the user entered the wrong type of supply
    if (domesticType != "DOMESTIC" && domesticType != "NON-DOMESTIC") {
        cout << "Invalid type of supply entered." << endl;
        return 1;
    }//display error message if the user entered negative number
    if (meter < 0) {
        cout << "Please enter a positive number." << endl;
        return 1;
    }

    totalCharge = charge;
    //display the total charge that the user need to pay
    cout << "\nWater Usage Charges Calculator" << endl << endl;
    cout << left << setw(25) << "State " << ": "<< typeOfCharge << endl;
    cout << setw(25)<< "Water Supply Type " << ": "<< domesticType <<" "<< meterType << endl;
    cout << setw(25)<< "Water Usage "<< ": " << meter << "m" << endl;
    cout << setw(25)<< "Minimum Charge "<< ": RM" << mincharge << endl;
    cout << setw(25)<< "Total Charge " << ": RM"<< totalCharge << endl << endl;

    if (totalCharge < mincharge) {
        cout << "Total charge is less than minimum charge, the water will be subsidies by the government." << endl;
        totalCharge = mincharge;
    } else {
        cout << "Total charge is more than the minimum charge. So you need to pay : "<<"RM" <<totalCharge << endl;
    }

    return 0;
}