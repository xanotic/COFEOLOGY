#include <iostream>
using namespace std;

int main (){

    int number;

    do
    {
        cout << "Enter integer number (negative number to quit) : ";
        cin >> number;

        if (number >= 0)
        {
            cout << "Number you are enter is " << number << endl;
        }
        else
        {
            cout << " **You are enter negative number. Bye2.. " << endl;
        }
        
    } while (number >= 0);
    

}