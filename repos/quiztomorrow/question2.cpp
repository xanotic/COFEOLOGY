#include <iostream>
using namespace std;

int main()
{
    int midTermGrader, finalTermGrade;
    float average;
    cout << "insert you mid term grade : ";
    cin >> midTermGrader;
    cout << "insert you final term grade : ";
    cin >> finalTermGrade;

    average = (midTermGrader + finalTermGrade) / 2;
    if (average >= 60)
    {
        cout << "pass" << endl;
    }
    else
    {
        cout << "fail" << endl;
    }
    return 0;
}