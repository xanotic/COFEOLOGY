#include <iostream>
using namespace std;
#include <string>


int main ()
{
    char account;
    double currentBalance, newbalance, interest = 0,serviceCharge = 0;
    int customernum = 1;
    
        cout << "insert your account type (S or s for saving, C or c for checking accounts) (Y to exit program) : ";
        cin >> account;
        cout << "insert your account current balance : ";
        cin >> currentBalance;
        

        if (account == 's' || account == 'S'){
        if (currentBalance < 300){
            serviceCharge += 10;
        }
        else if (currentBalance >= 300){
            interest += 0.04 * currentBalance;
        }
          
    }
    else if (account == 'c' || account == 'C'){
        if (currentBalance < 300){
        
            serviceCharge += 25;
        }
        else if (currentBalance >= 5000){
            interest += 0.05 * currentBalance;
        }
        else if (currentBalance < 5000){
            interest += 0.03 * currentBalance;
        }
        
    }
        
    newbalance = currentBalance - serviceCharge + interest;
    cout << currentBalance << endl;
    cout << newbalance;

}
  