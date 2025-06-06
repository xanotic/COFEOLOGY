#include <iostream>
#include <cmath>
#include <iomanip>

using namespace std;

int main() {
    int firstNumb, secNumb;

    cout << "Insert First Number: ";
    cin >> firstNumb;

    cout << "Insert Second Number: ";
    cin >> secNumb;

    cout << "\n\n";

    cout << setw(30) << "Calculator" << "\n"<< setw(30)<<"----------"<<"\n\n";

    int addition, subtraction, multiplication, division, remainder;

    addition = firstNumb + secNumb;
    subtraction = firstNumb - secNumb;
    multiplication = firstNumb * secNumb;
    division = firstNumb / secNumb;
    remainder = firstNumb % secNumb;

    
    cout << setfill('*') << setw(30)<<"Addition : " << addition << endl;
    cout << setw(30)<<"Subtraction : " << subtraction << endl;
    cout << setw(30)<<"Multiplication : " << multiplication << endl;
    cout << setw(30)<<"Division : " << division << endl;
    cout << setw(30)<<"Remainder : " << remainder << endl;

    return 0;
}
