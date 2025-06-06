#include <iostream>
using namespace std;
#include <iomanip>

//“ An application to find the sum  and average of three numbers.”

int main() {
    int num1, num2, num3;
    int sumNum;
    double avg;

    
    cout << "Enter the first number: ";
    cin >> num1;

    cout << "Enter the second number: ";
    cin >> num2;

    cout << "Enter the third number: ";
    cin >> num3;

    
    sumNum = num1 + num2 + num3;  
    avg = sumNum / 3;

    
    cout <<"The sum of "<< num1 <<", "<< num2 << " and " << num3 << " is " << sumNum << endl;
    cout << "The average of " << num1 <<", " << num2 << " and " << num3 << " is "  << setprecision(2) << avg << endl;


    return 0;
}