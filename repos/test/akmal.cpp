#include <iostream>
using namespace std;

int main()
{
    float testscore1, testscore2, testscore3, testscore4, testscore5, ave;
    string namestu, namehigh, namelow;
    int high = 0, low = 100, i = 0;

    while (i < 5)
    {
        cout << "Please enter the student name: ";
        getline(cin, namestu);

        cout << "Please enter 5 test scores for the student: ";
        cin >> testscore1 >> testscore2 >> testscore3 >> testscore4 >> testscore5;

        ave = (testscore1 + testscore2 + testscore3 + testscore4 + testscore5) / 5;

        if (ave > high)
        {
            high = ave;
            namehigh = namestu;
        }
        if (ave < low)
        {
            low = ave;
            namelow = namestu;
        }

        i++;
        cin.ignore(); 
    }

    cout << endl;
    cout << endl;
    cout << endl;
    cout << "PERFORMANCE OF FORM 5CS FOR COMPUTER SCIENCE SUBJECT" << endl;
    cout << "The student with the highest average score of " << high << " is " << namehigh << endl;
    cout << "The student with the lowest average score of " << low << " is " << namelow << endl;

    return 0;
}