#include <iostream>
using namespace std;

int main (){

    int i = 1;
    int j;

    cout << "Please enter a number: ";
    cin >> j;

    cout << "Multiplication table for the number "<< j << " is:"<< endl;

    while (i <= 12)
    {
        cout << i << " x " << j << " = " << i*j << endl;
        i++;
    }

}