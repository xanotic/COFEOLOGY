#include <iostream>
using namespace std;
#include <iomanip>

int main()
{
    char symbol;
    int num1, num2, answer;

    cout << setw(33) << left << "Enter a symbol (+,-,/,*) "
         << ":";
    cin >> symbol;
    cout << setw(33) << left << "Enter two numbers (integer only) "
         << ":";
    cin >> num1 >> num2;

    if (symbol == '+')
    {
        answer = num1 + num2;
    }
    else if (symbol == '-')
    {
        answer = num1 - num2;
    }
    else if (symbol == '/')
    {
        answer = num1 / num2;
    }
    else if (symbol == '*')
    {
        answer = num1 * num2;
    }
    else
    {
        cout << "Invalid" << endl;
        return 0;
    }

    cout << num1 << symbol << num2 << "=" << answer;

    return 0;
}