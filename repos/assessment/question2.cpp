#include <iostream>
using namespace std;

int main() 
{
    double kpi;
    double donationPercentage = 0;
    double incentiveA = 0;
    double incentiveB = 0;
    double incentiveC = 0;
    char certificate, attendance, charity;

    // ask employee for kpi
    cout << "Please enter your KPI of year 2023 :";
    cin >> kpi;

    // check the employee kpi 
    if (kpi >= 95 && kpi <= 100){
        incentiveA += 3200.00;
    } else if (kpi >= 90 && kpi <= 94.99){
        incentiveA += 1600.00;
    } else if (kpi >= 80 && kpi <= 89.99){
        incentiveA += 800.00;
    }

    // check if employee have professional board certificate
    cout << "Did you have professional board certificate? (Y/N) :";
    cin >> certificate;
    if (certificate == 'Y' || certificate == 'y') {
       incentiveB += 255.00;
    }

    // check if employee have full attendance
    cout << "Full attendance? (Y/N) :";
    cin >> attendance;
    if (attendance == 'Y' || attendance == 'y') {
        incentiveC += 745.00;
    }

    // check if employee wants to donate money to charity
    cout << "Donate for charity (Y/N) :";
    cin >> charity;
    if (charity == 'Y' || charity == 'y') {
        cout << "Enter the percentage (%) of donation :";
        cin >> donationPercentage;
        donationPercentage = (donationPercentage / 100) * (incentiveA + incentiveB + incentiveC);
    }

    // Calculate and display the final bonus
    double totalBonus = (incentiveA + incentiveB + incentiveC) - donationPercentage;
    cout << "Your bonus is : RM " << incentiveA + incentiveB + incentiveC << endl;
    cout << "Your net total bonus is RM : " << totalBonus << endl;

    return 0;
}
