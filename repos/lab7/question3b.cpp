#include <iostream>
using namespace std;

int main (){

    int sumOdd = 0;
    int number;

    do
    {
        cout << "enter integer number (999 to quit) ";
        cin >> number;
        
        if (number != 999 &&  number%2 != 0)
        {
            sumOdd += number;
        }
        
    } while (number != 999);

    cout << " The total of odd numbers are "<< sumOdd;

    return 0;

}
