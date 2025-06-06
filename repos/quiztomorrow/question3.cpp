#include <iostream>
using namespace std;

int main()
{

    float quiz, test, testrate, total;

    cout << "Enter the quiz score: ";
    cin >> quiz;
    cout << "Enter the test score: ";
    cin >> test;
    cout << "Enter the test rate: ";
    cin >> testrate;

    total = quiz + test * testrate;

    cout << "your total mark is " << total << endl;
    if (total >= 50)
    {
        cout << "you have pass the test" << endl;
    }
    else
    {
        cout << "you have failed the test" << endl;
    }

    return 0;
}