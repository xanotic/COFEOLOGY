#include <iostream>
using namespace std;
#include <string>

int main ()
{
    char accountType;
    double currentBalance, newBalance, interest, serviceCharge;
    int customerNo = 1;

    while (true) {

        interest = 0;
        serviceCharge = 0;

        cout << "Insert your account type (S or s for saving, C or c for checking accounts) (Y to exit program) : ";
        cin >> accountType;

        if (accountType == 'Y' || accountType == 'y') {
            break;
        }

        cout << "Insert your account current balance : ";
        cin >> currentBalance;

        if (accountType == 's' || accountType == 'S') {
            if (currentBalance < 300) {
                serviceCharge += 10;
            }
            else if (currentBalance >= 300) {
                interest += 0.04 * currentBalance;
            }
        }
        else if (accountType == 'c' || accountType == 'C') {
            if (currentBalance < 300) {
                serviceCharge += 25;
            }
            else if (currentBalance >= 5000) {
                interest += 0.05 * currentBalance;
            }
            else if (currentBalance < 5000) {
                interest += 0.03 * currentBalance;
            }
        }

        newBalance = currentBalance - serviceCharge + interest;
        cout << "Customer No: " << customerNo << endl;
        cout << "Account Type: " << accountType << endl;
        cout << "Current Balance: " << currentBalance << endl;
        cout << "New Balance: " << newBalance << endl << endl;

        customerNo++;
    }
    
    

    return 0;
}
