#include <iostream>
using namespace std;

int main()
{
    int year;
    float salary, bonus;

    cout << "how many years have you been working for : ";
    cin >> year;
    cout << "what is your salary : ";
    cin >> salary;
    if (year >= 10)
    {
        bonus = salary * 1.5;
    }
    else
    {
        bonus = salary * 1.2;
    }

    cout << "your bonus is : " << bonus << endl;
}