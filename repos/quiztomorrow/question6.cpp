#include <iostream>
using namespace std;

int main()
{
    double accbalance;

    cout << "Enter your account balance: ";
    cin >> accbalance;

    if (accbalance < 1000)
    {
        cout << "Your account balance is less than 1000 so there will be $5 fee" << endl;
        accbalance -= 5;
    }
    else
    {
        cout << "Your account balance is more than 1000 so there will be no fee" << endl;
    }

    cout << "Your new account balance is: $" << accbalance << endl;
    return 0;
}