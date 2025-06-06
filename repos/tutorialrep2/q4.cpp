#include <iostream>
using namespace std;

int main(){

    int n;
    cout << "Enter a non-negative integer: ";
    cin >> n;

    do
    {
        cout << "The integer you entered is negative." << endl;
        cout << "Enter a non-negative integer: ";
        cin >> n;
    } while (n < 0);
    
    


}