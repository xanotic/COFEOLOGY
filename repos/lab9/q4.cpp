#include <iostream>
using namespace std;

double outPatientCharge(string type){
    if (type == "normal"){
        return 70.00;
    }
    else if (type == "mild"){
        return 100.00;
    }
    else if (type == "serious"){
        return 200.00;
    }
    return 0; 
}

double wardedPatientCharge(string type, double days){
    double chargePerDay;
    if (type == "normal"){
        chargePerDay = 150.00;
    }
    else if (type == "mild"){
        chargePerDay = 200.00;
    }
    else if (type == "serious"){
        chargePerDay = 300.00;
    }
    else {
        return 0;
    }
    return chargePerDay * days;
}

int main (){
    char typeOfPatient;
    string type;
    double charge = 0;
    double days;

    cout << "Enter type of patient ('O' for outpatient and 'W' for warded patient): ";
    cin >> typeOfPatient;
    if (typeOfPatient == 'O'){
        cout << "Enter type of outpatient (normal, mild, serious): ";
        cin >> type;
        charge = outPatientCharge(type);
        cout << "The outpatient charge is: RM" << charge << endl;
    }
    else if (typeOfPatient == 'W'){
        cout << "Enter type of warded patient (normal, mild, serious): ";
        cin >> type;
        cout << "Enter number of days warded: ";
        cin >> days;
        charge = wardedPatientCharge(type, days);
        cout << "The warded patient charge for " << days << " days is: RM" << charge << endl;
    }
    else {
        cout << "Invalid type of patient entered." << endl;
    }
    
    return 0;
}
