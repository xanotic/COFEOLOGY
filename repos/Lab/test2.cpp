#include <iostream>
using namespace std;

void yardtometer();

int main() 
{
    double yard, meter;
    
    cout << "submit the yards: ";
    cin >> yard;

    meter = yard * 0.9144;
    cout << yard << "your yards is " << meter << " meter." << endl;

    yardtometer(); 

    return 0;
}

void yardtometer() 
{
    cout << " ";
}