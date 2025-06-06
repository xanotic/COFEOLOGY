#include <iostream>
using namespace std;
#include <string>

int main (){

    string typeOfCharge, domestic;
    double meter, totalCharge;
    double charge = 0;
    double mincharge = 0;

    cout << "Enter your state (NEGERI SEMBILAN, MELAKA, TERENGGANU): ";
    cin >> typeOfCharge;

    cout << "Enter your type of supply (DOMESTIC or NON-DOMESTIC): ";
    cin >> domestic;

    cout << "Enter your water usage in cubic meters: ";
    cin >> meter;

    if (typeOfCharge == "NEGERI SEMBILAN"){
        if (domestic == "individual") {
            mincharge = 5.00;
            if (meter <= 20){
                charge = 0.55;
            }
            else if (meter > 20 && meter <= 35){
                charge = 0.85;
            }
            else if (meter > 35){
                charge = 1.40;
            }
        }
        else if (domestic == "bulk"){
            mincharge = 30.00;
            charge = 1.40;
        }
        else if (domestic == "NON-DOMESTIC"){
            mincharge = 18.50;
        if (meter <= 35){
                charge = 1.85;
            }
            else if (meter > 35){
                charge = 2.70;
            }
        }
    }

    if (typeOfCharge == "MELAKA"){
        if (domestic == "individual") {
            mincharge = 7.00;
            if (meter <= 20){
                charge = 0.70;
            }
            else if (meter > 20 && meter <= 35){
                charge = 1.15;
            }
            else if (meter > 35){
                charge = 1.75;
            }
        }
        else if (domestic == "bulk"){
            charge = 1.80;
            mincharge = 25.00;
        }
        else if (domestic == "NON DOMESTIC"){
            mincharge = 25.00;
            if (meter <= 35){
                charge = 2.40;
            }
            else if (meter > 35){
                charge = 2.45;
            }
        }
    }

    if (typeOfCharge == "TERENGGANU"){
        if (domestic == "individual") {
            mincharge = 4.00;
            if (meter <= 20){
                charge = 0.42;
            }
            else if (meter > 20 && meter <= 35){
                charge = 0.65;
            }
            else if (meter > 35){
                charge = 0.90;
            }
        }
        else if (domestic == "bulk"){
            mincharge = 6.20;
            charge = 0.62;
        }
        else if (domestic == "NON DOMESTIC"){
            mincharge = 15.00;
        if (meter <= 35){
                charge = 1.00;
            }
            else if (meter > 35){
                charge = 1.40;
            }
        }
    }

    totalCharge = charge*meter;

    if (totalCharge < mincharge) {
        cout << "Total usage less than minimum charge, the water will be subsidies by the government." << endl;
        totalCharge = mincharge;
    } 
    else {
        cout << "Total usage is more than the minimum charge." << endl;
    }

    cout << "The water charge is: RM " << totalCharge << endl;


    
}

